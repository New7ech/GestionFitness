<?php

namespace Database\Seeders;

use App\Models\ChallengeType;
use App\Models\MeasurementType;
use Illuminate\Database\Seeder;

class FitnessReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $challengeTypes = [
            [
                'code' => 'perte_de_poids',
                'label' => 'Perte de poids',
                'description' => 'Challenge dédié à la perte de poids.',
            ],
            [
                'code' => 'diastasis',
                'label' => 'Rééducation de la diastasie',
                'description' => 'Programme de suivi pour la diastasie.',
            ],
        ];

        foreach ($challengeTypes as $challengeType) {
            ChallengeType::query()->firstOrCreate(
                ['code' => $challengeType['code']],
                $challengeType + ['is_active' => true]
            );
        }

        $measurementTypes = [
            ['code' => 'hanches', 'label' => 'Hanches', 'unit' => 'cm'],
            ['code' => 'cuisse', 'label' => 'Cuisse', 'unit' => 'cm'],
            ['code' => 'bras', 'label' => 'Bras', 'unit' => 'cm'],
            ['code' => 'poitrine', 'label' => 'Poitrine', 'unit' => 'cm'],
        ];

        foreach ($measurementTypes as $measurementType) {
            MeasurementType::query()->firstOrCreate(
                ['code' => $measurementType['code']],
                $measurementType + ['is_active' => true]
            );
        }
    }
}
