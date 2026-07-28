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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('course_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description', 500)->nullable();
            $table->longText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('discounted_price')->nullable();
            $table->unsignedBigInteger('final_price')->default(0);
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->unsignedInteger('access_days')->default(0);
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->tinyInteger('level')->default(1)->comment('1 = easy | 2 = medium | 3 = professional');
            $table->tinyInteger('is_latest')->default(0);
            $table->unsignedInteger('latest_sort_order')->nullable();
            $table->tinyInteger('is_popular')->default(0);
            $table->unsignedInteger('popular_sort_order')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('is_published')->default(0);
            $table->tinyInteger('is_purchasable')->default(1);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
