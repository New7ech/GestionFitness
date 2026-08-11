<?php

namespace Database\Factories;

use App\Enums\MeasurementStage;
use App\Enums\MediaType;
use App\Models\Challenge;
use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'mediable_type' => Challenge::class,
            'mediable_id' => Challenge::factory(),
            'type' => MediaType::Photo,
            'stage' => MeasurementStage::Initiale,
            'disk_path' => 'participantes/1/challenges/1/media/photo/private.jpg',
            'original_filename' => 'private.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 1024,
            'uploaded_by' => User::factory(),
        ];
    }
}
