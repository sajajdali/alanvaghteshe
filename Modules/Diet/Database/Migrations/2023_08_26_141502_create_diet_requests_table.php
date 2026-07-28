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
        Schema::create('diet_requests', function (Blueprint $table) {
            $table->id();
            //connect to users table
            $table->foreignId('user_id')->nullable()->constrained('users')
                ->onUpdate('no action')
                ->onDelete('set null');
            //connect to diet_plans table
            $table->foreignId('diet_plan_id')->nullable()->constrained('diet_plans')
                ->onUpdate('no action')
                ->onDelete('set null');
            $table->integer('status')->default(10);
            $table->integer('calories')->default(0);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->tinyInteger('active')->default('1');
            $table->json('user_information');
            $table->json('detail')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::dropIfExists('diet_requests');
    }
};
