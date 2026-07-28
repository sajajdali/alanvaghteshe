<?php

namespace Modules\Api\app\Resources\Api\Recipe;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Diet\Entities\FoodUnit;

class RecipeBasicFoodsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->pivot->quantity_per_unit,
            'unit' => FoodUnit::find($this->pivot->food_unit_id)->name,
        ];
    }
}
