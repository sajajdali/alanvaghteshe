<?php

namespace Modules\Api\app\Resources\Api\Course;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CoursePaginateResource extends ResourceCollection
{
    public $collects = CourseListResource::class;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'paginate' => [
                'current_page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'last_page' => $this->lastPage(),
            ],
        ];
    }
}
