@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('contenus')

<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Tableau de Bord Fitness</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="{{ route('accueil') }}">
                <i class="icon-home"></i>
            </a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Participantes actives</p>
                            <h4 class="card-title">{{ $nombreParticipantesActives ?? 0 }} <small class="text-muted">/ {{ $nombreParticipantes ?? 0 }}</small></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Challenges en cours</p>
                            <h4 class="card-title">{{ $nombreChallengesEnCours ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Revenus (mois en cours)</p>
                            <h4 class="card-title">{{ number_format($revenusMoisCourant ?? 0, 0, ',', ' ') }} FCFA</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Paiements en attente</p>
                            <h4 class="card-title">{{ $challengesImpayes ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Paiements encaissés (7 derniers jours)</div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 350px">
                    <canvas id="paiementsJournaliersChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-title">Challenges par type</div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 350px">
                    <canvas id="challengesParTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">Participantes récemment inscrites</h4>
            </div>
            <div class="card-body">
                @if($participantesRecentes->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Téléphone</th>
                                    <th>Inscription</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($participantesRecentes as $participante)
                                <tr>
                                    <td>{{ $participante->full_name }}</td>
                                    <td>{{ $participante->phone ?? '—' }}</td>
                                    <td>{{ $participante->registration_date?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('participantes.show', $participante) }}" class="btn btn-info btn-sm" title="Voir la fiche">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted mb-0">Aucune participante inscrite pour le moment.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">Challenges récents</h4>
                <div class="card-category">Derniers programmes ouverts</div>
            </div>
            <div class="card-body">
                @if($challengesRecents->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Participante</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($challengesRecents as $challenge)
                                <tr>
                                    <td>{{ $challenge->participante?->full_name ?? '—' }}</td>
                                    <td>{{ $challenge->challengeType?->label ?? '—' }}</td>
                                    <td><span class="badge bg-secondary">{{ $challenge->status->label() }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-info btn-sm" title="Voir le challenge">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted mb-0">Aucun challenge enregistré.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if($challengesImpayesList->isNotEmpty())
<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">Challenges avec solde impayé ou partiel</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Participante</th>
                                <th>Type</th>
                                <th>Prix</th>
                                <th>Statut paiement</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($challengesImpayesList as $challenge)
                            <tr>
                                <td>{{ $challenge->participante?->full_name ?? '—' }}</td>
                                <td>{{ $challenge->challengeType?->label ?? '—' }}</td>
                                <td>{{ number_format($challenge->price, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <span class="badge {{ $challenge->payment_status === \App\Enums\PaymentStatus::Impaye ? 'bg-danger' : 'bg-warning text-dark' }}">
                                        {{ $challenge->payment_status->label() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-warning btn-sm" title="Voir le challenge">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function generateColors(numColors) {
        const baseColors = ["#5793ff", "#ff6384", "#36a2eb", "#ffce56", "#4bc0c0", "#9966ff", "#ff9f40", "#E7E9ED"];
        let colors = [];
        for (let i = 0; i < numColors; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }
        return colors;
    }

    const paiementsCtx = document.getElementById('paiementsJournaliersChart');
    const paiementsLabels = @json($paiementsJournaliers['labels'] ?? []);
    const paiementsData = @json($paiementsJournaliers['data'] ?? []);

    if (paiementsCtx && paiementsLabels.length > 0) {
        new Chart(paiementsCtx, {
            type: 'line',
            data: {
                labels: paiementsLabels,
                datasets: [{
                    label: 'Paiements (FCFA)',
                    data: paiementsData,
                    borderColor: '#177dff',
                    backgroundColor: 'rgba(23, 125, 255, 0.2)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#177dff',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString('fr-FR') + ' FCFA';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return value.toLocaleString('fr-FR') + ' FCFA'; }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    } else if (paiementsCtx) {
        const ctx = paiementsCtx.getContext('2d');
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.font = '16px Public Sans';
        ctx.fillText('Pas de paiements récents.', paiementsCtx.width / 2, paiementsCtx.height / 2);
    }

    const challengesCtx = document.getElementById('challengesParTypeChart');
    const challengesLabels = @json($challengesParType['labels'] ?? []);
    const challengesData = @json($challengesParType['data'] ?? []);

    if (challengesCtx && challengesLabels.length > 0) {
        new Chart(challengesCtx, {
            type: 'doughnut',
            data: {
                labels: challengesLabels,
                datasets: [{
                    label: 'Challenges',
                    data: challengesData,
                    backgroundColor: generateColors(challengesLabels.length),
                    hoverOffset: 6,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15, boxWidth: 12, font: { size: 10 } }
                    }
                }
            }
        });
    } else if (challengesCtx) {
        const ctx = challengesCtx.getContext('2d');
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.font = '16px Public Sans';
        ctx.fillText('Pas de données par type.', challengesCtx.width / 2, challengesCtx.height / 2);
    }
});
</script>
@endpush
