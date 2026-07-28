<?php

namespace Modules\Diet\Livewire\Admin\Food;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Core\Entities\Disease;
use Modules\Diet\Entities\BasicFood;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Entities\Food;
use Modules\Diet\Entities\Meal;
use Modules\Diet\Enum\ConditionApplyItemEnum;
use Modules\Diet\Enum\ConditionKeyEnum;
use Modules\Diet\Enum\FoodTypeEnum;
use Modules\Diet\Livewire\Admin\Traits\ConditionTrait;
use Illuminate\Support\Str;
class FoodCreateOrUpdate extends Component
{
    use AuthorizesRequests, ConditionTrait;

    // fetch data
    public ?Food $food = null;
    private const CACHE_TAG = 'diet_food_page';

    public array $fetchData = [
        'max' => 20,
        'parent_id' => null
    ];


    public array $form = [
        'meals' => [],
        'basic_foods' => [],
        'conditions' => [],
        'countBasicFoods' => 1,
        'BasicFoodUnits' => null,
        'foods' => [],
        'interval_step' => null,
    ];

    public $computData = [
        'carb' => 0,
        'protein' => 0,
        'fat' => 0,
        'fiber' => 0,
        'recipe' => null,
        'min_calorie' => 0,
        'max_calorie' => null,
    ];
    public $isEdited = false;


    protected $rules = [
        'form.name' => 'required|string',
        'form.meals' => 'required|array',
        //        'form.main' => 'required',
    ];

    protected $messages = [
        'form.name.required' => 'نام غذا را وارد کنید',
        'form.main.required' => 'یک وعده را به عنوان وعده اصلی باید انتخاب کنید',
        'form.meals.required' => 'وعده هایی که این غذا میتواند در آن تجویز شود انتخاب نشده',
    ];

    public function clearFoodCaches()
    {
        Cache::tags(self::CACHE_TAG)->flush();

        session()->flash('success', 'کش‌های این صفحه پاک شد و داده‌ها بروزرسانی شدند');

        // 🔄 رفرش کامل صفحه
        $this->dispatch('reload-page');
    }
    public function mount(Food $food)
    {
        $ttl = now()->addWeek();

        $this->fetchData['mealList'] = Cache::tags(self::CACHE_TAG)->remember(
            'meals_all',
            $ttl,
            fn () => Meal::all()
        );

        $this->fetchData['disease'] = Cache::tags(self::CACHE_TAG)->remember(
            'disease_active',
            $ttl,
            fn () => Disease::active()->get()
        );

        $this->fetchData['conditions'] = Cache::tags(self::CACHE_TAG)->remember(
            'food_conditions',
            $ttl,
            fn () => Condition::whereJsonContains('apply_to', ConditionApplyItemEnum::FOOD->value)->get()
        );

        $this->fetchData['BasicFoods'] = Cache::tags(self::CACHE_TAG)->remember(
            'basic_foods_with_units',
            $ttl,
            fn () => BasicFood::active()->whereHas('units')->get()
        );
        // auto checked fields
        foreach ($this->fetchData['conditions'] as $value) {

            // remove all disease in list checked
            if ($value->key == ConditionKeyEnum::DISEASE) {
                continue;
            }
            // remove all disease in list checked

            foreach ($value->options['checked'] as $k => $check) {
                $this->form['conditions'][$value->id][$value->options['items']['keys'][$k]] = $value->options['checked'][$k];
            }
        }

        // auto checked fields

        $food = request()->route('food');
        if ($food instanceof Food) {
            //            $this->authorize('update', $food);
            $this->isEdited = true;
            $this->food = $food;
            $this->form['name'] = $this->food->name ?? '';
            $this->form['calories'] = $this->food->calories;
            $this->form['carb'] = $this->food->carb;
            $this->form['fat'] = $this->food->fat;
            $this->form['protein'] = $this->food->protein;
            $this->form['fiber'] = $this->food->fiber;
            $this->form['recipe'] = $this->food->recipe;
            $this->form['interval_step'] = $this->food->detail['interval_step'] ?? null;
            $this->fetchData['parent_id'] = $this->food->parent_id;

            foreach ($this->food->conditions as $condition) {
                $this->form['conditions'][$condition->id] = $this->convertToAssociativeArray(json_decode($condition->pivot->options));
            }

            $this->form['meals'] = $this->food->meals()->pluck('id')->toArray() ?? [];

            // list basic foods
            $basicFoodList = $this->food->basicFoodPivot()->orderBy('basic_food_pivot.id')->get();
            if (count($basicFoodList)) {
                $this->form['countBasicFoods'] = count($basicFoodList);
                $i = 0;
                foreach ($basicFoodList as $basicFood) {
                    $this->form['foods'][$i]['row_key'] = 'db_' . ($basicFood->pivot->id ?? Str::uuid());
                    $this->form['foods'][$i]['basic_food_id'] = $basicFood->id;
                    $this->form['foods'][$i]['unit_name'] = $basicFood->units()->where('is_primary', '1')->first()?->name ?? $basicFood->units()->first()->name;
                    $this->form['foods'][$i]['unit'] = $basicFood->pivot->quantity;
                    $this->form['foods'][$i]['max'] = $basicFood->pivot->maximum;
                    $this->form['foods'][$i]['extend'] = $basicFood->pivot->extend == 1;
                    $i++;
                }
                $this->calculateData();
            }
            // list basic foods
        }
        if (empty($this->form['foods'])) {
            $this->form['foods'][] = ['row_key' => (string) Str::uuid()];
            $this->form['countBasicFoods'] = 1;
        }
        $this->dispatch('updateUi');
    }


    #[Computed]
    public function calculateData(): void
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
        ];

        $foods = $this->form['foods'] ?? [];
        if (empty($foods)) return;

        $ids = collect($foods)->pluck('basic_food_id')->filter()->unique()->values();

        // 1 query (+ 1 query for pivot relation) instead of N queries
        $basicFoods = BasicFood::query()
            ->whereIn('id', $ids)
            ->with(['units' => fn($q) => $q->orderByDesc('basic_food_units.is_primary')])
            ->get()
            ->keyBy('id');

        $smaller = $this->getSmallestBasicFoodId(); // فقط یکبار

        foreach ($foods as $foodRow) {
            if (empty($foodRow['basic_food_id'])) {
                continue;
            }
            $basicFood = $basicFoods->get($foodRow['basic_food_id']);
            if (!$basicFood) continue;

            $foodFact = $basicFood->food_fact ?? [];

            // بدون update در DB: فقط با پیش‌فرض -1 محاسبه
            $cal = (float)($foodFact['CALORIE'] ?? -1);
            $pro = (float)($foodFact['PROTEIN'] ?? -1);
            $car = (float)($foodFact['CARBOHYDRATE'] ?? -1);
            $fib = (float)($foodFact['FIBER'] ?? -1);
            $fat = (float)($foodFact['FAT'] ?? -1);

            $unitModel = $basicFood->units->first();
            if (!$unitModel) continue;

            $quantityPerUnit = (float)($unitModel->pivot->quantity_per_unit ?? 0);
            $unit = (float)($foodRow['unit'] ?? 1);

            $caloriePerUnit = (($quantityPerUnit * $unit * $cal) / 100);
            $proteinPerUnit = (($quantityPerUnit * $unit * $pro) / 100);
            $carbPerUnit    = (($quantityPerUnit * $unit * $car) / 100);
            $fiberPerUnit   = (($quantityPerUnit * $unit * $fib) / 100);
            $fatPerUnit     = (($quantityPerUnit * $unit * $fat) / 100);

            $maxAmount = $foodRow['max'] ?? null;

            if ($maxAmount === null && $smaller !== false && $smaller['smallestBasicFoodId'] == $basicFood->id) {
                $this->fetchData['minSelected'] = $basicFood->name;
                $maxAmount = $this->fetchData['max']; // همون رفتار قبلی
            }

            $this->computData['min_calorie'] = round($this->computData['min_calorie'] + $caloriePerUnit, 2);
            $this->computData['carb']        = round($this->computData['carb'] + $carbPerUnit, 2);
            $this->computData['protein']     = round($this->computData['protein'] + $proteinPerUnit, 2);
            $this->computData['fiber']       = round($this->computData['fiber'] + $fiberPerUnit, 2);
            $this->computData['fat']         = round($this->computData['fat'] + $fatPerUnit, 2);

            if ($maxAmount) {
                $maxPerThisUnit = max(1, (int)floor($maxAmount / max($unit, 0.0001)));
                if ($this->computData['maximum_amount_of_food'] === null || $maxPerThisUnit < $this->computData['maximum_amount_of_food']) {
                    $this->computData['maximum_amount_of_food'] = $maxPerThisUnit;
                }
            }
        }
    }
    // Confirming the maximum value for an item whose value is lower than the others
    public function getSmallestBasicFoodId(): false|array
    {
        $foods = $this->form['foods'] ?? null;
        if (!$foods) return false;

        $hasAnyMax = false;
        $smallestUnit = null;
        $smallestBasicFoodId = null;

        foreach ($foods as $item) {
            if (isset($item['max']) && $item['max'] !== '' && $item['max'] !== null) {
                $hasAnyMax = true;
            }

            $u = (float)($item['unit'] ?? 0);
            if ($smallestUnit === null || $u < $smallestUnit) {
                $smallestUnit = $u;
                $smallestBasicFoodId = $item['basic_food_id'] ?? null;
            }
        }

        // اگر حداقل یک max توسط کاربر تعیین شده، دیگه حالت سیستمی نداریم
        if ($hasAnyMax) return false;

        return [
            'smallestUnit' => $smallestUnit,
            'smallestBasicFoodId' => $smallestBasicFoodId,
        ];
    }
    public function addBasicFoodItem(): void
    {
        $this->form['foods'][] = ['row_key' => (string) Str::uuid()];
        $this->form['countBasicFoods'] = count($this->form['foods']);
        $this->dispatch('updateUi');
    }

    public function removeBasicFoodItem($rowKey): void
    {
        // اگر فقط یک ردیف وجود دارد → ریستش کن، حذف نکن
        if (count($this->form['foods'] ?? []) === 1) {

            $this->form['foods'] = [[
                'row_key' => (string) \Illuminate\Support\Str::uuid(),
            ]];

            $this->form['countBasicFoods'] = 1;

            $this->calculateData();
            $this->dispatch('updateUi');
            return;
        }

        $index = collect($this->form['foods'])
            ->search(fn ($row) => ($row['row_key'] ?? null) === $rowKey);

        if ($index === false) return;

        if (
            isset($this->form['main']) &&
            ($this->form['main'] ?? null) ==
            ($this->form['foods'][$index]['basic_food_id'] ?? null)
        ) {
            unset($this->form['main']);
        }

        unset($this->form['foods'][$index]);
        $this->form['foods'] = array_values($this->form['foods']);
        $this->form['countBasicFoods'] = count($this->form['foods']);

        $this->calculateData();
        $this->dispatch('updateUi');
    }

    #[On('addBasicFood')]
    public function addBasicFood($basicFoodId = 0, $rowKey = null)
    {

        $index = collect($this->form['foods'] ?? [])
            ->search(fn ($row) => ($row['row_key'] ?? null) === $rowKey);

        if ($index === false) return;

        $baseFood = BasicFood::find($basicFoodId);

        $this->form['foods'][$index]['unit_name'] = $baseFood->units()->where('is_primary', '1')->first()?->name ?? $baseFood->units()->first()->name;
        $this->form['foods'][$index]['basic_food_id'] = $basicFoodId;
        $this->form['foods'][$index]['unit'] = 1;
        $this->form['foods'][$index]['max'] = '';

        $this->calculateData();
        $this->dispatch('updateUi');
    }

    public function updated($name, $value): void
    {
        if (str_starts_with($name, 'form.foods.') && (str_ends_with($name, '.unit') || str_ends_with($name, '.max'))) {
            $this->calculateData();
            return; // دیگه updateUi نزن
        }
    }

    public function updateOrCreate()
    {
        $this->validate();

        $modelCreateOrUpdate = $this->prepareModelData($this->form['name'], $this->computData);
        if ($this->isEdited) {
            $this->food->update($modelCreateOrUpdate);
            $message = 'وعده با موفقیت ویرایش شد';
        } else {

            $this->food = Food::create($modelCreateOrUpdate);
            $message = 'وعده با موفقیت اضافه شد';
        }
        $this->syncConditions($this->food, $this->form['conditions']);
        $this->syncBasicFoods($this->food, $this->form['foods']);
        $this->syncMeals($this->food, $this->form['meals']);

        // Creating a foods list if it is not the parent
        if ($this->food->parent_id == null){
            $this->createSampleDiets();
        }

//        return redirect()->route('admin.food.index')->with('success', $message);
        if (!isset($this->fetchData['parent_id']) && isset($this->food->parent_id)){
            return redirect()->route('admin.food.index' , ['parent_id' => $this->food->parent_id])->with('success', $message);

        }
        return redirect()->route('admin.food.index')->with('success', $message);
    }

    private function prepareModelData($name, $computData)
    {
        return [
            'name' => $name,
            'carb' => $computData['carb'] ?? 0,
            'max_carb' => isset($computData['carb']) ? $this->calculateMaxAmount($computData['carb']) : 0,
            'protein' => $computData['protein'] ?? 0,
            'max_protein' => isset($computData['protein']) ? $this->calculateMaxAmount($computData['protein']) : 0,
            'calories' => $computData['min_calorie'] ?? 0,
            'max_calories' => isset($computData['min_calorie']) ? $this->calculateMaxAmount($computData['min_calorie']) : 0,
            'fat' => $computData['fat'] ?? 0,
            'max_fat' => isset($computData['fat']) ? $this->calculateMaxAmount($computData['fat']) : 0,
            'fiber' => $computData['fiber'] ?? 0,
            'max_fiber' => isset($computData['fiber']) ? $this->calculateMaxAmount($computData['fiber']) : 0,
            'recipe' => $computData['recipe'] ?? '',
            'max_per_unit' => $computData['maximum_amount_of_food'],
            'type' => FoodTypeEnum::COMBINED->value,
            'detail' => [
                'interval_step' => $this->form['interval_step']
            ],
        ];
    }

    private function calculateMaxAmount($foodFact)
    {
        return $foodFact * $this->computData['maximum_amount_of_food'];
    }

    private function syncConditions($food, $conditions)
    {
        foreach ($conditions as $keyCondition => $valueCondition) {
            $filteredArray = array_filter($valueCondition, function ($value) {
                return $value === true;
            });
            $food->conditions()->detach($keyCondition);
            $food->conditions()->attach($keyCondition, ['options' => json_encode(array_keys($filteredArray), JSON_UNESCAPED_UNICODE)]);
        }
    }

    private function syncBasicFoods($food, $basicFoodList)
    {
        $food->basicFoodPivot()->detach();
        if (isset($basicFoodList)) {
            foreach ($basicFoodList as $basicFood) {
                $max = isset($basicFood['max']) && $basicFood['max'] !== '' ? $basicFood['max'] : null;
                if ($this->getSmallestBasicFoodId() !== false && $this->getSmallestBasicFoodId()['smallestBasicFoodId'] == $basicFood['basic_food_id']) {
                    $max = $this->fetchData['max'];
                }
                $food->basicFoodPivot()->syncWithPivotValues([$basicFood['basic_food_id']], [
                    'quantity' => ($basicFood['unit'] == "" || $basicFood['unit'] == null) ? 1 : $basicFood['unit'],
                    'maximum' => $max,
                    'extend' => isset($basicFood['extend']) && $basicFood['extend'] ? 1 : 0,
                ], false);
            }
        }
    }

    private function syncMeals($food, $meals)
    {
        $food->meals()->sync($meals);
    }

    public function saveFoodVariants(array $combinations, $parentId): void
    {
        $maxCalorie = 0;
        foreach ($combinations as $key => $combination) {
            // Because the first data is stored in the database
            if ($key == 0) {
//                continue;
            }
            $this->form['foods'] = $combination;
            $this->calculateData();
            $modelCreateOrUpdate = $this->prepareModelData($this->form['name'], $this->computData);
            $modelCreateOrUpdate['parent_id'] = $parentId;
            $this->food = Food::create($modelCreateOrUpdate);
            $this->syncConditions($this->food, $this->form['conditions']);
            $this->syncBasicFoods($this->food, $this->form['foods']);
            $this->syncMeals($this->food, $this->form['meals']);

            //check max calorie
            if ($this->food->calories > $maxCalorie){
                $maxCalorie = $this->food->calories;
            }
        }

        //update main food max calorie
        if ($this->food->parent != null) {
           $parentFood = $this->food->parent;
           if ($parentFood->max_calories < $maxCalorie) {
               $parentFood->max_calories = round($maxCalorie);
               $parentFood->save();
           }
        }

    }

    public function createSampleDiets()
    {
        $interval_step = isset($this->form['interval_step']) && $this->form['interval_step'] !== "" ? $this->form['interval_step'] : null;
//        $allSampleDiets = app('dietService')->generateFoodVariantsList($this->food , $interval_step);
//        $allSampleDiets = app('dietService')->generateFoodCombinations($this->food , $interval_step);
        $this->food->children()->delete();
        $allSampleDiets = $this->generateCombinationsWithStepLimit($this->food , $interval_step);
        $this->saveFoodVariants($allSampleDiets, $this->food->id);

    }

    private function generateCombinationsWithStepLimit(Food $food, $intervalStep)
    {
        $allSampleDiets = app('dietService')->generateFoodCombinations($food, $intervalStep);

        if (count($allSampleDiets) > 1000) {
            if ($intervalStep === null) {
                $intervalStep = 5;
            } elseif ($intervalStep > 1) {
                $intervalStep--;
            } else {
                return $allSampleDiets;
            }
            return $this->generateCombinationsWithStepLimit($food, $intervalStep);
        }
        $this->saveFinalIntervalStep($food, $intervalStep);
        return $allSampleDiets;
    }
    private function saveFinalIntervalStep(Food $food, $intervalStep)
    {
        $detail = $food->detail;
        $detail['interval_step'] = $intervalStep;

        $this->food->update([
            'detail' => $detail,
        ]);

    }

    private function computeSampleData($listBody)
    {

        return [
            'carb' => $this->sumNutrient($listBody, 'carb'),
            'protein' => $this->sumNutrient($listBody, 'protein'),
            'min_calorie' => $this->sumNutrient($listBody, 'calorie'),
            'fat' => $this->sumNutrient($listBody, 'fat'),
            'fiber' => $this->sumNutrient($listBody, 'fiber'),
            'recipe' => $this->computData['recipe'] ?? '',
            'maximum_amount_of_food' => $this->computData['maximum_amount_of_food'],
        ];
    }

    private function sumNutrient(array $foods, string $nutrient): float
    {
        $total = 0.0;

        foreach ($foods as $food) {
            if (isset($food['calculateFact'][$nutrient])) {
                $total += $food['calculateFact'][$nutrient];
            }
        }

        return $total;
    }

    private function syncBasicFoodPivot($food, $body)
    {
        $max = isset($body['max']) && $body['max'] !== '' ? $body['max'] : null;
        if ($this->getSmallestBasicFoodId() !== false && $this->getSmallestBasicFoodId()['smallestBasicFoodId'] == $body['food_id']) {
            $max = $this->fetchData['max'];
        }
        $food->basicFoodPivot()->syncWithPivotValues([$body['food_id']], [
            'quantity' => ($body['amount'] == "" || $body['amount'] == null) ? 1 : $body['amount'],
            'maximum' => $max,
            'extend' => 0,
        ], false);
    }

    public function render()
    {
        $title = (!is_null($this->food) && $this->food->exists) ? 'ویرایش غذا' : 'ایجاد غذای جدید';

        return view('diet::livewire.admin.food.food-create-or-update')->title($title);
    }
}
