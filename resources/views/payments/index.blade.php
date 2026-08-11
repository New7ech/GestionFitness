@extends('layouts.app')

@section('title', 'Liste des Paiements')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Paiements</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('payments.index') }}">Paiements</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title">Liste des Paiements</h4>
                @can('create', \App\Models\Paiement::class)
                    <a href="{{ route('payments.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Nouveau paiement
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('payments.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-6">
                    <label for="q" class="form-label">Recherche</label>
                    <input type="text" name="q" id="q" class="form-control" value="{{ request('q') }}" placeholder="Participante ou téléphone">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrer</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Réinitialiser</a>
                </div>
            </form>

            <div class="table-responsive">
                <table id="payments-table" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Participante</th>
                            <th>Challenge</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Mode</th>
                            <th>Reçu</th>
                            <th style="width: 12%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paiements as $paiement)
                            <tr>
                                <td>{{ $paiement->payment_date->format('d/m/Y') }}</td>
                                <td>{{ $paiement->challenge->participante->full_name }}</td>
                                <td>{{ $paiement->challenge->challengeType->label }}</td>
                                <td>{{ $paiement->type->label() }}</td>
                                <td>{{ number_format((float) $paiement->amount, 2, ',', ' ') }} FCFA</td>
                                <td>{{ $paiement->payment_mode->label() }}</td>
                                <td>{{ $paiement->recu?->receipt_number ?? 'Non généré' }}</td>
                                <td class="text-center">
                                    <div class="form-button-action">
                                        <a href="{{ route('payments.show', $paiement) }}" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Voir">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @can('update', $paiement)
                                            <a href="{{ route('payments.edit', $paiement) }}" class="btn btn-link btn-warning btn-lg" data-bs-toggle="tooltip" title="Modifier">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="alert alert-info mb-0">Aucun paiement trouvé.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $paiements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#payments-table').DataTable({
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
    });
</script>
@endpush
