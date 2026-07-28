<?php

namespace Modules\Api\app\Resources\Api\Course;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseLessonDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content_type' => $this->content_type,
            'duration_seconds' => (int) ($this->duration_seconds ?? 0),
            'duration_minutes' => (int) ceil(((int) ($this->duration_seconds ?? 0)) / 60),
            'is_preview' => (bool) $this->is_preview,
            'is_free' => (bool) $this->is_preview,
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
