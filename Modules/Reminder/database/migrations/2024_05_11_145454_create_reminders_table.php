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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->integer('send_for')->nullable();
            $table->tinyInteger('status')->default(1)->comment("1 = sms | 2 = notification");
            $table->text('body');
            $table->smallInteger('session_number')->nullable();
            $table->json('parameters')->nullable();
            $table->text('send_day')->nullable();
            $table->integer('send_time')->nullable();
            $table->tinyInteger('active');
            $table->json('detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
