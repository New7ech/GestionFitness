@extends('layouts.app')

@section('title', 'Liste des Challenges')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Gestion des Challenges</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('challenges.index') }}">Challenges</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title">Liste des Challenges</h4>
                @can('create', \App\Models\Challenge::class)
                    <a href="{{ route('challenges.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Ajouter un challenge
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('challenges.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label for="q" class="form-label">Recherche</label>
                    <input type="text" name="q" id="q" class="form-control" value="{{ request('q') }}" placeholder="Participante ou téléphone">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="challenge_type_id" class="form-label">Type</label>
                    <select name="challenge_type_id" id="challenge_type_id" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach ($challengeTypes as $challengeType)
                            <option value="{{ $challengeType->id }}" @selected((int) request('challenge_type_id') === $challengeType->id)>{{ $challengeType->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    <a href="{{ route('challenges.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table id="challenges-table" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Participante</th>
                            <th>Type</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Durée</th>
                            <th>Statut</th>
                            <th>Paiement</th>
                            <th style="width: 12%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($challenges as $challenge)
                            <tr>
                                <td>{{ $challenge->participante->full_name }}</td>
                                <td>{{ $challenge->challengeType->label }}</td>
                                <td>{{ $challenge->start_date->format('d/m/Y') }}</td>
                                <td>{{ $challenge->end_date->format('d/m/Y') }}</td>
                                <td>{{ $challenge->duration_days }} jours</td>
                                <td><span class="badge badge-info">{{ $challenge->status->label() }}</span></td>
                                <td>{{ $challenge->payment_status->label() }}</td>
                                <td class="text-center">
                                    <div class="form-button-action">
                                        <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Voir">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @can('update', $challenge)
                                            <a href="{{ route('challenges.edit', $challenge) }}" class="btn btn-link btn-warning btn-lg" data-bs-toggle="tooltip" title="Modifier">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $challenge)
                                            <form action="{{ route('challenges.destroy', $challenge) }}" method="POST" class="d-inline delete-form" data-challenge-name="{{ $challenge->participante->full_name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-danger btn-lg" data-bs-toggle="tooltip" title="Supprimer">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="alert alert-info mb-0">Aucun challenge trouvé.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $challenges->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#challenges-table').DataTable({
            paging: false,
            info: false,
            searching: false,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json"
            },
            columnDefs: [
                { orderable: false, targets: [7] }
            ]
        });

        $('[data-bs-toggle="tooltip"]').each(function () {
            new bootstrap.Tooltip(this);
        });

        $('.delete-form').on('submit', function (e) {
            e.preventDefault();
            var form = this;
            var challengeName = $(this).data('challenge-name');

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: 'Vous allez supprimer le challenge de ' + challengeName + '.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
