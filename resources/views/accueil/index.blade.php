@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Tableau de Bord')

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Tableau de Bord</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="{{ route('accueil') }}">
                <i class="icon-home"></i>
            </a>
        </li>
        {{-- Pas de séparateur ni d'autre item car c'est la page d'accueil --}}
    </ul>
</div>

{{-- Section des cartes de statistiques rapides --}}
{{-- Ces variables sont supposées être passées par AccueilController --}}
<div class="row">
    {{-- Carte Fournisseurs --}}
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-truck-loading"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Fournisseurs</p>
                            <h4 class="card-title">{{ $nombreFournisseurs ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carte Factures (par exemple, total des factures ou factures du mois) --}}
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Factures</p>
                            <h4 class="card-title">{{ $nombreFactures ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carte Chiffre d'Affaires (par exemple, du mois en cours) --}}
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-cash-register"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">CA (Mois en cours)</p>
                            <h4 class="card-title">{{ number_format($chiffreAffairesMoisCourant ?? 0, 0, ',', ' ') }} FCFA</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carte Articles en Alerte Stock --}}
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small"> {{-- icon-danger pour alerte --}}
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Articles Stock Faible</p>
                            <h4 class="card-title">{{ $articlesEnAlerteStock ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Section des graphiques --}}
<div class="row">
    {{-- Graphique des Ventes (par exemple, sur les 7 derniers jours ou mensuelles) --}}
    <div class="col-md-8">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Tendances des Ventes (7 derniers jours)</div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 350px">
                    <canvas id="ventesJournalieresChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphique des Articles par Catégorie (Donut) --}}
    <div class="col-md-4">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-title">Répartition des Articles par Catégorie</div>
            </div>
            <div class="card-body">
                 <div class="chart-container" style="height: 350px">
                    <canvas id="articlesParCategorieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Section pour d'autres informations, par exemple, dernières activités ou articles à stock faible --}}
<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">Articles Récemment Modifiés ou Ajoutés (Top 5)</h4>
                 <div class="card-category">
                    Suivi des dernières modifications dans le stock.
                  </div>
            </div>
            <div class="card-body">
                @if(isset($articlesRecents) && $articlesRecents->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom de l'article</th>
                                    <th>Catégorie</th>
                                    <th class="text-end">Quantité</th>
                                    <th class="text-center">Dernière MàJ</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($articlesRecents as $article)
                                <tr>
                                    <td>{{ $article->name }}</td>
                                    <td>{{ $article->categorie->name ?? 'N/A' }}</td>
                                    <td class="text-end fw-bold {{ $article->quantite <= ($seuilStockFaible ?? 5) ? 'text-danger' : '' }}">{{ $article->quantite }}</td>
                                    <td class="text-center">{{ $article->updated_at->diffForHumans() }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('articles.show', $article->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Voir Détails">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">Aucune activité récente sur les articles.</p>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
{{-- Chart.js est déjà inclus globalement via layouts.app.blade.php --}}
{{-- S'il ne l'est pas, décommentez la ligne ci-dessous ou ajoutez-la au layout principal --}}
{{-- <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script> --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour générer des couleurs pour les graphiques
    function generateColors(numColors) {
        const baseColors = ["#5793ff", "#ff6384", "#36a2eb", "#ffce56", "#4bc0c0", "#9966ff", "#ff9f40", "#E7E9ED"];
        let colors = [];
        for (let i = 0; i < numColors; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }
        return colors;
    }

    // Graphique des Ventes Journalières (Line Chart)
    // Vérifie si les variables Blade existent et sont des objets avant de les utiliser
    const ventesJournalieresCtx = document.getElementById('ventesJournalieresChart');
    const ventesJournalieresLabels = typeof @json($ventesJournalieres['labels'] ?? null) === 'object' ? @json($ventesJournalieres['labels'] ?? []) : [];
    const ventesJournalieresData = typeof @json($ventesJournalieres['data'] ?? null) === 'object' ? @json($ventesJournalieres['data'] ?? []) : [];

    if (ventesJournalieresCtx && ventesJournalieresLabels.length > 0 && ventesJournalieresData.length > 0) {
        new Chart(ventesJournalieresCtx, {
            type: 'line',
            data: {
                labels: ventesJournalieresLabels,
                datasets: [{
                    label: 'Ventes (FCFA)',
                    data: ventesJournalieresData,
                    borderColor: '#177dff', // Couleur primaire KaiAdmin
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
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    } else if(ventesJournalieresCtx) {
        const ctx = ventesJournalieresCtx.getContext('2d');
        ctx.textAlign = 'center'; ctx.textBaseline = 'middle'; ctx.font = '16px Public Sans';
        ctx.fillText('Pas de données de ventes récentes.', ventesJournalieresCtx.width / 2, ventesJournalieresCtx.height / 2);
    }


    // Graphique Articles par Catégorie (Doughnut Chart)
    const articlesParCategorieCtx = document.getElementById('articlesParCategorieChart');
    const articlesLabels = typeof @json($articlesParCategorieLabels ?? null) === 'object' ? @json($articlesParCategorieLabels ?? []) : [];
    const articlesData = typeof @json($articlesParCategorieData ?? null) === 'object' ? @json($articlesParCategorieData ?? []) : [];

    if (articlesParCategorieCtx && articlesLabels.length > 0 && articlesData.length > 0) {
         new Chart(articlesParCategorieCtx, {
            type: 'doughnut',
            data: {
                labels: articlesLabels,
                datasets: [{
                    label: 'Articles',
                    data: articlesData,
                    backgroundColor: generateColors(articlesLabels.length),
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
                         labels: { padding: 15, boxWidth: 12, font: {size: 10} }
                    },
                    tooltip: {
                         callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' article(s)';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } else if (articlesParCategorieCtx) {
        const ctx = articlesParCategorieCtx.getContext('2d');
        ctx.textAlign = 'center'; ctx.textBaseline = 'middle'; ctx.font = '16px Public Sans';
        ctx.fillText('Pas de données de catégorie.', articlesParCategorieCtx.width / 2, articlesParCategorieCtx.height / 2);
    }

    // Initialisation des tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
@endpush
