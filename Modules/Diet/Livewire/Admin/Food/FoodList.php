<?php

namespace Modules\Diet\Livewire\Admin\Food;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Entities\Disease;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Entities\Food;
use Modules\Diet\Entities\Meal;
use Modules\Diet\Enum\ConditionApplyItemEnum;
use Modules\Diet\Livewire\Admin\Traits\ConditionTrait;

#[Title('مدیریت غذا ها')]
class FoodList extends Component
{
    use WithPagination, ConditionTrait;

    #[Url]
    public array $search = [];

    public string $searchPanel = '';

    #[Url]
    public $samleDietList = null;

    #[Url]
    public int $page = 1;

    #[Url]
    public $parent_id = null;

    public $food = null;

    public int $iteration = 20;

    public bool $selectAll = false;

    public array $selectedFoods = [];

    /**
     * ids همان صفحه برای selectAll
     */
    public array $currentPageFoodIds = [];

    /**
     * version برای cache نتایج complexFoodResult (هر وقت خواستی همه رو invalidate کنی، اینو تغییر بده)
     */
    private const COMPLEX_CACHE_VERSION = 'v1';

    public function startSearch(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatedPage($page): void
    {
        $this->dispatch('changePage', ['page' => $page]);
        $this->resetSelection();
    }

    public function updatedSelectAll($value): void
    {
        if (! $value) {
            $this->selectedFoods = [];
            return;
        }

        // انتخاب فقط آیتم‌های همین صفحه
        $this->selectedFoods = $this->currentPageFoodIds;
    }

    public function updatedSelectedFoods(): void
    {
        // sync وضعیت selectAll با selectedFoods
        $this->selectAll = !empty($this->currentPageFoodIds)
            && count($this->selectedFoods) === count($this->currentPageFoodIds);
    }

    private function resetSelection(): void
    {
        $this->selectAll = false;
        $this->selectedFoods = [];
    }

    public function deleteSelected(): void
    {
        if (empty($this->selectedFoods)) {
            return;
        }

        // اگر Policy لازم داری، اینجا می‌تونی حلقه authorize بزنی
        Food::whereIn('id', $this->selectedFoods)->delete();

        // پاک کردن کش complex برای آیتم‌های حذف‌شده
        foreach ($this->selectedFoods as $foodId) {
            Cache::forget($this->complexCacheKey((int) $foodId));
        }

        $this->resetSelection();
        $this->resetPage();

        session()->flash('success', 'موارد انتخاب‌شده حذف شدند.');
    }

    #[On('delete')]
    public function delete(Food $model): void
    {
        $this->authorize('delete', $model);

        // قبل از delete، کشش رو پاک کن
        Cache::forget($this->complexCacheKey((int) $model->id));

        $model->delete();

        // update max calorie when delete child
        if ($model->parent_id !== null) {
            $parentFood = Food::find($model->parent_id);
            if ($parentFood) {
                $findMaxChildCalorie = $parentFood->children()->max('calories');
                if ($findMaxChildCalorie < $parentFood->max_calories) {
                    $parentFood->update(['max_calories' => $findMaxChildCalorie]);
                }
            }
        }
    }

    private function complexCacheKey(int $foodId): string
    {
        return 'diet:food:complex:' . self::COMPLEX_CACHE_VERSION . ':' . $foodId;
    }

    public function render()
    {
        $foodsQuery = Food::query()
            ->where('parent_id', $this->parent_id)
            ->when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($q) {
                $q->where('id', (int) $this->search['id']);
            })
            ->when(isset($this->search['name']) && filled($this->search['name']), function ($q) {
                $q->where('name', 'LIKE', "%{$this->search['name']}%");
            })
            ->when(isset($this->search['meal']) && filled($this->search['meal']), function ($q) {
                $q->whereHas('meals', fn ($mq) => $mq->where('id', (int) $this->search['meal']));
            });

        if (!empty($this->search['conditions'] ?? null)) {
            $foodsQuery = app('dietService')->handleConditionFoodSearch($foodsQuery, $this->search['conditions']);
        }

        $order = $this->parent_id !== null ? 'ASC' : 'DESC';

        // فقط ستون‌های لازم برای جدول (سبک‌تر)
        $foodsQuery->select([
            'id', 'parent_id', 'name',
            'calories', 'max_calories',
            'protein', 'carb', 'fat', 'fiber',
        ]);

        // جلوگیری از N+1 با eager load و withCount
        $foods = $foodsQuery
            ->when(is_null($this->parent_id), fn ($q) => $q->withCount('children'))
            ->withCount('basicFoodPivot')
            ->with([
                // اگر complexFoodResult نیاز داره، اینها معمولاً ضروری‌اند
                'basicFoodPivot.units',
                'meals:id',
            ])
            ->orderBy('id', $order)
            ->paginate(40);

        // ids همین صفحه برای selectAll
        $this->currentPageFoodIds = $foods->pluck('id')->values()->all();

        // لیست‌های ثابت (کش)
        $conditions = Cache::remember(
            'diet:food:conditions:' . ConditionApplyItemEnum::FOOD->value,
            now()->addHours(6),
            fn () => Condition::whereJsonContains('apply_to', ConditionApplyItemEnum::FOOD->value)->get()
        );

        $disease = Cache::remember(
            'diet:diseases:active',
            now()->addHours(6),
            fn () => Disease::active()->get()
        );

        $meals = Cache::remember(
            'diet:meals:priority',
            now()->addHours(6),
            fn () => Meal::orderBy('priority')->get(['id', 'name'])
        );

        /**
         * complexFoodResult:
         * - اگر متد سنگینه، cache per-food کمک زیادی می‌کنه
         * - چون خروجی HTML است و stable، 12 ساعت مناسبه
         */
        $complexResults = [];
        foreach ($foods as $food) {
            $key = $this->complexCacheKey((int) $food->id);

            $complexResults[$food->id] = Cache::remember(
                $key,
                now()->addHours(12),
                fn () => app('dietService')->complexFoodResult($food)
            );
        }

        return view('diet::livewire.admin.food.food-list', compact(
            'foods',
            'conditions',
            'disease',
            'meals',
            'complexResults'
        ));
    }

    public function getSpecificDays($startDate, $dayType, $numDays)
    {
        $days = [];
        $currentDate = Carbon::parse($startDate);

        $dayType = strtolower($dayType);
        $desiredDays = [];

        if ($dayType == 'odd') {
            $desiredDays = ['Sunday', 'Tuesday', 'Thursday'];
        } elseif ($dayType == 'even') {
            $desiredDays = ['Saturday', 'Monday', 'Wednesday'];
        }

        while (count($days) < $numDays) {
            $dayOfWeek = $currentDate->format('l');

            if (in_array($dayOfWeek, $desiredDays) && !$currentDate->isFriday()) {
                $days[] = $currentDate->format('Y/m/d');
            }

            $currentDate->addDay();
        }

        return $days;
    }
}
