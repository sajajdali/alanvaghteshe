<?php

namespace Modules\Api\Transformers\Notification;

use Illuminate\Http\Resources\Json\JsonResource;

class   NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id ?? 0,
            'title' => $this->data['title'] ?? "",
            'message' => $this->data['message'] ?? "",
            'excerpt' => $this->data['excerpt'] ?? "",
            'date' => $this->created_at->locale('fa')->diffForHumans(),
            'read' => $this->read_at !== null,
        ];
    }
}
