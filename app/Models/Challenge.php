<?php

namespace App\Models;

use App\Enums\ChallengeStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'challenge_type_id',
        'label',
        'start_date',
        'duration_days',
        'end_date',
        'capacite',
        'default_price',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'duration_days' => 'integer',
            'capacite' => 'integer',
            'default_price' => 'decimal:2',
            'status' => ChallengeStatus::class,
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

    protected function placesRestantes(): Attribute
    {
        return Attribute::get(function (): ?int {
            if ($this->capacite === null) {
                return null;
            }

            $inscrites = $this->inscriptions()
                ->where('status', '!=', \App\Enums\InscriptionStatus::Annulee->value)
                ->count();

            return max(0, $this->capacite - $inscrites);
        });
    }

    public function challengeType(): BelongsTo
    {
        return $this->belongsTo(ChallengeType::class);
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function activeInscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class)
            ->where('status', '!=', \App\Enums\InscriptionStatus::Annulee->value);
    }

    public function inscritesCount(): int
    {
        return $this->activeInscriptions()->count();
    }

    public function isFull(): bool
    {
        if ($this->capacite === null) {
            return false;
        }

        return $this->inscritesCount() >= $this->capacite;
    }

    public function displayLabel(): string
    {
        if ($this->label) {
            return $this->label;
        }

        return $this->challengeType->label.' — '.$this->start_date->format('d/m/Y');
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
