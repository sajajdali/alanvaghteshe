<?php

namespace Modules\Exercise\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Exercise\Entities\ExercisePlanStrategy;
use Modules\Exercise\Enum\ExercisePlanStrategyGenderEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyLevelEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyTargetEnum;

class ExercisePlanStrategyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        ExercisePlanStrategy::create([
            'name' => 'مرد آماتور سه جلسه',
            'session_count' => 3,
            'target' => ExercisePlanStrategyTargetEnum::TARGET_DECREASE_WEIGHT,
            'gender' => ExercisePlanStrategyGenderEnum::MALE,
            'level' => ExercisePlanStrategyLevelEnum::BEGINNER,
            'status' => true
            ]);

        ExercisePlanStrategy::create([
            'name' => 'زن آماتور سه جلسه',
            'session_count' => 3,
            'target' => ExercisePlanStrategyTargetEnum::TARGET_DECREASE_WEIGHT,
            'gender' => ExercisePlanStrategyGenderEnum::FEMALE,
            'level' => ExercisePlanStrategyLevelEnum::BEGINNER,
            'status' => true
        ]);
    }
}
