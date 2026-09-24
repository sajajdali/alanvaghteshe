<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diet_shopping_lists', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('diet_request_id')->constrained('diet_requests')->cascadeOnDelete();
            $table->string('period', 16);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 16)->default('pending')->index();
            $table->string('input_hash', 64);
            $table->json('result')->nullable();
            $table->string('model')->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'diet_request_id']);
            $table->unique(
                ['diet_request_id', 'period', 'input_hash'],
                'diet_shopping_lists_diet_period_hash_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_shopping_lists');
    }
};
