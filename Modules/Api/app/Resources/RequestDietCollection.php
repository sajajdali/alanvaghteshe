<?php

namespace Modules\Api\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RequestDietCollection extends ResourceCollection
{
    public $collects = RequestDietResourse::class;

    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        return [
            'request_diets' => $this->collection,
            'paginate' => [
                'current_page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'last_page' => $this->lastPage()
            ],
        ];
    }
}
