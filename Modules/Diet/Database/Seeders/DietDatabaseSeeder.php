<?php

namespace Modules\Diet\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DietDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(FoodUnitTableSeeder::class);
        $this->call(ConditionTableSeeder::class);
    }
}
