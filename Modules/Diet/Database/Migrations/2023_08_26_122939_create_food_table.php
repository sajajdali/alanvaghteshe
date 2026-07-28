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
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('carb')->default(0);
            $table->float('protein')->default(0);
            $table->float('fat')->default(0);
            $table->float('fiber')->default(0);
            $table->integer('calories')->default(0);
            $table->tinyInteger('type')->unsigned()->default(10);
            $table->integer('max_per_unit')->nullable();
            $table->json('detail')->nullable();
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
        Schema::dropIfExists('foods');
    }
};
