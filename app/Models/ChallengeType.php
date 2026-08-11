<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChallengeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'label',
        'description',
        'default_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class);
    }
}
