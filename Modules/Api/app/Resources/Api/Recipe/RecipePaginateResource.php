<?php

namespace Modules\Api\app\Resources\Api\Recipe;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RecipePaginateResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     */
    public $collects = RecipeListResource::class;
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'paginate' => [
                'current_page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'last_page' => $this->lastPage()
            ],
        ];
    }
}
