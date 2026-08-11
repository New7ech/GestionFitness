<?php

namespace Database\Factories;

use App\Models\Emplacement;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmplacementFactory extends Factory
{
    protected $model = Emplacement::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->bothify('Zone-###-??'),
            'description' => $this->faker->sentence(),
        ];
    }
}

