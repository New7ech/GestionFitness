<?php

namespace App\Http\Requests;

use App\Enums\AttendanceStatus;
use App\Models\Presence;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePresenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit-attendance') ?? false;
    }

    public function rules(): array
    {
        return [
            'inscription_id' => ['required', 'exists:inscriptions,id'],
            'attendance_date' => [
                'required',
                'date',
            ],
            'status' => ['required', Rule::enum(AttendanceStatus::class)],
            'comment' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'inscription_id.required' => 'L\'inscription est obligatoire.',
            'inscription_id.exists' => 'L\'inscription sélectionnée est invalide.',
            'attendance_date.required' => 'La date de présence est obligatoire.',
            'attendance_date.date' => 'La date de présence doit être une date valide.',
            'attendance_date.unique' => 'Une présence existe déjà pour cette inscription à cette date.',
            'status.required' => 'Le statut de présence est obligatoire.',
            'status' => 'Le statut de présence sélectionné est invalide.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $presence = $this->route('presence');
            $attendanceDate = $this->date('attendance_date')?->toDateString();

            if (! $presence || ! $attendanceDate) {
                return;
            }

            if ((int) $this->input('inscription_id') !== (int) $presence->inscription_id) {
                $validator->errors()->add('inscription_id', 'La présence doit rester rattachée à l\'inscription d\'origine.');

                return;
            }

            $challenge = $presence->inscription->challenge;

            if (
                Presence::query()
                    ->where('inscription_id', $presence->inscription_id)
                    ->whereDate('attendance_date', $attendanceDate)
                    ->whereKeyNot($presence->id)
                    ->exists()
            ) {
                $validator->errors()->add('attendance_date', 'Une présence existe déjà pour cette inscription à cette date.');
            }

            if ($attendanceDate < $challenge->start_date->toDateString() || $attendanceDate > $challenge->end_date->toDateString()) {
                $validator->errors()->add('attendance_date', 'La date de présence doit être comprise dans la période du challenge.');
            }
        });
    }
}
