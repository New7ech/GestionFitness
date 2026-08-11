<?php

namespace App\Http\Requests;

use App\Enums\AttendanceStatus;
use App\Models\Challenge;
use App\Models\Presence;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePresenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('record-attendance') ?? false;
    }

    public function rules(): array
    {
        return [
            'challenge_id' => ['required', 'exists:challenges,id'],
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
            'challenge_id.required' => 'Le challenge est obligatoire.',
            'challenge_id.exists' => 'Le challenge sélectionné est invalide.',
            'attendance_date.required' => 'La date de présence est obligatoire.',
            'attendance_date.date' => 'La date de présence doit être une date valide.',
            'attendance_date.unique' => 'Une présence existe déjà pour ce challenge à cette date.',
            'status.required' => 'Le statut de présence est obligatoire.',
            'status' => 'Le statut de présence sélectionné est invalide.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $challengeId = $this->input('challenge_id');
            $attendanceDate = $this->date('attendance_date')?->toDateString();

            if (! $challengeId || ! $attendanceDate) {
                return;
            }

            $challenge = Challenge::query()->find($challengeId);

            if (! $challenge) {
                return;
            }

            if (
                Presence::query()
                    ->where('challenge_id', $challenge->id)
                    ->whereDate('attendance_date', $attendanceDate)
                    ->exists()
            ) {
                $validator->errors()->add('attendance_date', 'Une présence existe déjà pour ce challenge à cette date.');
            }

            if ($attendanceDate < $challenge->start_date->toDateString() || $attendanceDate > $challenge->end_date->toDateString()) {
                $validator->errors()->add('attendance_date', 'La date de présence doit être comprise dans la période du challenge.');
            }
        });
    }
}
