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
        Schema::create('food_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_id')->nullable()->constrained("meals")->nullOnDelete();
            $table->foreignId('food_unit_id')->nullable()->constrained("food_units")->nullOnDelete();
            $table->float('quantity')->default(1);  // Quantity of food consumed
            $table->morphs('consumable');  // This creates consumable_id and consumable_type columns
            $table->float('calories')->default(0);
            $table->float('protein')->default(0);
            $table->float('carb')->default(0);
            $table->float('fat')->default(0);
            $table->float('fiber')->default(0);
            $table->timestamp('consumed_at')->useCurrent();  // When the food was consumed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_consumptions');
    }
};
