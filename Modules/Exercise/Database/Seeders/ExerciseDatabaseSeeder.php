<?php

namespace Modules\Exercise\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ExerciseDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

         $this->call(ExerciseBodyCategoryTableSeeder::class);
         $this->call(ExerciseTableSeeder::class);
         $this->call(ExercisePlanStrategyTableSeeder::class);
        // $this->call("OthersTableSeeder");
    }
}
