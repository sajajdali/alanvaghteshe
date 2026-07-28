<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->foreign('parent_id')->references('id')->on('foods')->onDelete('cascade');
            $table->float('max_carb')->default(0)->after('carb');
            $table->float('max_protein')->default(0)->after('protein');
            $table->float('max_fat')->default(0)->after('fat');
            $table->float('max_fiber')->default(0)->after('fiber');
            $table->integer('max_calories')->default(0)->after('calories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->dropColumn(['max_carb', 'max_protein', 'max_fat', 'max_fiber', 'max_calories']);
        });
    }
};
