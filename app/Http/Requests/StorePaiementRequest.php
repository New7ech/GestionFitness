<?php

namespace App\Http\Requests;

use App\Enums\PaymentMode;
use App\Enums\PaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create-payments') ?? false;
    }

    public function rules(): array
    {
        return [
            'inscription_id' => ['required', 'exists:inscriptions,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', Rule::enum(PaymentType::class)],
            'payment_date' => ['required', 'date'],
            'payment_mode' => ['required', Rule::enum(PaymentMode::class)],
            'comment' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'inscription_id.required' => 'L\'inscription est obligatoire.',
            'inscription_id.exists' => 'L\'inscription sélectionnée est invalide.',
            'amount.required' => 'Le montant est obligatoire.',
            'amount.numeric' => 'Le montant doit être un nombre.',
            'amount.min' => 'Le montant doit être strictement positif.',
            'type.required' => 'Le type de paiement est obligatoire.',
            'type' => 'Le type de paiement sélectionné est invalide.',
            'payment_date.required' => 'La date de paiement est obligatoire.',
            'payment_date.date' => 'La date de paiement doit être une date valide.',
            'payment_mode.required' => 'Le mode de paiement est obligatoire.',
            'payment_mode' => 'Le mode de paiement sélectionné est invalide.',
        ];
    }
}
