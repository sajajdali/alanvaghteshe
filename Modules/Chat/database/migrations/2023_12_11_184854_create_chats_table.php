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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            //user_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('status')->default(0);
            $table->integer('new_message_by_user')->default(0);
            $table->integer('new_message_by_support')->default(0);
            $table->boolean('ban')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
