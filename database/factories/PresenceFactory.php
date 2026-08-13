<?php

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Models\Inscription;
use App\Models\Presence;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PresenceFactory extends Factory
{
    protected $model = Presence::class;

    public function definition(): array
    {
        return [
            'inscription_id' => Inscription::factory(),
            'attendance_date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(AttendanceStatus::cases()),
            'comment' => $this->faker->optional()->sentence(),
            'recorded_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
