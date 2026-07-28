<?php

namespace Modules\Api\Transformers\Exercise;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ExerciseCollection extends ResourceCollection
{

    public $collects = ExerciseRequestWithOutDetailResource::class;


    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'exercise_plan' => $this->collection,
            'paginate' => [
                'current_page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'last_page' => $this->lastPage()
            ],
        ];
    }
}
