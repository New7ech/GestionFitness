<?php

namespace Database\Factories;

use App\Models\Fournisseur;
use Illuminate\Database\Eloquent\Factories\Factory;

class FournisseurFactory extends Factory
{
    protected $model = Fournisseur::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'nom_entreprise' => $this->faker->company(),
            'adresse' => $this->faker->address(),
            'telephone' => $this->faker->unique()->numerify('+###########'),
            'email' => $this->faker->unique()->safeEmail(),
            'ville' => $this->faker->city(),
            'pays' => $this->faker->country(),
        ];
    }
}

