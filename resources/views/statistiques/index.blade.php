@extends('layouts.app')

@section('title', 'Statistiques Fitness')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Statistiques Fitness</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('statistiques.index') }}">Statistiques</a></li>
        </ul>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="numbers text-center">
                        <p class="card-category">Participantes actives</p>
                        <h4 class="card-title">{{ $totalParticipantesActives }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="numbers text-center">
                        <p class="card-category">Revenus (30 derniers jours)</p>
                        <h4 class="card-title">{{ number_format($totalRevenus30Jours, 0, ',', ' ') }} FCFA</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="numbers text-center">
                        <p class="card-category">Présences (30 jours)</p>
                        <h4 class="card-title">{{ $totalPresences30Jours }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="numbers text-center">
                        <p class="card-category">Mesures (30 jours)</p>
                        <h4 class="card-title">{{ $totalMesures30Jours }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card card-round">
                <div class="card-header">Répartition des challenges par type</div>
                <div class="card-body">
                    <div style="position: relative; height:350px; width:100%;">
                        <canvas id="challengesParTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-round">
                <div class="card-header">Tendances des paiements (30 derniers jours)</div>
                <div class="card-body">
                    <div style="position: relative; height:350px; width:100%;">
                        <canvas id="paiementsTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header">Top 5 types de challenge (inscriptions, 30 jours)</div>
                <div class="card-body">
                    <div style="position: relative; height:350px; width:100%;">
                        <canvas id="topChallengeTypesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header">Challenges avec solde impayé ou partiel</div>
                <div class="card-body">
                    @if($challengesImpayes->isEmpty())
                        <p class="text-center text-muted mb-0">Aucun challenge en attente de paiement.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Participante</th>
                                        <th>Type</th>
                                        <th>Début</th>
                                        <th>Prix</th>
                                        <th>Statut paiement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($challengesImpayes as $challenge)
                                    <tr>
                                        <td>{{ $challenge->participante?->full_name ?? '—' }}</td>
                                        <td>{{ $challenge->challengeType?->label ?? '—' }}</td>
                                        <td>{{ $challenge->start_date?->format('d/m/Y') ?? '—' }}</td>
                                        <td>{{ number_format($challenge->price, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            <span class="badge {{ $challenge->payment_status === \App\Enums\PaymentStatus::Impaye ? 'bg-danger' : 'bg-warning text-dark' }}">
                                                {{ $challenge->payment_status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function generateChartColors(numColors) {
        const baseColors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
            '#E7E9ED', '#707070', '#FFD700', '#ADFF2F', '#00FFFF', '#FF00FF'
        ];
        let colors = [];
        for (let i = 0; i < numColors; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }
        return colors;
    }

    const challengesParTypeLabels = @json($challengesParTypeLabels);
    const challengesParTypeData = @json($challengesParTypeData);
    const challengesParTypeCanvas = document.getElementById('challengesParTypeChart');

    if (challengesParTypeCanvas && challengesParTypeLabels.length > 0) {
        new Chart(challengesParTypeCanvas, {
            type: 'pie',
            data: {
                labels: challengesParTypeLabels,
                datasets: [{
                    label: 'Challenges',
                    data: challengesParTypeData,
                    backgroundColor: generateChartColors(challengesParTypeLabels.length),
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } }
            }
        });
    }

    const paiementsTrendLabels = @json($paiementsTrendLabels);
    const paiementsTrendData = @json($paiementsTrendData);
    const paiementsTrendCanvas = document.getElementById('paiementsTrendChart');

    if (paiementsTrendCanvas && paiementsTrendLabels.length > 0) {
        new Chart(paiementsTrendCanvas, {
            type: 'line',
            data: {
                labels: paiementsTrendLabels,
                datasets: [{
                    label: 'Paiements journaliers (FCFA)',
                    data: paiementsTrendData,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return value.toLocaleString('fr-FR') + ' FCFA'; }
                        }
                    }
                }
            }
        });
    }

    const topChallengeTypesLabels = @json($topChallengeTypesLabels);
    const topChallengeTypesData = @json($topChallengeTypesData);
    const topChallengeTypesCanvas = document.getElementById('topChallengeTypesChart');

    if (topChallengeTypesCanvas && topChallengeTypesLabels.length > 0) {
        new Chart(topChallengeTypesCanvas, {
            type: 'bar',
            data: {
                labels: topChallengeTypesLabels,
                datasets: [{
                    label: 'Inscriptions',
                    data: topChallengeTypesData,
                    backgroundColor: generateChartColors(topChallengeTypesLabels.length),
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
