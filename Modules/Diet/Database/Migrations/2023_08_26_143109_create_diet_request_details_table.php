<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() : void
    {
        Schema::create('diet_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_request_id')->constrained('diet_requests')->onDelete('cascade')->onUpdate('no action');
            $table->foreignId('meal_id')->constrained('meals')->onDelete('no action')->onUpdate('no action');
            $table->morphs('foodable');
            $table->float('carb')->default(0);
            $table->float('protein')->default(0);
            $table->float('fat')->default(0);
            $table->float('fiber')->default(0);
            $table->integer('calories')->default(0);
            $table->integer('day_number')->default(0);
            $table->date('date_of_day')->nullable();
            $table->bigInteger('replaced_parent_id')->nullable();
            $table->tinyInteger('is_done')->default(0);
            $table->float('calculated_value')->nullable();
            $table->float('number_of_unit')->default(1);
            $table->tinyInteger('cheat_meal')->default(0);
            $table->json('detail')->nullable();
            $table->enum('main_nutrition',['all','carb','protein','fat','fiber','special'])->default('all');
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
        Schema::dropIfExists('diet_request_details');
    }
};
