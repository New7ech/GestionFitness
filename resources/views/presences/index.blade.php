@extends('layouts.app')

@section('title', 'Liste des Présences')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Présences</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('presences.index') }}">Présences</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title">Feuille de présence</h4>
                @can('create', \App\Models\Presence::class)
                    <a href="{{ route('presences.create', request()->only('challenge_id')) }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Nouvelle présence
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('presences.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label for="q" class="form-label">Recherche</label>
                    <input type="text" name="q" id="q" class="form-control" value="{{ request('q') }}" placeholder="Participante ou téléphone">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Statut</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tous</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Du</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Au</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrer</button>
                    <a href="{{ route('presences.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Réinitialiser</a>
                </div>
            </form>

            <div class="table-responsive">
                <table id="presences-table" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Participante</th>
                            <th>Challenge</th>
                            <th>Statut</th>
                            <th>Enregistré par</th>
                            <th>Modifié par</th>
                            <th style="width: 12%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($presences as $presence)
                            <tr>
                                <td>{{ $presence->attendance_date->format('d/m/Y') }}</td>
                                <td>{{ $presence->challenge->participante->full_name }}</td>
                                <td>{{ $presence->challenge->challengeType->label }}</td>
                                <td>
                                    <span class="badge badge-{{ $presence->status === \App\Enums\AttendanceStatus::Presente ? 'success' : 'danger' }}">
                                        {{ $presence->status->label() }}
                                    </span>
                                </td>
                                <td>{{ $presence->recordedBy?->name ?? 'N/A' }}</td>
                                <td>{{ $presence->updatedBy?->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <div class="form-button-action">
                                        <a href="{{ route('presences.show', $presence) }}" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Voir">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @can('update', $presence)
                                            <a href="{{ route('presences.edit', $presence) }}" class="btn btn-link btn-warning btn-lg" data-bs-toggle="tooltip" title="Modifier">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-info mb-0">Aucune présence trouvée.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $presences->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#presences-table').DataTable({
            paging: false,
            info: false,
            searching: false,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json"
            },
            columnDefs: [
                { orderable: false, targets: [6] }
            ]
        });
    });
</script>
@endpush
