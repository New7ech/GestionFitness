@extends('layouts.app')

@section('title', 'Liste des Participantes')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Gestion des Participantes</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('participantes.index') }}">Participantes</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Liste des Participantes</h4>
                        @can('create', \App\Models\Participante::class)
                            <a href="{{ route('participantes.create') }}" class="btn btn-primary btn-round ms-auto">
                                <i class="fa fa-plus"></i>
                                Ajouter une participante
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('participantes.index') }}" class="row g-3 align-items-end mb-4">
                        <div class="col-md-5">
                            <label for="q" class="form-label">Recherche</label>
                            <input type="text" name="q" id="q" class="form-control" value="{{ request('q') }}" placeholder="Nom, téléphone ou email">
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
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrer</button>
                            <a href="{{ route('participantes.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Réinitialiser</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="participantes-table" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Nom complet</th>
                                    <th>Téléphone</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                    <th>Challenges</th>
                                    <th>Inscription</th>
                                    <th style="width: 12%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($participantes as $participante)
                                    <tr>
                                        <td>
                                            <img src="{{ $participante->photo_url }}" alt="Photo de {{ $participante->full_name }}" class="img-thumbnail" style="width: 54px; height: 54px; object-fit: cover;">
                                        </td>
                                        <td>{{ $participante->full_name }}</td>
                                        <td>{{ $participante->phone }}</td>
                                        <td>{{ $participante->email ?: 'N/A' }}</td>
                                        <td><span class="badge badge-{{ $participante->status->value === 'active' ? 'success' : 'default' }}">{{ $participante->status->label() }}</span></td>
                                        <td>{{ $participante->challenges_count }}</td>
                                        <td>{{ $participante->registration_date->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <div class="form-button-action">
                                                <a href="{{ route('participantes.show', $participante) }}" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Voir">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @can('update', $participante)
                                                    <a href="{{ route('participantes.edit', $participante) }}" class="btn btn-link btn-warning btn-lg" data-bs-toggle="tooltip" title="Modifier">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete', $participante)
                                                    <form action="{{ route('participantes.destroy', $participante) }}" method="POST" class="d-inline delete-form" data-participante-name="{{ $participante->full_name }}">
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
                                            <div class="alert alert-info mb-0" role="alert">Aucune participante trouvée.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $participantes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#participantes-table').DataTable({
            paging: false,
            info: false,
            searching: false,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json"
            },
            columnDefs: [
                { orderable: false, targets: [0, 7] }
            ]
        });

        $('[data-bs-toggle="tooltip"]').each(function () {
            new bootstrap.Tooltip(this);
        });

        $('.delete-form').on('submit', function (e) {
            e.preventDefault();
            var form = this;
            var participanteName = $(this).data('participante-name');

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: 'Vous allez supprimer la fiche de ' + participanteName + '.',
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
