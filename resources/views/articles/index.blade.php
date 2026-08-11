@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Liste des Articles')

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Articles</h3>
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
            <a href="#">Articles</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('articles.index') }}">Liste des Articles</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour la liste des articles --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour afficher la table des articles --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Articles</h4>
                    {{-- Bouton pour ajouter un nouvel article, aligné à droite --}}
                    <a href="{{ route('articles.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Ajouter un Article
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Conteneur pour rendre la table responsive --}}
                <div class="table-responsive">
                    {{-- Table DataTable pour afficher les articles --}}
                    <table id="articles-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                {{-- Colonnes de la table --}}
                                <th style="width: 10%;">Image</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th style="width: 10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Boucle pour afficher chaque article --}}
                            @forelse($articles as $article)
                            <tr>
                                <td>
                                    @if($article->imageUrl)
                                        <div class="article-image-container">
                                            <img src="{{ $article->imageUrl }}" alt="Image de {{ $article->name }}" class="img-thumbnail" onerror="this.onerror=null;this.src='{{ asset('assets/img/profile.jpg') }}';">
                                        </div>
                                    @else
                                        <div class="no-image-placeholder">
                                            <i class="fas fa-image"></i>
                                            <span>Pas d'image</span>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $article->name }}</td>
                                <td>{{ Str::limit($article->description, 40) }}</td> {{-- Réduit un peu pour faire de la place --}}
                                <td>{{ number_format($article->prix, 2, ',', ' ') }} FCFA</td>
                                <td>{{ $article->quantite }}</td>
                                <td>
                                    {{-- Groupe de boutons pour les actions --}}
                                    <div class="form-button-action">
                                        {{-- Bouton Voir --}}
                                        <a href="{{ route('articles.show', $article->id) }}"
                                           data-bs-toggle="tooltip" title="Voir les détails"
                                           class="btn btn-link btn-primary btn-lg">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        {{-- Bouton Modifier --}}
                                        <a href="{{ route('articles.edit', $article->id) }}"
                                           data-bs-toggle="tooltip" title="Modifier l'article"
                                           class="btn btn-link btn-warning btn-lg">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        {{-- Formulaire pour la suppression --}}
                                        <form action="{{ route('articles.destroy', $article->id) }}" method="POST"
                                              class="d-inline delete-form" data-article-name="{{ $article->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link btn-danger btn-lg"
                                                    data-bs-toggle="tooltip" title="Supprimer l'article">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            {{-- Cas où aucun article n'est trouvé --}}
                            <tr>
                                <td colspan="6" class="text-center"> {{-- Augmenté le colspan à 6 --}}
                                    <div class="alert alert-info" role="alert">
                                        Aucun article trouvé pour le moment.
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
        // Initialisation de DataTable pour la table des articles
        $('#articles-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json" // Traduction française
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 5] } // Désactiver le tri pour la colonne Image et Actions
            ]
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
            var articleName = $(this).data('article-name'); // Récupérer le nom de l'article

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Vous êtes sur le point de supprimer l'article : \"" + articleName + "\". Cette action est irréversible !",
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
