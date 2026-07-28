<?php

namespace Modules\Api\app\Resources\Api\Consumption;

use Illuminate\Http\Resources\Json\JsonResource;

class FoodsResource extends JsonResource
{
    protected $favoriteFoods;

    public function __construct($resource, $favoriteFoods = null)
    {
        parent::__construct($resource);
        $this->favoriteFoods = $favoriteFoods;
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_favorite' => $this->favoriteFoods && $this->favoriteFoods->contains($this->id),
        ];
    }
}
