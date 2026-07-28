<?php

namespace Modules\Diet\Livewire\Admin\Plan;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Core\Entities\Disease;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\Meal;
use Modules\Diet\Enum\ConditionApplyItemEnum;
use Modules\Diet\Enum\ConditionKeyEnum;
use Modules\Diet\Enum\FoodTypeEnum;
use Modules\Diet\Livewire\Admin\Traits\ConditionTrait;
use Str;

class DietPlanCreateOrUpdate extends Component
{
    use AuthorizesRequests , ConditionTrait;

    public ?DietPlan $dietPlan = null;


    public array $form =  [
        'status' => false,
        'general_pattern' => false,
        'food_type' => FoodTypeEnum::SIMPLE->value,
        'special_meal' => [] ,
        'detail' => [
            'application' => [
                'status' => false
            ],
            'fasting' =>[
                'status' => false
            ]
        ]
    ];

    public array $fetchData = [];

    public $isEdited = false;

    protected $rules = [
        'form.name' => 'required|string',
        'form.dayCount' => 'required_if:general_pattern,false|numeric|not_in:0',
        'form.status_meal' => 'required',
        'form.reducedCalories' => 'nullable|numeric',
    ];

    protected $messages = [
        'form.name.required' => 'نام وعده را وارد کنید',
        'form.reducedCalories.required' => 'میزان کالری باید به عدد باشد',
        'form.dayCount.required' => 'وارد کردن تعداد روز اجباری است',
        'form.status_meal.required' => 'حداقل باید یک وعده را انتخاب کنید',
        'form.dayCount.numeric' => 'تعداد روز باید از نوع عدد باشد',
        'form.dayCount.not_in' => 'تعداد روز نمیتواند صفر باشد',
        'form.fiber.*.numeric' => 'مقدار حتما باید به صورت عددی وارد شود',
        'form.calorie.*.max' => 'درصد وارد شده کالری اشتباه است',
        'form.calorie.*.min' => 'درصد وارد شده کالری اشتباه است',
        'form.calorie.*.numeric' => 'درصد وارد شده کالری اشتباه است',
        'form.calorie.*.required' => 'وارد کردن کالری اجباری است',
    ];

    public function mount()
    {
        $this->fetchData['disease'] = Disease::active()->get();
        $this->fetchData['conditions'] = Condition::whereJsonContains('apply_to', ConditionApplyItemEnum::FOOD->value)->get();
        $this->form['food_type'] = FoodTypeEnum::COMBINED->value;
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
        $dietPlan = request()->route('diet_plan');
        if ($dietPlan instanceof DietPlan) {
            $this->authorize('update', $dietPlan);
            $this->isEdited = true;
            $this->dietPlan = $dietPlan;
            $this->form['name'] = $dietPlan->name ?? '';

            $this->form['reducedCalories'] = $dietPlan->reduced_calories ?? '';
            $this->form['status'] = $dietPlan->status ?? true;
            $this->form['general_pattern'] = $dietPlan->general_pattern ?? false;
            $this->form['dayCount'] = $dietPlan->day_count ?? '';
            $this->form['food_type'] = $dietPlan->food_type->value ;
            $this->form['detail'] = $dietPlan->detail ?? [];
            foreach ($this->dietPlan->meals as $meal) {
                $this->form['status_meal'][$meal->id] = true;
                $this->form['fiber'][$meal->id] = $meal->pivot->fiber;
                $this->form['protein'][$meal->id] = $meal->pivot->protein;
                $this->form['fat'][$meal->id] = $meal->pivot->fat;
                $this->form['calorie'][$meal->id] = $meal->pivot->calorie_percent;
                $this->form['carb'][$meal->id] = $meal->pivot->carb;
            }

            // load condition
            foreach ($this->dietPlan->conditions as $condition) {
                $this->form['conditions'][$condition->id] = $this->convertToAssociativeArray(json_decode($condition->pivot->options));
            }
            // load condition

            // check special foods
            if (isset($dietPlan->detail[DietPlan::DETAIL_SPECIAL_BASIC_FOODS])){
                foreach ($dietPlan->detail[DietPlan::DETAIL_SPECIAL_BASIC_FOODS] as $meal => $basicFoods){
                    $this->fetchData['specialFoods'][$meal] = app('dietService')->getBasicFoodsSpecial($meal , $this->form['food_type']);
                    $this->form['special_meal'][$meal] = true;
                    foreach ($basicFoods as $basicFood){
                        $this->form['special_basic_food'][$meal][$basicFood] = true;
                    }
                }
            }
            // check special foods

            // check handwritten
            if (isset($dietPlan->detail[DietPlan::DETAIL_HANDWRITTEN])){
                foreach ($dietPlan->detail[DietPlan::DETAIL_HANDWRITTEN] as $meal => $handwritten){
                    $this->form['handwritten'][$meal]['status'] = true;
                    $this->form['handwritten'][$meal]['title'] = $handwritten['title'];
                    $this->form['handwritten'][$meal]['body'] = $handwritten['body'];
                }
            }
            // check handwritten
        }
        $this->fetchData['meals'] = Meal::orderBy('priority')->orderBy('id')->get();

    }

    public function updated($name, $value)
    {

        if (Str::startsWith($name, 'form.food_type')) {
            if(isset($this->form['special_basic_food'])){
                // Define a function to set all values to false
                $setFalse = function (&$item) {
                    $item = false;
                };
                array_walk_recursive($this->form['special_basic_food'], $setFalse);
            }
            if (isset($this->form['special_meal']) && count($this->form['special_meal'])){
                foreach ($this->form['special_meal'] as $mealKey => $status){
                    if ($this->form['special_meal'][$mealKey]){
                        $this->form['special_meal'][$mealKey] = false;
                    }
                }
            }

        }
        if (Str::startsWith($name, 'form.special_meal')) {
            $pattern = '/form\.special_meal\.(\d+)/';
            $matches = '';
            if (preg_match($pattern, $name, $matches)) {
                $mealId = $matches[1];
                $this->fetchData['specialFoods'][$mealId] = app('dietService')->getBasicFoodsSpecial($mealId , $this->form['food_type']);
            }
        }
    }

    #[On('status-meal')]
    public function statusMeal($mealIdSelected, $status = false): void
    {
        $this->form['status_meal'][$mealIdSelected] = $status;
        if ($status === false) {
            unset($this->form['calorie'][$mealIdSelected]);
        }
    }

    #[On('status-meal-special')]
    public function statusMealSpecial($mealIdSelected, $status = false): void
    {
        $this->form['status_meal_special'][$mealIdSelected] = $status;
        if ($status === false) {
            unset($this->form['calorie'][$mealIdSelected]);
        }
    }

    public function updateOrCreate()
    {

        $this->validate();



        // insert special basicFoods
        $specialBasicFoods = [];
        if (isset($this->form['special_basic_food'])){
            foreach ($this->form['special_basic_food'] as $meal => $basicFoods){
                if ($this->form['special_meal'][$meal] && $this->form['status_meal'][$meal]) {
                    foreach ($basicFoods as $basicFoodId => $status) {
                        if ($status) {
                            $specialBasicFoods[$meal][] = $basicFoodId;
                        }
                    }
                }
            }
        }
        // insert special basicFoods


        // handwritten
        $handWritten = [];
        if (isset($this->form['handwritten'])){
            foreach ($this->form['handwritten'] as $meal => $value){
                if ($this->form['handwritten'][$meal]['status'] && $this->form['handwritten'][$meal]['status'] === true) {
                    $handWritten[$meal]['title'] = $value['title'] ?? '';
                    $handWritten[$meal]['body'] = $value['body'] ?? '';
                }
            }
        }
        // handwritten



        $conditionBody = [
            'name' => $this->form['name'],
            'day_count' => $this->form['dayCount'] ?? 0,
            'food_type' => $this->form['food_type'],
            'status' => $this->form['status'],
            'general_pattern' => $this->form['general_pattern'],
            'reduced_calories' => $this->form['reducedCalories'] ?? 0,
        ];

        $conditionApplication = [];
        $conditionFasting = [];
        if (isset($this->form['detail']['fasting']['status'])) {
            $conditionFasting = [
                'status' => $this->form['detail']['fasting']['status'],
                'start_at' => $this->form['detail']['fasting']['start_at'] ?? '8',
                'fasting_hours' => $this->form['detail']['fasting']['fasting_hours'] ?? '16',
            ];
        }
        if (isset($this->form['detail']['application']['status'])) {
            $conditionApplication = [
                'status' => $this->form['detail']['application']['status'],
                'name' => $this->form['detail']['application']['name'] ?? '',
                'priority' => $this->form['detail']['application']['priority'] ?? '',
                'image' => $this->form['detail']['application']['image'] ?? '',
                'description' => $this->form['detail']['application']['description'] ?? '',
            ];
        }
        if ($this->isEdited) {
            $detail = $this->form['detail'];
            if ( !isset($this->form['detail']['application']['status'])){
                $conditionApplication = [];
            }

            if ( !isset($this->form['detail']['fasting']['status'])){
                $conditionFasting = [];
            }

            $detail[DietPlan::DETAIL_SPECIAL_BASIC_FOODS] = $specialBasicFoods;
            $detail[DietPlan::DETAIL_HANDWRITTEN] = $handWritten;
            $detail[DietPlan::DETAIL_APPLICATION] = $conditionApplication;
            $detail[DietPlan::DETAIL_FASTING] = $conditionFasting;
            $conditionBody['detail'] = $detail;
            $this->dietPlan->update($conditionBody);
            $message = 'پلن با موفقیت ویرایش شد';
        } else {
            $conditionBody['detail'][DietPlan::DETAIL_SPECIAL_BASIC_FOODS] = $specialBasicFoods;
            $conditionBody['detail'][DietPlan::DETAIL_HANDWRITTEN] = $handWritten;
            $conditionBody['detail'][DietPlan::DETAIL_APPLICATION] = $conditionApplication;
            $conditionBody['detail'][DietPlan::DETAIL_FASTING] = $conditionFasting;
            $this->dietPlan = DietPlan::create($conditionBody);
            $message = 'پلن با موفقیت اضافه شد';
        }



        foreach ($this->form['status_meal'] as $key => $value) {
            $this->dietPlan->meals()->detach($key, []);
            if ($this->form['status_meal'][$key]) {
                $this->dietPlan->meals()->attach($key, [
                    'fiber' => $this->form['food_type'] == FoodTypeEnum::SIMPLE->value  ? ( $this->form['fiber'][$key] ?? '0' ) : '0',
                    'carb' => $this->form['food_type'] == FoodTypeEnum::SIMPLE->value  ? ( $this->form['carb'][$key] ?? '0' ) : '0',
                    'protein' => $this->form['food_type'] == FoodTypeEnum::SIMPLE->value  ? ( $this->form['protein'][$key] ?? '0' ) : '0',
                    'fat' => $this->form['food_type'] == FoodTypeEnum::SIMPLE->value  ? ( $this->form['fat'][$key] ?? '0' ) : '0',
                    'calorie_percent' =>  $this->form['food_type'] == FoodTypeEnum::COMBINED->value  ? ( $this->form['calorie'][$key] ?? '0' ) : '0',
                ]);
            }
        }

        //conditions
        foreach ($this->form['conditions'] as $keyCondition => $valueCondition) {
            $filteredArray = array_filter($valueCondition, function ($value) {
                return $value === true;
            });
            $this->dietPlan->conditions()->detach($keyCondition);
            $this->dietPlan->conditions()->attach($keyCondition, ['options' => json_encode(array_keys($filteredArray), JSON_UNESCAPED_UNICODE)]);
        }
        //conditions

        return redirect()->route('admin.diet_plan.index')->with('success', $message);
    }

    public function render()
    {
        $title = (! is_null($this->dietPlan) && $this->dietPlan->exists) ? 'ویرایش پلن' : 'ایجاد پلن جدید';

        return view('diet::livewire.admin.plan.diet-plan-create-or-update')->title($title);
    }
}
