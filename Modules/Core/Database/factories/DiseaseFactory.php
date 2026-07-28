<?php

namespace Modules\Core\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DiseaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Core\Entities\Disease::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }
}

