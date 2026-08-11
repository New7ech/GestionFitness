<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'confirmed', Password::defaults()],
            'photo'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'phone'     => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'address'   => ['nullable', 'string', 'max:255'],
            'birthdate' => ['nullable', 'date'],
            'role_id'   => ['required', 'integer', 'exists:roles,id'],
            'status'    => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => "Le nom de l'utilisateur est obligatoire.",
            'email.required'        => "L'adresse e-mail est obligatoire.",
            'email.email'           => "L'adresse e-mail n'est pas valide.",
            'email.unique'          => 'Cette adresse e-mail est déjà utilisée.',
            'password.required'     => 'Le mot de passe est obligatoire.',
            'password.confirmed'    => 'La confirmation du mot de passe ne correspond pas.',
            'photo.image'           => 'Le fichier doit être une image.',
            'photo.mimes'           => 'La photo doit être de type : jpeg, png, jpg ou webp.',
            'photo.max'             => 'La photo ne doit pas dépasser 2 Mo.',
            'phone.max'             => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',
            'phone.unique'          => 'Ce numéro de téléphone est déjà utilisé.',
            'birthdate.date'        => 'La date de naissance n\'est pas valide.',
            'role_id.required'      => 'Le rôle est obligatoire.',
            'role_id.exists'        => 'Le rôle sélectionné est invalide.',
        ];
    }
}
