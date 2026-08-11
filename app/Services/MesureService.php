<?php

namespace App\Services;

use App\Models\MeasurementType;
use App\Models\Mesure;
use Illuminate\Support\Facades\DB;

class MesureService
{
    public function create(array $data, int $recordedBy): Mesure
    {
        return DB::transaction(function () use ($data, $recordedBy): Mesure {
            $values = $data['measurement_values'] ?? [];
            unset($data['measurement_values']);

            $data['recorded_by'] = $recordedBy;

            $mesure = Mesure::query()->create($data);
            $this->storeValues($mesure, is_array($values) ? $values : []);

            return $mesure->load(['challenge.participante', 'challenge.challengeType', 'values.measurementType']);
        });
    }

    private function storeValues(Mesure $mesure, array $values): void
    {
        if ($values === []) {
            return;
        }

        $activeTypeIds = MeasurementType::query()
            ->where('is_active', true)
            ->whereIn('id', array_keys($values))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        foreach ($values as $typeId => $value) {
            if ($value === null || $value === '' || ! in_array((int) $typeId, $activeTypeIds, true)) {
                continue;
            }

            $mesure->values()->create([
                'measurement_type_id' => (int) $typeId,
                'value' => $value,
            ]);
        }
    }
}
