<?php

namespace App\Models;

use App\Enums\InscriptionStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'participante_id',
        'challenge_id',
        'inscription_date',
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
            'inscription_date' => 'date',
            'goal_weight' => 'decimal:2',
            'goal_waist' => 'decimal:2',
            'price' => 'decimal:2',
            'status' => InscriptionStatus::class,
            'payment_status' => PaymentStatus::class,
        ];
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(Participante::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
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
            'inscription_id',
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
            'inscription_id',
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

    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
