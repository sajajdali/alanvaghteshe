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
    public function up()
    {
        Schema::table('foods_related_pivot', function (Blueprint $table) {
            $table->tinyInteger('type')->default(1)->comment('1 = Selectable for replacement | 2 = selected for the main meal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('food_related', function (Blueprint $table) {
            $table->removeColumn('type');
        });
    }
};
