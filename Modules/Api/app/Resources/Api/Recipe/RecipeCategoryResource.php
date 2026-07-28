<?php

namespace Modules\Api\app\Resources\Api\Recipe;

use Illuminate\Http\Resources\Json\JsonResource;

class RecipeCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
        ];
    }
}
