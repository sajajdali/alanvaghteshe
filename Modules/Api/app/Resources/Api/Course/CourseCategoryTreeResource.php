<?php

namespace Modules\Api\app\Resources\Api\Course;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseCategoryTreeResource extends JsonResource
{
    public function toArray($request): array
    {
        $children = self::collection($this->whenLoaded('children'));

        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image,
            'parent_id' => $this->parent_id,
            'courses_count' => (int) ($this->courses_count ?? 0),
            'total_courses_count' => $this->resolveTotalCoursesCount(),
            'children' => $children,
        ];
    }

    private function resolveTotalCoursesCount(): int
    {
        $ownCoursesCount = (int) ($this->courses_count ?? 0);
        $children = $this->whenLoaded('children');

        if (! $children instanceof \Illuminate\Support\Collection || $children->isEmpty()) {
            return $ownCoursesCount;
        }

        return $ownCoursesCount + $children->sum(function ($child) {
            return (int) ($child->courses_count ?? 0);
        });
    }
}
