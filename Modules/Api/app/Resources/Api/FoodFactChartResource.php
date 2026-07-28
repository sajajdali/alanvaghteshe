<?php

namespace Modules\Api\app\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class FoodFactChartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'protein' => (string) round(($this['proteinAmount'] ) , 2),
            'fat' => (string) round($this['fatAmount']  , 2 ),
            'carbohydrate' => (string) round($this['carbohydrateAmount'] / 100 , 2 ),
            'sugar' => (string) round($this['sugarAmount']  , 2 ),
            'fiber' => (string) round($this['fiberAmount']  , 2 ),
            'sodium' => (string) round($this['sodiumAmount'] / 100 , 2 ),
            'potassium' => (string) round($this['potassiumAmount'] / 100 , 2 ),
            'calcium' => (string) round($this['calciumAmount'] / 100 , 2 ),
            'magnesium' => (string) round($this['magnesiumAmount'] / 100 , 2 ),
            'iron' => (string) round($this['ironAmount'] / 100 , 2 ),
            'cholesterol' => (string) round($this['cholesterolAmount'] / 100 , 2 ),
            'phosphor' => (string) round($this['phosphorAmount'] / 100 , 2 ),
            'saturatedFat' => (string) round($this['saturatedFatAmount'] / 100 , 2 ),
            'polyunsaturatedFat' => (string) round($this['polyunsaturatedFatAmount'] / 100 , 2 ),
            'transFat' => (string) round($this['transFatAmount'] / 100 , 2 ),
            'monounsaturatedFat' => (string) round($this['monounsaturatedFatAmount'] / 100 , 2 ),
        ];
    }
}
