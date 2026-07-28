<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Modules\Course\app\Models\CourseUser::class)->constrained('course_users')->cascadeOnDelete();
            $table->foreignIdFor(\Modules\User\Entities\User::class)->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(\Modules\Course\app\Models\Course::class)->constrained('courses')->cascadeOnDelete();
            $table->foreignIdFor(\Modules\Course\app\Models\CourseLesson::class)->constrained('course_lessons')->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['course_user_id', 'course_lesson_id'], 'course_lesson_progress_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lesson_progress');
    }
};
