@extends('layouts.app')

@section('title', 'Enregistrer un Paiement')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Paiements</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('payments.index') }}">Paiements</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Créer</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">Nouveau paiement</div></div>
        <div class="card-body">
            <form action="{{ route('payments.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @include('payments._form')
                <div class="card-action text-end">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
