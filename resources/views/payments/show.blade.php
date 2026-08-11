@extends('layouts.app')

@section('title', 'Détail Paiement')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Paiement</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('payments.index') }}">Paiements</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Détail</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title mb-0">{{ $paiement->challenge->participante->full_name }}</h4>
                <div class="ms-auto">
                    @can('generate', [\App\Models\Recu::class, $paiement])
                        <form action="{{ route('payments.recu.store', $paiement) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-round">
                                <i class="fas fa-receipt"></i> {{ $paiement->recu ? 'Voir le reçu' : 'Générer le reçu' }}
                            </button>
                        </form>
                    @endcan
                    @can('update', $paiement)
                        <a href="{{ route('payments.edit', $paiement) }}" class="btn btn-warning btn-round"><i class="fas fa-edit"></i> Modifier</a>
                    @endcan
                    <a href="{{ route('challenges.show', $paiement->challenge) }}" class="btn btn-secondary btn-round"><i class="fas fa-dumbbell"></i> Challenge</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Challenge</dt>
                <dd class="col-sm-9">{{ $paiement->challenge->challengeType->label }}</dd>
                <dt class="col-sm-3">Date</dt>
                <dd class="col-sm-9">{{ $paiement->payment_date->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Type</dt>
                <dd class="col-sm-9">{{ $paiement->type->label() }}</dd>
                <dt class="col-sm-3">Montant</dt>
                <dd class="col-sm-9">{{ number_format((float) $paiement->amount, 2, ',', ' ') }} FCFA</dd>
                <dt class="col-sm-3">Solde restant</dt>
                <dd class="col-sm-9">{{ number_format($remainingAmount, 2, ',', ' ') }} FCFA</dd>
                <dt class="col-sm-3">Mode</dt>
                <dd class="col-sm-9">{{ $paiement->payment_mode->label() }}</dd>
                <dt class="col-sm-3">Enregistré par</dt>
                <dd class="col-sm-9">{{ $paiement->recordedBy?->name ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Commentaire</dt>
                <dd class="col-sm-9">{{ $paiement->comment ?: 'N/A' }}</dd>
                <dt class="col-sm-3">Reçu</dt>
                <dd class="col-sm-9">
                    @if ($paiement->recu)
                        <a href="{{ route('recus.show', $paiement->recu) }}">{{ $paiement->recu->receipt_number }}</a>
                    @else
                        Non généré
                    @endif
                </dd>
            </dl>
        </div>
    </div>
</div>
@endsection
