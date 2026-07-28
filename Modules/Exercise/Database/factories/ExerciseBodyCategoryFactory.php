<?php

namespace Modules\Exercise\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseBodyCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Exercise\Entities\ExerciseBodyCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() : array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}

