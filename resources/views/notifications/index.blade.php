@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Mes Notifications')

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
    </ul>
</div>

{{-- Conteneur principal pour la liste des notifications --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Toutes mes notifications</h4>
                    {{-- Bouton pour marquer toutes les notifications comme lues --}}
                    @if(Auth::user()->unreadNotifications->isNotEmpty())
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="ms-auto mark-all-read-form">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-round btn-sm">
                            <i class="fas fa-check-double me-1"></i> Marquer toutes comme lues
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($notifications->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted fs-lg">Vous n'avez aucune notification pour le moment.</p>
                    </div>
                @else
                    <ul class="list-group list-group-flush notification-list">
                        {{-- Boucle sur les notifications --}}
                        @foreach($notifications as $notification)
                            <li class="list-group-item notification-item {{ !$notification->read_at ? 'notification-unread' : 'notification-read' }}">
                                <a href="{{ route('notifications.show', $notification->id) }}" class="stretched-link text-decoration-none text-dark"></a>
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div>
                                        {{-- Icône basée sur le type de notification (exemple) --}}
                                        @php
                                            $iconClass = 'fas fa-bell text-primary'; // Icône par défaut
                                            if (isset($notification->data['type'])) {
                                                switch ($notification->data['type']) {
                                                    case 'stock_faible':
                                                        $iconClass = 'fas fa-exclamation-triangle text-warning';
                                                        break;
                                                    case 'nouvelle_facture':
                                                        $iconClass = 'fas fa-file-invoice text-info';
                                                        break;
                                                    // Ajoutez d'autres cas ici
                                                }
                                            }
                                        @endphp
                                        <span class="notification-icon me-3 fs-xl">
                                            <i class="{{ $iconClass }}"></i>
                                        </span>
                                        <span class="notification-message">{{ $notification->data['message'] }}</span>
                                    </div>
                                    <small class="text-muted notification-time ms-3">{{ $notification->created_at->diffForHumans(null, true) }}</small>
                                </div>
                                <div class="mt-2 d-flex justify-content-end align-items-center">
                                    @if(!$notification->read_at)
                                        <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="mark-one-read-form me-2">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm py-1 px-2" data-bs-toggle="tooltip" title="Marquer comme lue">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge badge-success"><i class="fas fa-check-circle me-1"></i>Lue</span>
                                    @endif
                                    {{-- Bouton de suppression de notification (optionnel) --}}
                                    {{--
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="delete-notification-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm py-1 px-2" data-bs-toggle="tooltip" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    --}}
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Pagination --}}
                    @if($notifications->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('styles')
<style>
    .notification-list .list-group-item {
        padding: 1rem 1.25rem;
        transition: background-color 0.2s ease-in-out;
        position: relative; /* Pour le lien étiré */
    }
    .notification-list .list-group-item:hover {
        background-color: #f8f9fa; /* Gris clair au survol */
    }
    .notification-unread {
        background-color: #fff3cd; /* Jaune clair pour non lues (Bootstrap warning light) */
        border-left: 4px solid #ffc107; /* Bordure jaune (Bootstrap warning) */
    }
    .notification-read {
        /* background-color: #e9ecef; /* Gris très clair pour lues */
    }
    .notification-icon {
        opacity: 0.8;
    }
    .notification-message {
        font-weight: 500;
    }
    .notification-time {
        white-space: nowrap; /* Empêcher le temps de passer à la ligne */
    }
    .badge-success { /* Assurer la visibilité du badge "Lue" */
        color: white !important;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialisation des tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Confirmation SweetAlert pour "Marquer toutes comme lues"
    $('.mark-all-read-form').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Confirmer',
            text: "Voulez-vous vraiment marquer toutes les notifications comme lues ?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, marquer toutes',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Optionnel: Confirmation SweetAlert pour supprimer une notification (si le bouton est décommenté)
    /*
    $('.delete-notification-form').on('submit', function(e) {
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
