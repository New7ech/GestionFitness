@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', "Détails du Fournisseur : " . $fournisseur->name)

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Fournisseurs</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="{{ route('accueil') }}">
                <i class="icon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('fournisseurs.index') }}">Fournisseurs</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            {{-- Utilisation de Str::limit pour tronquer les noms longs dans le fil d'Ariane --}}
            <a href="#">Détails : {{ Str::limit($fournisseur->name, 30) }}</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour l'affichage des détails du fournisseur --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour afficher les détails --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    Informations détaillées du fournisseur : <strong>{{ $fournisseur->name }}</strong>
                </div>
                <div class="card-category">
                    Entreprise : {{ $fournisseur->nom_entreprise ?: 'Non spécifié' }}
                </div>
            </div>
            <div class="card-body">
                {{-- Utilisation d'une structure plus lisible pour les détails --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Nom du Contact</label>
                            <p class="form-control-static">{{ $fournisseur->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Nom de l'Entreprise</label>
                            <p class="form-control-static">{{ $fournisseur->nom_entreprise ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="form-group form-group-default">
                    <label>Description / Activité</label>
                    <p class="form-control-static">{{ $fournisseur->description ?: 'N/A' }}</p>
                </div>

                <div class="form-group form-group-default">
                    <label>Adresse</label>
                    <p class="form-control-static">{{ $fournisseur->adresse ?: 'N/A' }}</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Téléphone</label>
                            <p class="form-control-static">{{ $fournisseur->telephone ?: 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Adresse Email</label>
                            <p class="form-control-static">{{ $fournisseur->email ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Ville</label>
                            <p class="form-control-static">{{ $fournisseur->ville ?: 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Pays</label>
                            <p class="form-control-static">{{ $fournisseur->pays ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <hr class="mt-3 mb-3"> {{-- Séparateur visuel --}}

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Date de Création</label>
                            {{-- Formatage de la date pour une meilleure lisibilité --}}
                            <p class="form-control-static">{{ $fournisseur->created_at->format('d/m/Y à H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Dernière Modification</label>
                            <p class="form-control-static">{{ $fournisseur->updated_at->format('d/m/Y à H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Section des actions --}}
            <div class="card-action text-end">
                <a href="{{ route('fournisseurs.edit', $fournisseur->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                {{-- Formulaire pour la suppression, avec confirmation SweetAlert --}}
                <form action="{{ route('fournisseurs.destroy', $fournisseur->id) }}" method="POST"
                      class="d-inline delete-form" data-fournisseur-name="{{ $fournisseur->name }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
                <a href="{{ route('fournisseurs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

{{-- Section pour les scripts JavaScript spécifiques à cette page --}}
@push('scripts')
<script>
    $(document).ready(function () {
        // Gestion de la soumission du formulaire de suppression avec SweetAlert
        $('.delete-form').on('submit', function (e) {
            e.preventDefault(); // Empêcher la soumission standard
            var form = this;
            var fournisseurName = $(this).data('fournisseur-name');

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: "Vous êtes sur le point de supprimer le fournisseur : <strong>\"" + fournisseurName + "\"</strong>.<br>Cette action est irréversible et pourrait affecter les articles et factures associés !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Soumettre si confirmation
                }
            });
        });
    });
</script>
@endpush
