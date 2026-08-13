<?php

namespace App\Models;

use App\Enums\ParticipantStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participante extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'photo_path',
        'birthdate',
        'status',
        'has_cesarean',
        'cesarean_comment',
        'health_notes',
        'registration_date',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'registration_date' => 'date',
            'has_cesarean' => 'boolean',
            'status' => ParticipantStatus::class,
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(
            fn (): string => trim($this->first_name.' '.$this->last_name)
        );
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::get(
            fn (): string => $this->photo_path
                ? route('participantes.photo', $this)
                : asset('assets/img/profile.jpg')
        );
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function commentaires(): MorphMany
    {
        return $this->morphMany(Commentaire::class, 'commentable');
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
