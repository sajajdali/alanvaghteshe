<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diet_plan_suggestions', function (Blueprint $table) {
            $table->dropIndex('diet_suggestion_target_conditions_idx');
            $table->unsignedTinyInteger('athlete_or_not')->nullable()->after('weight_loss_medication');
            $table->index(
                ['food_budget', 'diet_type', 'weight_loss_medication', 'athlete_or_not', 'session_from', 'session_to'],
                'diet_suggestion_conditions_lookup_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('diet_plan_suggestions', function (Blueprint $table) {
            $table->dropIndex('diet_suggestion_conditions_lookup_idx');
            $table->dropColumn('athlete_or_not');
            $table->index(
                ['food_budget', 'diet_type', 'weight_loss_medication', 'session_from', 'session_to'],
                'diet_suggestion_target_conditions_idx'
            );
        });
    }
};
