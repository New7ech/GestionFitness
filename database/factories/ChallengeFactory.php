<?php

namespace Database\Factories;

use App\Enums\ChallengeStatus;
use App\Models\Challenge;
use App\Models\ChallengeType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChallengeFactory extends Factory
{
    protected $model = Challenge::class;

    public function definition(): array
    {
        $durations = config('fitness.durations', [15, 30]);

        return [
            'challenge_type_id' => ChallengeType::factory(),
            'label' => $this->faker->optional()->sentence(3),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'duration_days' => $this->faker->randomElement($durations),
            'capacite' => $this->faker->optional()->numberBetween(5, 20),
            'default_price' => $this->faker->randomFloat(2, 5000, 50000),
            'status' => ChallengeStatus::Planifie,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
