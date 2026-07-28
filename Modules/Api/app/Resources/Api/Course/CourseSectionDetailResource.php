<?php

namespace Modules\Api\app\Resources\Api\Course;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseSectionDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        $lessons = $this->whenLoaded('lessons');
        $lessonsCollection = $lessons instanceof \Illuminate\Support\Collection ? $lessons : collect();
        $totalDurationSeconds = (int) $lessonsCollection->sum(fn ($lesson) => (int) ($lesson->duration_seconds ?? 0));

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'sort_order' => (int) $this->sort_order,
            'lessons_count' => $lessonsCollection->count(),
            'total_duration_seconds' => $totalDurationSeconds,
            'total_duration_minutes' => (int) ceil($totalDurationSeconds / 60),
            'lessons' => CourseLessonDetailResource::collection($this->whenLoaded('lessons')),
        ];
    }
}
