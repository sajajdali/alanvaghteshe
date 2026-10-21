<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diet_plan_suggestions', function (Blueprint $table) {
            $table->unsignedTinyInteger('diet_type')->nullable()->after('food_budget');
            $table->index(
                ['food_budget', 'diet_type', 'weight_loss_medication', 'session_from', 'session_to'],
                'diet_suggestion_target_conditions_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('diet_plan_suggestions', function (Blueprint $table) {
            $table->dropIndex('diet_suggestion_target_conditions_idx');
            $table->dropColumn('diet_type');
        });
    }
};
