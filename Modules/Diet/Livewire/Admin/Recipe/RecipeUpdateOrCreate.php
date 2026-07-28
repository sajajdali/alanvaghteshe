<?php

namespace Modules\Diet\Livewire\Admin\Recipe;

use App\trait\UploadFileTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Diet\Entities\BasicFood;
use Modules\Diet\Entities\Food;
use Modules\Diet\Entities\FoodUnit;
use Modules\Recipe\app\Models\Recipe;
use Modules\Recipe\app\Models\RecipeCategory;

class RecipeUpdateOrCreate extends Component
{
    use WithFileUploads, UploadFileTrait;

    protected $filePath = 'recipe';

    // =========================
    // Cache config
    // =========================
    private const CACHE_TAG = 'admin:recipe_form';
    private const CACHE_TTL = 604800; // 1 week in seconds

    private const CACHE_KEY_CATEGORIES = 'recipe_form:categories:v1';
    private const CACHE_KEY_BASIC_FOODS = 'recipe_form:basic_foods_active:v1';
    private const CACHE_KEY_FOODS_ROOT = 'recipe_form:foods_root:v1';
    private const CACHE_KEY_FOOD_UNITS  = 'recipe_form:food_units_all:v1';

    // =========================
    // State
    // =========================
    public ?Recipe $recipe = null;

    public array $fetchData = [];

    public array $form = [
        'instructionsCounter' => 1,
        'countBasicFoods' => 1,
        'active' => true,
    ];

    public $isEdited = false;

    public $computData = [
        'carb' => 0,
        'protein' => 0,
        'fat' => 0,
        'fiber' => 0,
        'recipe' => null,
        'min_calorie' => 0,
        'max_calorie' => null,
    ];

    /**
     * برای جلوگیری از Queryهای تکراری در calculateData،
     * مواد غذایی انتخاب‌شده را یک‌بار eager-load می‌کنیم.
     */
    private array $basicFoodCache = []; // [id => BasicFood model(with units)]

    protected function messages()
    {
        return [
            'form.name.required' => 'فیلد نام غذا اجباری است',
            'form.name.max' => 'کاراکتر های وارده بیش از حد مجاز است',
            'form.time.required' => 'فیلد زمان اجباری است',
            'form.serving.required' => 'فیلد برای چند نفر اجباری است',
            'form.weight.required' => 'فیلد وزن غذا اجباری است',
            'form.calorie.required' => 'فیلد کالری اجباری است',
            'form.difficulty.required' => 'فیلد درجه سختی اجباری است',
            'form.category.required' => 'فیلد دسته بندی اجباری است',
            'form.recipe.unit.required' => 'لطفا واحد غذا را انتخاب کنید',
            'form.recipe.gram.required' => 'لطفا مقدار گرم در هر واحد غذایی را وارد کنید',
            'form.instroduction.0.required' => 'وارد کردن دستور پخت الزامی میباشد',
            'form.instroduction.*.required' => 'در صورت عدم نیاز به این فیلد آن را حدف کنید و یا در آن مقدار بنویسید',
            'form.foods.0.basic_food_id.required' => 'لطفا مواد تشکیل دهنده را انتخاب کنید',
            'form.foods.*.basic_food_id.required' => 'لطفا مواد تشکیل دهنده را به صورت کامل انتخاب کنید',
            'form.foods.*.unit_type.required' => 'لطفا مواد تشکیل دهنده را به صورت کامل انتخاب کنید',
            'form.foods.*.unit.required' => 'لطفا میزان مواد تشکیل دهنده را به صورت کامل انتخاب کنید',
        ];
    }

    protected function rules()
    {
        $rules = [
            'form.name' => 'required|string|max:225',
            'form.time' => 'required|integer',
            'form.serving' => 'required|integer',
            'form.weight' => 'required|integer',
            'form.calorie' => 'required|integer',
            'form.description' => 'nullable|max:5000',
            'form.difficulty' => 'required|integer',
            'form.category' => 'required',
            'form.recipe.unit' => 'required',
            'form.recipe.gram' => 'required',
            'form.instroduction.0' => 'required',
            'form.foods.0.basic_food_id' => 'required',
            'form.foods.0.unit_type' => 'required',
            'form.foods.0.unit' => 'required',
        ];

        for ($i = 1; $i < ($this->form['instructionsCounter'] ?? 1); $i++) {
            $rules['form.instroduction.' . $i] = 'required';
        }

        for ($i = 1; $i < ($this->form['countBasicFoods'] ?? 1); $i++) {
            $rules['form.foods.' . $i . '.basic_food_id'] = 'required';
            $rules['form.foods.' . $i . '.unit_type'] = 'required';
            $rules['form.foods.' . $i . '.unit'] = 'required';
        }

        return $rules;
    }

    // =========================
    // Cache helpers
    // =========================
    private function rememberTagged(string $key, \Closure $callback)
    {
        return Cache::tags(self::CACHE_TAG)->remember($key, self::CACHE_TTL, $callback);
    }

    private function warmFetchData(): void
    {
        // دسته‌بندی‌ها (فقط ستون‌های لازم)
        $this->fetchData['categories'] = $this->rememberTagged(self::CACHE_KEY_CATEGORIES, function () {
            return RecipeCategory::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });

        // BasicFoods فعال برای select (فقط ستون‌های لازم برای نمایش)
        // NOTE: food_fact برای نمایش کالری لازم بود، همونو نگه داشتیم.
        $this->fetchData['BasicFoods'] = $this->rememberTagged(self::CACHE_KEY_BASIC_FOODS, function () {
            return BasicFood::query()
                ->active()
                ->select(['id', 'name', 'food_fact'])
                ->orderBy('name')
                ->get();
        });

        // Food های ریشه (برای غذای اصلی) - فقط ستون‌های لازم
        $this->fetchData['foods'] = $this->rememberTagged(self::CACHE_KEY_FOODS_ROOT, function () {
            return Food::query()
                ->whereNull('parent_id')
                ->select(['id', 'name', 'calories'])
                ->orderBy('name')
                ->get();
        });

        // واحدها
        $this->fetchData['fooduits'] = $this->rememberTagged(self::CACHE_KEY_FOOD_UNITS, function () {
            return FoodUnit::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });

        // اولویت
        // (این یکی بهتره کش نشه چون با اضافه شدن رکورد جدید تغییر می‌کنه)
        $this->form['priority'] = Recipe::maxOrder();
    }

    public function requestClearRecipeFormCaches(): void
    {
        // فقط برای نمایش confirm در JS
        $this->dispatch('confirm-clear-recipe-form-caches');
    }

    public function clearRecipeFormCaches(): void
    {
        Cache::tags(self::CACHE_TAG)->flush();

        // reload cached data
        $this->warmFetchData();

        // اگر در حالت ویرایش هستی، فرم دوباره پر بشه
        $recipe = request()->route('recipe');
        if ($recipe instanceof Recipe) {
            $this->fillTheform();
        }

        $this->dispatch('updateUi');
        session()->flash('success', 'کش‌های فرم دستور پخت پاک شد و داده‌ها بروزرسانی شدند.');
    }

    // =========================
    // Photo
    // =========================
    public function deletePhoto()
    {
        if ($this->uploadedPhotoUrl) {
            $relativePath = str_replace(Storage::disk('public')->url(''), '', $this->uploadedPhotoUrl);
            Storage::disk('public')->delete($relativePath);
        }

        $this->photo = null;
        $this->uploadedPhotoUrl = null;
    }

    // =========================
    // Save
    // =========================
    public function Recepie()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('updateUi', true);
            throw $e;
        }

        $instroduction = [];
        for ($i = 0; $i < ($this->form['instructionsCounter'] ?? 1); $i++) {
            $instroduction[] = ['text' => $this->form['instroduction'][$i] ?? ''];
        }

        $model = [
            'category_id' => $this->form['category'],
            'name' => $this->form['name'],
            'description' => $this->form['description'] ?? null,
            'time' => $this->form['time'],
            'serving' => $this->form['serving'],
            'weight' => $this->form['weight'],
            'calorie' => $this->form['calorie'],
            'difficulty' => $this->form['difficulty'],
            'instructions' => $instroduction,
            'suggested' => isset($this->form['sugested']) ? $this->form['sugested'] : 0,
            'priority' => $this->form['priority'],
            'active' => $this->form['active'],
        ];

        if (isset($this->form['mainFood'])) {
            $model['food_id'] = $this->form['mainFood'];
        }

        // photo move & watermark
        if (!empty($this->form['photo'])) {
            $temporaryPath = $this->form['photo'];
            $finalPath = str_replace('temp/', '', $temporaryPath);

            Storage::disk('public')->move($temporaryPath, $finalPath);

            $manager = ImageManager::gd();
            $image = $manager->read(public_path('storage/' . $finalPath));
            $icon = $manager->read(public_path('default/logo1.png'));
            $icon2 = $manager->read(public_path('default/logo2.png'));
            $image->place($icon, 'bottom-right', 10, 10);
            $image->place($icon2, 'top-left', 10, 10);
            $image->save(public_path('storage/' . $finalPath));

            $model['detail'][Recipe::DETAIL_IMAGE] = $finalPath;
        }

        $foodFacts = [
            'createdAt' => Carbon::now()->toDateTimeString(),
            'updatedAt' => Carbon::now()->toDateTimeString(),
            'fatAmount' => $this->computData['fat'] ?? 0,
            'ironAmount' => $this->computData['iron'] ?? 0,
            'fiberAmount' => $this->computData['fiber'] ?? 0,
            'sugarAmount' => $this->computData['suger'] ?? 0,
            'sodiumAmount' => $this->computData['sodium'] ?? 0,
            'calciumAmount' => $this->computData['calcium'] ?? 0,
            'calorieAmount' => $this->computData['min_calorie'] ?? 0,
            'proteinAmount' => $this->computData['protein'] ?? 0,
            'phosphorAmount' => $this->computData['phosphor'] ?? 0,
            'transFatAmount' => $this->computData['trans_fat'] ?? 0,
            'magnesiumAmount' => $this->computData['magnesium'] ?? 0,
            'potassiumAmount' => $this->computData['porassium'] ?? 0,
            'cholesterolAmount' => $this->computData['cholestrol'] ?? 0,
            'carbohydrateAmount' => $this->computData['carb'] ?? 0,
            'saturatedFatAmount' => $this->computData['saturated_fat'] ?? 0,
            'monounsaturatedFatAmount' => $this->computData['mono_fat'] ?? 0,
            'polyunsaturatedFatAmount' => $this->computData['mono_fat'] ?? 0, // همون قبلی برای عدم تغییر رفتار
        ];

        $model['foodFact'] = $foodFacts;

        if ($this->isEdited && $this->recipe) {
            $this->recipe->update($model);

            $this->recipe->units()->updateOrCreate(
                ['recipe_id' => $this->recipe->id],
                [
                    'food_unit_id' => $this->form['recipe']['unit'],
                    'quantity_per_unit' => $this->form['recipe']['gram']
                ]
            );

            $syncData = [];
            foreach (($this->form['foods'] ?? []) as $basicFood) {
                if (!isset($basicFood['basic_food_id'])) continue;

                $syncData[$basicFood['basic_food_id']] = [
                    'food_unit_id' => $basicFood['unit_id'] ?? $basicFood['unit_type'] ?? null,
                    'quantity_per_unit' => $basicFood['unit'] ?? 0,
                ];
            }
            $this->recipe->basicFoods()->sync($syncData);

            $msg = 'دستور پخت با موفقیت ویرایش شد';
        } else {
            $rec = Recipe::create($model);

            $rec->units()->create([
                'food_unit_id' => $this->form['recipe']['unit'],
                'quantity_per_unit' => $this->form['recipe']['gram']
            ]);

            foreach (($this->form['foods'] ?? []) as $basicFood) {
                if (!isset($basicFood['basic_food_id'])) continue;

                $rec->basicFoods()->attach($basicFood['basic_food_id'], [
                    'food_unit_id' => $basicFood['unit_id'] ?? $basicFood['unit_type'] ?? null,
                    'quantity_per_unit' => $basicFood['unit'] ?? 0,
                ]);
            }

            $msg = 'دستور پخت با موفقیت اضافه شد';
        }

        return redirect()->route('admin.recipe.list')->with('success', $msg);
    }

    // =========================
    // Counters
    // =========================
    public function addcounter($counterName): void
    {
        $this->form[$counterName] = (int)($this->form[$counterName] ?? 0) + 1;
        $this->dispatch('updateUi');
    }

    public function removeCounter($counterName, $itemSelected): void
    {
        $this->form[$counterName] = max(1, (int)($this->form[$counterName] ?? 1) - 1);

        if ($counterName == 'instructionsCounter') {
            unset($this->form['instroduction'][(int)$itemSelected]);
            $this->form['instroduction'] = array_values($this->form['instroduction'] ?? []);
        } else {
            if (isset($this->form['foods'][(int)$itemSelected])) {
                unset($this->form['foods'][(int)$itemSelected]);
                $this->form['foods'] = array_values($this->form['foods'] ?? []);
                $this->calculateData();
            }
        }

        $this->dispatch('updateUi');
    }

    // =========================
    // Performance: preload selected BasicFoods + units once
    // =========================
    private function preloadSelectedBasicFoods(): void
    {
        $ids = collect($this->form['foods'] ?? [])
            ->pluck('basic_food_id')
            ->filter(fn($v) => filled($v))
            ->map(fn($v) => (int)$v)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            $this->basicFoodCache = [];
            return;
        }

        // فقط همین ID ها + eager-load units (pivot quantity_per_unit, is_primary)
        $models = BasicFood::query()
            ->whereIn('id', $ids->all())
            ->select(['id', 'food_fact']) // برای محاسبات کافی است
            ->with([
                'units' => function ($q) {
                    $q->select(['food_units.id', 'food_units.name'])
                        ->withPivot(['quantity_per_unit', 'is_primary']);
                }
            ])
            ->get()
            ->keyBy('id');

        $this->basicFoodCache = $models->all();
    }

    public function calculateData()
    {
        $this->computData = [
            'calorie' => 0,
            'carb' => 0,
            'protein' => 0,
            'fat' => 0,
            'fiber' => 0,
            'recipe' => null,
            'min_calorie' => 0,
            'maximum_amount_of_food' => null,
            'suger' => 0,
            'iron' => 0,
            'sodium' => 0,
            'calcium' => 0,
            'phosphor' => 0,
            'trans_fat' => 0,
            'magnesium' => 0,
            'porassium' => 0,
            'cholestrol' => 0,
            'saturated_fat' => 0,
            'mono_fat' => 0,
            'poly_fat' => 0,
            'total_quantity_per_gram' => 0,
            'recipe_list' => [],
        ];

        $nutritionKeys = [
            'CALORIE' => 'min_calorie',
            'PROTEIN' => 'protein',
            'CARBOHYDRATE' => 'carb',
            'FIBER' => 'fiber',
            'FAT' => 'fat',
            'IRON' => 'iron',
            'SUGAR' => 'suger',
            'SODIUM' => 'sodium',
            'CALCIUM' => 'calcium',
            'PHOSPHOR' => 'phosphor',
            'TRANS_FAT' => 'trans_fat',
            'MAGNESIUM' => 'magnesium',
            'POTASSIUM' => 'porassium',
            'CHOLESTEROL' => 'cholestrol',
            'SATURATED_FAT' => 'saturated_fat',
            'MONOUNSATURATED_FAT' => 'mono_fat',
            'POLY_UNSATURATED_FAT' => 'poly_fat',
        ];

        // مهم: preload یک‌بار
        $this->preloadSelectedBasicFoods();

        foreach (($this->form['foods'] ?? []) as $i => $food) {
            $basicFoodId = $food['basic_food_id'] ?? null;
            $unit_type = $this->form['foods'][$i]['unit_type'] ?? null;
            $numberEachUnit = $this->form['foods'][$i]['unit'] ?? null;

            if (!$basicFoodId || !$unit_type || $numberEachUnit === null) {
                continue;
            }

            /** @var BasicFood|null $basicFood */
            $basicFood = $this->basicFoodCache[(int)$basicFoodId] ?? null;
            if (!$basicFood) {
                continue;
            }

            $quantityPerGram = 0.0;
            $quantityPerUnit = 0.0;

            // unit_type==3 => گرم مستقیم
            if ((int)$unit_type === 3) {
                $quantityPerGram = (float)$numberEachUnit;
                $quantityPerUnit = 1.0;
            } else {
                // پیدا کردن pivot quantity_per_unit بدون query
                $unitModel = $basicFood->units->firstWhere('id', (int)$unit_type);
                $quantityPerUnit = (float)optional($unitModel)->pivot?->quantity_per_unit;
                $quantityPerGram = $quantityPerUnit * (float)$numberEachUnit;
            }

            if ($quantityPerGram <= 0) {
                continue;
            }

            $this->computData['total_quantity_per_gram'] = round(
                (float)($this->computData['total_quantity_per_gram'] ?? 0) + $quantityPerGram,
                2
            );

            // محاسبه فکت‌ها
            $foodFact = (array)($basicFood->food_fact ?? []);

            foreach ($nutritionKeys as $foodFactKey => $computDataKey) {
                if (!isset($foodFact[$foodFactKey])) {
                    continue;
                }

                $quantityFoodFact = (float)$foodFact[$foodFactKey];
                if ($quantityFoodFact <= 0) {
                    continue;
                }

                // چون food_fact معمولاً per 100g هست:
                $valuePerUnit = ($quantityFoodFact / 100) * $quantityPerGram;

                $this->computData[$computDataKey] = round(
                    (float)($this->computData[$computDataKey] ?? 0) + $valuePerUnit,
                    2
                );
            }

            $this->computData['recipe_list'][] = '';
        }

        $this->form['weight'] = (int)($this->computData['total_quantity_per_gram'] ?? 0);
        $this->form['calorie'] = (int)($this->computData['min_calorie'] ?? 0);

        $this->computData['recipe'] = implode("<hr>", array_filter($this->computData['recipe_list'] ?? []));
    }

    // =========================
    // Events from JS
    // =========================
    #[On('addBasicFood')]
    public function addBasicFood($basicFoodId = 0, $foodNumber = 0)
    {
        $basicFoodId = (int)$basicFoodId;
        $foodNumber  = (int)$foodNumber;

        // اینجا فقط یک بار می‌خونیم تا units برای UI آماده بشه
        $baseFood = BasicFood::query()
            ->select(['id'])
            ->with([
                'units' => function ($q) {
                    $q->select(['food_units.id', 'food_units.name'])->withPivot(['quantity_per_unit', 'max_allowed', 'is_primary']);
                }
            ])
            ->find($basicFoodId);

        if (!$baseFood) {
            return;
        }

        $this->form['foods'][$foodNumber]['basic_food_id'] = $basicFoodId;
        $this->form['foods'][$foodNumber]['unit'] = 0;
        $this->form['foods'][$foodNumber]['max'] = '';
        $this->form['foods'][$foodNumber]['units'] = $baseFood->units;
        $this->form['foods'][$foodNumber]['unit_selected'] = $baseFood->units->firstWhere('pivot.is_primary', true)?->id
            ?? $baseFood->units->first()?->id
            ?? null;

        $this->dispatch('updateUi');
    }

    #[On('addUnitType')]
    public function addUnitType($unitTypeId = 0, $unitNumber = 0)
    {
        $unitTypeId = (int)$unitTypeId;
        $unitNumber = (int)$unitNumber;

        $this->form['foods'][$unitNumber]['unit'] = null;
        $this->form['foods'][$unitNumber]['unit_type'] = $unitTypeId;

        // جلوگیری از query تکراری: از fetchData استفاده کن
        $unit = collect($this->fetchData['fooduits'] ?? [])->firstWhere('id', $unitTypeId)
            ?: FoodUnit::query()->select(['id', 'name'])->find($unitTypeId);

        $this->form['foods'][$unitNumber]['unit_name'] = $unit?->name;
        $this->form['foods'][$unitNumber]['unit_id'] = $unit?->id;

        $this->dispatch('updateUi');
    }

    public function updated($name, $value): void
    {
        // فقط وقتی unit/unit_type/max تغییر کرد محاسبه کن
        if (preg_match('/\b(unit|max|unit_type)\b/', (string)$name)) {
            $this->calculateData();
        }

        $this->dispatch('updateUi');
    }

    // =========================
    // Fill form (edit)
    // =========================
    public function fillTheform()
    {
        $this->isEdited = true;

        $this->form['category'] = $this->recipe->category_id;
        $this->form['name'] = $this->recipe->name;
        $this->form['time'] = $this->recipe->time;
        $this->form['description'] = $this->recipe->description;
        $this->form['serving'] = $this->recipe->serving;
        $this->form['weight'] = $this->recipe->weight;
        $this->form['calorie'] = $this->recipe->calorie;
        $this->form['difficulty'] = (int)$this->recipe->difficulty->value;
        $this->form['suggested'] = $this->recipe->suggested;
        $this->form['priority'] = $this->recipe->priority;
        $this->form['active'] = $this->recipe->active;
        $this->form['mainFood'] = $this->recipe->food_id;

        $instructions = $this->recipe->instructions ?? [];

        if (isset($this->recipe->detail[Recipe::DETAIL_IMAGE])) {
            $this->uploadedPhotoUrl = Storage::disk('public')->url($this->recipe->detail[Recipe::DETAIL_IMAGE]);
            $this->uploadedFileName = basename($this->recipe->detail[Recipe::DETAIL_IMAGE]);
            $this->uploadedFileType = getFileIconClass(pathinfo($this->recipe->detail[Recipe::DETAIL_IMAGE], PATHINFO_EXTENSION));
        } else {
            $this->uploadedPhotoUrl = null;
        }

        $this->form['instructionsCounter'] = max(1, count($instructions));
        foreach ($instructions as $key => $instruction) {
            $this->form['instroduction'][$key] = $instruction['text'] ?? '';
        }

        $foodUnit = $this->recipe->units->first();
        if ($foodUnit) {
            $this->form['recipe']['unit'] = $foodUnit->food_unit_id;
            $this->form['recipe']['gram'] = $foodUnit->quantity_per_unit;
        }

        $basicFoods = $this->recipe->basicFoods;

        foreach ($basicFoods as $key => $basicFood) {
            $unitId = (int)$basicFood->pivot->food_unit_id;

            $unit = collect($this->fetchData['fooduits'] ?? [])->firstWhere('id', $unitId)
                ?: FoodUnit::query()->select(['id', 'name'])->find($unitId);

            $this->form['foods'][$key]['basic_food_id'] = $basicFood->id;
            $this->form['foods'][$key]['unit_name'] = $unit?->name;
            $this->form['foods'][$key]['unit_selected'] = $unitId;

            // units برای select (بدون query تکراری: یک eager-load)
            $units = $basicFood->relationLoaded('units')
                ? $basicFood->units
                : $basicFood->units()->select(['food_units.id', 'food_units.name'])->withPivot(['quantity_per_unit', 'is_primary'])->get();

            $this->form['foods'][$key]['units'] = $units;
            $this->form['foods'][$key]['unit_id'] = $unitId;
            $this->form['foods'][$key]['unit_type'] = $unitId;
            $this->form['foods'][$key]['unit'] = $basicFood->pivot->quantity_per_unit;
        }

        $this->form['countBasicFoods'] = max(1, count($basicFoods));

        $this->calculateData();
    }

    // =========================
    // Lifecycle
    // =========================
    public function mount()
    {
        $this->warmFetchData();

        $recipe = request()->route('recipe');
        if ($recipe instanceof Recipe) {
            $this->recipe = $recipe->load(['units', 'basicFoods.units']);
            $this->fillTheform();
        }
    }

    public function render()
    {
        return view('diet::livewire.admin.recipe.recipe-update-or-create');
    }
}
