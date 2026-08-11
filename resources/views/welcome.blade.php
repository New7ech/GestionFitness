@extends('layouts.app')
@section('contenus')

<div class="page-inner">
  <!-- Header amélioré -->
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
      <h3 class="fw-bold mb-3">Tableau de Bord</h3>
      <h6 class="op-7 mb-2">Système de gestion intégré</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
      <a href="{{ url('articles') }}" class="btn btn-label-success btn-round me-2">
        <i class="fas fa-box"></i> Articles
      </a>
      <a href="{{ url('fournisseurs') }}" class="btn btn-label-info btn-round me-2">
        <i class="fas fa-truck"></i> Fournisseurs
      </a>
      <a href="{{ url('users') }}" class="btn btn-label-warning btn-round">
        <i class="fas fa-users"></i> Utilisateurs
      </a>
    </div>
  </div>

    <!-- Statistiques principales -->
    <div class="row">
      <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-primary bubble-shadow-small">
                  <i class="fas fa-file-invoice"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Total Factures</p>
                  <h4 class="card-title">{{ number_format($nombreFactures, 0, ',', ' ') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {{-- <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-info bubble-shadow-small">
                  <i class="fas fa-money-bill-wave"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Montant Total</p>
                  <h4 class="card-title">{{ number_format($montantTotal, 0, ',', ' ') }} FCFA</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> --}}
      <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-success bubble-shadow-small">
                  <i class="fas fa-check-circle"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Factures Payées</p>
                  <h4 class="card-title">{{ number_format($nombreFacturesPayees, 0, ',', ' ') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                  <i class="fas fa-times-circle"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Factures Impayées</p>
                  <h4 class="card-title">{{ number_format($nombreFacturesImpayees, 0, ',', ' ') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistiques supplémentaires -->
    <div class="row mt-4">
      <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-warning bubble-shadow-small">
                  <i class="fas fa-box"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Articles</p>
                  <h4 class="card-title">{{ number_format($nombreArticles, 0, ',', ' ') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-danger bubble-shadow-small">
                  <i class="fas fa-truck"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Fournisseurs</p>
                  <h4 class="card-title">{{ number_format($nombreFournisseurs, 0, ',', ' ') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-info bubble-shadow-small">
                  <i class="fas fa-money-bill-wave"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Montant Total</p>
                  <h4 class="card-title">{{ number_format($montantTotal, 0, ',', ' ') }} FCFA</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {{-- <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-info bubble-shadow-small">
                  <i class="fas fa-users"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Utilisateurs</p>
                  <h4 class="card-title">{{ number_format($nombreUtilisateurs, 0, ',', ' ') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> --}}
      {{-- <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-success bubble-shadow-small">
                  <i class="fas fa-tags"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Catégories</p>
                  <h4 class="card-title">{{ number_format($nombreCategories, 0, ',', ' ') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> --}}
    </div>
    <div class="row">
      <div class="col-md-8">
        <div class="card card-round">
          <div class="card-header">
            <div class="card-head-row">
              <div class="card-title">Statistiques des Factures par Mode de Paiement</div>
              <div class="card-tools">
                <a href="#" class="btn btn-label-success btn-round btn-sm me-2">
                  <span class="btn-label">
                    <i class="fa fa-pencil"></i>
                  </span>
                  Export
                </a>
                <a href="#" class="btn btn-label-info btn-round btn-sm">
                  <span class="btn-label">
                    <i class="fa fa-print"></i>
                  </span>
                  Print
                </a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-container" style="min-height: 375px">
              <canvas id="statisticsChart"></canvas>
            </div>
            <div id="myChartLegend"></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-primary card-round">
          <div class="card-header">
            <div class="card-head-row">
              <div class="card-title">Statistiques des Impayés</div>
              <div class="card-tools">
                <div class="dropdown">
                  <button class="btn btn-sm btn-label-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Export
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body pb-0">
            <div class="mb-4 mt-2">
              <h1>{{ number_format($montantImpayes, 0, ',', ' ') }} FCFA</h1>
            </div>
            <div class="pull-in">
              <canvas id="dailySalesChart"></canvas>
            </div>
          </div>
        </div>
        <div class="card card-round">
          <div class="card-body pb-0">
            <div class="h1 fw-bold float-end text-primary">+5%</div>
            <h2 class="mb-2">{{ number_format($nombreFacturesMoisCourant, 0, ',', ' ') }}</h2>
            <p class="text-muted">Factures ce mois-ci</p>
            <div class="pull-in sparkline-fix">
              <div id="lineChart"></div>
            </div>
           </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-round">
          <div class="card-header">
            <div class="card-head-row card-tools-still-right">
              <div class="card-title">Top 5 Fournisseurs Actifs</div>
              <div class="card-tools">
                <div class="dropdown">
                  <button class="btn btn-icon btn-clean me-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-container" style="position: relative; height: 300px;">
              <canvas id="fournisseursChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Graphiques et statistiques en horizontal - Optimisé pour grand écran -->
    <div class="row mt-4">
      <div class="col-lg-6 col-xl-8">
        <div class="card card-round">
          <div class="card-header">
            <div class="card-head-row">
              <div class="card-title">Évolution des Impayés</div>
              <div class="card-tools">
                <div class="dropdown">
                  <button class="btn btn-sm btn-label-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Export
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-container" style="position: relative; height: 450px;">
              <canvas id="dailySalesChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-xl-4">
        <div class="card card-round">
          <div class="card-header">
            <div class="card-head-row card-tools-still-right">
              <div class="card-title">Top 5 Fournisseurs Actifs</div>
              <div class="card-tools">
                <div class="dropdown">
                  <button class="btn btn-icon btn-clean me-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-container" style="position: relative; height: 450px;">
              <canvas id="fournisseursChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Deuxième ligne de graphiques - Optimisé pour grand écran -->
    <div class="row mt-4">
      <div class="col-lg-6">
        <div class="card card-round">
          <div class="card-header">
            <div class="card-head-row">
              <div class="card-title">Répartition des Modes de Paiement</div>
              <div class="card-tools">
                <div class="dropdown">
                  <button class="btn btn-sm btn-label-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Export
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-container" style="position: relative; height: 400px;">
              <canvas id="paiementChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card card-round">
          <div class="card-header">
            <div class="card-head-row">
              <div class="card-title">Top 5 Catégories d'Articles</div>
              <div class="card-tools">
                <div class="dropdown">
                  <button class="btn btn-sm btn-label-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Export
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-container" style="position: relative; height: 400px;">
              <canvas id="categoriesChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tableau des factures récentes -->
    <div class="row mt-4">
      <div class="col-md-12">
        <div class="card card-round">
          <div class="card-header">
            <div class="card-head-row card-tools-still-right">
              <div class="card-title">Factures Récentes</div>
              <div class="card-tools">
                <div class="dropdown">
                  <button class="btn btn-icon btn-clean me-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Numéro de Facture</th>
                    <th scope="col" class="text-end">Date</th>
                    <th scope="col" class="text-end">Montant</th>
                    <th scope="col" class="text-end">Statut</th>
                    <th scope="col" class="text-end">Mode de Paiement</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($facturesRecentes as $facture)
                  <tr>
                    <th scope="row">
                      <button class="btn btn-icon btn-round btn-{{ $facture->statut_paiement == 'payé' ? 'success' : 'danger' }} btn-sm me-2">
                        <i class="fa fa-{{ $facture->statut_paiement == 'payé' ? 'check' : 'times' }}"></i>
                      </button>
                      Facture #{{ $facture->id }}
                    </th>
                    <td class="text-end">{{ $facture->date_facture }}</td>
                    <td class="text-end">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end">
                      <span class="badge badge-{{ $facture->statut_paiement == 'payé' ? 'success' : 'danger' }}">{{ $facture->statut_paiement }}</span>
                    </td>
                    <td class="text-end">{{ $facture->mode_paiement }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Graphique des modes de paiement
  var paiementCtx = document.getElementById('paiementChart').getContext('2d');
  var paiementChart = new Chart(paiementCtx, {
    type: 'doughnut',
    data: {
      labels: @json($paiementModes->pluck('mode_paiement')),
      datasets: [{
        data: @json($paiementModes->pluck('total')),
        backgroundColor: [
          '#177dff',
          '#f5365c',
          '#fb6340',
          '#11cdef',
          '#2dce89'
        ],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  // Graphique des catégories
  var categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
  var categoriesChart = new Chart(categoriesCtx, {
    type: 'bar',
    data: {
      labels: @json($articlesParCategorie->pluck('category')),
      datasets: [{
        label: 'Nombre d\'articles',
        data: @json($articlesParCategorie->pluck('count')),
        backgroundColor: '#177dff',
        borderColor: '#177dff',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Graphique des fournisseurs
  var fournisseursCtx = document.getElementById('fournisseursChart').getContext('2d');
  var fournisseursChart = new Chart(fournisseursCtx, {
    type: 'bar',
    data: {
      labels: @json($fournisseursActifs->pluck('name')),
      datasets: [{
        label: 'Articles',
        data: @json($fournisseursActifs->pluck('articles_count')),
        backgroundColor: '#2dce89',
        borderColor: '#2dce89',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: 'y',
      scales: {
        x: {
          beginAtZero: true
        }
      }
    }
  });

  // Graphique d'évolution des impayés
  var ctxDailySales = document.getElementById('dailySalesChart').getContext('2d');
  var dailySalesChart = new Chart(ctxDailySales, {
    type: 'line',
    data: {
      labels: @json($labels),
      datasets: [{
        label: 'Évolution des Impayés',
        data: @json($data),
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 2,
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      },
      plugins: {
        legend: {
          display: true,
          position: 'top'
        }
      }
    }
  });
</script>
@endpush
@endsection
