<?php

namespace Database\Factories;

use App\Enums\InscriptionStatus;
use App\Enums\PaymentStatus;
use App\Models\Challenge;
use App\Models\Inscription;
use App\Models\Participante;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InscriptionFactory extends Factory
{
    protected $model = Inscription::class;

    public function definition(): array
    {
        return [
            'participante_id' => Participante::factory(),
            'challenge_id' => Challenge::factory(),
            'inscription_date' => now()->toDateString(),
            'status' => InscriptionStatus::Reservee,
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
