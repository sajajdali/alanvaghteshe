<?php

namespace Modules\Api\app\Resources\Api\Recipe;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Api\app\Resources\Api\FoodFactResource;
use Modules\Api\app\Resources\ButtonResource;
use Modules\Api\Enum\PopupEnum;
use Modules\Api\Enum\RouteEnum;

class RecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'disable_recipe' => $this->checkActive(),
            'image' => $this->recipeImage(),
            'name' => $this->name,
            'id' => $this->id,
            'calorie' => round($this->calorie / 2),
            'time' => $this->time,
            'is_favorite' => $this->checkFavorite(),
            'serving' => $this->serving,
            'difficulty' => $this->difficulty->getName(),
            'description' => $this->description,
            'category' => RecipeCategoryResource::make($this->category),
            'foods' => RecipeBasicFoodsResource::collection($this->basicFoods),
            'instructions' => RecipeInstructionsResource::collection($this->instructions),
            'number_of_foods_available' => ' این غذا از' . $this->basicFoods->count() . ' ماده اصلی تشکیل شده است',
            'food_fact' => FoodFactResource::make($this),
        ];
    }

    private function checkActive() {
        $user = auth()->user();
        if (!$user->activePackage()){
            return [
                'title' => 'دسترسی ندارید',
                'body' => [
                    'برای دسترسی به امکانات کامل اپلیکیشن و استفاده از رژیم های تخصصی ، باید اقدام به خرید نمایید.',
                    'با خرید پکیج دسترسی شما به رژیم ها ، بانک کالری غذاها و دستورات آشپزی فعال خواهد شد'
                ],
                'button'   =>ButtonResource::make([
                    'title' => 'خرید و سفارش',
                    'route' => RouteEnum::ORDER_PACKAGE,
                    'type' => 'success',
                    'data' => null
                ])
            ];
        }
        return null;
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
