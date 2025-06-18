{{-- @extends('layouts.app')

@section('title', 'Département ' . $departement->nom . ' - Observatoire des Collectivités')
@section('page-title', 'Département de ' . $departement->nom)

@section('content')
<div class="departement-dashboard">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('dashboard.index') }}">Tableau de bord</a>
        <span>/</span>
        <a href="{{ route('regions.show', $departement->region->id) }}">{{ $departement->region->nom }}</a>
        <span>/</span>
        <span>{{ $departement->nom }}</span>
    </div>

    <!-- Stats Cards -->
    <div class="dept-stats-grid">
        <div class="dept-stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-city"></i>
            </div>
            <div class="stat-content">
                <h4>Nombre de Communes</h4>
                <div class="stat-number">{{ $stats['nb_communes'] }}</div>
            </div>
        </div>

        <div class="dept-stat-card">
            <div class="stat-icon green">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h4>Taux Moyen de Réalisation</h4>
                <div class="stat-number">{{ number_format($stats['taux_moyen_realisation'], 2) }}%</div>
            </div>
        </div>

        <div class="dept-stat-card">
            <div class="stat-icon red">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h4>Total des Dettes</h4>
                <div class="stat-number">{{ number_format($stats['total_dettes'], 0) }} FCFA</div>
            </div>
        </div>

        <div class="dept-stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h4>Communes Conformes</h4>
                <div class="stat-number">{{ number_format($stats['communes_conformes'], 1) }}%</div>
            </div>
        </div>
    </div>

    <!-- Évolution des Performances -->
    <div class="chart-section">
        <div class="chart-container">
            <div class="chart-header">
                <h3>Évolution des Performances du Département</h3>
                <div class="chart-filters">
                    <select class="filter-select">
                        <option>5 dernières années</option>
                        <option>3 dernières années</option>
                        <option>Année courante</option>
                    </select>
                </div>
            </div>
            <div class="chart-content">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Détails des Communes -->
    <div class="communes-section">
        <div class="section-header">
            <h3>Détails des Communes du Département</h3>
            <div class="section-actions">
                <button class="btn btn-export">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Commune</th>
                        <th>Type</th>
                        <th>Population</th>
                        <th>Receveur</th>
                        <th>Ordonnateur</th>
                        <th>Dépôt {{ $annee }}</th>
                        <th>Prévision</th>
                        <th>Réalisation</th>
                        <th>Taux</th>
                        <th>Évaluation</th>
                        <th>Dettes</th>
                        <th>Défaillances</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($communes as $commune)
                    <tr>
                        <td>
                            <a href="{{ route('communes.show', $commune['id']) }}" class="commune-link">
                                {{ $commune['nom'] }}
                            </a>
                        </td>
                        <td>{{ $commune['type'] }}</td>
                        <td>{{ number_format($commune['population']) }}</td>
                        <td>{{ $commune['receveur'] ?? '-' }}</td>
                        <td>{{ $commune['ordonnateur'] ?? '-' }}</td>
                        <td>
                            @if($commune['depot_date'])
                                <span class="date-badge {{ $commune['depot_valide'] ? 'valid' : 'invalid' }}">
                                    {{ date('d/m/Y', strtotime($commune['depot_date'])) }}
                                </span>
                            @else
                                <span class="no-data">Non déposé</span>
                            @endif
                        </td>
                        <td>{{ number_format($commune['prevision'], 0) }} FCFA</td>
                        <td>{{ number_format($commune['realisation'], 0) }} FCFA</td>
                        <td>
                            <span class="taux-badge {{ $commune['taux_realisation'] >= 75 ? 'good' : ($commune['taux_realisation'] >= 50 ? 'medium' : 'bad') }}">
                                {{ number_format($commune['taux_realisation'], 1) }}%
                            </span>
                        </td>
                        <td>{{ $commune['evaluation'] }}</td>
                        <td>{{ number_format($commune['dettes_total'], 0) }} FCFA</td>
                        <td>
                            @if($commune['nb_defaillances'] > 0)
                                <span class="defaillance-count">{{ $commune['nb_defaillances'] }}</span>
                            @else
                                <span class="no-defaillance">0</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ strtolower(str_replace(' ', '-', $commune['status'])) }}">
                                {{ $commune['status'] }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('communes.show', $commune['id']) }}" class="btn-action view" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn-action edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action report" title="Générer rapport">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique d'évolution des performances
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionData = @json($evolutionPerformances);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: evolutionData.map(item => item.annee_exercice),
            datasets: [{
                label: 'Taux de Réalisation Moyen (%)',
                data: evolutionData.map(item => item.taux_moyen),
                borderColor: '#2E8B57',
                backgroundColor: 'rgba(46, 139, 87, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Évolution du Taux de Réalisation Moyen'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection --}}



@extends('layouts.app')

@section('title', 'Département ' . $departement->nom . ' - Observatoire des Collectivités')
@section('page-title', 'Département de ' . $departement->nom)

@section('content')
<div class="departement-dashboard">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('dashboard.index') }}">Tableau de bord</a>
        <span>/</span>
        <a href="{{ route('regions.show', $departement->region->id) }}">{{ $departement->region->nom }}</a>
        <span>/</span>
        <span>{{ $departement->nom }}</span>
    </div>

    <!-- Stats Cards -->
    <div class="dept-stats-grid">
        <div class="dept-stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-city"></i>
            </div>
            <div class="stat-content">
                <h4>Nombre de Communes</h4>
                <div class="stat-number">{{ $stats['nb_communes'] }}</div>
            </div>
        </div>

        <div class="dept-stat-card">
            <div class="stat-icon green">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h4>Taux Moyen de Réalisation</h4>
                <div class="stat-number">{{ number_format($stats['taux_moyen_realisation'], 2) }}%</div>
            </div>
        </div>

        <div class="dept-stat-card">
            <div class="stat-icon red">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h4>Total des Dettes</h4>
                <div class="stat-number">{{ number_format($stats['total_dettes'], 0) }} FCFA</div>
            </div>
        </div>

        <div class="dept-stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h4>Communes Conformes</h4>
                <div class="stat-number">{{ number_format($stats['communes_conformes'], 1) }}%</div>
            </div>
        </div>
    </div>

    <!-- Évolution des Performances -->
    <div class="chart-section">
        <div class="chart-container">
            <div class="chart-header">
                <h3>Évolution des Performances du Département</h3>
                <div class="chart-filters">
                    <select class="filter-select">
                        <option>5 dernières années</option>
                        <option>3 dernières années</option>
                        <option>Année courante</option>
                    </select>
                </div>
            </div>
            <div class="chart-content">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Détails des Communes -->
    <div class="communes-section">
        <div class="section-header">
            <h3>Détails des Communes du Département</h3>
            <div class="section-actions">
                <button class="btn btn-export">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Commune</th>
                        <th>Code</th>
                        <th>Téléphone</th>
                        <th>Receveur</th>
                        <th>Ordonnateur</th>
                        <th>Dépôt {{ $annee }}</th>
                        <th>Prévision</th>
                        <th>Réalisation</th>
                        <th>Taux</th>
                        <th>Évaluation</th>
                        <th>Dettes</th>
                        <th>Défaillances</th>
                        <th>Retards</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($communes as $commune)
                    <tr>
                        <td>
                            <a href="{{ route('communes.show', $commune['id']) }}" class="commune-link">
                                {{ $commune['nom'] }}
                            </a>
                        </td>
                        <td>
                            <span class="commune-code">{{ $commune['code'] }}</span>
                        </td>
                        <td>{{ $commune['telephone'] ?? '-' }}</td>
                        <td>{{ $commune['receveur'] ?? '-' }}</td>
                        <td>{{ $commune['ordonnateur'] ?? '-' }}</td>
                        <td>
                            @if($commune['depot_date'])
                                <span class="date-badge {{ $commune['depot_valide'] ? 'valid' : 'invalid' }}">
                                    {{ date('d/m/Y', strtotime($commune['depot_date'])) }}
                                </span>
                            @else
                                <span class="no-data">Non déposé</span>
                            @endif
                        </td>
                        <td>{{ number_format($commune['prevision'], 0) }} FCFA</td>
                        <td>{{ number_format($commune['realisation'], 0) }} FCFA</td>
                        <td>
                            <span class="taux-badge {{ $commune['taux_realisation'] >= 75 ? 'good' : ($commune['taux_realisation'] >= 50 ? 'medium' : 'bad') }}">
                                {{ number_format($commune['taux_realisation'], 1) }}%
                            </span>
                        </td>
                        <td>{{ $commune['evaluation'] }}</td>
                        <td>{{ number_format($commune['dettes_total'], 0) }} FCFA</td>
                        <td>
                            @if($commune['nb_defaillances'] > 0)
                                <span class="defaillance-count alert">{{ $commune['nb_defaillances'] }}</span>
                            @else
                                <span class="no-defaillance">0</span>
                            @endif
                        </td>
                        <td>
                            @if($commune['nb_retards'] > 0)
                                <span class="retard-count warning">{{ $commune['nb_retards'] }}</span>
                            @else
                                <span class="no-retard">0</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ strtolower(str_replace(' ', '-', $commune['status'])) }}">
                                {{ $commune['status'] }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('communes.show', $commune['id']) }}" class="btn-action view" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn-action edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action report" title="Générer rapport">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique d'évolution des performances
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionData = @json($evolutionPerformances);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: evolutionData.map(item => item.annee_exercice),
            datasets: [{
                label: 'Taux de Réalisation Moyen (%)',
                data: evolutionData.map(item => item.taux_moyen),
                borderColor: '#2E8B57',
                backgroundColor: 'rgba(46, 139, 87, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Évolution du Taux de Réalisation Moyen'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection