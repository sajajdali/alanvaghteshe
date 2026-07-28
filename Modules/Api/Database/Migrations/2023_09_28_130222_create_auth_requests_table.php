<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('auth_requests', function (Blueprint $table) {
            $table->id();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->char('code', 8)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('request_count')->default(1);
            $table->timestamp('expire_at')->nullable();
            $table->timestamp('next_request_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_requests');
    }
};
