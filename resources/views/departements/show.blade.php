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

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --warning-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ffa8a8 100%);
    --info-gradient: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
    
    --shadow-light: 0 2px 15px rgba(0,0,0,0.08);
    --shadow-medium: 0 8px 30px rgba(0,0,0,0.12);
    --shadow-heavy: 0 15px 40px rgba(0,0,0,0.15);
    
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

.departement-dashboard {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    animation: fadeInUp 0.6s ease-out;
}

/* Breadcrumb moderne */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-light);
    margin-bottom: 2rem;
    font-size: 0.875rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.breadcrumb a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
}

.breadcrumb a:hover {
    background: rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

.breadcrumb span {
    color: #64748b;
    font-weight: 400;
}

/* Stats Cards modernes */
.dept-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.dept-stat-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--shadow-medium);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.dept-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
    opacity: 0;
    transition: var(--transition);
}

.dept-stat-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-heavy);
}

.dept-stat-card:hover::before {
    opacity: 1;
}

.dept-stat-card .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
    color: white;
    position: relative;
}

.dept-stat-card .stat-icon.blue {
    background: var(--info-gradient);
}

.dept-stat-card .stat-icon.green {
    background: var(--success-gradient);
}

.dept-stat-card .stat-icon.red {
    background: var(--danger-gradient);
}

.dept-stat-card .stat-icon.orange {
    background: var(--warning-gradient);
}

.dept-stat-card .stat-content h4 {
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.dept-stat-card .stat-number {
    font-size: 2.25rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
}

/* Section graphique moderne */
.chart-section {
    margin-bottom: 3rem;
}

.chart-container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-medium);
    border: 1px solid rgba(255, 255, 255, 0.3);
    overflow: hidden;
}

.chart-header {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 2rem 2rem 1rem 2rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.chart-header h3 {
    font-size: 1.375rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.chart-filters {
    display: flex;
    gap: 1rem;
}

.filter-select {
    padding: 0.5rem 1rem;
    border: 2px solid rgba(102, 126, 234, 0.2);
    border-radius: 10px;
    background: white;
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    transition: var(--transition);
    cursor: pointer;
}

.filter-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.chart-content {
    padding: 2rem;
}

/* Section communes moderne */
.communes-section {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-medium);
    border: 1px solid rgba(255, 255, 255, 0.3);
    overflow: hidden;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem 2rem 1rem 2rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.section-header h3 {
    font-size: 1.375rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.btn-export {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-export:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

/* Table moderne */
.table-container {
    overflow-x: auto;
    padding: 1rem;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.data-table th {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 1rem 0.75rem;
    text-align: left;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.75rem;
    border-bottom: 2px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 10;
}

.data-table td {
    padding: 1rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    transition: var(--transition);
}

.data-table tr:hover td {
    background: rgba(102, 126, 234, 0.02);
}

.commune-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
}

.commune-link:hover {
    color: #4338ca;
}

.commune-code {
    background: rgba(102, 126, 234, 0.1);
    color: #4338ca;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.75rem;
}

/* Badges modernes */
.date-badge, .taux-badge, .status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.75rem;
    text-align: center;
    display: inline-block;
    min-width: 70px;
}

.date-badge.valid {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.date-badge.invalid {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.taux-badge.good {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.taux-badge.medium {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.taux-badge.bad {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.no-data, .no-defaillance, .no-retard {
    color: #9ca3af;
    font-style: italic;
}

.defaillance-count.alert {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-weight: 700;
}

.retard-count.warning {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-weight: 700;
}

/* Actions buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.btn-action {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    text-decoration: none;
    font-size: 0.875rem;
}

.btn-action.view {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
}

.btn-action.view:hover {
    background: rgba(59, 130, 246, 0.2);
    transform: translateY(-2px);
}

.btn-action.edit {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
}

.btn-action.edit:hover {
    background: rgba(245, 158, 11, 0.2);
    transform: translateY(-2px);
}

.btn-action.report {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.btn-action.report:hover {
    background: rgba(16, 185, 129, 0.2);
    transform: translateY(-2px);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dept-stat-card {
    animation: fadeInUp 0.6s ease-out;
}

.dept-stat-card:nth-child(2) {
    animation-delay: 0.1s;
}

.dept-stat-card:nth-child(3) {
    animation-delay: 0.2s;
}

.dept-stat-card:nth-child(4) {
    animation-delay: 0.3s;
}

/* Responsive Design */
@media (max-width: 768px) {
    .departement-dashboard {
        padding: 1rem;
    }
    
    .dept-stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .chart-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .section-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .data-table {
        font-size: 0.75rem;
    }
    
    .data-table th,
    .data-table td {
        padding: 0.5rem 0.25rem;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .btn-action {
        width: 32px;
        height: 32px;
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .breadcrumb {
        flex-wrap: wrap;
        padding: 0.75rem 1rem;
    }
    
    .dept-stat-card {
        padding: 1.5rem;
    }
    
    .dept-stat-card .stat-number {
        font-size: 1.875rem;
    }
    
    .chart-header,
    .section-header {
        padding: 1.5rem 1rem 1rem 1rem;
    }
    
    .chart-content {
        padding: 1rem;
    }
}

/* Loading states */
.loading {
    position: relative;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid rgba(102, 126, 234, 0.3);
    border-top: 2px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Scrollbar styling */
.table-container::-webkit-scrollbar {
    height: 8px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush

@section('content')
<div class="departement-dashboard">
    <!-- Breadcrumb -->
    <div class="breadcrumb animate__animated animate__fadeInDown">
        <a href="{{ route('dashboard.index') }}">
            <i class="fas fa-home"></i> Tableau de bord
        </a>
        <span>/</span>
        <a href="{{ route('regions.show', $departement->region->id) }}">{{ $departement->region->nom }}</a>
        <span>/</span>
        <span>{{ $departement->nom }}</span>
    </div>

    <!-- Stats Cards -->
    <div class="dept-stats-grid">
        <div class="dept-stat-card animate__animated animate__fadeInUp">
            <div class="stat-icon blue">
                <i class="fas fa-city"></i>
            </div>
            <div class="stat-content">
                <h4>Nombre de Communes</h4>
                <div class="stat-number">{{ $stats['nb_communes'] }}</div>
            </div>
        </div>

        <div class="dept-stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="stat-icon green">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h4>Taux Moyen de Réalisation</h4>
                <div class="stat-number">{{ number_format($stats['taux_moyen_realisation'], 2) }}%</div>
            </div>
        </div>

        <div class="dept-stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="stat-icon red">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h4>Total des Dettes</h4>
                <div class="stat-number">{{ number_format($stats['total_dettes'], 0) }} FCFA</div>
            </div>
        </div>

        <div class="dept-stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
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
    <div class="chart-section animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
        <div class="chart-container">
            <div class="chart-header">
                <h3>
                    <i class="fas fa-chart-area" style="color: #667eea; margin-right: 0.5rem;"></i>
                    Évolution des Performances du Département
                </h3>
                <div class="chart-filters">
                    <select class="filter-select" id="periodFilter">
                        <option value="5">5 dernières années</option>
                        <option value="3">3 dernières années</option>
                        <option value="1">Année courante</option>
                    </select>
                </div>
            </div>
            <div class="chart-content">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Détails des Communes -->
    <div class="communes-section animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
        <div class="section-header">
            <h3>
                <i class="fas fa-table" style="color: #667eea; margin-right: 0.5rem;"></i>
                Détails des Communes du Département
            </h3>
            <div class="section-actions">
                <button class="btn-export" onclick="exportData()">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table" id="communesTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-city"></i> Commune</th>
                        <th><i class="fas fa-code"></i> Code</th>
                        <th><i class="fas fa-phone"></i> Téléphone</th>
                        <th><i class="fas fa-user-tie"></i> Receveur</th>
                        <th><i class="fas fa-user-cog"></i> Ordonnateur</th>
                        <th><i class="fas fa-calendar-check"></i> Dépôt {{ $annee }}</th>
                        <th><i class="fas fa-calculator"></i> Prévision</th>
                        <th><i class="fas fa-coins"></i> Réalisation</th>
                        <th><i class="fas fa-percentage"></i> Taux</th>
                        {{-- <th><i class="fas fa-star"></i> Évaluation</th> --}}
                        <th><i class="fas fa-credit-card"></i> Dettes</th>
                        <th><i class="fas fa-exclamation"></i> Défaillances</th>
                        <th><i class="fas fa-clock"></i> Retards</th>
                        <th><i class="fas fa-flag"></i> Status</th>
                        <th><i class="fas fa-tools"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($communes as $commune)
                    <tr class="animate__animated animate__fadeInUp" style="animation-delay: {{ 0.6 + $loop->index * 0.05 }}s;">
                        <td>
                            <a href="{{ route('communes.show', $commune['id']) }}" class="commune-link">
                                <i class="fas fa-building" style="margin-right: 0.5rem; color: #667eea;"></i>
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
                                    <i class="fas fa-calendar"></i>
                                    {{ date('d/m/Y', strtotime($commune['depot_date'])) }}
                                </span>
                            @else
                                <span class="no-data">
                                    <i class="fas fa-times"></i> Non déposé
                                </span>
                            @endif
                        </td>
                        <td>{{ number_format($commune['prevision'], 0) }} FCFA</td>
                        <td>{{ number_format($commune['realisation'], 0) }} FCFA</td>
                        <td>
                            <span class="taux-badge {{ $commune['taux_realisation'] >= 75 ? 'good' : ($commune['taux_realisation'] >= 50 ? 'medium' : 'bad') }}">
                                {{ number_format($commune['taux_realisation'], 1) }}%
                            </span>
                        </td>
                        {{-- <td>{{ $commune['evaluation'] }}</td> --}}
                        <td>{{ number_format($commune['dettes_total'], 0) }} FCFA</td>
                        <td>
                            @if($commune['nb_defaillances'] > 0)
                                <span class="defaillance-count alert">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $commune['nb_defaillances'] }}
                                </span>
                            @else
                                <span class="no-defaillance">
                                    <i class="fas fa-check"></i> 0
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($commune['nb_retards'] > 0)
                                <span class="retard-count warning">
                                    <i class="fas fa-clock"></i> {{ $commune['nb_retards'] }}
                                </span>
                            @else
                                <span class="no-retard">
                                    <i class="fas fa-check"></i> 0
                                </span>
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
                                <button class="btn-action edit" title="Modifier" onclick="editCommune({{ $commune['id'] }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action report" title="Générer rapport" onclick="generateReport({{ $commune['id'] }})">
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration du graphique moderne
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionData = @json($evolutionPerformances);
    
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: evolutionData.map(item => item.annee_exercice),
            datasets: [{
                label: 'Taux de Réalisation Moyen (%)',
                data: evolutionData.map(item => item.taux_moyen),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1e293b',
                    bodyColor: '#64748b',
                    borderColor: 'rgba(102, 126, 234, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            weight: 500
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)',
                    },
                    ticks: {
                        color: '#64748b',
                        callback: function(value) {
                            return value + '%';
                        },
                        font: {
                            weight: 500
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Gestionnaire de filtre de période
    const periodFilter = document.getElementById('periodFilter');
    periodFilter.addEventListener('change', function() {
        // Logique de filtrage ici
        console.log('Période sélectionnée:', this.value);
    });

    // Animations au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '50px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observer les éléments à animer
    document.querySelectorAll('.dept-stat-card, .chart-section, .communes-section').forEach(el => {
        observer.observe(el);
    });
});

// Fonctions utilitaires
function exportData() {
    // Logique d'export
    const table = document.getElementById('communesTable');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    
    let csvContent = 'Commune,Code,Téléphone,Receveur,Ordonnateur,Dépôt,Prévision,Réalisation,Taux,Évaluation,Dettes,Défaillances,Retards,Status\n';
    
    rows.forEach(row => {
        const cells = Array.from(row.cells);
        const rowData = cells.slice(0, -1).map(cell => {
            return '"' + cell.textContent.replace(/"/g, '""').trim() + '"';
        });
        csvContent += rowData.join(',') + '\n';
    });

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'communes_{{ $departement->nom }}.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function editCommune(id) {
    window.location.href = `/communes/${id}/edit`;
}

function generateReport(id) {
    window.open(`/communes/${id}/report`, '_blank');
}

// Recherche et filtrage
function initializeSearch() {
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Rechercher une commune...';
    searchInput.className = 'search-input';
    
    const sectionHeader = document.querySelector('.communes-section .section-header');
    sectionHeader.insertBefore(searchInput, sectionHeader.querySelector('.section-actions'));
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.data-table tbody tr');
        
        rows.forEach(row => {
            const communeName = row.querySelector('.commune-link').textContent.toLowerCase();
            if (communeName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

// Initialisation de la recherche après le chargement
document.addEventListener('DOMContentLoaded', initializeSearch);
</script>
@endpush