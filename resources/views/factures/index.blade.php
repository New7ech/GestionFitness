@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Liste des Factures')

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Factures</h3>
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
            <a href="#">Factures</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('factures.index') }}">Liste des Factures</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour la liste des factures --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour afficher la table des factures --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Factures</h4>
                    {{-- Bouton pour ajouter une nouvelle facture, aligné à droite --}}
                    <a href="{{ route('factures.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Créer une Facture
                    </a>
                </div>
                {{-- Le formulaire de recherche existant est supprimé au profit de la recherche DataTable --}}
            </div>
            <div class="card-body">
                {{-- Conteneur pour rendre la table responsive --}}
                <div class="table-responsive">
                    {{-- Table DataTable pour afficher les factures --}}
                    <table id="factures-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                {{-- Colonnes de la table --}}
                                <th>Numéro</th>
                                <th>Client</th>
                                <th>Date Facture</th>
                                <th class="text-end">Montant HT</th>
                                <th class="text-end">Montant TTC</th>
                                <th class="text-center">Statut Paiement</th>
                                <th style="width: 15%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Boucle pour afficher chaque facture --}}
                            @forelse($factures as $facture)
                            <tr>
                                <td>{{ $facture->numero ?? $facture->id }}</td>
                                <td>{{ $facture->client_nom ?? 'N/A' }} {{ $facture->client_prenom ?? '' }}</td>
                                <td>{{ $facture->date_facture ? \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') : 'N/A' }}</td>
                                <td class="text-end">{{ number_format($facture->montant_ht, 2, ',', ' ') }} FCFA</td>
                                <td class="text-end">{{ number_format($facture->montant_ttc, 2, ',', ' ') }} FCFA</td>
                                <td class="text-center">
                                    {{-- Badge stylisé pour le statut de paiement --}}
                                    @php
                                        $statutClass = '';
                                        if ($facture->statut_paiement == 'payé') {
                                            $statutClass = 'badge-success';
                                        } elseif ($facture->statut_paiement == 'impayé') {
                                            $statutClass = 'badge-danger';
                                        } else {
                                            $statutClass = 'badge-warning'; // Pour d'autres statuts éventuels
                                        }
                                    @endphp
                                    <span class="badge {{ $statutClass }}">
                                        {{ ucfirst($facture->statut_paiement) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{-- Groupe de boutons pour les actions --}}
                                    <div class="form-button-action">
                                        {{-- Bouton Voir --}}
                                        <a href="{{ route('factures.show', $facture->id) }}"
                                           data-bs-toggle="tooltip" title="Voir la facture"
                                           class="btn btn-link btn-primary btn-lg">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        {{-- Bouton Modifier --}}
                                        <a href="{{ route('factures.edit', $facture->id) }}"
                                           data-bs-toggle="tooltip" title="Modifier la facture"
                                           class="btn btn-link btn-warning btn-lg">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        {{-- Bouton Télécharger PDF --}}
                                        <a href="{{ route('factures.pdf', $facture->id) }}"
                                           data-bs-toggle="tooltip" title="Télécharger PDF"
                                           class="btn btn-link btn-info btn-lg" target="_blank"> {{-- btn-info pour PDF --}}
                                            <i class="fa fa-file-pdf"></i>
                                        </a>
                                        {{-- Formulaire pour la suppression --}}
                                        <form action="{{ route('factures.destroy', $facture->id) }}" method="POST"
                                              class="d-inline delete-form" data-facture-numero="{{ $facture->numero ?? $facture->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link btn-danger btn-lg"
                                                    data-bs-toggle="tooltip" title="Supprimer la facture">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            {{-- Cas où aucune facture n'est trouvée --}}
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-info" role="alert">
                                        Aucune facture trouvée pour le moment.
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- La pagination Laravel est gérée par DataTable, donc la section ci-dessous est supprimée --}}
                {{-- @if($factures->hasPages())
                <div class="mt-3">
                    {{ $factures->links() }}
                </div>
                @endif --}}
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
        // Initialisation de DataTable pour la table des factures
        $('#factures-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json", // Traduction française
                "searchPlaceholder": "Numéro, client, date..." // Placeholder pour la recherche
            },
            "order": [[ 0, "desc" ]], // Trier par numéro de facture (première colonne) en ordre décroissant par défaut
            "columnDefs": [
                { "orderable": false, "targets": 6 } // Désactiver le tri pour la colonne Actions
            ]
            // La recherche sur plusieurs colonnes est native à DataTable, pas besoin de configuration complexe ici
            // pour remplacer le formulaire de recherche simple précédent.
        });

        // Initialisation des tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Gestion de la soumission des formulaires de suppression avec SweetAlert
        $('.delete-form').on('submit', function (e) {
            e.preventDefault(); // Empêcher la soumission standard du formulaire
            var form = this;
            var factureNumero = $(this).data('facture-numero'); // Récupérer le numéro de la facture

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: "Vous êtes sur le point de supprimer la facture N° <strong>\"" + factureNumero + "\"</strong>.<br>Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Soumettre le formulaire si confirmation
                }
            });
        });
    });
</script>
@endpush
