<?php

namespace Modules\Api\app\Resources\Api\Recipe;

use Illuminate\Http\Resources\Json\JsonResource;

class RecipeListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'calorie' => $this->calorie / 2,
            'is_favorite' => $this->checkFavorite(),
            'image' => $this->recipeImage(),
            'time' => $this->time
        ];
    }
    private function recipeImage()
    {
        if (isset($this->detail['detail_image']) && file_exists(public_path('storage/' .$this->detail['detail_image']))) {
            return asset('storage/' .$this->detail['detail_image']);
        }
        return  asset('assets/recipe/no_food.jpg');
    }

    private function checkFavorite(): bool
    {
        $favoriteRecipe = auth()->user()->favoriteRecipe;
        if (!$favoriteRecipe){
            return false;
        }
        $favoriteRecipe = json_decode($favoriteRecipe, true);
        if (in_array($this->id , $favoriteRecipe)){
            return true;
        }
        return false;
    }
}
