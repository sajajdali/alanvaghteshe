<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Modules\Recipe\app\Models\RecipeCategory::class , 'category_id')->constrained('recipe_categories')->cascadeOnDelete()->nullable();
            $table->string('name');
            $table->integer('time')->default(0);
            $table->integer('serving')->default(1);
            $table->integer('weight')->default(0);
            $table->integer('calorie')->default(0);
            $table->text('description')->nullable();
            $table->tinyInteger('difficulty')->default(2)->comment('1 = easy | 2 = normal | 3 = hard');
            $table->json('foodFact')->nullable();
            $table->json('instructions')->nullable();
            $table->tinyInteger('suggested')->default(0);
            $table->integer('priority')->default(1);
            $table->integer('number_views')->default(0);
            $table->tinyInteger('active')->default(1);
            $table->json('detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
