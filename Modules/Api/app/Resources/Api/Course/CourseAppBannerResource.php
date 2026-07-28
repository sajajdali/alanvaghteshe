<?php

namespace Modules\Api\app\Resources\Api\Course;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Api\Enum\RouteEnum;
use Modules\Api\app\Resources\ButtonResource;
use Modules\Course\app\Models\CourseAppBanner;

class CourseAppBannerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image,
            'position' => $this->position,
            'position_label' => CourseAppBanner::positions()[$this->position] ?? $this->position,
            'sort_order' => (int) $this->sort_order,
            'priority' => (int) $this->priority,
            'course_id' => $this->course_id,
            'course_title' => $this->course?->title,
            'button' => ButtonResource::make([
                'title' => $this->title,
                'subtitle' => $this->course?->title ?? '',
                'route' => RouteEnum::COURSE_DETAIL,
                'data' => [
                    'course_id' => $this->course_id,
                    'slug' => $this->course?->slug,
                ],
            ]),
        ];
    }
}
