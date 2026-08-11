@extends('layouts.app')

@section('title', 'Liste des Mesures')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Mesures</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('mesures.index') }}">Mesures</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title">Historique des mesures</h4>
                @can('create', \App\Models\Mesure::class)
                    <a href="{{ route('mesures.create', request()->only('challenge_id')) }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Nouvelle mesure
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('mesures.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-5">
                    <label for="q" class="form-label">Recherche</label>
                    <input type="text" name="q" id="q" class="form-control" value="{{ request('q') }}" placeholder="Participante ou téléphone">
                </div>
                <div class="col-md-3">
                    <label for="stage" class="form-label">Étape</label>
                    <select name="stage" id="stage" class="form-select">
                        <option value="">Toutes les étapes</option>
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->value }}" @selected(request('stage') === $stage->value)>{{ $stage->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrer</button>
                    <a href="{{ route('mesures.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Réinitialiser</a>
                </div>
            </form>

            <div class="table-responsive">
                <table id="mesures-table" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Participante</th>
                            <th>Challenge</th>
                            <th>Étape</th>
                            <th>Poids</th>
                            <th>Tour de taille</th>
                            <th style="width: 12%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mesures as $mesure)
                            <tr>
                                <td>{{ $mesure->measured_at->format('d/m/Y') }}</td>
                                <td>{{ $mesure->challenge->participante->full_name }}</td>
                                <td>{{ $mesure->challenge->challengeType->label }}</td>
                                <td>{{ $mesure->stage->label() }}</td>
                                <td>{{ number_format((float) $mesure->weight, 2, ',', ' ') }} kg</td>
                                <td>{{ $mesure->waist ? number_format((float) $mesure->waist, 2, ',', ' ').' cm' : 'N/A' }}</td>
                                <td class="text-center">
                                    <div class="form-button-action">
                                        <a href="{{ route('mesures.show', $mesure) }}" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Voir">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @can('update', $mesure)
                                            <a href="{{ route('mesures.edit', $mesure) }}" class="btn btn-link btn-warning btn-lg" data-bs-toggle="tooltip" title="Corriger">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-info mb-0">Aucune mesure trouvée.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $mesures->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#mesures-table').DataTable({
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
