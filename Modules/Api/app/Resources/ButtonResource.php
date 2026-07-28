<?php

namespace Modules\Api\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ButtonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'title' => $this['title'] ?? null,
            'sub_title' => $this['subtitle'] ?? null,
            'route' => $this['route'] ?? null,
            'icon' => $this['icon'] ?? null,
            'type' => $this['type'] ?? null,
            'is_web_page' => $this['is_web_page'] ?? false,
            'data' => $this['data'] ?? null,
        ];
    }
}
