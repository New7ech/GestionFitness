<?php

namespace App\Models;

use App\Enums\ChallengeStatus;
use App\Enums\PaymentStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'participante_id',
        'challenge_type_id',
        'start_date',
        'duration_days',
        'end_date',
        'status',
        'goal_text',
        'goal_weight',
        'goal_waist',
        'goal_personal',
        'observations',
        'price',
        'payment_status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'duration_days' => 'integer',
            'goal_weight' => 'decimal:2',
            'goal_waist' => 'decimal:2',
            'price' => 'decimal:2',
            'status' => ChallengeStatus::class,
            'payment_status' => PaymentStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Challenge $challenge): void {
            if ($challenge->start_date && $challenge->duration_days) {
                $challenge->end_date = CarbonImmutable::parse($challenge->start_date)
                    ->addDays((int) $challenge->duration_days)
                    ->toDateString();
            }
        });
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(Participante::class);
    }

    public function challengeType(): BelongsTo
    {
        return $this->belongsTo(ChallengeType::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function mesures(): HasMany
    {
        return $this->hasMany(Mesure::class);
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function commentaires(): MorphMany
    {
        return $this->morphMany(Commentaire::class, 'commentable');
    }

    public function recus(): HasManyThrough
    {
        return $this->hasManyThrough(
            Recu::class,
            Paiement::class,
            'challenge_id',
            'payment_id',
            'id',
            'id'
        );
    }

    public function dernierRecu(): HasOneThrough
    {
        return $this->hasOneThrough(
            Recu::class,
            Paiement::class,
            'challenge_id',
            'payment_id',
            'id',
            'id'
        )->latest('recus.issued_at');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
