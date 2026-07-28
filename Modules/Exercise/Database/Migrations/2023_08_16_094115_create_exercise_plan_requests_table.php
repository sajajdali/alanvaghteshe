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
        Schema::create('exercise_plan_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('no action')->onDelete('set null');
            $table->foreignId('exercise_plan_strategy_id')->nullable()->constrained('exercise_plan_strategies','id','jsmno_epsepr2_id_fk')->onUpdate('no action')->onDelete('set null');
            $table->integer('session_count')->default(3);
            $table->integer('target')->default(1);
            $table->integer('gender')->default(0);
            $table->integer('level')->default(0);
            $table->integer('status')->default(0);
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->string('message')->nullable();
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
        Schema::dropIfExists('exercise_plan_requests');
    }
};
