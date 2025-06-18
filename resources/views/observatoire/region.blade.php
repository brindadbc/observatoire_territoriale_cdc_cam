{{-- @extends('layouts.app')

@section('title', 'Région ' . $region->nom . ' - Observatoire des Collectivités')
@section('page-title', 'OBSERVATOIRE DES COLLECTIVITÉS TERRITORIALES DÉCENTRALISÉES')

@section('content')
<div class="region-dashboard">
    <!-- Filtres et sélecteurs -->
    <div class="dashboard-filters">
        <div class="filter-row">
            <select class="filter-select" name="type">
                <option>Tous</option>
                <option>Urbain</option>
                <option>Rural</option>
            </select>
            <select class="filter-select" name="statut">
                <option>Statut</option>
                <option>Conforme</option>
                <option>Non conforme</option>
            </select>
            <select class="filter-select" name="annee">
                <option>{{ $annee }}</option>
                @for($i = date('Y'); $i >= 2018; $i--)
                    <option value="{{ $i }}" {{ $i == $annee ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="region-subtitle">
        Tableau de bord de suivi des départements et communes de la région {{ $region->nom }} de l'analyse
    </div>

    <!-- Stats Cards -->
    <div class="region-stats-grid">
        <div class="region-stat-card">
            <div class="stat-content">
                <h4>Nombre de Communes de la région</h4>
                <div class="stat-number">{{ $stats['nb_communes'] }}</div>
            </div>
        </div>

        <div class="region-stat-card success">
            <div class="stat-content">
                <h4>Taux Moyen de Réalisation</h4>
                <div class="stat-number">{{ number_format($stats['taux_moyen_realisation'], 2) }}%</div>
            </div>
        </div>

        <div class="region-stat-card danger">
            <div class="stat-content">
                <h4>Total Dettes CNPS</h4>
                <div class="stat-number">{{ number_format($stats['total_dettes_cnps'], 0) }} FCFA</div>
            </div>
        </div>

        <div class="region-stat-card warning">
            <div class="stat-content">
                <h4>Conformité des Dépôts</h4>
                <div class="stat-number">{{ number_format($stats['conformite_depots'], 1) }}%</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="region-charts-section">
        <div class="chart-container">
            <div class="chart-header">
                <h3>Budget Prévisions vs Réalisations (2023)</h3>
            </div>
            <div class="chart-content">
                <canvas id="budgetChart"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-header">
                <h3>Taux de Réalisation par département</h3>
            </div>
            <div class="chart-content">
                <canvas id="tauxRealisationChart"></canvas>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color green"></span>
                    <span>CONFORME (84,1%)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color yellow"></span>
                    <span>ACCEPTABLE</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color blue"></span>
                    <span>EN RETARD (12,1%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- État des Comptes et Conformités Table -->
    <div class="table-section">
        <div class="table-header">
            <h3>État des Comptes et Conformités</h3>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Commune</th>
                        <th>Receveur</th>
                        <th>Ordonnateur</th>
                        <th>Dépôt 2023</th>
                        <th>Prévision</th>
                        <th>Réalisation</th>
                        <th>Dettes CNPS</th>
                        <th>Dettes Fiscale</th>
                        <th>Dettes Salariale</th>
                        <th>Taux Réalisation</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($etatComptes as $compte)
                    <tr>
                        <td>{{ $compte['commune'] }}</td>
                        <td>{{ $compte['receveur'] ?? '-' }}</td>
                        <td>{{ $compte['ordonnateur'] ?? '-' }}</td>
                        <td>{{ $compte['depot_date'] ? date('d/m/Y', strtotime($compte['depot_date'])) : '-' }}</td>
                        <td>{{ number_format($compte['prevision'], 0) }} FCFA</td>
                        <td>{{ number_format($compte['realisation'], 0) }} FCFA</td>
                        <td>{{ number_format($compte['dette_cnps'], 0) }} FCFA</td>
                        <td>-</td>
                        <td>-</td>
                        <td>{{ number_format($compte['taux_realisation'], 1) }}%</td>
                        <td>
                            <span class="status-badge {{ strtolower(str_replace(' ', '-', $compte['status'])) }}">
                                {{ $compte['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="table-pagination">
            <div class="pagination-controls">
                <button class="pagination-btn">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="pagination-info">1 de 2</span>
                <button class="pagination-btn">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Défaillances et Problèmes de Conformité -->
    <div class="table-section">
        <div class="table-header">
            <h3>Défaillances et Problèmes de Conformité</h3>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Commune</th>
                        <th>Type de Défaillance</th>
                        <th>Date Constat</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($defaillances as $defaillance)
                    <tr>
                        <td>{{ $defaillance['commune'] }}</td>
                        <td>{{ $defaillance['type_defaillance'] }}</td>
                        <td>{{ date('d/m/Y', strtotime($defaillance['date_constat'])) }}</td>
                        <td>{{ $defaillance['description'] }}</td>
                        <td>
                            <span class="status-badge {{ $defaillance['status'] == 'Résolu' ? 'conforme' : 'non-resolu' }}">
                                {{ $defaillance['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Données pour les graphiques
const budgetData = @json($budgetData);
const tauxRealisationData = @json($tauxRealisationData);

// Configuration du graphique budget
const budgetCtx = document.getElementById('budgetChart').getContext('2d');
new Chart(budgetCtx, {
    type: 'bar',
    data: {
        labels: budgetData.map(item => item.departement),
        datasets: [
            {
                label: 'Prévisions',
                data: budgetData.map(item => item.previsions),
                backgroundColor: '#20B2AA',
                borderColor: '#20B2AA',
                borderWidth: 1
            },
            {
                label: 'Réalisations',
                data: budgetData.map(item => item.realisations),
                backgroundColor: '#90EE90',
                borderColor: '#90EE90',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + ' FCFA';
                    }
                }
            }
        }
    }
});

// Configuration du graphique taux de réalisation (donut)
const tauxCtx = document.getElementById('tauxRealisationChart').getContext('2d');
new Chart(tauxCtx, {
    type: 'doughnut',
    data: {
        labels: ['Conforme (84,1%)', 'Acceptable', 'En retard (12,1%)'],
        datasets: [{
            data: [84.1, 3.8, 12.1],
            backgroundColor: ['#90EE90', '#FFD700', '#87CEEB'],
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



{{-- resources/views/regions/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des Régions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Gestion des Régions</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Régions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Messages de succès/erreur --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">Liste des Régions</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('regions.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Nouvelle Région
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filtres --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('observatoire.region') }}">
                                <div class="input-group">
                                    <select class="form-select" name="annee" onchange="this.form.submit()">
                                        @for($year = date('Y'); $year >= 2020; $year--)
                                            <option value="{{ $year }}" {{ $annee == $year ? 'selected' : '' }}>
                                                Année {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Tableau des régions --}}
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap w-100" id="regionsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Région</th>
                                    <th>Départements</th>
                                    <th>Communes</th>
                                    <th>Taux Moyen (%)</th>
                                    <th>Dettes CNPS</th>
                                    <th>Conformité (%)</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($regions as $region)
                                    <tr>
                                        <td>
                                            <strong>{{ $region['nom'] }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $region['nb_departements'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $region['nb_communes'] }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    @php
                                                        $taux = $region['taux_moyen_realisation'];
                                                        $color = $taux >= 85 ? 'success' : ($taux >= 70 ? 'warning' : 'danger');
                                                    @endphp
                                                    <div class="progress" style="width: 60px; height: 8px;">
                                                        <div class="progress-bar bg-{{ $color }}" 
                                                             style="width: {{ min($taux, 100) }}%"></div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <strong>{{ number_format($taux, 1) }}%</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($region['total_dettes_cnps'] > 0)
                                                <span class="text-danger">
                                                    {{ number_format($region['total_dettes_cnps'], 0, ',', ' ') }} FCFA
                                                </span>
                                            @else
                                                <span class="text-success">Aucune dette</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $conformite = $region['conformite_depots'];
                                                $badgeClass = $conformite >= 80 ? 'success' : ($conformite >= 60 ? 'warning' : 'danger');
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">
                                                {{ number_format($conformite, 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $status = $region['status'];
                                                $statusClass = match($status) {
                                                    'Excellent' => 'success',
                                                    'Bon' => 'primary',
                                                    'Moyen' => 'warning',
                                                    default => 'danger'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ $status }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('regions.show', $region['id']) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Voir les détails">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a href="{{ route('regions.edit', $region['id']) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Modifier">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDelete({{ $region['id'] }}, '{{ $region['nom'] }}')"
                                                        title="Supprimer">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="bx bx-map-alt font-size-48 text-muted mb-3"></i>
                                                <h5 class="text-muted">Aucune région trouvée</h5>
                                                <p class="text-muted">Commencez par créer votre première région.</p>
                                                <a href="{{ route('regions.create') }}" class="btn btn-primary">
                                                    <i class="bx bx-plus me-1"></i> Créer une région
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques globales --}}
    @if($regions->count() > 0)
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Total Régions</p>
                                <h4 class="mb-2">{{ $regions->count() }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="bx bx-map-alt font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Total Départements</p>
                                <h4 class="mb-2">{{ $regions->sum('nb_departements') }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-info mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-info">
                                        <i class="bx bx-buildings font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Total Communes</p>
                                <h4 class="mb-2">{{ $regions->sum('nb_communes') }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-warning mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-warning">
                                        <i class="bx bx-home font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Taux Moyen National</p>
                                <h4 class="mb-2">{{ number_format($regions->avg('taux_moyen_realisation'), 1) }}%</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-success mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-success">
                                        <i class="bx bx-trending-up font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Modal de confirmation de suppression --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la région <strong id="regionName"></strong> ?</p>
                <p class="text-warning">
                    <i class="bx bx-warning me-1"></i>
                    Cette action est irréversible et ne peut être effectuée que si la région ne contient aucun département.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

    // Données pour les graphiques
const budgetData = @json($budgetData);
const tauxRealisationData = @json($tauxRealisationData);

// Configuration du graphique budget
const budgetCtx = document.getElementById('budgetChart').getContext('2d');
new Chart(budgetCtx, {
    type: 'bar',
    data: {
        labels: budgetData.map(item => item.departement),
        datasets: [
            {
                label: 'Prévisions',
                data: budgetData.map(item => item.previsions),
                backgroundColor: '#20B2AA',
                borderColor: '#20B2AA',
                borderWidth: 1
            },
            {
                label: 'Réalisations',
                data: budgetData.map(item => item.realisations),
                backgroundColor: '#90EE90',
                borderColor: '#90EE90',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                    }
                }
            }
        },
        plugins: {
            legend: {
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + 
                               new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                    }
                }
            }
        }
    }
});

// Configuration du graphique taux de réalisation (donut)
const tauxCtx = document.getElementById('tauxRealisationChart').getContext('2d');

// Calcul des pourcentages de conformité
const conformeCount = tauxRealisationData.filter(item => item.taux >= 90).length;
const moyenCount = tauxRealisationData.filter(item => item.taux >= 75 && item.taux < 90).length;
const nonConformeCount = tauxRealisationData.filter(item => item.taux < 75).length;
const total = tauxRealisationData.length;

const conformePct = total > 0 ? (conformeCount / total * 100).toFixed(1) : 0;
const moyenPct = total > 0 ? (moyenCount / total * 100).toFixed(1) : 0;
const nonConformePct = total > 0 ? (nonConformeCount / total * 100).toFixed(1) : 0;

new Chart(tauxCtx, {
    type: 'doughnut',
    data: {
        labels: [
            `Conforme (${conformePct}%)`, 
            `Moyen (${moyenPct}%)`, 
            `Non conforme (${nonConformePct}%)`
        ],
        datasets: [{
            data: [conformeCount, moyenCount, nonConformeCount],
            backgroundColor: ['#90EE90', '#FFD700', '#FF6B6B'],
            borderWidth: 2,
            borderColor: '#fff'
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
                callbacks: {
                    label: function(context) {
                        const label = context.label;
                        const value = context.parsed;
                        return `${label}: ${value} département${value > 1 ? 's' : ''}`;
                    }
                }
            }
        }
    }
});

// Gestion des filtres
document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('.filter-select');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Logique de filtrage à implémenter
            console.log(`Filtre ${this.name} changé: ${this.value}`);
        });
    });
    
    // Gestion des actions sur les défaillances
    const actionButtons = document.querySelectorAll('.btn-action');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.classList.contains('view') ? 'view' : 'resolve';
            console.log(`Action ${action} sur défaillance`);
        });
    });
});



    // Initialisation DataTable
    $(document).ready(function() {
        $('#regionsTable').DataTable({
            "pageLength": 10,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            },
            "order": [[0, "asc"]]
        });
    });

    // Fonction de confirmation de suppression
    function confirmDelete(regionId, regionName) {
        document.getElementById('regionName').textContent = regionName;
        document.getElementById('deleteForm').action = `/regions/${regionId}`;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

</script> 

<style>
.commune-code {
    font-family: 'Courier New', monospace;
    font-weight: bold;
    color: #666;
}

.commune-link {
    color: #007bff;
    text-decoration: none;
}

.commune-link:hover {
    text-decoration: underline;
}

.depot-date {
    font-weight: 500;
}

.taux-container {
    min-width: 120px;
}

.taux-bar {
    width: 100%;
    height: 4px;
    background-color: #e9ecef;
    border-radius: 2px;
    margin-top: 4px;
}

.taux-progress {
    height: 100%;
    background: linear-gradient(90deg, #ff6b6b 0%, #ffd93d 50%, #6bcf7f 100%);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.defaillance-type {
    padding: 2px 8px;
    background-color: #f8f9fa;
    border-radius: 12px;
    font-size: 0.85em;
}

.gravite-badge {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: 500;
}

.gravite-badge.grave {
    background-color: #fee;
    color: #dc3545;
}

.gravite-badge.normale {
    background-color: #e7f3ff;
    color: #0056b3;
}

.description-cell {
    max-width: 200px;
}

.description-text {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.action-buttons {
    display: flex;
    gap: 4px;
}

.btn-action {
    background: none;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9em;
}

.btn-action.view {
    color: #007bff;
}

.btn-action.edit {
    color: #28a745;
}

.btn-action:hover {
    background-color: rgba(0,0,0,0.1);
}

.departements-summary {
    margin-top: 2rem;
}

.departements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.departement-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    background-color: #fff;
}

.departement-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.departement-header h4 {
    margin: 0;
    color: #495057;
}

.communes-count {
    font-size: 0.85em;
    color: #6c757d;
}

.departement-stats {
    display: flex;
    gap: 1rem;
    margin: 1rem 0;
}

.stat-item {
    display: flex;
    flex-direction: column;
}

.stat-label {
    font-size: 0.8em;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.stat-value {
    font-weight: 600;
    color: #495057;
}

.btn.btn-outline {
    background: none;
    border: 1px solid #007bff;
    color: #007bff;
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.875rem;
}

.btn.btn-outline:hover {
    background-color: #007bff;
    color: white;
}
</style>

@endpush





