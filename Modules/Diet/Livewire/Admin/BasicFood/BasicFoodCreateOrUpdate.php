<?php

namespace Modules\Diet\Livewire\Admin\BasicFood;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Modules\Core\Entities\Disease;
use Modules\Diet\Entities\BasicFood;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Entities\FoodCategory;
use Modules\Diet\Entities\FoodUnit;
use Modules\Diet\Enum\BasicFoodTypeEnum;
use Modules\Diet\Enum\ConditionApplyItemEnum;
use Modules\Diet\Enum\ConditionKeyEnum;
use Modules\Diet\Livewire\Admin\Traits\ConditionTrait;
use Str;

class BasicFoodCreateOrUpdate extends Component
{
    use AuthorizesRequests, ConditionTrait;

    public ?BasicFood $basicFood = null;
    public string $name;
    public  $prevItem;
    public  $nextItem;
    public $isEdited = false;
    public int $foodUnitCounter = 1;
    public int $foodCategoryCounter = 0;

    public array $fetchData = [];
    public array $form = [
        'typeFood' => false,
        'food_unit' => [],
        'quantity_per_unit' => [],
        'max_allowed' => [],
        'calories_per_gram' => 100,
        'foodUnits' => [],
        'food_categories' => []
    ];


    protected $messages = [
        'form.name.required' => 'نام غذا را وارد کنید',
    ];

    public function mount()
    {
        // fetch data
        $this->fetchData['foodUnits'] = FoodUnit::all();
        $this->fetchData['foodCategories'] = FoodCategory::all();
        $this->fetchData['disease'] = Disease::active()->get();
        // fetch data

        // conditions
        $this->fetchData['conditions'] = Condition::whereJsonContains('apply_to', ConditionApplyItemEnum::BASIC_FOOD->value)->get();
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
        // conditions


        $basicFood = request()->route('basic_food');
        if ($basicFood instanceof BasicFood) {
            $this->authorize('update', $basicFood);
            $this->isEdited = true;
            $this->basicFood = $basicFood;
            $this->form['name'] = $basicFood->name ?? '';
            $this->form['typeFood'] = $basicFood->type == BasicFoodTypeEnum::COMBINED ?? false;
            $this->form['recipe'] = $basicFood->recipe ?? '';
            $this->form['foodFact'] = $basicFood->food_fact ?? '';

            foreach ($this->basicFood->conditions as $condition) {
                $this->form['conditions'][$condition->id] = $this->convertToAssociativeArray(json_decode($condition->pivot->options));
            }

            // units
            $units = $this->basicFood->units;
            if ($units->count()) {
                foreach ($this->basicFood->units as $unit) {
                    if ($unit->pivot->is_primary == 1) {
                        $this->form['isPrimary'] = $this->foodUnitCounter;
                    }
                    $this->form['foodUnits'][$this->foodUnitCounter] = $this->fetchData['foodUnits'];
                    $this->form['quantity_per_unit'][$this->foodUnitCounter] = $unit->pivot->quantity_per_unit;
                    $this->form['max_allowed'][$this->foodUnitCounter] = $unit->pivot->max_allowed;
                    $this->form['food_unit'][$this->foodUnitCounter] = $unit->id;
                    $this->foodUnitCounter++;
                }
            } else {
                $this->form['foodUnits'][$this->foodUnitCounter] = $this->fetchData['foodUnits'];
            }

            // categories
            if ($this->basicFood->category()->count()) {
                $this->form['food_categories'] = $this->basicFood->category->pluck('id')->toArray();
                $this->foodCategoryCounter = $this->basicFood->category()->count() - 1;
            } else {
                $this->form['food_categories'][0] = '';
            }
        } else {
            $this->form['foodUnits'][$this->foodUnitCounter] = $this->fetchData['foodUnits'];
        }
    }

    public function removeFoodCategory($number)
    {
        --$this->foodCategoryCounter;
        unset($this->form['food_categories'][$number]);
        $this->form['food_categories'] = array_values($this->form['food_categories']);
    }
    public function addCategory()
    {
        ++$this->foodCategoryCounter;
        $this->form['food_categories'][$this->foodCategoryCounter] = '';
    }
    public function addFoodUnit()
    {
        ++$this->foodUnitCounter;
        $this->form['foodUnits'][$this->foodUnitCounter] = $this->fetchData['foodUnits'];
    }

    public function render()
    {
        if (! is_null($this->basicFood) && $this->basicFood->exists) {
            $this->prevItem = BasicFood::where('id', '<', $this->basicFood->id)->orderBy('id', 'desc')->first()?->id;
            $this->nextItem = BasicFood::where('id', '>', $this->basicFood->id)->orderBy('id', 'asc')->first()?->id;
        }
        $title = (! is_null($this->basicFood) && $this->basicFood->exists) ? 'ویرایش غذا' : 'ایجاد غذا جدید';

        return view('diet::livewire.admin.basic-food.basic-food-create-or-update')->title($title);
    }

    public function removeFoodUnit($number)
    {
        unset($this->form['foodUnits'][$number]);
        unset($this->form['quantity_per_unit'][$number]);
        unset($this->form['food_unit'][$number]);
        unset($this->form['max_allowed'][$number]);
    }

    public function updated($name, $value)
    {

        if (Str::startsWith($name, 'form.food_unit')) {
            if (isset($this->form['max_allowed.*'])) {
                $this->form['max_allowed.*'] = null;
            }
        }
    }

    public function rules()
    {
        return  [

            'form.name'             => 'required',
            'form.foodFact'         => 'required',
            'form.foodFact.CALORIE' => 'required',
            'form.foodFact.PROTEIN' => 'required',
            'form.foodFact.FAT'     => 'required',
            'form.foodFact.CARBOHYDRATE' => 'required',
            'form.foodFact.FIBER'   => 'required',
            'form.isPrimary'        => 'required',

        ];
    }
    public function updateOrCreate()
    {
        $this->validate();
        $foocFactsEnums = \Modules\Diet\Enum\FoodFactEnum::cases();
        foreach ($foocFactsEnums as $foodFactEnum) {
            if (! in_array($foodFactEnum->value, $this->form['foodFact'])) {
                $this->form['foodFact'][$foodFactEnum->value] = "-1";
            }
        }
        $condition = [
            'name' => $this->form['name'],
            'food_fact' => $this->form['foodFact'],
            'type' => $this->form['typeFood'] ? BasicFoodTypeEnum::COMBINED->value : BasicFoodTypeEnum::SIMPLE->value,
            'recipe' => $this->form['recipe'] ?? null,
        ];

        if ($this->isEdited) {
            $this->basicFood->update($condition);
            $message = 'غذای پایه با موفقیت ویرایش شد';
        } else {
            $this->basicFood = BasicFood::create($condition);
            $message = 'غذای پایه با موفقیت اضافه شد';
        }

        // food units

        $this->basicFood->units()->detach();
        $this->basicFood->category()->detach();
        if (count($this->form['food_categories'])) {
            $this->form['food_categories'] = array_values(array_filter($this->form['food_categories']));
            $this->basicFood->category()->sync($this->form['food_categories']);
        }
        if (count($this->form['food_unit'])) {
            foreach ($this->form['food_unit'] as $unitId => $unitValue) {
                if ($unitValue == "") {
                    continue;
                }
                $this->basicFood->units()->syncWithPivotValues([$unitValue], [
                    'quantity_per_unit' => $this->form['quantity_per_unit'][$unitId] ?? 0,
                    'max_allowed' => $this->form['max_allowed'][$unitId] ?? null,
                    'is_primary' => $unitId == $this->form['isPrimary'] ? 1 : 0,
                ], false);
            }
        }
        //conditions
        if (isset($this->form['conditions'])) {
            foreach ($this->form['conditions'] as $keyCondition => $valueCondition) {
                $filteredArray = array_filter($valueCondition, function ($value) {
                    return $value === true;
                });
                $this->basicFood->conditions()->detach($keyCondition);
                $this->basicFood->conditions()->attach($keyCondition, ['options' => json_encode(array_keys($filteredArray), JSON_UNESCAPED_UNICODE)]);
            }
        }
        //conditions


        return redirect()->route('admin.basic_food.index')->with('success', $message);
    }
}
