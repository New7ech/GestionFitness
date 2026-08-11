@extends('layouts.app')

@section('contenus')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h1>{{ $article->name }}</h1>
        </div>
        <div class="card-body">
            {{-- Affichage de l'image principale --}}
            @if($article->imageUrl)
                <div class="mb-4 text-center">
                    <img src="{{ $article->imageUrl }}" alt="Image de {{ $article->name }}" class="img-fluid rounded" style="max-height: 400px; max-width: 100%;" onerror="this.onerror=null;this.src='{{ asset('assets/img/profile.jpg') }}';">
                </div>
            @elseif($article->image_principale && (str_starts_with($article->image_principale, 'http://') || str_starts_with($article->image_principale, 'https://')))
                {{-- Fallback pour les anciennes URL directes si l'accesseur ne les gère pas comme prévu --}}
                <div class="mb-4 text-center">
                    <img src="{{ $article->image_principale }}" alt="Image de {{ $article->name }}" class="img-fluid rounded" style="max-height: 400px; max-width: 100%;" onerror="this.onerror=null;this.src='{{ asset('assets/img/profile.jpg') }}';">
                </div>
            @else
                <div class="mb-4 text-center text-muted">
                    <p>Aucune image principale disponible pour cet article.</p>
                </div>
            @endif

            <dl class="row">
                {{-- Nouveaux champs e-commerce --}}
                <dt class="col-sm-3">Slug :</dt>
                <dd class="col-sm-9">{{ $article->slug ?: 'N/A' }}</dd>

                <dt class="col-sm-3">SKU :</dt>
                <dd class="col-sm-9">{{ $article->sku ?: 'N/A' }}</dd>

                <dt class="col-sm-3">Statut :</dt>
                <dd class="col-sm-9"><span class="badge bg-info text-dark">{{ ucfirst($article->statut) }}</span></dd>

                <dt class="col-sm-3">Visible :</dt>
                <dd class="col-sm-9">{{ $article->est_visible ? 'Oui' : 'Non' }}</dd>

                @if($article->prix_promotionnel)
                <dt class="col-sm-3">Prix Promotionnel :</dt>
                <dd class="col-sm-9 text-danger fw-bold">{{ number_format($article->prix_promotionnel, 2, ',', ' ') }} FCFA</dd>
                @endif

                @if($article->poids)
                <dt class="col-sm-3">Poids :</dt>
                <dd class="col-sm-9">{{ $article->poids }} kg</dd>
                @endif

                <hr class="my-3"> {{-- Séparateur visuel --}}

                <dt class="col-sm-3">Description :</dt>
                <dd class="col-sm-9">{{ $article->description ?: 'N/A' }}</dd>

                <dt class="col-sm-3">Prix :</dt>
                <dd class="col-sm-9">{{ number_format($article->prix, 2, ',', ' ') }} FCFA</dd>

                <dt class="col-sm-3">Quantité en stock :</dt>
                <dd class="col-sm-9">{{ $article->quantite }}</dd>

                <dt class="col-sm-3">Catégorie :</dt>
                <dd class="col-sm-9">{{ $article->categorie->name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Fournisseur :</dt>
                <dd class="col-sm-9">{{ $article->fournisseur->name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Emplacement :</dt>
                <dd class="col-sm-9">{{ $article->emplacement->name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Créé par :</dt>
                <dd class="col-sm-9">{{ $article->user->name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Créé le :</dt>
                <dd class="col-sm-9">{{ $article->created_at->format('d/m/Y H:i') }}</dd>

                <dt class="col-sm-3">Modifié le :</dt>
                <dd class="col-sm-9">{{ $article->updated_at->format('d/m/Y H:i') }}</dd>
            </dl>
            <hr>
            <div class="mt-4">
                <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
                <a href="{{ route('articles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
