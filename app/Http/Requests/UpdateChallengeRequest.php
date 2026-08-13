<?php

namespace App\Http\Requests;

use App\Enums\ChallengeStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChallengeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit-challenges') ?? false;
    }

    public function rules(): array
    {
        return [
            'challenge_type_id' => ['required', 'exists:challenge_types,id'],
            'label' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'duration_days' => ['required', 'integer', Rule::in(config('fitness.durations', [15, 30]))],
            'capacite' => ['nullable', 'integer', 'min:1'],
            'default_price' => ['nullable', 'numeric', 'min:0.01'],
            'status' => ['required', Rule::enum(ChallengeStatus::class)],
            'confirm_schedule_change' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'challenge_type_id.required' => 'Le type de challenge est obligatoire.',
            'challenge_type_id.exists' => 'Le type de challenge sélectionné est invalide.',
            'start_date.required' => 'La date de début est obligatoire.',
            'start_date.date' => 'La date de début doit être une date valide.',
            'duration_days.required' => 'La durée est obligatoire.',
            'duration_days.integer' => 'La durée doit être un nombre entier.',
            'duration_days.in' => 'La durée sélectionnée est invalide.',
            'capacite.integer' => 'La capacité doit être un nombre entier.',
            'capacite.min' => 'La capacité doit être au moins de 1.',
            'default_price.numeric' => 'Le tarif par défaut doit être un nombre.',
            'default_price.min' => 'Le tarif par défaut doit être positif.',
            'status.required' => 'Le statut est obligatoire.',
            'status' => 'Le statut sélectionné est invalide.',
        ];
    }
}
