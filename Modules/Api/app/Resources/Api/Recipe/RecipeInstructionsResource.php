<?php

namespace Modules\Api\app\Resources\Api\Recipe;

use Illuminate\Http\Resources\Json\JsonResource;

class RecipeInstructionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return
             $this['text']
        ;
    }
}
