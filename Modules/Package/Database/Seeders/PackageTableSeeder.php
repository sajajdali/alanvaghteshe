<?php

namespace Modules\Package\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\Enum\PackageTypeEnum;

class PackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'بسته یک ماهه رژیم',
                'days' => 30,
                'price' => 100000,
                'special_price' => 50000,
                'type' => PackageTypeEnum::DIET,
            ],
            [
                'name' => 'بسته سه ماهه رژیم',
                'days' => 90,
                'price' => 300000,
                'special_price' => 150000,
                'type' => PackageTypeEnum::DIET,
            ],
            [
                'name' => 'بسته شش ماهه رژیم',
                'days' => 180,
                'price' => 600000,
                'special_price' => 300000,
                'type' => PackageTypeEnum::DIET,
            ],
            [
                'name' => 'بسته یک ماهه ورزشی',
                'days' => 30,
                'price' => 100000,
                'special_price' => 50000,
                'type' => PackageTypeEnum::EXERCISE,
            ],
            [
                'name' => 'بسته سه ماهه ورزشی',
                'days' => 90,
                'price' => 300000,
                'special_price' => 150000,
                'type' => PackageTypeEnum::EXERCISE,
            ],
            [
                'name' => 'بسته شش ماهه ورزشی',
                'days' => 180,
                'price' => 600000,
                'special_price' => 300000,
                'type' => PackageTypeEnum::EXERCISE,
            ],
            [
                'name' => 'بسته یک ماهه کامل',
                'days' => 30,
                'price' => 100000,
                'special_price' => 50000,
                'type' => PackageTypeEnum::DIET_AND_EXERCISE,
            ],
            [
                'name' => 'بسته سه ماهه کامل',
                'days' => 90,
                'price' => 300000,
                'special_price' => 150000,
                'type' => PackageTypeEnum::DIET_AND_EXERCISE,
            ],
            [
                'name' => 'بسته شش ماهه کامل',
                'days' => 180,
                'price' => 600000,
                'special_price' => 300000,
                'type' => PackageTypeEnum::DIET_AND_EXERCISE,
            ]
        ];

        foreach ($packages as $index => $package) {
            \Modules\Package\Entities\Package::create(
                [
                    'name' => $package['name'],
                    'days' => $package['days'],
                    'price' => $package['price'],
                    'special_price' => $package['special_price'],
                    'type' => $package['type'],
                    'priority' => $index * 10,
                ]
            );
        }
    }
}
