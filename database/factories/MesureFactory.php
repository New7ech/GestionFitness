<?php

namespace Database\Factories;

use App\Enums\MeasurementStage;
use App\Models\Inscription;
use App\Models\Mesure;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MesureFactory extends Factory
{
    protected $model = Mesure::class;

    public function definition(): array
    {
        return [
            'inscription_id' => Inscription::factory(),
            'measured_at' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'stage' => $this->faker->randomElement(MeasurementStage::cases()),
            'weight' => $this->faker->randomFloat(2, 50, 120),
            'waist' => $this->faker->optional()->randomFloat(2, 60, 140),
            'comment' => $this->faker->optional()->sentence(),
            'recorded_by' => User::factory(),
        ];
    }
}
