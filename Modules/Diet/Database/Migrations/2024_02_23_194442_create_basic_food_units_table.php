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
        Schema::create('basic_food_units', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Modules\Diet\Entities\BasicFood::class)->constrained()->nullable()->nullable();
            $table->foreignIdFor(\Modules\Diet\Entities\FoodUnit::class)->constrained()->nullable()->nullable();
            $table->float('quantity_per_unit')->default(0);
            $table->tinyInteger('is_primary')->default(1);
            $table->integer('max_allowed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basic_food_units');
    }
};
