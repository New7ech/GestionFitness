@extends('layouts.app')
@section('contenus')
    <div class="container">
        <div class="page-inner">
            <h3 class="fw-bold mb-3">STATISTIQUES DES VENTES..</h3>
            <div class="row">
                <!-- Diagramme circulaire des statuts de paiement -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Répartition des statuts de paiement</div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="doughnutChart" style="width: 100%; height: 300px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphique à barres du CA mensuel -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Chiffre d'affaires mensuel TTC</div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="multipleBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courbe d'évolution du CA -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Évolution du chiffre d'affaires</div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="lineChart"></canvas>
                            </div>
                            <div id="myChartLegend"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts Chart.js -->
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>
    <script>
        // Donut Chart (Statuts de paiement)
        var doughnutChart = document.getElementById('doughnutChart').getContext('2d');
        new Chart(doughnutChart, {
            type: 'doughnut',
            data: {
                labels: @json($paymentStatus->pluck('statut_paiement')),
                datasets: [{
                    data: @json($paymentStatus->pluck('count')),
                    backgroundColor: ['#59d05d', '#f3545d', '#fdaf4b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Bar Chart (CA mensuel)
        var barChart = document.getElementById('multipleBarChart').getContext('2d');
        new Chart(barChart, {
            type: 'bar',
            data: {
                labels: @json($sales->pluck('mois')),
                datasets: [{
                    label: "Chiffre d'affaires TTC",
                    data: @json($sales->pluck('total_ttc')),
                    backgroundColor: '#177dff',
                    borderColor: '#177dff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Line Chart (Évolution CA)
        var lineChart = document.getElementById('lineChart').getContext('2d');
        new Chart(lineChart, {
            type: 'line',
            data: {
                labels: @json($sales->pluck('mois')),
                datasets: [{
                    label: "CA TTC",
                    data: @json($sales->pluck('total_ttc')),
                    borderColor: '#177dff',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
@endsection
