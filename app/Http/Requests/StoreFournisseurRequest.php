<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFournisseurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'nom_entreprise'=> 'required|string|max:255',
            'adresse'       => 'required|string|max:255',
            'telephone'     => 'required|string|max:20|unique:fournisseurs,telephone',
            'email'         => 'required|email|max:255|unique:fournisseurs,email',
            'ville'         => 'required|string|max:100',
            'pays'          => 'required|string|max:100',
            'photo'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Le nom du fournisseur est obligatoire.',
            'nom_entreprise.required'=> "Le nom de l'entreprise est obligatoire.",
            'adresse.required'       => "L'adresse est obligatoire.",
            'telephone.required'     => 'Le numéro de téléphone est obligatoire.',
            'telephone.unique'       => 'Ce numéro de téléphone est déjà utilisé.',
            'email.required'         => "L'adresse e-mail est obligatoire.",
            'email.email'            => "L'adresse e-mail n'est pas valide.",
            'email.unique'           => 'Cette adresse e-mail est déjà utilisée.',
            'ville.required'         => 'La ville est obligatoire.',
            'pays.required'          => 'Le pays est obligatoire.',
            'photo.image'            => 'Le fichier doit être une image.',
            'photo.mimes'            => 'La photo doit être de type : jpeg, png, jpg, gif ou webp.',
            'photo.max'              => 'La photo ne doit pas dépasser 2 Mo.',
        ];
    }
}
