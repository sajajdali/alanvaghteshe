<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() : void
    {
        Schema::create('diet_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(1);
            $table->boolean('general_pattern')->default(0);
            $table->integer('day_count')->default(1);
            $table->integer('reduced_calories')->default(0);
            $table->tinyInteger('food_type')->default(\Modules\Diet\Enum\FoodTypeEnum::SIMPLE)->comment('10 = simple | 20 = combined');
            $table->json('detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::dropIfExists('diet_plans');
    }
};
