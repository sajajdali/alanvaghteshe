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
        Schema::create('water_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Modules\User\Entities\User::class)->constrained()->cascadeOnDelete();
            $table->float('quantity')->default(1);  // Quantity of food consumed
            $table->timestamp('consumed_at')->useCurrent();  // When the food was consumed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_consumptions');
    }
};
