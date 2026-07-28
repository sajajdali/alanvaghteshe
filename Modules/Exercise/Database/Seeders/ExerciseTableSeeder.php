<?php

namespace Modules\Exercise\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Exercise\Entities\Exercise;
use Modules\Exercise\Enum\ExerciseConditionEnum;

class ExerciseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //chest
        $chestBodyCategory = \Modules\Exercise\Entities\ExerciseBodyCategory::where('name', 'سینه')->first();
        $topChest = \Modules\Exercise\Entities\ExerciseBodyCategory::where('name', 'بالا سینه')->first();
        $crossOver = Exercise::create([
                'name' => 'کراس اور',
                'conditions' => ExerciseConditionEnum::make(
                    isForMen: true,
                    isForWomen: true,
                    isForBeginners: true,
                    isForAdvanced: true,
                    isExerciseComplementary: true
                )
            ]
        );
        $chestBodyCategory->exercises()->attach($crossOver);
        $smeet = Exercise::create([
                'name' => 'بالاسینه اسمیت',
                'conditions' => ExerciseConditionEnum::make(
                    isForMen: true,
                    isForWomen: true,
                    isForBeginners: true,
                    isForAdvanced: true,
                    isExerciseBased: true
                )
            ]
        );
        $topChest->exercises()->attach($smeet);

    }
}
