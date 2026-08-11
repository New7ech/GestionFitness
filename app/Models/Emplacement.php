<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Emplacement extends Model
{
    use HasFactory;

    protected $table = 'emplacements';

    protected $fillable = [
        'name',
        'description',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'emplacement_id');
    }
}

