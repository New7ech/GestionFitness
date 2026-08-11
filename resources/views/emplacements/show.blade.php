@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', "Détails de l'Emplacement : " . $emplacement->name)

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Emplacements</h3>
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
            <a href="{{ route('emplacements.index') }}">Emplacements</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Détails : {{ Str::limit($emplacement->name, 30) }}</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour l'affichage des détails de l'emplacement --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour afficher les détails --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    Informations de l'emplacement : <strong>{{ $emplacement->name }}</strong>
                </div>
            </div>
            <div class="card-body">
                {{-- Champ Nom de l'emplacement --}}
                <div class="form-group form-group-default">
                    <label>Nom de l'Emplacement</label>
                    <p class="form-control-static">{{ $emplacement->name }}</p>
                </div>

                {{-- Champ Description --}}
                <div class="form-group form-group-default">
                    <label>Description</label>
                    <p class="form-control-static">{{ $emplacement->description ?: 'N/A' }}</p>
                </div>

                <hr class="mt-3 mb-3"> {{-- Séparateur visuel --}}

                {{-- Dates de création et de modification --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Date de Création</label>
                            <p class="form-control-static">{{ $emplacement->created_at->format('d/m/Y à H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Dernière Modification</label>
                            <p class="form-control-static">{{ $emplacement->updated_at->format('d/m/Y à H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Section des actions --}}
            <div class="card-action text-end">
                <a href="{{ route('emplacements.edit', $emplacement->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                {{-- Formulaire pour la suppression, avec confirmation SweetAlert --}}
                <form action="{{ route('emplacements.destroy', $emplacement->id) }}" method="POST"
                      class="d-inline delete-form" data-emplacement-name="{{ $emplacement->name }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
                <a href="{{ route('emplacements.index') }}" class="btn btn-secondary">
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
            var emplacementName = $(this).data('emplacement-name');

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: "Vous êtes sur le point de supprimer l'emplacement : <strong>\"" + emplacementName + "\"</strong>.<br>Cette action est irréversible et pourrait affecter les articles qui y sont stockés !",
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
