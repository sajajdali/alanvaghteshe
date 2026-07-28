<?php

namespace Modules\Api\Http\Controllers\Profile;

use Illuminate\Routing\Controller;
use Modules\Api\app\Resources\Api\Recipe\RecipeCategoryResource;
use Modules\Api\app\Resources\Api\Recipe\RecipeListResource;
use Modules\Api\app\Resources\Api\Recipe\RecipePaginateResource;
use Modules\Api\app\Resources\Api\Recipe\RecipeResource;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Recipe\app\Models\Recipe;
use Modules\Recipe\app\Models\RecipeCategory;

class RecipeController extends Controller
{
    use ApiHandlerTrait;

    public function index()
    {
        return $this->ok([
            'categories' => $this->categories(),
            'suggested' => $this->suggestedRecipe(),
            'grouping_on_calories' => $this->groupingOnCalories(),
            'foods' => $this->list()
        ]);
    }

    private function categories(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $categories = RecipeCategory::orderBy('priority')->orderByDesc('id')->get();
        return RecipeCategoryResource::collection($categories);
    }

    private function suggestedRecipe(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $recipe = Recipe::suggested()->limit(4)->get();
        return RecipeListResource::collection($recipe);

    }

    private function groupingOnCalories(): array
    {
        return [
            '100-300',
            '300-500',
            '500-700',
            '700-1000',
            '1200-1400'
        ];
    }

    private function list()
    {
        $query = Recipe::query();

        if (request()->has('filter')) {
            $filter = request()->get('filter');

            //filter favorite foods
            if ($filter == '-1') {
                $favoriteRecipe = isset(auth()->user()->favoriteRecipe) ? json_decode(auth()->user()->favoriteRecipe) : [];
                $query->whereIn('id', $favoriteRecipe);
            } else {
                $query->whereHas('category', function ($q) use ($filter) {
                    $q->where('id', $filter);
                });
            }

        }

        if (request()->has('q')) {
            $q = request()->get('q');
            $query->where('name', 'LIKE', "%$q%");
        }

        if (request()->has('calorie_limit')) {
            $calorieLimit = request()->get('calorie_limit');
            [$min, $max] = explode('-', $calorieLimit);
           $min = (int) $min * 2;
            $max = (int) $max * 2;
            $query->whereBetween('calorie', [$min, $max]);
        }

        $recipes = $query->orderByDesc('id')->paginate(30);


        return RecipePaginateResource::make($recipes);
    }

    public function recipe(Recipe $recipe)
    {
        return $this->ok(
            RecipeResource::make($recipe)
        );
    }

    public function favorite(Recipe $recipe)
    {

        $user = auth()->user();
        $favoriteRecipe = $user->favoriteRecipe;
        if ($favoriteRecipe){
            $favoriteRecipe = json_decode($favoriteRecipe , true);
            if (in_array( $recipe->id , $favoriteRecipe)){
                $favoriteRecipe = array_filter($favoriteRecipe, fn($item) => $item !== $recipe->id);
            } else {
                $favoriteRecipe[] = $recipe->id;
            }
        } else {
            $favoriteRecipe = [$recipe->id];
        }
        $favoriteRecipe = array_values( array_unique($favoriteRecipe));
        $user->favoriteRecipe = json_encode($favoriteRecipe);

        return $this->ok([
             'status' => true
        ]);

    }

}
