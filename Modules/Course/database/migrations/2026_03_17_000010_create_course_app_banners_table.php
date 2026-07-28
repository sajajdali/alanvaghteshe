<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_app_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignIdFor(\Modules\Course\app\Models\Course::class)->constrained('courses')->cascadeOnDelete();
            $table->string('position', 20)->comment('top | middle');
            $table->unsignedInteger('sort_order')->default(1);
            $table->unsignedInteger('priority')->default(1);
            $table->string('image');
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            $table->index(['position', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_app_banners');
    }
};
