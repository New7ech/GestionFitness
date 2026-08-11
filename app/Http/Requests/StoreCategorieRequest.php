<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategorieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.string'   => 'Le nom de la catégorie doit être une chaîne de caractères.',
            'name.max'      => 'Le nom de la catégorie ne doit pas dépasser 255 caractères.',
            'name.unique'   => 'Ce nom de catégorie existe déjà.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max'    => 'La description ne doit pas dépasser 255 caractères.',
        ];
    }
}
