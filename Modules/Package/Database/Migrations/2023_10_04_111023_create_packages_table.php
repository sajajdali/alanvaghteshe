<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('days')->default(30);
            $table->integer('price')->default(0);
            $table->integer('special_price')->default(0);
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('type')->default(10);
            $table->integer('priority')->default(0);
            $table->json('detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
