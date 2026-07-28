<?php

namespace Modules\Api\app\Resources\Api\Consumption;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FoodsPaginateResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     */
    public $collects = FoodsResource::class;

    public function toArray($request): array
    {
        $favoriteFoods = collect($this->additional['favorite_foods'] ?? []);

        return [
            'data' => $this->collection->map(function ($item) use ($favoriteFoods) {
                return new FoodsResource($item, $favoriteFoods);
            }),
            'paginate' => [
                'current_page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'last_page' => $this->lastPage(),
            ],
        ];
    }
}
