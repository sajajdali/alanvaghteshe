<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basic_foods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('type')->unsigned()->default(10);
            $table->tinyInteger('active')->default(1);
            $table->text('recipe')->nullable();
            $table->json('food_fact')->nullable();
            $table->json('detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('basic_foods');
    }
};
