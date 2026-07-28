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
        Schema::create('user_supports', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')
                ->onUpdate('no action')
                ->onDelete('cascade');
            $table->foreignId('support_id')->constrained('users')
                ->onUpdate('no action')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_supports');
    }
};
