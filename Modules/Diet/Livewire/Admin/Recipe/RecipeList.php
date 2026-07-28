<?php

namespace Modules\Diet\Livewire\Admin\Recipe;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Recipe\app\Models\Recipe;

class RecipeList extends Component
{
    use WithPagination;

    // =========================
    // Cache config
    // =========================
    private const CACHE_TAG = 'admin:recipe_list';
    private const CACHE_TTL = 604800; // 1 week

    public array $form = [
        'search' => [
            'id' => null,
            'name' => null,
        ],
    ];

    public $searchPanel = '';

    // برای اینکه با تغییر سرچ، صفحه‌بندی برگرده صفحه 1
    protected $updatesQueryString = [
        'form.search.id' => ['except' => null],
        'form.search.name' => ['except' => null],
        'page' => ['except' => 1],
    ];

    // =========================
    // Actions
    // =========================
    public function startSearch(): void
    {
        $this->resetPage();
    }

    public function ignoresearch(): void
    {
        unset($this->form['search']);
        $this->form['search'] = ['id' => null, 'name' => null];
        $this->resetPage();
    }

    /**
     * نسخه بهینه‌شده تصویر:
     * - هیچ Component جدیدی ساخته نمی‌شود
     * - بدون file_exists سنگین (I/O دیسک)
     * - URL نهایی کش می‌شود (۱ هفته)
     */
    public function recipeImageUrl(?array $detail): string
    {
        $fallback = asset('assets/recipe/no_image.jpeg');

        if (!is_array($detail)) {
            return $fallback;
        }

        $relative = $detail['detail_image'] ?? $detail['detail_image'] ?? null;
        if (!$relative) {
            return $fallback;
        }

        $key = "recipe_image_url:v2:" . md5($relative);

        return Cache::tags(self::CACHE_TAG)->remember($key, self::CACHE_TTL, function () use ($relative, $fallback) {
            // اگر مسیرش داخل public disk هست:
            // توجه: Storage::disk('public')->exists خیلی سریع‌تر از file_exists(public_path(...)) هست
            $clean = ltrim(str_replace('storage/', '', $relative), '/');

            try {
                if (Storage::disk('public')->exists($clean)) {
                    return asset('storage/' . $clean);
                }
            } catch (\Throwable $e) {
                // اگر هر مشکلی بود fallback
            }

            return $fallback;
        });
    }

    #[On('delete')]
    public function deleteRecipe(Recipe $model)
    {
        $model->delete();

        // چون ممکنه تصویر/لیست تغییر کنه
        Cache::tags(self::CACHE_TAG)->flush();

        return redirect()->route('admin.recipe.list')
            ->with('success', 'دستور غذا با موفقیت حذف شذ');
    }

    public function duplicateRecipe(int $id)
    {
        return $this->copyRecipe($id);
    }

    public function copyRecipe(int $recipeId)
    {
        try {
            // لازم‌ها رو eager-load کن (بدون query اضافی)
            $originalRecipe = Recipe::with(['units', 'basicFoods'])->findOrFail($recipeId);

            // replicate مستقیم بهتر از toArray برای cast/json هاست
            $newRecipe = $originalRecipe->replicate();
            $newRecipe->name = $originalRecipe->name . ' (Copy)';
            $newRecipe->created_at = now();
            $newRecipe->updated_at = now();
            $newRecipe->save();

            // units
            if ($originalRecipe->units && $originalRecipe->units->isNotEmpty()) {
                $newRecipe->units()->createMany(
                    $originalRecipe->units->map(fn($unit) => [
                        'food_unit_id' => $unit->food_unit_id,
                        'quantity_per_unit' => $unit->quantity_per_unit,
                    ])->toArray()
                );
            }

            // basic foods pivot
            $syncData = [];
            foreach ($originalRecipe->basicFoods as $basicFood) {
                $syncData[$basicFood->id] = [
                    'food_unit_id' => $basicFood->pivot->food_unit_id,
                    'quantity_per_unit' => $basicFood->pivot->quantity_per_unit,
                ];
            }
            $newRecipe->basicFoods()->sync($syncData);

            // چون لیست تغییر می‌کنه
            Cache::tags(self::CACHE_TAG)->flush();

            return redirect()->route('admin.recipe.edit', $newRecipe->id)
                ->with('success', 'دستور با موفقیت ایجاد و شما در صفحه ویرایش آن هستید');
        } catch (\Exception $e) {
            // اینجا بهتره redirect با خطا بدی که UI نخوابه
            return redirect()->route('admin.recipe.list')
                ->with('error', 'خطا در کپی دستور: ' . $e->getMessage());
        }
    }

    public function requestClearRecipeListCaches(): void
    {
        $this->dispatch('confirm-clear-recipe-list-caches');
    }

    public function clearRecipeListCaches(): void
    {
        Cache::tags(self::CACHE_TAG)->flush();
        $this->dispatch('updateUi');
        session()->flash('success', 'کش‌های لیست دستور پخت پاک شد.');
    }

    // =========================
    // Performance: reactive search
    // =========================
    public function updated($name, $value): void
    {
        // هر وقت سرچ تغییر کرد، صفحه 1
        if (str_starts_with((string)$name, 'form.search.')) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $searchId = $this->form['search']['id'] ?? null;
        $searchName = $this->form['search']['name'] ?? null;

        // Query بهینه:
        // - ستون‌های لازم
        // - eager-load category با select
        $query = Recipe::query()
            ->select([
                'id',
                'name',
                'category_id',
                'calorie',
                'suggested',
                'active',
                'detail',
            ])
            ->with([
                'category' => fn($q) => $q->select(['id', 'name'])
            ])
            ->orderByDesc('id');

        if (filled($searchId)) {
            $query->where('id', (int)$searchId);
        }

        if (filled($searchName)) {
            $query->where('name', 'LIKE', '%' . $searchName . '%');
        }

        // NOTE: کش کردن paginate معمولاً به page/querystring وابسته است و پیچیدگی دارد.
        // اما گلوگاه‌های اصلی حل شدند (N+1 و file IO و ساخت component در blade).
        $recipes = $query->paginate(50);

        return view('diet::livewire.admin.recipe.recipe-list', [
            'recipes' => $recipes,
        ]);
    }
}
