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
        Schema::create('package_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Modules\User\Entities\User::class)->constrained('users')->cascadeOnDelete()->nullable();
            $table->foreignIdFor(\Modules\Package\Entities\Package::class)->constrained('packages')->cascadeOnDelete()->nullable();
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->tinyInteger('type')->default(0)->comment('0 = pending | 1 = in_use | 2 = end | 3 = cancel');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_users');
    }
};
