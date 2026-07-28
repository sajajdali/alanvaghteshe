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
        Schema::create('basic_food_category', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Modules\Diet\Entities\BasicFood::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\Modules\Diet\Entities\FoodCategory::class)->nullable()->constrained('food_categories')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basic_food_category');
    }
};
