@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', "Détails de la Facture N° " . ($facture->numero ?? $facture->id))

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
            <a href="{{ route('factures.index') }}">Factures</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Détails Facture N° {{ $facture->numero ?? $facture->id }}</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour l'affichage des détails de la facture --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour afficher les détails --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Détails de la Facture N° <strong>{{ $facture->numero ?? $facture->id }}</strong></h4>
                    {{-- Bouton pour télécharger le PDF, aligné à droite --}}
                    <a href="{{ route('factures.pdf', $facture->id) }}" class="btn btn-info btn-round ms-auto" target="_blank">
                        <i class="fas fa-file-pdf"></i>
                        Télécharger PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Section Informations Client --}}
                <h5 class="card-title mb-3 fw-bold text-primary">Informations du Client</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Nom Complet</label>
                            <p class="form-control-static">{{ $facture->client_nom ?? '' }} {{ $facture->client_prenom ?? '' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Téléphone</label>
                            <p class="form-control-static">{{ $facture->client_telephone ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Adresse</label>
                            <p class="form-control-static">{{ $facture->client_adresse ?: 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-group-default">
                            <label>Email</label>
                            <p class="form-control-static">{{ $facture->client_email ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <hr>
                {{-- Section Détails de la Facture --}}
                <h5 class="card-title mt-4 mb-3 fw-bold text-primary">Détails de la Facture</h5>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group form-group-default">
                            <label>Date de Facturation</label>
                            <p class="form-control-static">{{ $facture->date_facture ? \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group form-group-default">
                            <label>Mode de Paiement</label>
                            <p class="form-control-static">{{ ucfirst($facture->mode_paiement) ?: 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group form-group-default">
                            <label>Statut du Paiement</label>
                            <p class="form-control-static">
                                @php
                                    $statutClass = '';
                                    if ($facture->statut_paiement == 'payé') $statutClass = 'badge-success';
                                    elseif ($facture->statut_paiement == 'impayé') $statutClass = 'badge-danger';
                                    else $statutClass = 'badge-warning';
                                @endphp
                                <span class="badge {{ $statutClass }}">{{ ucfirst($facture->statut_paiement) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                 @if($facture->date_paiement)
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group form-group-default">
                            <label>Date de Paiement</label>
                            <p class="form-control-static">{{ \Carbon\Carbon::parse($facture->date_paiement)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                @endif


                <hr>
                {{-- Section Articles --}}
                <h5 class="card-title mt-4 mb-3 fw-bold text-primary">Articles Facturés</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Article</th>
                                <th class="text-end">Quantité</th>
                                <th class="text-end">Prix Unitaire HT</th>
                                <th class="text-end">Montant HT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($facture->articles as $article)
                            <tr>
                                <td>{{ $article->name }}</td>
                                <td class="text-end">{{ $article->pivot->quantite }}</td>
                                <td class="text-end">{{ number_format($article->pivot->prix_unitaire, 2, ',', ' ') }} FCFA</td>
                                <td class="text-end">{{ number_format($article->pivot->quantite * $article->pivot->prix_unitaire, 2, ',', ' ') }} FCFA</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Aucun article sur cette facture.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Section Totaux --}}
                <div class="row mt-4 justify-content-end">
                    <div class="col-md-5">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th class="text-end">Montant HT Total :</th>
                                    <td class="text-end fw-bold">{{ number_format($facture->montant_ht, 2, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th class="text-end">TVA ({{ $facture->tva ?? 18 }}%) :</th>
                                    <td class="text-end fw-bold">{{ number_format($facture->montant_ttc - $facture->montant_ht, 2, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th class="text-end fs-5 text-primary">Montant TTC Total :</th>
                                    <td class="text-end fs-5 fw-bold text-primary">{{ number_format($facture->montant_ttc, 2, ',', ' ') }} FCFA</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- Section des actions --}}
            <div class="card-action text-end">
                <a href="{{ route('factures.edit', $facture->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                {{-- Formulaire pour la suppression, avec confirmation SweetAlert --}}
                <form action="{{ route('factures.destroy', $facture->id) }}" method="POST"
                      class="d-inline delete-form" data-facture-numero="{{ $facture->numero ?? $facture->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
                <a href="{{ route('factures.index') }}" class="btn btn-secondary">
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
            var factureNumero = $(this).data('facture-numero');

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
                    form.submit(); // Soumettre si confirmation
                }
            });
        });
    });
</script>
@endpush
