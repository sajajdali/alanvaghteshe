<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_queues', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reminder_id')->constrained()->onDelete('cascade');

            $table->unsignedTinyInteger('type')->comment('ReminderTypeEnum');
            $table->unsignedTinyInteger('session_number')->nullable()->comment('Optional: for diet/exercise sessions');
            $table->dateTime('send_at');
            $table->enum('status', ['pending', 'sent', 'cancelled'])->default('pending');

            $table->json('detail')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['status', 'send_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_queues');
    }
};
