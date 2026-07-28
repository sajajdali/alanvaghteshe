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
        Schema::create('foods_related_pivot', function (Blueprint $table) {
            $table->foreignId('food_id')->constrained('foods')
                ->onUpdate('no action')
                ->onDelete('cascade');
            $table->foreignId('related_id')->constrained('foods')
                ->onUpdate('no action')
                ->onDelete('cascade');
            $table->enum('main_nutrition',['carb','protein','fat','fiber'])->default('protein');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::dropIfExists('foods_related_pivot');
    }
};
