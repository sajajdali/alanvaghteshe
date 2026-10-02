<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diet_plan_suggestions', function (Blueprint $table) {
            $table->unsignedTinyInteger('weight_loss_medication')->nullable()->after('food_budget');
            $table->index(
                ['food_budget', 'weight_loss_medication', 'session_from', 'session_to'],
                'diet_suggestion_conditions_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('diet_plan_suggestions', function (Blueprint $table) {
            $table->dropIndex('diet_suggestion_conditions_idx');
            $table->dropColumn('weight_loss_medication');
        });
    }
};
