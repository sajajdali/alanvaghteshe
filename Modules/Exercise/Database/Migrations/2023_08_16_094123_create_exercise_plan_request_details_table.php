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
        Schema::create('exercise_plan_request_details', function (Blueprint $table) {
            $table->id();
            //request number
            $table->foreignId('exercise_plan_request_id')->constrained('exercise_plan_requests','id','jsmno_eprdepr3_id_fk')->onUpdate('no action')->cascadeOnDelete();
            //exercise part
            $table->foreignId('exercise_id')->nullable()->constrained('exercises','id','jsmno_eprdepr4_id_fk')->onUpdate('no action')->nullOnDelete();
            $table->string('exercise_name')->nullable();
            //exercise body category
            $table->foreignId('exercise_body_category_id')->nullable()->constrained('exercise_body_categories','id','jsmno_eprdepr5_id_fk')->onUpdate('no action')->nullOnDelete();

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
        Schema::dropIfExists('exercise_plan_request_details');
    }
};
