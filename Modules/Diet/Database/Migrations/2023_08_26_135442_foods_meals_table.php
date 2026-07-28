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
        Schema::create('foods_meals_pivot', function (Blueprint $table) {
            $table->foreignId('food_id')->constrained('foods')
                ->onUpdate('no action')
                ->onDelete('cascade');
            $table->foreignId('meal_id')->constrained('meals')
                ->onUpdate('no action')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::dropIfExists('foods_meals_pivot');
    }
};
