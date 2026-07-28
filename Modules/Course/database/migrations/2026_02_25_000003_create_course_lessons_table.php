<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Course\app\Models\CourseSection;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CourseSection::class)->constrained('course_sections')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('content_type', 20)->default('video');
            $table->string('video_url')->nullable();
            $table->string('document_url')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->tinyInteger('is_preview')->default(0);
            $table->unsignedInteger('sort_order')->default(1);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};
