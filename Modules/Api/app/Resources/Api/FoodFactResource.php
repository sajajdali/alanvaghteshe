<?php

namespace Modules\Api\app\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class FoodFactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'calorie' => [
                'amount' => (string) $this->calorie,
                'unit' => 'کالری',
                'name' => 'کالری',
                'percent' => (string) $this->clcUserCalorie(),
            ],
            'protein' =>[
                'amount' => (string) $this->foodFact['proteinAmount'],
                'unit' => 'گرم',
                'name' => 'پروتئین',
                'percent' => $this->handlePercent($this->foodFact['proteinAmount'] , 1)
            ],
            'fat' => [
                'amount' => (string) $this->foodFact['fatAmount'],
                'unit' => 'گرم',
                'name' => 'چربی',
                'percent' => $this->handlePercent($this->foodFact['fatAmount'] , 1)
            ],
            'carbohydrate' => [
                'amount' => (string) $this->foodFact['carbohydrateAmount'],
                'unit' => 'گرم',
                'name' => 'کربوهیدرات',
                'percent' => $this->handlePercent($this->foodFact['carbohydrateAmount'] , 1)
            ],
            'sugar' => [
                'amount' => (string) $this->foodFact['sugarAmount'],
                'unit' => 'گرم',
                'name' => 'شکر',
                'percent' => $this->handlePercent($this->foodFact['sugarAmount'] , 1)
            ],
            'fiber' => [
                'amount' => (string) $this->foodFact['fiberAmount'],
                'unit' => 'گرم',
                'name' => 'فیبر',
                'percent' => $this->handlePercent($this->foodFact['fiberAmount'] , 1)
            ],
            'sodium' => [
                'amount' => (string) $this->foodFact['sodiumAmount'],
                'unit' => 'میلی گرم',
                'name' => 'سدیم',
                'percent' => $this->handlePercent($this->foodFact['sodiumAmount'])
            ],
            'potassium' =>[
                'amount' =>  (string) $this->foodFact['potassiumAmount'],
                'unit' => 'میلی گرم',
                'name' => 'پتاسیم',
                'percent' => $this->handlePercent($this->foodFact['potassiumAmount'])
            ],
            'calcium' => [
                'amount' => (string) $this->foodFact['calciumAmount'],
                'unit' => 'میلی گرم',
                'name' => 'کلسیم',
                'percent' => $this->handlePercent($this->foodFact['calciumAmount'])
            ],
            'magnesium' => [
                'amount' => (string) $this->foodFact['magnesiumAmount'],
                'unit' => 'میلی گرم',
                'name' => 'منیزیم',
                'percent' => $this->handlePercent($this->foodFact['magnesiumAmount'])
            ],
            'iron' => [
                'amount' => (string) $this->foodFact['ironAmount'],
                'unit' => 'میلی گرم',
                'name' => 'آهن',
                'percent' => $this->handlePercent($this->foodFact['ironAmount'])
            ],
            'cholesterol' => [
                'amount' => (string) $this->foodFact['cholesterolAmount'],
                'unit' => ' میلی گرم',
                'name' => 'کلسترول',
                'percent' => $this->handlePercent($this->foodFact['cholesterolAmount'] , 1)
            ],
            'phosphor' =>  [
                'amount' => (string) $this->foodFact['phosphorAmount'],
                'unit' => 'میلی گرم',
                'name' => 'فسفر',
                'percent' => $this->handlePercent($this->foodFact['phosphorAmount'])
            ],
            'saturatedFat' => [
                'amount' => (string) $this->foodFact['saturatedFatAmount'],
                'unit' => ' گرم',
                'name' => 'چربی اشباع',
                'percent' => $this->handlePercent($this->foodFact['saturatedFatAmount'])
            ],
            'polyunsaturatedFat' => [
                'amount' => (string) $this->foodFact['polyunsaturatedFatAmount'],
                'unit' => ' گرم',
                'name' => 'اسید چرب غیر اشباع',
                'percent' => $this->handlePercent($this->foodFact['polyunsaturatedFatAmount'] , 1)
            ],
            'transFat' => [
                'amount' => (string) $this->foodFact['transFatAmount'],
                'unit' => ' گرم',
                'name' => 'اسید چرب ترانس',
                'percent' => $this->handlePercent($this->foodFact['transFatAmount'] , 1)
            ],
            'monounsaturatedFat' => [
                'amount' => (string) $this->foodFact['monounsaturatedFatAmount'],
                'unit' => ' گرم',
                'name' => 'اسید چرب تک غیر اشباع',
                'percent' => $this->handlePercent($this->foodFact['monounsaturatedFatAmount'] , 1)
            ],
        ];
    }

    private function clcUserCalorie()
    {
        $foodCalorie = $this->calorie ;
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
