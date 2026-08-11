<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facture extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_IMPAYEE = 'impayé';
    public const STATUS_PAYEE = 'payé';
    public const TVA_RATE = 18.00;
    public const LOW_STOCK_THRESHOLD = 5;
    public const PAYMENT_MODES = ['carte', 'chèque', 'espèces', 'virement'];

    protected $table = 'factures';

    protected $fillable = [
        'client_nom',
        'client_prenom',
        'client_adresse',
        'client_telephone',
        'client_email',
        'numero',
        'date_facture',
        'montant_ht',
        'tva',
        'montant_ttc',
        'statut_paiement',
        'date_paiement',
        'mode_paiement',
    ];

    protected function casts(): array
    {
        return [
            'date_facture' => 'date',
            'date_paiement' => 'date',
            'montant_ht' => 'decimal:2',
            'montant_ttc' => 'decimal:2',
            'tva' => 'decimal:2',
        ];
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_facture')
            ->withPivot('quantite', 'prix_unitaire')
            ->withTimestamps();
    }

    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {
        return $query->when($filters['search'] ?? null, function (Builder $query, string $search) {
            $query->where(function (Builder $nestedQuery) use ($search): void {
                $nestedQuery
                    ->where('numero', 'like', "%{$search}%")
                    ->orWhere('client_nom', 'like', "%{$search}%")
                    ->orWhere('client_email', 'like', "%{$search}%")
                    ->orWhere('statut_paiement', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        });
    }
}

