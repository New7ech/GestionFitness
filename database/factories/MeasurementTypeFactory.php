<?php

namespace Database\Factories;

use App\Models\MeasurementType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MeasurementTypeFactory extends Factory
{
    protected $model = MeasurementType::class;

    public function definition(): array
    {
        $label = $this->faker->unique()->word();

        return [
            'code' => Str::slug($label, '_'),
            'label' => Str::ucfirst($label),
            'unit' => 'cm',
            'is_active' => true,
        ];
    }
}
