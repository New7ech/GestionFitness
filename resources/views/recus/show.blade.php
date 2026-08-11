@extends('layouts.app')

@section('title', 'Détail Reçu')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Reçu {{ $recu->receipt_number }}</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('recus.index') }}">Reçus</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Détail</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title mb-0">{{ $recu->participante_full_name }}</h4>
                <div class="ms-auto">
                    <a href="{{ route('recus.pdf', $recu) }}" class="btn btn-success btn-round"><i class="fas fa-file-pdf"></i> Télécharger PDF</a>
                    <a href="{{ route('payments.show', $recu->paiement) }}" class="btn btn-secondary btn-round"><i class="fas fa-money-bill"></i> Paiement</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Numéro</dt>
                <dd class="col-sm-9">{{ $recu->receipt_number }}</dd>
                <dt class="col-sm-3">Émis le</dt>
                <dd class="col-sm-9">{{ $recu->issued_at->format('d/m/Y H:i') }}</dd>
                <dt class="col-sm-3">Challenge</dt>
                <dd class="col-sm-9">{{ $recu->challenge_type_label }} - {{ $recu->challenge_duration_days }} jours</dd>
                <dt class="col-sm-3">Montant payé</dt>
                <dd class="col-sm-9">{{ number_format((float) $recu->amount_paid, 2, ',', ' ') }} FCFA</dd>
                <dt class="col-sm-3">Reste à payer</dt>
                <dd class="col-sm-9">{{ number_format((float) $recu->amount_remaining, 2, ',', ' ') }} FCFA</dd>
                <dt class="col-sm-3">Mode</dt>
                <dd class="col-sm-9">{{ $recu->payment_mode }}</dd>
                <dt class="col-sm-3">Émis par</dt>
                <dd class="col-sm-9">{{ $recu->issued_by_name ?: $recu->generatedBy?->name ?: 'N/A' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
