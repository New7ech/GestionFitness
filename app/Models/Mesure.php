<?php

namespace App\Models;

use App\Enums\MeasurementStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'challenge_id',
        'measured_at',
        'stage',
        'weight',
        'waist',
        'comment',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'measured_at' => 'date',
            'stage' => MeasurementStage::class,
            'weight' => 'decimal:2',
            'waist' => 'decimal:2',
        ];
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(MeasurementValue::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
