<?php

namespace App\Http\Requests;

use App\Enums\InscriptionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit-inscriptions') ?? false;
    }

    public function rules(): array
    {
        return [
            'inscription_date' => ['required', 'date'],
            'status' => ['required', Rule::enum(InscriptionStatus::class)],
            'goal_text' => ['nullable', 'string'],
            'goal_weight' => ['nullable', 'numeric', 'min:0.01'],
            'goal_waist' => ['nullable', 'numeric', 'min:0.01'],
            'goal_personal' => ['nullable', 'string'],
            'observations' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'inscription_date.required' => 'La date d\'inscription est obligatoire.',
            'inscription_date.date' => 'La date d\'inscription doit être une date valide.',
            'status.required' => 'Le statut est obligatoire.',
            'status' => 'Le statut sélectionné est invalide.',
            'goal_weight.numeric' => 'Le poids objectif doit être un nombre.',
            'goal_weight.min' => 'Le poids objectif doit être positif.',
            'goal_waist.numeric' => 'Le tour de taille objectif doit être un nombre.',
            'goal_waist.min' => 'Le tour de taille objectif doit être positif.',
            'price.required' => 'Le tarif est obligatoire.',
            'price.numeric' => 'Le tarif doit être un nombre.',
            'price.min' => 'Le tarif doit être positif.',
        ];
    }
}
