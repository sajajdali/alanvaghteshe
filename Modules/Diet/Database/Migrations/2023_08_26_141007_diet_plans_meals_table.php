<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() : void
    {
        Schema::create('diet_plans_meals_pivot', function (Blueprint $table) {
            $table->foreignId('diet_plan_id')->constrained('diet_plans')
                ->onUpdate('no action')
                ->onDelete('cascade');
            $table->foreignId('meal_id')->constrained('meals')
                ->onUpdate('no action')
                ->onDelete('cascade');
            $table->integer('carb')->default(0);
            $table->integer('protein')->default(0);
            $table->integer('fat')->default(0);
            $table->integer('fiber')->default(0);
            $table->integer('calorie_percent')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::dropIfExists('diet_plans_meals_pivot');
    }
};
