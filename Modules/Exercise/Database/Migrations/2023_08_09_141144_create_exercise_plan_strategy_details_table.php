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
        Schema::create('exercise_plan_strategy_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_plan_strategy_id')->constrained('exercise_plan_strategies','id','jsmno_epsdeps_id_fk')
                ->onUpdate('no action')
                ->onDelete('cascade');
            //morph to exercise or exercise category
            $table->morphs('exerciseable', 'jsmno_exerciseable');
            $table->unsignedBigInteger('super_set_id')->nullable();
            $table->tinyInteger('set_type')->default(1);
            $table->tinyInteger('type')->default(0);
            $table->integer('day')->default(1);
            $table->json('reps')->nullable();
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
        Schema::dropIfExists('exercise_plan_strategy_details');
    }
};
