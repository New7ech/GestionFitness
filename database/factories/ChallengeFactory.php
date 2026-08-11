<?php

namespace Database\Factories;

use App\Enums\ChallengeStatus;
use App\Enums\PaymentStatus;
use App\Models\Challenge;
use App\Models\ChallengeType;
use App\Models\Participante;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChallengeFactory extends Factory
{
    protected $model = Challenge::class;

    public function definition(): array
    {
        $durations = config('fitness.durations', [15, 30]);

        return [
            'participante_id' => Participante::factory(),
            'challenge_type_id' => ChallengeType::factory(),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'duration_days' => $this->faker->randomElement($durations),
            'status' => ChallengeStatus::Planifie,
            'goal_text' => $this->faker->optional()->sentence(),
            'goal_weight' => $this->faker->optional()->randomFloat(2, 55, 95),
            'goal_waist' => $this->faker->optional()->randomFloat(2, 60, 120),
            'goal_personal' => $this->faker->optional()->sentence(),
            'observations' => $this->faker->optional()->sentence(),
            'price' => $this->faker->randomFloat(2, 5000, 50000),
            'payment_status' => PaymentStatus::Impaye,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
