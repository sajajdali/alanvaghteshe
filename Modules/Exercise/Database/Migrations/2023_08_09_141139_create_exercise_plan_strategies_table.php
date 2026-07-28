<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() : void
    {
        Schema::create('exercise_plan_strategies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('session_count')->default(3);
            $table->integer('target')->default(1);
            $table->integer('gender')->default(0);
            $table->integer('level')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::dropIfExists('exercise_plan_strategies');
    }
};
