<?php

namespace Modules\Diet\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class FoodUnitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //create some units
        $unitNames = [
            'قاشق',
            'گرم',
            'عدد',
            'لیوان',
            'دانه'
        ];

        foreach ($unitNames as $unitName) {
            \Modules\Diet\Entities\FoodUnit::create([
                'name' => $unitName
            ]);
        }
    }
}
