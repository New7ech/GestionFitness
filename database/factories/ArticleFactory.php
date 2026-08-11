<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Models\Emplacement;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true); // Ensure name is unique for unique slug generation
        $prix = $this->faker->randomFloat(2, 10, 1000);
        // Ensure promo price is less than actual price, and handle cases where price is too low for a promo
        $prix_promotionnel = null;
        if ($prix > 5) { // Only set promo if price is high enough to have a promo
            $prix_promotionnel = $this->faker->optional(0.3) // 30% chance of having a promotional price
                                ->randomFloat(2, 5, $prix - 1);
        }


        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'est_visible' => $this->faker->boolean(90), // 90% chance of being visible
            'description' => $this->faker->sentence,
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####??')), // Generates unique SKU like SKU-1234AB
            'image_principale' => null, // Les images seront gérées par UploadedFile::fake() dans les tests
            'prix' => $prix,
            'prix_promotionnel' => $prix_promotionnel,
            'quantite' => $this->faker->numberBetween(0, 100), // Quantité peut être 0
            'statut' => $this->faker->randomElement(['disponible', 'brouillon', 'archivé', 'en_rupture_de_stock']),
            'poids' => $this->faker->optional(0.7)->randomFloat(3, 0.1, 5), // 70% chance of having weight, between 0.1kg and 5kg
            'category_id' => Categorie::factory(),
            'fournisseur_id' => Fournisseur::factory(),
            'emplacement_id' => Emplacement::factory(),
            'created_by' => User::factory(),
        ];
    }
}
