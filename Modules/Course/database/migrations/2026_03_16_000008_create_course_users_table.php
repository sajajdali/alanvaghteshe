<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Modules\User\Entities\User::class)->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(\Modules\Course\app\Models\Course::class)->constrained('courses')->cascadeOnDelete();
            $table->foreignIdFor(\Modules\Transaction\Entities\Transaction::class)->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignIdFor(\Modules\User\Entities\User::class, 'assigned_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->tinyInteger('paid_by')->comment('1 = online | 2 = card to card | 3 = by admin | 4 = by bazar');
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->json('detail')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_users');
    }
};
