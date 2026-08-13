<?php

namespace Database\Factories;

use App\Enums\PaymentMode;
use App\Enums\PaymentType;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaiementFactory extends Factory
{
    protected $model = Paiement::class;

    public function definition(): array
    {
        return [
            'inscription_id' => Inscription::factory(),
            'amount' => $this->faker->randomFloat(2, 5000, 50000),
            'type' => PaymentType::Paiement,
            'payment_date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'payment_mode' => $this->faker->randomElement(PaymentMode::cases()),
            'comment' => $this->faker->optional()->sentence(),
            'recorded_by' => User::factory(),
        ];
    }
}
