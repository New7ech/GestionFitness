<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:[.-][a-z0-9]+)*$/',
                Rule::unique('permissions', 'name')->where(fn ($query) => $query->where('guard_name', 'web')),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $name = Str::ascii((string) $this->input('name', ''));
        $name = Str::lower(trim($name));
        $name = preg_replace('/[\s_]+/', '-', $name) ?? '';
        $name = preg_replace('/[^a-z0-9.-]+/', '-', $name) ?? '';
        $name = preg_replace('/[-.]{2,}/', '-', $name) ?? '';
        $name = trim($name, '-.');

        $this->merge([
            'name' => $name,
        ]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la permission est obligatoire.',
            'name.string' => 'Le nom de la permission doit etre une chaine de caracteres.',
            'name.max' => 'Le nom de la permission ne doit pas depasser 255 caracteres.',
            'name.unique' => 'Ce nom de permission existe deja pour ce guard.',
            'name.regex' => 'Utilisez uniquement lettres minuscules, chiffres, points et tirets (ex: articles.read).',
        ];
    }
}
