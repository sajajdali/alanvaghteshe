<?php

namespace Modules\Api\Transformers\Chat;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->getName(),
            'content' => $this->content,
            'is_question' => $this->is_question,
            'time' => $this->created_at->format('H:i'),
            'avatar' => $this->user?->avatar,
        ];
    }
}
