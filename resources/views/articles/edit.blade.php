@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', "Modifier l'Article : " . $article->name)

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
            <a href="{{ route('articles.index') }}">Articles</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            {{-- Utilisation de Str::limit pour tronquer les noms d'articles longs dans le fil d'Ariane --}}
            <a href="#">Modifier : {{ Str::limit($article->name, 30) }}</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour le formulaire de modification d'article --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour le formulaire --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Formulaire de Modification d'Article</div>
                <div class="card-category">Modifiez les informations de l'article "{{ $article->name }}" ci-dessous.</div>
            </div>
            <div class="card-body">
                {{-- Affichage des erreurs de validation générales --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Erreur !</strong> Veuillez corriger les erreurs ci-dessous.
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Formulaire de modification d'article --}}
                {{-- La classe 'needs-validation' active la validation Bootstrap côté client --}}
                {{-- 'novalidate' désactive la validation HTML5 par défaut pour laisser Bootstrap gérer --}}
                <form action="{{ route('articles.update', $article->id) }}" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- Méthode HTTP pour la mise à jour --}}

                    {{-- Champ Nom de l'article --}}
                    <div class="form-group">
                        <label for="name">Nom de l'article <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               required value="{{ old('name', $article->name) }}" placeholder="Entrez le nom de l'article">
                        {{-- Message d'erreur spécifique pour le champ 'name' --}}
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        {{-- Message de validation standard de Bootstrap --}}
                        <div class="invalid-feedback">Le nom de l'article est requis.</div>
                    </div>

                    {{-- Champ Slug --}}
                    <div class="form-group">
                        <label for="slug">Slug (URL)</label>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $article->slug) }}" placeholder="laisser-vide-pour-generation-auto">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Sera généré à partir du nom si laissé vide. Doit être unique.</small>
                    </div>

                    {{-- Champ Description --}}
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Décrivez brièvement l'article">{{ old('description', $article->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Champ SKU (Code Article) --}}
                    <div class="form-group">
                        <label for="sku">SKU (Code Article)</label>
                        <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror"
                               value="{{ old('sku', $article->sku) }}" placeholder="Ex: ARTICLE001">
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Code unique pour la gestion de stock.</small>
                    </div>

                    {{-- Champ Image Principale (Upload) --}}
                    <div class="form-group">
                        <label for="image_principale">Image Principale</label>
                        <input type="file" name="image_principale" id="image_principale" class="form-control @error('image_principale') is-invalid @enderror">
                        @error('image_principale')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Laissez vide pour conserver l'image actuelle. Formats : jpeg, png, jpg, gif, svg, webp. Max : 2Mo.</small>

                        @if ($article->image_principale)
                            <div class="mt-2">
                                <label>Image actuelle :</label><br>
                                <img src="{{ $article->imageUrl }}" alt="Image actuelle de {{ $article->name }}" style="max-width: 200px; max-height: 200px; border-radius: 5px;" onerror="this.onerror=null;this.src='{{ asset('assets/img/profile.jpg') }}';">
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="supprimer_image_principale" id="supprimer_image_principale" value="1">
                                    <label class="form-check-label" for="supprimer_image_principale">
                                        Supprimer l'image principale actuelle
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>


                    {{-- Ligne pour les champs Prix et Quantité --}}
                    <div class="row">
                        <div class="col-md-6">
                            {{-- Champ Prix --}}
                            <div class="form-group">
                                <label for="prix">Prix (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="prix" id="prix"
                                       class="form-control @error('prix') is-invalid @enderror" required value="{{ old('prix', $article->prix) }}" placeholder="0.00">
                                @error('prix')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback">Le prix est requis et doit être un nombre positif.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Champ Quantité --}}
                            <div class="form-group">
                                <label for="quantite">Quantité en stock <span class="text-danger">*</span></label>
                                <input type="number" min="0" name="quantite" id="quantite"
                                       class="form-control @error('quantite') is-invalid @enderror" required value="{{ old('quantite', $article->quantite) }}" placeholder="0">
                                @error('quantite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback">La quantité est requise et doit être un nombre positif ou nul.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Champ Prix Promotionnel --}}
                    <div class="form-group">
                        <label for="prix_promotionnel">Prix Promotionnel (FCFA)</label>
                        <input type="number" step="0.01" min="0" name="prix_promotionnel" id="prix_promotionnel"
                               class="form-control @error('prix_promotionnel') is-invalid @enderror" value="{{ old('prix_promotionnel', $article->prix_promotionnel) }}" placeholder="Optionnel">
                        @error('prix_promotionnel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Laissez vide s'il n'y a pas de promotion. Doit être inférieur au prix normal.</small>
                    </div>

                    {{-- Ligne pour les champs Statut et Poids --}}
                    <div class="row">
                        <div class="col-md-6">
                            {{-- Champ Statut --}}
                            <div class="form-group">
                                <label for="statut">Statut <span class="text-danger">*</span></label>
                                <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                    <option value="disponible" {{ old('statut', $article->statut) == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                    <option value="brouillon" {{ old('statut', $article->statut) == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                    <option value="archivé" {{ old('statut', $article->statut) == 'archivé' ? 'selected' : '' }}>Archivé</option>
                                    <option value="en_rupture_de_stock" {{ old('statut', $article->statut) == 'en_rupture_de_stock' ? 'selected' : '' }}>En rupture de stock</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Champ Poids --}}
                            <div class="form-group">
                                <label for="poids">Poids (kg)</label>
                                <input type="number" step="0.001" min="0" name="poids" id="poids"
                                       class="form-control @error('poids') is-invalid @enderror" value="{{ old('poids', $article->poids) }}" placeholder="0.000">
                                @error('poids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Poids en kilogrammes (ex: 0.5 pour 500g).</small>
                            </div>
                        </div>
                    </div>

                    {{-- Champ Catégorie --}}
                    <div class="form-group">
                        <label for="category_id">Catégorie</label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- Choisir une catégorie --</option>
                            @foreach($categories as $categorie)
                                <option value="{{ $categorie->id }}" {{ old('category_id', $article->category_id) == $categorie->id ? 'selected' : '' }}>
                                    {{ $categorie->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Champ Fournisseur --}}
                    <div class="form-group">
                        <label for="fournisseur_id">Fournisseur</label>
                        <select name="fournisseur_id" id="fournisseur_id" class="form-select @error('fournisseur_id') is-invalid @enderror">
                            <option value="">-- Choisir un fournisseur --</option>
                            @foreach($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id', $article->fournisseur_id) == $fournisseur->id ? 'selected' : '' }}>
                                    {{ $fournisseur->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('fournisseur_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Champ Emplacement --}}
                    <div class="form-group">
                        <label for="emplacement_id">Emplacement de stockage</label>
                        <select name="emplacement_id" id="emplacement_id" class="form-select @error('emplacement_id') is-invalid @enderror">
                            <option value="">-- Choisir un emplacement --</option>
                            @foreach($emplacements as $emplacement)
                                <option value="{{ $emplacement->id }}" {{ old('emplacement_id', $article->emplacement_id) == $emplacement->id ? 'selected' : '' }}>
                                    {{ $emplacement->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('emplacement_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Champ Est Visible --}}
                    <div class="form-group form-check">
                        <input type="hidden" name="est_visible" value="0">
                        <input type="checkbox" name="est_visible" id="est_visible" class="form-check-input @error('est_visible') is-invalid @enderror"
                               value="1" {{ old('est_visible', $article->est_visible) ? 'checked' : '' }}>
                        <label class="form-check-label" for="est_visible">Visible sur le site public</label>
                        @error('est_visible')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Section des actions du formulaire --}}
                    <div class="card-action text-end">
                        {{-- Bouton Mettre à jour --}}
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à Jour l'Article
                        </button>
                        {{-- Bouton Annuler/Retour --}}
                        <a href="{{ route('articles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

{{-- Section pour les scripts JavaScript spécifiques à cette page --}}
@push('scripts')
<script>
    // La validation Bootstrap est gérée globalement dans layouts.app.blade.php.
    // Aucun script supplémentaire n'est généralement nécessaire ici pour la validation de base.
    // Si des fonctionnalités JavaScript spécifiques à ce formulaire de modification sont requises (ex: Select2),
    // elles peuvent être ajoutées ici.

    // Exemple (commenté) pour initialiser Select2, si applicable :
    // $(document).ready(function() {
    //     $('#category_id, #fournisseur_id, #emplacement_id').select2({
    //         theme: 'bootstrap-5' // Ou le thème approprié pour KaiAdmin
    //     });
    // });
</script>
@endsection
