<?php

namespace App\Http\Requests;

use App\Models\Facture;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFactureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'client_nom'               => ['required', 'string', 'max:255'],
            'client_prenom'            => ['nullable', 'string', 'max:255'],
            'client_adresse'           => ['nullable', 'string', 'max:255'],
            'client_telephone'         => ['nullable', 'string', 'max:20'],
            'client_email'             => ['nullable', 'email', 'max:255'],
            'statut_paiement'          => ['required', Rule::in([Facture::STATUS_IMPAYEE, Facture::STATUS_PAYEE])],
            'mode_paiement'            => ['nullable', Rule::in(Facture::PAYMENT_MODES)],
            'articles'                 => ['required', 'array', 'min:1'],
            'articles.*.article_id'    => ['required', 'integer', 'exists:articles,id'],
            'articles.*.quantity'      => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_nom.required'               => 'Le nom du client est obligatoire.',
            'statut_paiement.required'          => 'Le statut de paiement est obligatoire.',
            'statut_paiement.in'                => 'Le statut de paiement sélectionné est invalide.',
            'mode_paiement.in'                  => 'Le mode de paiement sélectionné est invalide.',
            'articles.required'                 => 'Ajoutez au moins un article à la facture.',
            'articles.array'                    => 'Le format des lignes d\'articles est invalide.',
            'articles.min'                      => 'Ajoutez au moins un article à la facture.',
            'articles.*.article_id.required'    => 'Sélectionnez un article.',
            'articles.*.article_id.exists'      => 'Un des articles sélectionnés est introuvable.',
            'articles.*.quantity.required'      => 'La quantité est obligatoire.',
            'articles.*.quantity.integer'       => 'La quantité doit être un entier.',
            'articles.*.quantity.min'           => 'La quantité doit être supérieure à 0.',
        ];
    }
}
