<?php

namespace Modules\Api\app\Resources\Api\Consumption;

use Illuminate\Http\Resources\Json\JsonResource;

class UserFoodsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->consumable->name,
            'quantity' => $this->quantity,
            'unit' => $this->quantity . ' ' . $this->unit->name,
//            'unit_name' => $this->unit,
            'calories' => round($this->calories),
            'protein' => round($this->protein),
            'carb' => round($this->carb),
            'fat' => round($this->fat),
            'fiber' => round($this->fiber),
            'date' => getPersianDate($this->consumed_at),
        ];
    }
}
