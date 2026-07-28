<?php

namespace Modules\Chat\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Chat\app\Models\Chat::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

