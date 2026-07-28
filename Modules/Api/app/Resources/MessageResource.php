<?php

namespace Modules\Api\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'is_read' => $this->is_read,
            'created_at' => verta($this->created_at)->format('Y/m/d ساعت H:i'),
        ];
    }
}
