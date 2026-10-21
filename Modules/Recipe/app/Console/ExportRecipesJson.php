<?php

namespace Modules\Recipe\app\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use JsonException;

class ExportRecipesJson extends Command
{
    protected $signature = 'recipe:export-json
                            {--path= : Output path; relative paths are resolved from the project root}
                            {--recipe-id= : Export only this recipe ID}
                            {--recipes-only : Exclude reference catalogs for a small review file}
                            {--compact : Write JSON without indentation}';

    protected $description = 'Export every recipe and all data used by the admin recipe form to an import-ready JSON file';

    public function handle(): int
    {
        $outputPath = $this->resolveOutputPath($this->option('path'));
        $recipeId = $this->option('recipe-id') !== null ? (int) $this->option('recipe-id') : null;

        if ($recipeId !== null && ! DB::table('recipes')->where('id', $recipeId)->exists()) {
            $this->error("Recipe ID {$recipeId} does not exist.");

            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($outputPath));
        $temporaryPath = $outputPath.'.tmp-'.getmypid();
        $handle = fopen($temporaryPath, 'wb');

        if ($handle === false) {
            $this->error("Could not open output file: {$temporaryPath}");

            return self::FAILURE;
        }

        $this->info('Reading recipes and related tables...');

        // Keep only compact lookup data and pivot rows in memory. Large catalog
        // records are written one at a time so this works with a 128 MB limit.
        $categoriesById = DB::table('recipe_categories')->select('id', 'name')->get()->keyBy('id');
        $foodUnitsById = DB::table('food_units')->select('id', 'name')->get()->keyBy('id');
        $basicFoodsById = DB::table('basic_foods')->select('id', 'name')->get()->keyBy('id');
        $referencedFoodIds = DB::table('recipes')->whereNotNull('food_id')->pluck('food_id');
        $foodReferenceQuery = DB::table('foods')
            ->whereNull('parent_id')
            ->when($referencedFoodIds->isNotEmpty(), fn ($query) => $query->orWhereIn('id', $referencedFoodIds));
        $foodsById = (clone $foodReferenceQuery)->select('id', 'name')->get()->keyBy('id');
        $ingredientsByRecipe = DB::table('recipe_basic_foods')
            ->when($recipeId !== null, fn ($query) => $query->where('recipe_id', $recipeId))
            ->orderBy('id')->get()->groupBy('recipe_id');
        $unitsByRecipe = DB::table('recipe_units')
            ->when($recipeId !== null, fn ($query) => $query->where('recipe_id', $recipeId))
            ->orderBy('id')->get()->groupBy('recipe_id');

        $recipeCount = DB::table('recipes')->when($recipeId !== null, fn ($query) => $query->where('id', $recipeId))->count();
        $ingredientCount = DB::table('recipe_basic_foods')->when($recipeId !== null, fn ($query) => $query->where('recipe_id', $recipeId))->count();
        $servingUnitCount = DB::table('recipe_units')->when($recipeId !== null, fn ($query) => $query->where('recipe_id', $recipeId))->count();
        $issues = [];

        try {
            fwrite($handle, $this->option('compact') ? '{' : "{\n");
            $this->writeProperty($handle, 'format', 'alanvaghteshe-recipes', 1, true);
            $this->writeProperty($handle, 'version', 1, 1, true);
            $this->writeProperty($handle, 'exported_at', now()->toIso8601String(), 1, true);
            $this->writeProperty($handle, 'instructions_fa', [
                'فیلدهای id و pivot_id را تغییر ندهید.',
                'برای انتخاب دسته‌بندی، ماده اولیه، واحد یا غذای اصلی از شناسه‌های بخش reference_data استفاده کنید.',
                'هر دستور باید حداقل یک مرحله پخت، یک ماده اولیه و یک serving_unit داشته باشد.',
                'difficulty فقط می‌تواند 1، 2 یا 3 باشد.',
                'نام‌های کنار شناسه‌ها برای خوانایی هستند؛ در ورود اطلاعات، شناسه‌ها مبنا خواهند بود.',
            ], 1, true);
            $this->writeProperty($handle, 'field_guide', [
                'time_minutes' => 'مدت زمان پخت به دقیقه',
                'serving' => 'تعداد نفرات',
                'weight_grams' => 'وزن کل غذا به گرم',
                'calorie' => 'کالری کل دستور',
                'difficulty' => '1 آسان، 2 متوسط، 3 سخت',
                'ingredients.quantity' => 'مقدار ماده بر اساس واحد انتخاب‌شده',
                'serving_units.grams_per_unit' => 'وزن هر واحد سرو به گرم',
                'image' => 'مسیر تصویر روی دیسک public',
            ], 1, true);

            if (! $this->option('recipes-only')) {
                $this->writeObjectStart($handle, 'reference_data', 1);
                $this->writeIterableProperty($handle, 'recipe_categories', DB::table('recipe_categories')->orderBy('id')->cursor(), fn (object $item): array => [
                    'id' => (int) $item->id, 'name' => $item->name, 'image' => $item->image,
                    'priority' => (int) $item->priority, 'detail' => $this->decodeJson($item->detail),
                ], 2, true);
                $this->writeIterableProperty($handle, 'food_units', DB::table('food_units')->orderBy('id')->cursor(), fn (object $item): array => [
                    'id' => (int) $item->id, 'name' => $item->name, 'detail' => $this->decodeJson($item->detail),
                ], 2, true);
                $this->writeIterableProperty($handle, 'basic_foods', DB::table('basic_foods')->orderBy('id')->cursor(), fn (object $item): array => [
                    'id' => (int) $item->id, 'name' => $item->name, 'type' => $item->type,
                    'active' => (bool) $item->active, 'recipe' => $item->recipe,
                    'food_fact' => $this->decodeJson($item->food_fact), 'detail' => $this->decodeJson($item->detail),
                ], 2, true);
                $this->writeIterableProperty($handle, 'basic_food_units', DB::table('basic_food_units')->orderBy('id')->cursor(), fn (object $item): array => [
                    'id' => (int) $item->id, 'basic_food_id' => (int) $item->basic_food_id,
                    'food_unit_id' => (int) $item->food_unit_id, 'quantity_per_unit' => (float) $item->quantity_per_unit,
                    'is_primary' => (bool) $item->is_primary,
                    'max_allowed' => $item->max_allowed !== null ? (float) $item->max_allowed : null,
                ], 2, true);
                $this->writeIterableProperty($handle, 'foods', (clone $foodReferenceQuery)->orderBy('id')->cursor(), fn (object $item): array => [
                    'id' => (int) $item->id, 'parent_id' => $item->parent_id !== null ? (int) $item->parent_id : null,
                    'available_as_main_food' => $item->parent_id === null, 'name' => $item->name,
                    'carb' => (float) $item->carb, 'max_carb' => (float) $item->max_carb,
                    'protein' => (float) $item->protein, 'max_protein' => (float) $item->max_protein,
                    'fat' => (float) $item->fat, 'max_fat' => (float) $item->max_fat,
                    'fiber' => (float) $item->fiber, 'max_fiber' => (float) $item->max_fiber,
                    'calories' => (int) $item->calories, 'max_calories' => (int) $item->max_calories,
                    'type' => $item->type, 'max_per_unit' => $item->max_per_unit !== null ? (int) $item->max_per_unit : null,
                    'detail' => $this->decodeJson($item->detail),
                ], 2, false);
                $this->writeObjectEnd($handle, 1, true);
            }

            $this->writeIterableProperty(
                $handle,
                'recipes',
                DB::table('recipes')->when($recipeId !== null, fn ($query) => $query->where('id', $recipeId))->orderBy('id')->cursor(),
                function (object $recipe) use ($categoriesById, $foodUnitsById, $basicFoodsById, $foodsById, $ingredientsByRecipe, $unitsByRecipe, &$issues): array {
                    $ingredients = $ingredientsByRecipe->get($recipe->id, collect());
                    $servingUnits = $unitsByRecipe->get($recipe->id, collect());
                    $recipeIssues = $this->validateRecipe($recipe, $ingredients, $servingUnits, $categoriesById, $foodUnitsById, $basicFoodsById, $foodsById);

                    if ($recipeIssues !== []) {
                        $issues[] = ['recipe_id' => (int) $recipe->id, 'recipe_name' => $recipe->name, 'issues' => $recipeIssues];
                    }

                    return $this->recipeToArray($recipe, $ingredients, $servingUnits, $categoriesById, $foodUnitsById, $basicFoodsById, $foodsById);
                },
                1,
                true
            );

            $this->writeProperty($handle, 'validation_report', ['recipes_with_issues' => count($issues), 'items' => $issues], 1, true);
            $this->writeProperty($handle, 'summary', [
                'recipes' => $recipeCount,
                'recipe_categories' => DB::table('recipe_categories')->count(),
                'basic_foods' => DB::table('basic_foods')->count(),
                'food_units' => DB::table('food_units')->count(),
                'foods_available_in_admin' => (clone $foodReferenceQuery)->count(),
                'recipe_ingredient_rows' => $ingredientCount,
                'recipe_serving_unit_rows' => $servingUnitCount,
                'recipes_with_issues' => count($issues),
            ], 1, false);
            fwrite($handle, $this->option('compact') ? '}' : "\n}\n");
            fclose($handle);

            if (! rename($temporaryPath, $outputPath)) {
                throw new \RuntimeException("Could not move temporary export to {$outputPath}");
            }
        } catch (\Throwable $exception) {
            if (is_resource($handle)) {
                fclose($handle);
            }
            if (File::exists($temporaryPath)) {
                File::delete($temporaryPath);
            }
            $this->error('Export failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Recipe export completed successfully.');
        $this->line("Recipes: {$recipeCount}");
        $this->line("Ingredients: {$ingredientCount}");
        $this->line("Serving units: {$servingUnitCount}");
        $this->line('Recipes with validation notices: '.count($issues));
        $this->line("File: {$outputPath}");
        $this->line('Size: '.$this->humanFileSize(File::size($outputPath)));

        return self::SUCCESS;
    }

    private function recipeToArray(
        object $recipe,
        Collection $ingredients,
        Collection $servingUnits,
        Collection $categoriesById,
        Collection $foodUnitsById,
        Collection $basicFoodsById,
        Collection $foodsById
    ): array {
        $category = $recipe->category_id ? $categoriesById->get($recipe->category_id) : null;
        $mainFood = $recipe->food_id ? $foodsById->get($recipe->food_id) : null;
        $detail = $this->decodeJson($recipe->detail);

        return [
            'id' => (int) $recipe->id,
            'name' => $recipe->name,
            'category' => [
                'id' => $recipe->category_id !== null ? (int) $recipe->category_id : null,
                'name' => $category?->name,
            ],
            'main_food' => [
                'id' => $recipe->food_id !== null ? (int) $recipe->food_id : null,
                'name' => $mainFood?->name,
            ],
            'time_minutes' => (int) $recipe->time,
            'serving' => (int) $recipe->serving,
            'weight_grams' => (int) $recipe->weight,
            'calorie' => (int) $recipe->calorie,
            'description' => $recipe->description,
            'difficulty' => (int) $recipe->difficulty,
            'instructions' => $this->normaliseInstructions($this->decodeJson($recipe->instructions)),
            'ingredients' => $ingredients->map(function (object $ingredient) use ($basicFoodsById, $foodUnitsById): array {
                $basicFood = $ingredient->basic_food_id ? $basicFoodsById->get($ingredient->basic_food_id) : null;
                $foodUnit = $ingredient->food_unit_id ? $foodUnitsById->get($ingredient->food_unit_id) : null;

                return [
                    'pivot_id' => (int) $ingredient->id,
                    'basic_food_id' => $ingredient->basic_food_id !== null ? (int) $ingredient->basic_food_id : null,
                    'basic_food_name' => $basicFood?->name,
                    'food_unit_id' => $ingredient->food_unit_id !== null ? (int) $ingredient->food_unit_id : null,
                    'food_unit_name' => $foodUnit?->name,
                    'quantity' => (float) $ingredient->quantity_per_unit,
                ];
            })->values()->all(),
            'serving_units' => $servingUnits->map(function (object $unit) use ($foodUnitsById): array {
                $foodUnit = $unit->food_unit_id ? $foodUnitsById->get($unit->food_unit_id) : null;

                return [
                    'id' => (int) $unit->id,
                    'food_unit_id' => $unit->food_unit_id !== null ? (int) $unit->food_unit_id : null,
                    'food_unit_name' => $foodUnit?->name,
                    'grams_per_unit' => (float) $unit->quantity_per_unit,
                ];
            })->values()->all(),
            'nutrition_facts' => $this->decodeJson($recipe->foodFact),
            'suggested' => (bool) $recipe->suggested,
            'priority' => (int) $recipe->priority,
            'number_views' => (int) $recipe->number_views,
            'active' => (bool) $recipe->active,
            'image' => is_array($detail) ? ($detail['detail_image'] ?? null) : null,
            'detail' => $detail,
            'created_at' => $recipe->created_at,
            'updated_at' => $recipe->updated_at,
        ];
    }

    /** @param resource $handle */
    private function writeProperty($handle, string $name, mixed $value, int $depth, bool $commaAfter): void
    {
        fwrite(
            $handle,
            $this->indent($depth).$this->encode($name).($this->option('compact') ? ':' : ': ').$this->encode($value)
            .($commaAfter ? ',' : '').$this->jsonNewLine()
        );
    }

    /** @param resource $handle */
    private function writeObjectStart($handle, string $name, int $depth): void
    {
        fwrite($handle, $this->indent($depth).$this->encode($name).($this->option('compact') ? ':{' : ': {').$this->jsonNewLine());
    }

    /** @param resource $handle */
    private function writeObjectEnd($handle, int $depth, bool $commaAfter): void
    {
        fwrite($handle, $this->indent($depth).'}'.($commaAfter ? ',' : '').$this->jsonNewLine());
    }

    /** @param resource $handle */
    private function writeIterableProperty($handle, string $name, iterable $items, callable $transform, int $depth, bool $commaAfter): void
    {
        fwrite($handle, $this->indent($depth).$this->encode($name).($this->option('compact') ? ':[' : ': [').$this->jsonNewLine());

        $first = true;
        foreach ($items as $item) {
            if (! $first) {
                fwrite($handle, ','.$this->jsonNewLine());
            }

            $encoded = $this->encode($transform($item));
            if (! $this->option('compact')) {
                $encoded = str_replace("\n", "\n".$this->indent($depth + 1), $encoded);
            }
            fwrite($handle, $this->indent($depth + 1).$encoded);
            $first = false;
        }

        if (! $first) {
            fwrite($handle, $this->jsonNewLine());
        }
        fwrite($handle, $this->indent($depth).']'.($commaAfter ? ',' : '').$this->jsonNewLine());
    }

    private function encode(mixed $value): string
    {
        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR;
        if (! $this->option('compact')) {
            $flags |= JSON_PRETTY_PRINT;
        }

        return json_encode($value, $flags);
    }

    private function indent(int $depth): string
    {
        return $this->option('compact') ? '' : str_repeat('    ', $depth);
    }

    private function jsonNewLine(): string
    {
        return $this->option('compact') ? '' : "\n";
    }

    private function resolveOutputPath(?string $path): string
    {
        if (! $path) {
            return Storage::disk('local')->path('exports/recipes/recipes-export-'.now()->format('Y-m-d_H-i-s').'.json');
        }

        return str_starts_with($path, DIRECTORY_SEPARATOR) ? $path : base_path($path);
    }

    private function decodeJson(?string $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $value;
        }
    }

    private function normaliseInstructions(mixed $instructions): array
    {
        if (! is_array($instructions)) {
            return [];
        }

        return collect($instructions)->map(function (mixed $instruction, int $index): array {
            return [
                'step' => $index + 1,
                'text' => is_array($instruction) ? (string) ($instruction['text'] ?? '') : (string) $instruction,
            ];
        })->values()->all();
    }

    private function validateRecipe(
        object $recipe,
        Collection $ingredients,
        Collection $servingUnits,
        Collection $categoriesById,
        Collection $foodUnitsById,
        Collection $basicFoodsById,
        Collection $foodsById
    ): array {
        $issues = [];
        $instructions = $this->normaliseInstructions($this->decodeJson($recipe->instructions));

        if (trim((string) $recipe->name) === '') {
            $issues[] = ['level' => 'error', 'code' => 'missing_name', 'message_fa' => 'نام دستور خالی است.'];
        }
        if ($recipe->category_id === null || ! $categoriesById->has($recipe->category_id)) {
            $issues[] = ['level' => 'error', 'code' => 'invalid_category', 'message_fa' => 'دسته‌بندی معتبر نیست.'];
        }
        if ($recipe->food_id === null) {
            $issues[] = ['level' => 'notice', 'code' => 'missing_main_food', 'message_fa' => 'غذای اصلی انتخاب نشده است.'];
        } elseif (! $foodsById->has($recipe->food_id)) {
            $issues[] = ['level' => 'error', 'code' => 'invalid_main_food', 'message_fa' => 'شناسه غذای اصلی معتبر نیست.'];
        }
        if (! in_array((int) $recipe->difficulty, [1, 2, 3], true)) {
            $issues[] = ['level' => 'error', 'code' => 'invalid_difficulty', 'message_fa' => 'درجه سختی باید 1، 2 یا 3 باشد.'];
        }
        if ($instructions === [] || collect($instructions)->contains(fn (array $step): bool => trim($step['text']) === '')) {
            $issues[] = ['level' => 'error', 'code' => 'invalid_instructions', 'message_fa' => 'مراحل پخت خالی یا ناقص است.'];
        }
        if ($ingredients->isEmpty()) {
            $issues[] = ['level' => 'error', 'code' => 'missing_ingredients', 'message_fa' => 'هیچ ماده اولیه‌ای ثبت نشده است.'];
        }

        foreach ($ingredients as $ingredient) {
            if (! $basicFoodsById->has($ingredient->basic_food_id)) {
                $issues[] = ['level' => 'error', 'code' => 'invalid_basic_food', 'message_fa' => "ماده اولیه ردیف {$ingredient->id} معتبر نیست."];
            }
            if (! $foodUnitsById->has($ingredient->food_unit_id)) {
                $issues[] = ['level' => 'error', 'code' => 'invalid_ingredient_unit', 'message_fa' => "واحد ماده اولیه ردیف {$ingredient->id} معتبر نیست."];
            }
        }

        $duplicateIngredients = $ingredients->groupBy('basic_food_id')->filter(fn (Collection $rows): bool => $rows->count() > 1);
        if ($duplicateIngredients->isNotEmpty()) {
            $issues[] = [
                'level' => 'notice',
                'code' => 'duplicate_ingredients',
                'message_fa' => 'یک یا چند ماده اولیه در این دستور تکراری است.',
                'basic_food_ids' => $duplicateIngredients->keys()->map(fn ($id): int => (int) $id)->values()->all(),
            ];
        }

        if ($servingUnits->isEmpty()) {
            $issues[] = ['level' => 'error', 'code' => 'missing_serving_unit', 'message_fa' => 'واحد سرو غذا ثبت نشده است.'];
        } elseif ($servingUnits->count() > 1) {
            $issues[] = ['level' => 'notice', 'code' => 'multiple_serving_units', 'message_fa' => 'بیش از یک واحد سرو برای دستور ثبت شده است.'];
        }
        foreach ($servingUnits as $unit) {
            if (! $foodUnitsById->has($unit->food_unit_id)) {
                $issues[] = ['level' => 'error', 'code' => 'invalid_serving_unit', 'message_fa' => "واحد سرو ردیف {$unit->id} معتبر نیست."];
            }
        }

        return $issues;
    }

    private function humanFileSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return number_format($bytes / (1024 * 1024), 1).' MB';
    }
}
