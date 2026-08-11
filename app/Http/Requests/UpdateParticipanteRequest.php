<?php

namespace App\Http\Requests;

use App\Enums\ParticipantStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParticipanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit-participantes') ?? false;
    }

    public function rules(): array
    {
        $participanteId = $this->route('participante')?->id;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('participantes', 'email')->ignore($participanteId)],
            'address' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'status' => ['required', Rule::enum(ParticipantStatus::class)],
            'has_cesarean' => ['nullable', 'boolean'],
            'cesarean_comment' => ['nullable', 'string'],
            'health_notes' => ['nullable', 'string'],
            'registration_date' => ['required', 'date'],
            'confirm_duplicate_phone' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.max' => 'Le prénom ne doit pas dépasser 255 caractères.',
            'last_name.required' => 'Le nom est obligatoire.',
            'last_name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'phone.required' => 'Le téléphone est obligatoire.',
            'phone.max' => 'Le téléphone ne doit pas dépasser 50 caractères.',
            'email.email' => "L'adresse email doit être valide.",
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'address.max' => "L'adresse ne doit pas dépasser 255 caractères.",
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'La photo doit être de type : jpeg, png, jpg ou webp.',
            'photo.max' => 'La photo ne doit pas dépasser 2 Mo.',
            'birthdate.date' => 'La date de naissance doit être une date valide.',
            'birthdate.before' => 'La date de naissance doit être antérieure à aujourd’hui.',
            'status.required' => 'Le statut est obligatoire.',
            'status' => 'Le statut sélectionné est invalide.',
            'has_cesarean.boolean' => 'La valeur césarienne doit être vraie ou fausse.',
            'registration_date.required' => "La date d'inscription est obligatoire.",
            'registration_date.date' => "La date d'inscription doit être une date valide.",
        ];
    }
}
