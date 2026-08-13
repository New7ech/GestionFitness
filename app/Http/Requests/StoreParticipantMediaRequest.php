<?php

namespace App\Http\Requests;

use App\Enums\MeasurementStage;
use App\Enums\MediaType;
use App\Models\Mesure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreParticipantMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-media') ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(MediaType::class)],
            'stage' => ['required', Rule::enum(MeasurementStage::class)],
            'media' => $this->mediaRules(),
            'mesure_id' => ['nullable', 'exists:mesures,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Le type de média est obligatoire.',
            'type' => 'Le type de média sélectionné est invalide.',
            'stage.required' => 'L’étape du média est obligatoire.',
            'stage' => 'L’étape sélectionnée est invalide.',
            'media.required' => 'Le fichier est obligatoire.',
            'media.file' => 'Le fichier envoyé est invalide.',
            'media.image' => 'La photo doit être une image.',
            'media.mimes' => 'Le format du fichier est invalide.',
            'media.mimetypes' => 'Le type MIME du fichier est invalide.',
            'media.max' => 'Le fichier dépasse la taille maximale autorisée.',
            'mesure_id.exists' => 'La mesure sélectionnée est invalide.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $inscription = $this->route('inscription');
            $mesureId = $this->input('mesure_id');

            if (! $inscription || ! $mesureId) {
                return;
            }

            $belongsToInscription = Mesure::query()
                ->whereKey($mesureId)
                ->where('inscription_id', $inscription->id)
                ->exists();

            if (! $belongsToInscription) {
                $validator->errors()->add('mesure_id', 'La mesure sélectionnée ne correspond pas à cette inscription.');
            }
        });
    }

    private function mediaRules(): array
    {
        $type = $this->input('type');

        if ($type === MediaType::Photo->value) {
            return [
                'required',
                'file',
                'image',
                'mimes:'.implode(',', config('fitness.media.photo.mimes', ['jpeg', 'png', 'jpg', 'webp'])),
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:'.config('fitness.media.photo.max_kb', 5120),
            ];
        }

        if ($type === MediaType::Video->value) {
            return [
                'required',
                'file',
                'mimes:'.implode(',', config('fitness.media.video.mimes', ['mp4', 'mov', 'webm'])),
                'mimetypes:video/mp4,video/quicktime,video/webm',
                'max:'.config('fitness.media.video.max_kb', 102400),
            ];
        }

        return ['required', 'file'];
    }
}
