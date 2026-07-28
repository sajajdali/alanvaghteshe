<?php

    namespace Modules\Api\app\Resources\Api\Consumption;

    use Illuminate\Http\Resources\Json\JsonResource;
    use Modules\Diet\Entities\FoodUnit;
    use Modules\Diet\Entities\Meal;

    class BasicFoodResource extends JsonResource
    {
        /**
         * Transform the resource into an array.
         */
        public function toArray($request): array
        {
            $user = auth()->user();
            $userBmi = app('dietService')->userBmi($user , true);
            $userMeals = app('dietService')->getUserMeals($user);
            $userCalorieNeed = $this->calculateCaloriePercentage($this->food_fact['CALORIE'] ?? 1, $userBmi->calorie);
            $unitsCollection = $this->units;

            if (!$unitsCollection->contains('id', 3)) {
                $unitsCollection = $unitsCollection->push(FoodUnit::find(3));
            }
            return [
                'name' => $this->name,
                'units' =>  BasicFoodUnitsResource::collection($unitsCollection),
                'food_fact' => $this->changeFoodFact(),
                'calorie_percentage' => $userCalorieNeed,
//                'meals_old' => MealResource::collection(Meal::orderBy('priority')->get()),
                'meals' => $userMeals
//                'foodFact' => $this
            ];
        }


        private function calculateCaloriePercentage($foodCalories, $userCalorieNeed) : int {
            if ($userCalorieNeed == 0) {
                return 0; // جلوگیری از تقسیم بر صفر
            }

            // محاسبه درصد کالری و محدود کردن به حداکثر 100%
            $percentage = ($foodCalories / $userCalorieNeed) * 100;

            return round(min(round($percentage, 2), 100)); // حداکثر مقدار 100%
        }

        private function changeFoodFact()
        {
            // Check if the foodFact attribute exists
            if (isset($this->food_fact)) {
                return [
                    'calorie' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['CALORIE'], 'کالری'),
                        [
                            'name' => 'کالری',
                            'percent' => (string) $this->clcUserCalorie(),
                        ]
                    ),
                    'protein' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['PROTEIN'], 'گرم'),
                        [
                            'name' => 'پروتئین',
                            'percent' => $this->handlePercent($this->food_fact['PROTEIN'], 1),
                        ]
                    ),
                    'fat' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['FAT'], 'گرم'),
                        [
                            'name' => 'چربی',
                            'percent' => $this->handlePercent($this->food_fact['FAT'], 1),
                        ]
                    ),
                    'carbohydrate' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['CARBOHYDRATE'], 'گرم'),
                        [
                            'name' => 'کربوهیدرات',
                            'percent' => $this->handlePercent($this->food_fact['CARBOHYDRATE'], 1),
                        ]
                    ),
                    'sugar' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['SUGAR'], 'گرم'),
                        [
                            'name' => 'شکر',
                            'percent' => $this->handlePercent($this->food_fact['SUGAR'], 1),
                        ]
                    ),
                    'fiber' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['FIBER'], 'گرم'),
                        [
                            'name' => 'فیبر',
                            'percent' => $this->handlePercent($this->food_fact['FIBER'], 1),
                        ]
                    ),
                    'sodium' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['SODIUM'], 'میلی گرم'),
                        [
                            'name' => 'سدیم',
                            'percent' => $this->handlePercent($this->food_fact['SODIUM']),
                        ]
                    ),
                    'potassium' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['POTASSIUM'], 'میلی گرم'),
                        [
                            'name' => 'پتاسیم',
                            'percent' => $this->handlePercent($this->food_fact['POTASSIUM']),
                        ]
                    ),
                    'calcium' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['CALCIUM'], 'میلی گرم'),
                        [
                            'name' => 'کلسیم',
                            'percent' => $this->handlePercent($this->food_fact['CALCIUM']),
                        ]
                    ),
                    'magnesium' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['MAGNESIUM'], 'میلی گرم'),
                        [
                            'name' => 'منیزیم',
                            'percent' => $this->handlePercent($this->food_fact['MAGNESIUM']),
                        ]
                    ),
                    'iron' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['IRON'], 'میلی گرم'),
                        [
                            'name' => 'آهن',
                            'percent' => $this->handlePercent($this->food_fact['IRON']),
                        ]
                    ),
                    'cholesterol' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['CHOLESTEROL'], 'میلی گرم'),
                        [
                            'name' => 'کلسترول',
                            'percent' => $this->handlePercent($this->food_fact['CHOLESTEROL'], 1),
                        ]
                    ),
                    'phosphor' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['PHOSPHOR'], 'میلی گرم'),
                        [
                            'name' => 'فسفر',
                            'percent' => $this->handlePercent($this->food_fact['PHOSPHOR']),
                        ]
                    ),
                    'saturatedFat' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['SATURATED_FAT'], 'گرم'),
                        [
                            'name' => 'چربی اشباع',
                            'percent' => $this->handlePercent($this->food_fact['SATURATED_FAT']),
                        ]
                    ),
                    'polyunsaturatedFat' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['POLY_UNSATURATED_FAT'], 'گرم'),
                        [
                            'name' => 'اسید چرب غیر اشباع',
                            'percent' => $this->handlePercent($this->food_fact['POLY_UNSATURATED_FAT'], 1),
                        ]
                    ),
                    'transFat' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['TRANS_FAT'], 'گرم'),
                        [
                            'name' => 'اسید چرب ترانس',
                            'percent' => $this->handlePercent($this->food_fact['TRANS_FAT'], 1),
                        ]
                    ),
                    'monounsaturatedFat' => array_merge(
                        $this->formatAmountWithUnit($this->food_fact['MONOUNSATURATED_FAT'], 'گرم'),
                        [
                            'name' => 'اسید چرب تک غیر اشباع',
                            'percent' => $this->handlePercent($this->food_fact['MONOUNSATURATED_FAT'], 1),
                        ]
                    ),
                ];
            }

            return null; // or return an empty array if that's more appropriate
        }

        /**
         * Helper function to format amount/unit with custom logic.
         *
         * @param mixed $value
         * @param string $unit
         * @return array
         */
        private function formatAmountWithUnit($value, $unit = 'گرم'): array
        {
            if (in_array($value, ['-1', null, '', 'Null', 'null'], true)) {
                return ['amount' => '-', 'unit' => ''];
            }
            return ['amount' => (string) $value, 'unit' => $unit];
        }

        private function clcUserCalorie()
        {
            $foodCalorie = $this->food_fact['CALORIE'] ;
            $baseCalorie = app('dietService')->computeCalorie(auth()->user())->base_calorie;

            if ($baseCalorie == 0) {
                return null;
            }

            // Calculate the percentage
            return round(($foodCalorie / $baseCalorie) * 100 );
        }

        private function handlePercent($food , $divisibleAmount = 1000): string
        {
            if ($food == '-1' || $food == '' || $food == null || $food == 'Null' || $food == 'null'){
                return "0";
            }
            if ($divisibleAmount == 1){
                return $food;
            }

            $food = (float) ($food);
            return (string) ($food / $divisibleAmount);
        }
    }
