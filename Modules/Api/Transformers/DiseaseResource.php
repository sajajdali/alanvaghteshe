<?php

namespace Modules\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class DiseaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        return [
            'id' => $this->id ?? 0,
            'name' => $this->name ?? ""
        ];
    }
}
