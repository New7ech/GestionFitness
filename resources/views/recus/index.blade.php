@extends('layouts.app')

@section('title', 'Liste des Reçus')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Reçus</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('recus.index') }}">Reçus</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header"><h4 class="card-title">Liste des Reçus</h4></div>
        <div class="card-body">
            <form method="GET" action="{{ route('recus.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-6">
                    <label for="q" class="form-label">Recherche</label>
                    <input type="text" name="q" id="q" class="form-control" value="{{ request('q') }}" placeholder="Numéro ou participante">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrer</button>
                    <a href="{{ route('recus.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Réinitialiser</a>
                </div>
            </form>

            <div class="table-responsive">
                <table id="recus-table" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Date</th>
                            <th>Participante</th>
                            <th>Challenge</th>
                            <th>Montant</th>
                            <th>Reste</th>
                            <th style="width: 10%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recus as $recu)
                            <tr>
                                <td>{{ $recu->receipt_number }}</td>
                                <td>{{ $recu->issued_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $recu->participante_full_name }}</td>
                                <td>{{ $recu->challenge_type_label }}</td>
                                <td>{{ number_format((float) $recu->amount_paid, 2, ',', ' ') }} FCFA</td>
                                <td>{{ number_format((float) $recu->amount_remaining, 2, ',', ' ') }} FCFA</td>
                                <td class="text-center">
                                    <a href="{{ route('recus.show', $recu) }}" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Voir">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('recus.pdf', $recu) }}" class="btn btn-link btn-success btn-lg" data-bs-toggle="tooltip" title="PDF">
                                        <i class="fa fa-file-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-info mb-0">Aucun reçu trouvé.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $recus->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#recus-table').DataTable({
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
