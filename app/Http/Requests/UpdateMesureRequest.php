<?php

namespace App\Http\Requests;

use Illuminate\Validation\Validator;

class UpdateMesureRequest extends StoreMesureRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit-measurements') ?? false;
    }

    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);

        $validator->after(function (Validator $validator): void {
            $mesure = $this->route('mesure');

            if (! $mesure) {
                return;
            }

            if ((int) $this->input('challenge_id') !== (int) $mesure->challenge_id) {
                $validator->errors()->add('challenge_id', 'La correction doit rester rattachée au challenge d’origine.');
            }
        });
    }
}
