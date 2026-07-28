<?php

namespace Modules\Api\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestDietDetailResourcev2 extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'title' => $this->meal->title ?? 'نامشخص', // عنوان وعده
            'icon_url' => $this->meal->icon_url ?? null, // آیکون وعده
            'is_done' => (bool) $this->is_done, // وضعیت انجام‌شده
            'priority' => $this->meal->priority ?? 0, // اولویت وعده
            'hand_written' => null, // اگر دستی نوشته شده
            'meal_id' => $this->meal_id,
            'meals' => $this->getFormattedMeals(), // غذاهای این وعده
            'meal_recipe' => $this->meal_recipe ?? null, // دستور پخت وعده
            'meal_calories' => $this->calories ?? 0, // مجموع کالری وعده
        ];
    }

    /**
     * Format meals data for the response.
     *
     * @return array
     */
    private function getFormattedMeals()
    {
        $details = json_decode($this->detail, true);
        $meals = $details['food_snapshot']['basic_foods'] ?? [];

        return array_map(function ($food) {
            return [
                'type' => 'all',
                'id' => $food['basic_food_id'] ?? 0,
                'title' => "{$food['quantity']} {$food['unit']} {$food['name']} ({$food['weight_per_gram']} گرم)",
                'carb' => ( isset($food['carb']) && $food['carb'] > 0) ? $food['carb']  : 0,
                'protein' => ( isset($food['protein']) && $food['protein'] > 0) ? $food['protein']  : 0,
                'fat' => ( isset($food['fat']) && $food['fat'] > 0) ? $food['fat']  : 0,
                'fiber' => ( isset($food['fiber']) && $food['fiber'] > 0) ? $food['fiber']  : 0,
                'calorie' => $food['calorie'] ?? 0,
//                'weight_per_gram' => $food['weight_per_gram'] ?? 0,
            ];
        }, $meals);
    }
}
