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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->char('code' , 20)->unique();
            $table->bigInteger('value');
            $table->tinyInteger('is_percent')->default(0)->comment('0 => fixed , 1=> perecent');
            $table->integer('usable_count')->nullable();
            $table->integer('minimum_spend')->nullable();
            $table->integer('maximum_spend')->nullable();
            $table->smallInteger('used')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('can_used_for')->default(30)->comment("diet = 10 | exercise = 20 |diet and  exercise = 30");
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('coupons');
    }
};
