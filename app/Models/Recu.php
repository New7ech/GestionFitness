<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recus';

    protected $fillable = [
        'payment_id',
        'receipt_number',
        'issued_at',
        'participante_full_name',
        'challenge_type_label',
        'challenge_duration_days',
        'amount_paid',
        'amount_remaining',
        'payment_mode',
        'issued_by_name',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'challenge_duration_days' => 'integer',
            'amount_paid' => 'decimal:2',
            'amount_remaining' => 'decimal:2',
        ];
    }

    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiement::class, 'payment_id');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
