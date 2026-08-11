<?php

namespace Database\Factories;

use App\Enums\ParticipantStatus;
use App\Models\Participante;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipanteFactory extends Factory
{
    protected $model = Participante::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstNameFemale(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'email' => $this->faker->boolean(80) ? $this->faker->unique()->safeEmail() : null,
            'address' => $this->faker->optional()->address(),
            'photo_path' => null,
            'birthdate' => $this->faker->optional()->dateTimeBetween('-55 years', '-18 years')?->format('Y-m-d'),
            'status' => ParticipantStatus::Active,
            'has_cesarean' => $this->faker->optional()->boolean(),
            'cesarean_comment' => $this->faker->optional()->sentence(),
            'health_notes' => $this->faker->optional()->sentence(),
            'registration_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
