<?php

namespace Modules\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     */
    public function toArray($request): array
    {
        return [
            'question' => $this->question ?? '',
            'answer' => $this->answer ?? '',
        ];
    }
}
