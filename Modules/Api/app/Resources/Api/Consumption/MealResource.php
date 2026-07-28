<?php

namespace Modules\Api\app\Resources\Api\Consumption;

use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon_url
        ];
    }
}
