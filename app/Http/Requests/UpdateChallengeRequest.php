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
            'participante_id' => ['required', 'exists:participantes,id'],
            'challenge_type_id' => ['required', 'exists:challenge_types,id'],
            'start_date' => ['required', 'date'],
            'duration_days' => ['required', 'integer', Rule::in(config('fitness.durations', [15, 30]))],
            'status' => ['required', Rule::enum(ChallengeStatus::class)],
            'goal_text' => ['nullable', 'string'],
            'goal_weight' => ['nullable', 'numeric', 'min:0.01'],
            'goal_waist' => ['nullable', 'numeric', 'min:0.01'],
            'goal_personal' => ['nullable', 'string'],
            'observations' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'confirm_schedule_change' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'participante_id.required' => 'La participante est obligatoire.',
            'participante_id.exists' => 'La participante sélectionnée est invalide.',
            'challenge_type_id.required' => 'Le type de challenge est obligatoire.',
            'challenge_type_id.exists' => 'Le type de challenge sélectionné est invalide.',
            'start_date.required' => 'La date de début est obligatoire.',
            'start_date.date' => 'La date de début doit être une date valide.',
            'duration_days.required' => 'La durée est obligatoire.',
            'duration_days.integer' => 'La durée doit être un nombre entier.',
            'duration_days.in' => 'La durée sélectionnée est invalide.',
            'status.required' => 'Le statut est obligatoire.',
            'status' => 'Le statut sélectionné est invalide.',
            'goal_weight.numeric' => 'Le poids objectif doit être un nombre.',
            'goal_weight.min' => 'Le poids objectif doit être positif.',
            'goal_waist.numeric' => 'Le tour de taille objectif doit être un nombre.',
            'goal_waist.min' => 'Le tour de taille objectif doit être positif.',
            'price.required' => 'Le prix est obligatoire.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix doit être positif.',
        ];
    }
}
