<?php

namespace Modules\Diet\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Diet\Enum\ConditionApplyItemEnum;
use Modules\Diet\Enum\ConditionKeyEnum;

class ConditionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $cnditions = [
            [
                'name' => 'فصل',
                'key' => ConditionKeyEnum::SEASON,
                'options' => [
                    'تابستان',
                    'پاییز',
                    'زمستان',
                    'بهار',
                ],
                'apply_to' => ConditionApplyItemEnum::make(
                    food: true,
                    diet_plan: true,
                    diet_request: true,
                ),
            ],
            [
                'name' => 'نوع رژیم',
                'key' => ConditionKeyEnum::DIET_TYPE,
                'options' => [
                    'کاهش وزن',
                    'افزایش وزن',
                    'تثبیت وزن',
                ],
                'apply_to' => ConditionApplyItemEnum::make(
                    diet_plan: true,
                    diet_request: true,
                ),
            ],
            [
                'name' => 'شرایط غذایی',
                'key' => ConditionKeyEnum::FOOD_CONDITION,
                'options' => [
                    'وگن',
                    'حساسیت به لاکتوز',
                    'وجترین'
                ],
                'apply_to' => ConditionApplyItemEnum::make(
                    food: true,
                    diet_plan: true,
                    diet_request: true,
                ),
            ],
            [],
        ];
    }
}
