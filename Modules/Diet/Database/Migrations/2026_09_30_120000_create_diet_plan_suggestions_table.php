<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diet_plan_suggestions', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('food_budget');
            $table->unsignedInteger('session_from');
            $table->unsignedInteger('session_to');
            $table->foreignId('diet_plan_id')->constrained('diet_plans')->cascadeOnDelete();
            $table->timestamps();
            $table->index(['food_budget', 'session_from', 'session_to'], 'diet_suggestion_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_plan_suggestions');
    }
};
