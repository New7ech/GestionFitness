<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $roleId = $this->route('role')?->id;

        return [
            'name' => 'required|string|max:255|unique:roles,name,'.$roleId,
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du rôle est obligatoire.',
            'name.string' => 'Le nom du rôle doit être une chaîne de caractères.',
            'name.max' => 'Le nom du rôle ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Ce nom de rôle existe déjà.',
            'permissions.array' => 'Les permissions doivent être fournies sous forme de tableau.',
            'permissions.*.integer' => 'Chaque permission doit être un identifiant numérique.',
            'permissions.*.exists' => 'Une ou plusieurs permissions sélectionnées n\'existent pas.',
        ];
    }
}
