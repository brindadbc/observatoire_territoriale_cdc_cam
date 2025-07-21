{{-- @extends('layouts.app')

@section('title', 'Tableau de Bord - Observatoire des Collectivités')
@section('page-title', 'Tableau de Bord')

@section('content')
<div class="dashboard-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <h3>Masse budgetaire</h3>
                <div class="stat-icon yellow">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="stat-value">
                {{ number_format($stats['total_depots'], 0) }} FCFA
            </div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                12,8% depuis 2018
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3>Communes Enregistrées</h3>
                <div class="stat-icon green">
                    <i class="fas fa-city"></i>
                </div>
            </div>
            <div class="stat-value">
                {{ $stats['communes_enregistrees'] }}
            </div>
            <div class="stat-change positive">
                <i class="fas fa-plus"></i>
                5 nouvelles en 2024
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3>Départements</h3>
                <div class="stat-icon blue">
                    <i class="fas fa-map"></i>
                </div>
            </div>
            <div class="stat-value">
                {{ $stats['departements'] }}
            </div>
            <div class="stat-subtitle">
                Données complètes
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3>Dette Moyenne CNPS</h3>
                <div class="stat-icon red">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
            <div class="stat-value">
                {{ number_format($stats['dette_moyenne_cnps'], 0) }} FCFA
            </div>
            <div class="stat-change negative">
                <i class="fas fa-arrow-down"></i>
                8,3% depuis 2023
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="chart-container">
            <div class="chart-header">
                <h3>Évolution des dépots de compte (depuis 2018)</h3>
                <div class="chart-filters">
                    <button class="filter-btn active">Annuel</button>
                </div>
            </div>
            <div class="chart-content">
                <canvas id="evolutionChart"></canvas>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color teal"></span>
                    <span>depots de compte</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color yellow"></span>
                    <span>Prévisions</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color red"></span>
                    <span>Dettes cnps</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color orange"></span>
                    <span>Dettes fiscale</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color purple"></span>
                    <span>Dettes feicom</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color pink"></span>
                    <span>Dettes salariale</span>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-header">
                <h3>Répartition par Catégorie</h3>
                <div class="chart-filters">
                    <button class="filter-btn active"></button>
                </div>
            </div>
            <div class="chart-content">
                <canvas id="repartitionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="map-section">
        <div class="map-header">
            <h3>Carte des dépots par Région</h3>
            <div class="map-filters">
                <button class="filter-btn active"></button>
            </div>
        </div>
        <div class="map-container">
            <div class="map-placeholder">
                <img src="{{ asset('images/cameroon-map.png') }}" alt="Carte du Cameroun" class="cameroon-map">
                <!-- Les régions seront ajoutées dynamiquement avec JavaScript -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Données pour les graphiques
const evolutionData = @json($stats['evolution_depots']);
const repartitionData = @json($stats['repartition_categories']);
const regionsData = @json($regions);

// Configuration du graphique d'évolution
const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
new Chart(evolutionCtx, {
    type: 'bar',
    data: {
        labels: evolutionData.map(item => item.annee),
        datasets: [{
            label: 'Dépôts de compte',
            data: evolutionData.map(item => item.total),
            backgroundColor: '#20B2AA',
            borderColor: '#20B2AA',
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

// Configuration du graphique de répartition
const repartitionCtx = document.getElementById('repartitionChart').getContext('2d');
new Chart(repartitionCtx, {
    type: 'doughnut',
    data: {
        labels: ['Dépôts compte', 'Prévisions', 'Dettes CNPS', 'Dettes fiscale', 'Dettes feicom', 'Dettes salariale'],
        datasets: [{
            data: [
                repartitionData.depots_compte,
                repartitionData.previsions,
                repartitionData.dettes_cnps,
                repartitionData.dettes_fiscale,
                repartitionData.dettes_feicom,
                repartitionData.dettes_salariale
            ],
            backgroundColor: [
                '#20B2AA',
                '#FFD700',
                '#FF6B6B',
                '#FFA500',
                '#8A2BE2',
                '#FF69B4'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endpush
@endsection --}}







@extends('layouts.app')

@section('title', 'Vue d\'ensemble - Observatoire des Collectivités Territoriales')
@section('page-title', 'Vue d\'ensemble')

@push('styles')
<style>
    /* Styles spécifiques au dashboard */
    .stats-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 25px;
        box-shadow: var(--shadow);
        border: none;
        height: 100%;
        transition: var(--transition);
    }

    .stats-card:hover {
        box-shadow: var(--shadow-hover);
        transform: translateY(-2px);
    }

    .stats-card .icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }

    .stats-card.primary .icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .stats-card.success .icon {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .stats-card.warning .icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .stats-card.danger .icon {
        background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%);
        color: white;
    }

    .stats-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 5px;
    }

    .stats-label {
        color: var(--text-muted);
        font-size: 14px;
        font-weight: 500;
    }

    .stats-change {
        font-size: 12px;
        margin-top: 10px;
    }

    .stats-change.positive {
        color: var(--success-color);
    }

    .stats-change.negative {
        color: var(--danger-color);
    }

    .chart-container {
        background: white;
        border-radius: var(--border-radius);
        padding: 25px;
        box-shadow: var(--shadow);
        margin-bottom: 30px;
        height: 400px;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }

    .chart-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--dark-color);
    }

    .alert-custom {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: var(--shadow);
        margin-bottom: 15px;
        border-left: 4px solid;
    }

    .alert-custom.alert-danger {
        border-left-color: var(--danger-color);
        background: rgba(220, 53, 69, 0.1);
    }

    .alert-custom.alert-warning {
        border-left-color: var(--warning-color);
        background: rgba(255, 193, 7, 0.1);
    }

    .alert-custom.alert-info {
        border-left-color: var(--info-color);
        background: rgba(23, 162, 184, 0.1);
    }

    .table-custom {
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--shadow);
    }

    .table-custom th {
        background: var(--primary-color);
        color: white;
        font-weight: 600;
        border: none;
        padding: 15px;
    }

    .table-custom td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
    }

    .badge-custom {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .section-title {
        font-size: 22px;
        font-weight: 600;
        color: var(--dark-color);
    }

    @media (max-width: 768px) {
        .stats-value {
            font-size: 2rem;
        }
        
        .chart-container {
            height: 300px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête avec période -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Tableau de bord des collectivités territoriales décentralisées</p>
                </div>
                <div>
                    <select class="form-select" id="periodeSelect">
                        <option value="2024">Année 2024</option>
                        <option value="2023">Année 2023</option>
                        <option value="q1-2024">Q1 2024</option>
                        <option value="q2-2024">Q2 2024</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card primary">
                <div class="icon">
                    <i class="fas fa-city"></i>
                </div>
                <div class="stats-value">{{ $stats['total_communes'] }}</div>
                <div class="stats-label">Communes actives</div>
                <div class="stats-change positive">
                    <i class="fas fa-arrow-up"></i> +2 depuis 2023
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card success">
                <div class="icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stats-value">{{ number_format($stats['budget_total'], 1) }}</div>
                <div class="stats-label">Budget total (Mds FCFA)</div>
                <div class="stats-change positive">
                    <i class="fas fa-arrow-up"></i> +3.9% vs 2023
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card warning">
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stats-value">{{ $stats['taux_execution'] }}%</div>
                <div class="stats-label">Taux d'exécution moyen</div>
                <div class="stats-change positive">
                    <i class="fas fa-arrow-up"></i> +2.3 points
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card danger">
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stats-value">{{ $stats['communes_defaillantes'] }}</div>
                <div class="stats-label">Communes défaillantes</div>
                <div class="stats-change negative">
                    <i class="fas fa-arrow-down"></i> -5 depuis Q1
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques principaux -->
    <div class="row mb-4">
        <!-- Evolution du budget -->
        <div class="col-lg-8 mb-4">
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Évolution budgétaire (2019-2024)</h3>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active">Budget</button>
                        <button type="button" class="btn btn-outline-primary">Exécution</button>
                    </div>
                </div>
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
        <!-- Répartition des ressources -->
        <div class="col-lg-4 mb-4">
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Répartition des ressources</h3>
                </div>
                <canvas id="repartitionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Performance régionale -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-container" style="height: 500px;">
                <div class="chart-header">
                    <h3 class="chart-title">Performance par région</h3>
                    <button class="btn btn-outline-primary btn-sm" onclick="exportData('pdf')">
                        <i class="fas fa-download"></i> Exporter
                    </button>
                </div>
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Alertes et activités -->
    <div class="row">
        <!-- Alertes -->
        <div class="col-lg-6 mb-4">
            <div class="section-header">
                <h3 class="section-title">Alertes récentes</h3>
                {{-- <a href="{{ route('alertes.index') }}" class="btn btn-outline-primary btn-sm">Voir tout</a> --}}
            </div>
            
            @foreach($alertes as $alerte)
            <div class="alert alert-custom alert-{{ $alerte['type'] }}">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        @if($alerte['type'] == 'danger')
                            <i class="fas fa-exclamation-circle"></i>
                        @elseif($alerte['type'] == 'warning')
                            <i class="fas fa-exclamation-triangle"></i>
                        @else
                            <i class="fas fa-info-circle"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">{{ $alerte['titre'] }}</h6>
                        <p class="mb-1">{{ $alerte['message'] }}</p>
                        <small class="text-muted">{{ $alerte['date']->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Top communes -->
        <div class="col-lg-6 mb-4">
            <div class="section-header">
                <h3 class="section-title">Top communes performantes</h3>
                {{-- <a href="{{ route('rapports-performance.index') }}" class="btn btn-outline-primary btn-sm">Classement complet</a> --}}
            </div>

            <div class="table-custom">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Commune</th>
                            <th>Budget</th>
                            <th>Exécution</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCommunes as $commune)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $commune['nom'] }}</strong>
                                    <br><small class="text-muted">{{ $commune['region'] }}</small>
                                </div>
                            </td>
                            <td>{{ $commune['budget'] }} FCFA</td>
                            <td>
                                <span class="badge bg-success badge-custom">{{ $commune['taux_execution'] }}%</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">{{ $commune['score_gouvernance'] }}/10</div>
                                    <div class="progress" style="width: 60px; height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ $commune['score_gouvernance'] * 10 }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Configuration Chart.js
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.color = '#6c757d';

    // Données depuis le contrôleur
    const evolutionData = @json($evolutionBudget);
    const repartitionData = @json($repartitionRessources);
    const budgetRegionData = @json($budgetParRegion);

    // Graphique d'évolution budgétaire
    const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionChart = new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: evolutionData.map(item => item.annee),
            datasets: [{
                label: 'Budget (Mds FCFA)',
                data: evolutionData.map(item => item.budget),
                borderColor: '#2c5282',
                backgroundColor: 'rgba(44, 82, 130, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Exécution (Mds FCFA)',
                data: evolutionData.map(item => item.execution),
                borderColor: '#4299e1',
                backgroundColor: 'rgba(66, 153, 225, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Graphique de répartition des ressources
    const repartitionCtx = document.getElementById('repartitionChart').getContext('2d');
    const repartitionChart = new Chart(repartitionCtx, {
        type: 'doughnut',
        data: {
            labels: repartitionData.map(item => item.type),
            datasets: [{
                data: repartitionData.map(item => item.pourcentage),
                backgroundColor: [
                    '#2c5282',
                    '#4299e1',
                    '#28a745',
                    '#ffc107'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Graphique de performance par région
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    const performanceChart = new Chart(performanceCtx, {
        type: 'bar',
        data: {
            labels: budgetRegionData.map(item => item.region),
            datasets: [{
                label: 'Budget (Mds FCFA)',
                data: budgetRegionData.map(item => item.budget),
                backgroundColor: 'rgba(44, 82, 130, 0.8)',
                yAxisID: 'y'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Budget (Mds FCFA)'
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });

    // Gestionnaire pour le changement de période
    document.getElementById('periodeSelect').addEventListener('change', function(e) {
        const periode = e.target.value;
        
        // Appel AJAX pour récupérer les nouvelles données
        fetch(`/dashboard/periode/${periode}`)
            .then(response => response.json())
            .then(data => {
                // Mettre à jour les graphiques avec les nouvelles données
                evolutionChart.data.datasets[0].data = data.evolution.map(item => item.budget);
                evolutionChart.data.datasets[1].data = data.evolution.map(item => item.execution);
                evolutionChart.update();
                
                // Mettre à jour les statistiques
                document.querySelector('.stats-card.success .stats-value').textContent = data.budget_total;
                document.querySelector('.stats-card.warning .stats-value').textContent = data.taux_execution + '%';
            })
            .catch(error => console.error('Erreur:', error));
    });

    // Animation au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100);
            }, index * 150);
        });
    });

    // Fonction pour exporter les données
    function exportData(format) {
        window.location.href = `/dashboard/export/${format}`;
    }
</script>
@endpush