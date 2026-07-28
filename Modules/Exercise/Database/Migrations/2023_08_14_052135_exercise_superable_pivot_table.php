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
        Schema::create('exercises_superable_pivot', function (Blueprint $table) {
            $table->foreignId('exercise_id')->constrained('exercises')
                ->onUpdate('no action')
                ->onDelete('cascade');
            $table->foreignId('super_exercise_id')->constrained('exercises')
                ->onUpdate('no action')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::dropIfExists('exercises_superable_pivot');
    }
};
