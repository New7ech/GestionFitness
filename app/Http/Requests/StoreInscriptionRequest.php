<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create-inscriptions') ?? false;
    }

    public function rules(): array
    {
        return [
            'participante_id' => ['required', 'exists:participantes,id'],
            'challenge_id' => ['required', 'exists:challenges,id'],
            'inscription_date' => ['required', 'date'],
            'goal_text' => ['nullable', 'string'],
            'goal_weight' => ['nullable', 'numeric', 'min:0.01'],
            'goal_waist' => ['nullable', 'numeric', 'min:0.01'],
            'goal_personal' => ['nullable', 'string'],
            'observations' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0.01'],
            'confirm_full_challenge' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'participante_id.required' => 'La participante est obligatoire.',
            'participante_id.exists' => 'La participante sélectionnée est invalide.',
            'challenge_id.required' => 'Le challenge est obligatoire.',
            'challenge_id.exists' => 'Le challenge sélectionné est invalide.',
            'inscription_date.required' => 'La date d\'inscription est obligatoire.',
            'inscription_date.date' => 'La date d\'inscription doit être une date valide.',
            'goal_weight.numeric' => 'Le poids objectif doit être un nombre.',
            'goal_weight.min' => 'Le poids objectif doit être positif.',
            'goal_waist.numeric' => 'Le tour de taille objectif doit être un nombre.',
            'goal_waist.min' => 'Le tour de taille objectif doit être positif.',
            'price.numeric' => 'Le tarif doit être un nombre.',
            'price.min' => 'Le tarif doit être positif.',
        ];
    }
}
