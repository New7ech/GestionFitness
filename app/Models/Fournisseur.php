<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
    use HasFactory;

    protected $table = 'fournisseurs';

    protected $fillable = [
        'name',
        'description',
        'nom_entreprise',
        'adresse',
        'telephone',
        'email',
        'ville',
        'pays',
        'photo',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'fournisseur_id');
    }

    public function getPhotoUrlAttribute(): string
    {
        if (empty($this->photo)) {
            return asset('assets/img/profile.jpg');
        }

        return asset('storage/' . ltrim($this->photo, '/'));
    }
}
