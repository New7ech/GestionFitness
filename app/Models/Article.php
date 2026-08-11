<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    protected $fillable = [
        'name',
        'slug',
        'est_visible',
        'description',
        'sku',
        'image_principale',
        'prix',
        'prix_promotionnel',
        'quantite',
        'statut',
        'poids',
        'category_id',
        'fournisseur_id',
        'emplacement_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'prix_promotionnel' => 'decimal:2',
        'poids' => 'decimal:3',
        'est_visible' => 'boolean',
        'quantite' => 'integer',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_principale) {
            return null;
        }

        $rawPath = trim((string) $this->image_principale);

        if (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) {
            return $rawPath;
        }

        $normalizedPath = ltrim($rawPath, '/');

        // Backward compatibility for legacy values like "storage/articles_images/..".
        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        return url('media/public/' . $normalizedPath);
    }

    public function factures(): BelongsToMany
    {
        return $this->belongsToMany(Facture::class, 'article_facture')
            ->withPivot('quantite', 'prix_unitaire')
            ->withTimestamps();
    }

    public function scopeSearchByText(Builder $query, string $searchTerm): Builder
    {
        return $query->where(function (Builder $query) use ($searchTerm): void {
            $query->where('name', 'like', "%{$searchTerm}%")
                ->orWhere('description', 'like', "%{$searchTerm}%");
        });
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'category_id');
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

    public function emplacement(): BelongsTo
    {
        return $this->belongsTo(Emplacement::class, 'emplacement_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'category_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

