<?php

namespace Database\Factories;

use App\Models\ChallengeType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ChallengeTypeFactory extends Factory
{
    protected $model = ChallengeType::class;

    public function definition(): array
    {
        $label = $this->faker->unique()->words(3, true);

        return [
            'code' => Str::slug($label, '_'),
            'label' => Str::title($label),
            'description' => $this->faker->optional()->sentence(),
            'default_price' => $this->faker->optional()->randomFloat(2, 5000, 50000),
            'is_active' => true,
        ];
    }
}
