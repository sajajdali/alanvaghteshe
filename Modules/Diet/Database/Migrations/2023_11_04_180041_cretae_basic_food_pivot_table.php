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
    public function up(): void
    {
        Schema::create('basic_food_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_food_id')->constrained('basic_foods')
                ->onUpdate('no action')
                ->onDelete('cascade');
            $table->foreignId('food_id')->constrained('foods')
                ->onUpdate('no action')
                ->onDelete('cascade');
            $table->float('quantity')->default(1);
            $table->float('maximum')->nullable();
            $table->tinyInteger('is_main')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('basic_food_pivot');
    }
};
