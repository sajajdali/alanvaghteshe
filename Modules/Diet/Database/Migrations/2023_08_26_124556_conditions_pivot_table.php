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
        Schema::create('conditions_pivot', function (Blueprint $table) {
            $table->foreignId('condition_id')->constrained('conditions')
                ->onUpdate('no action')
                ->onDelete('cascade');
            $table->morphs('conditionable');
            $table->json('options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::dropIfExists('conditions_pivot');
    }
};
