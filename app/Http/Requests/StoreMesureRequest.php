<?php

namespace App\Http\Requests;

use App\Enums\MeasurementStage;
use App\Models\MeasurementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMesureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('record-measurements') ?? false;
    }

    public function rules(): array
    {
        return [
            'challenge_id' => ['required', 'exists:challenges,id'],
            'measured_at' => ['required', 'date'],
            'stage' => ['required', Rule::enum(MeasurementStage::class)],
            'weight' => ['required', 'numeric', 'min:0.01'],
            'waist' => ['nullable', 'numeric', 'min:0.01'],
            'comment' => ['nullable', 'string'],
            'measurement_values' => ['nullable', 'array'],
            'measurement_values.*' => ['nullable', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'challenge_id.required' => 'Le challenge est obligatoire.',
            'challenge_id.exists' => 'Le challenge sélectionné est invalide.',
            'measured_at.required' => 'La date de mesure est obligatoire.',
            'measured_at.date' => 'La date de mesure doit être une date valide.',
            'stage.required' => 'L’étape de mesure est obligatoire.',
            'stage' => 'L’étape sélectionnée est invalide.',
            'weight.required' => 'Le poids est obligatoire.',
            'weight.numeric' => 'Le poids doit être un nombre.',
            'weight.min' => 'Le poids doit être strictement positif.',
            'waist.numeric' => 'Le tour de taille doit être un nombre.',
            'waist.min' => 'Le tour de taille doit être strictement positif.',
            'measurement_values.array' => 'Les mesures complémentaires sont invalides.',
            'measurement_values.*.numeric' => 'Chaque mesure complémentaire doit être un nombre.',
            'measurement_values.*.min' => 'Chaque mesure complémentaire doit être strictement positive.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $values = $this->input('measurement_values', []);

            if (! is_array($values) || $values === []) {
                return;
            }

            $ids = collect(array_keys($values))
                ->filter(fn ($id): bool => ctype_digit((string) $id))
                ->map(fn ($id): int => (int) $id)
                ->values();

            if ($ids->count() !== count($values)) {
                $validator->errors()->add('measurement_values', 'Une mesure complémentaire est invalide.');

                return;
            }

            $activeIds = MeasurementType::query()
                ->where('is_active', true)
                ->whereIn('id', $ids)
                ->pluck('id')
                ->map(fn ($id): int => (int) $id);

            if ($ids->diff($activeIds)->isNotEmpty()) {
                $validator->errors()->add('measurement_values', 'Une mesure complémentaire sélectionnée est invalide.');
            }
        });
    }
}
