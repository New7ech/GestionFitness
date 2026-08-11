@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Détail de la Notification')

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Notifications</h3>
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
            <a href="{{ route('notifications.index') }}">Notifications</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Détail de la Notification</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour l'affichage des détails de la notification --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    Détails de la Notification
                </div>
                <div class="card-category">
                    Reçue {{ $notification ? $notification->created_at->diffForHumans() : 'N/A' }}
                </div>
            </div>
            <div class="card-body">
                @if ($notification)
                    {{-- Message de la notification --}}
                    <div class="alert alert-{{ $notification->read_at ? 'light' : 'warning' }} notification-detail-message" role="alert">
                        @php
                            $iconClass = 'fas fa-bell'; // Icône par défaut
                            if (isset($notification->data['type'])) {
                                switch ($notification->data['type']) {
                                    case 'stock_faible':
                                        $iconClass = 'fas fa-exclamation-triangle';
                                        break;
                                    case 'nouvelle_facture':
                                        $iconClass = 'fas fa-file-invoice';
                                        break;
                                    // Ajoutez d'autres cas ici
                                }
                            }
                        @endphp
                        <i class="{{ $iconClass }} me-2"></i>
                        <strong>{{ $notification->data['message'] ?? 'Message non disponible.' }}</strong>
                    </div>

                    {{-- Informations sur la notification --}}
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Date de Réception</label>
                                <p class="form-control-static">{{ $notification->created_at->format('d/m/Y à H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Statut</label>
                                <p class="form-control-static">
                                    @if($notification->read_at)
                                        <span class="badge badge-success"><i class="fas fa-check-circle me-1"></i>Lue le {{ $notification->read_at->format('d/m/Y à H:i:s') }}</span>
                                    @else
                                        <span class="badge badge-warning"><i class="fas fa-eye-slash me-1"></i>Non lue</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Lien associé (si présent dans les données de la notification) --}}
                    @if(isset($notification->data['link']) && !empty($notification->data['link']))
                        <div class="mt-3">
                            <a href="{{ url($notification->data['link']) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-link me-1"></i> Voir l'élément associé
                            </a>
                        </div>
                    @endif

                @else
                    {{-- Cas où la notification n'est pas trouvée --}}
                    <div class="alert alert-danger text-center">
                        <p class="mb-0">Notification non trouvée ou accès non autorisé.</p>
                    </div>
                @endif
            </div>
            <div class="card-action text-end">
                {{-- Bouton Marquer comme lue/non lue (si pertinent de pouvoir la marquer non lue à nouveau) --}}
                @if ($notification && !$notification->read_at)
                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-check me-1"></i> Marquer comme lue
                        </button>
                    </form>
                @endif
                 {{-- Bouton de suppression (optionnel) --}}
                {{--
                @if ($notification)
                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline delete-notification-form-show">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash me-1"></i> Supprimer
                    </button>
                </form>
                @endif
                --}}
                <a href="{{ route('notifications.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour aux notifications
                </a>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('styles')
<style>
    .notification-detail-message {
        font-size: 1.1rem; /* Augmenter la taille du message principal */
    }
     .badge-success {
        color: white !important;
    }
    .badge-warning {
        color: #664d03 !important; /* Couleur de texte pour badge warning Bootstrap */
        background-color: #fff3cd !important;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Optionnel: Confirmation SweetAlert pour supprimer une notification (si le bouton est décommenté)
    /*
    $('.delete-notification-form-show').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cette notification sera supprimée définitivement.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
    */
});
</script>
@endpush
