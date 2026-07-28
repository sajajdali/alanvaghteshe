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
        Schema::create('recipe_units', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Modules\Recipe\app\Models\Recipe::class)->constrained()->cascadeOnDelete()->nullable();
            $table->foreignIdFor(\Modules\Diet\Entities\FoodUnit::class)->constrained()->cascadeOnDelete()->nullable();
            $table->float('quantity_per_unit')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recip_units');
    }
};
