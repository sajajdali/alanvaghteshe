<?php

namespace Modules\Exercise\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ExerciseBodyCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $bodyCategories = [
            'پا' => [
                'جلو پا',
                'ساق'
            ],
            'سینه' => [
                'بالا سینه'
            ],
            'سرشانه' => [
                'جلو سرشانه',
                'سرشانه میانی',
                'پشت سرشانه',
                'کول سرشانه'
            ],
            'زیر بغل'=>[],
            'شکم' => [] ,
            'پشت بازو' => [
                'پشت بازو داخلی',
                'پشت بازو خارجی',
            ],
            'جلو بازو' => [
                'جلو بازو داخلی',
                'جلو بازو خارجی',
            ],
        ];

        foreach ($bodyCategories as $parent => $children) {
            $parent = \Modules\Exercise\Entities\ExerciseBodyCategory::create([
                'name' => $parent
            ]);

            if (is_array($children) && count($children) > 0) {
                foreach ($children as $child) {
                    $parent->children()->create([
                        'name' => $child
                    ]);
                }
            }
        }
    }
}
