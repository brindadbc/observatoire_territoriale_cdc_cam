 @extends('layouts.app')

@section('title', 'Région ' . $region->nom . ' - Observatoire des Collectivités')
@section('page-title', 'OBSERVATOIRE DES COLLECTIVITÉS TERRITORIALES DÉCENTRALISÉES')

@section('content')
<div class="region-dashboard" >
    <!-- Filtres et sélecteurs -->
    
    <div class="dashboard-filters">
        <div class="filter-row">
            <select class="filter-select" name="departement">
                <option>Tous les départements</option>
                @foreach($region->departements as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->nom }}</option>
                @endforeach
            </select>
            <select class="filter-select" name="statut">
                <option>Statut</option>
                <option>Conforme</option>
                <option>Moyen</option>
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
                <h3>Budget Prévisions vs Réalisations ({{ $annee }})</h3>
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
                    <span>CONFORME (≥90%)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color yellow"></span>
                    <span>MOYEN (75-89%)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color red"></span>
                    <span>NON CONFORME (<75%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- État des Comptes et Conformités Table -->
    <div class="table-section">
        <div class="table-header">
            <h3>État des Comptes et Conformités</h3>
            <div class="table-actions">
                <button class="btn btn-primary">
                    <i class="fas fa-download"></i> Exporter
                </button>
            </div>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Commune</th>
                        <th>Département</th>
                        <th>Téléphone</th>
                        <th>Receveur</th>
                        <th>Ordonnateur</th>
                        <th>Dépôt {{ $annee }}</th>
                        <th>Prévision</th>
                        <th>Réalisation</th>
                        <th>Taux Réalisation</th>
                        <th>Dettes CNPS</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($etatComptes as $compte)
                    <tr>
                        <td><span class="commune-code">{{ $compte['code'] ?? '-' }}</span></td>
                        <td>
                            <a href="{{ route('communes.show', $compte['id']) }}" class="commune-link">
                                {{ $compte['commune'] }}
                            </a>
                        </td>
                        <td>{{ $compte['departement'] ?? '-' }}</td>
                        <td>{{ $compte['telephone'] ?? '-' }}</td>
                        <td>{{ $compte['receveur'] ?? '-' }}</td>
                        <td>{{ $compte['ordonnateur'] ?? '-' }}</td>
                        <td>
                            @if($compte['depot_date'])
                                <span class="depot-date">{{ date('d/m/Y', strtotime($compte['depot_date'])) }}</span>
                                @if($compte['depot_valide'])
                                    <i class="fas fa-check-circle text-success" title="Validé"></i>
                                @else
                                    <i class="fas fa-clock text-warning" title="En attente"></i>
                                @endif
                            @else
                                <span class="text-muted">Non déposé</span>
                            @endif
                        </td>
                        <td>{{ number_format($compte['prevision'] ?? 0, 0) }} FCFA</td>
                        <td>{{ number_format($compte['realisation'] ?? 0, 0) }} FCFA</td>
                        <td>
                            <div class="taux-container">
                                <span class="taux-value">{{ number_format($compte['taux_realisation'] ?? 0, 1) }}%</span>
                                <div class="taux-bar">
                                    <div class="taux-progress" style="width: {{ min($compte['taux_realisation'] ?? 0, 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td>{{ number_format($compte['dette_cnps'] ?? 0, 0) }} FCFA</td>
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
            <div class="pagination-info">
                Affichage de {{ count($etatComptes) }} communes
            </div>
            <div class="pagination-controls">
                <button class="pagination-btn" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="pagination-info">1 de 1</span>
                <button class="pagination-btn" disabled>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Défaillances et Problèmes de Conformité -->
    <div class="table-section">
        <div class="table-header">
            <h3>Défaillances et Problèmes de Conformité</h3>
            <div class="table-filters">
                <select class="filter-select small">
                    <option>Toutes les défaillances</option>
                    <option>Non résolues</option>
                    <option>Résolues</option>
                </select>
            </div>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Commune</th>
                        <th>Type de Défaillance</th>
                        <th>Date Constat</th>
                        <th>Gravité</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($defaillances as $defaillance)
                    <tr>
                        <td>{{ $defaillance['commune'] }}</td>
                        <td>
                            <span class="defaillance-type">{{ $defaillance['type_defaillance'] }}</span>
                        </td>
                        <td>{{ date('d/m/Y', strtotime($defaillance['date_constat'])) }}</td>
                        <td>
                            <span class="gravite-badge {{ $defaillance['gravite'] ?? 'normale' }}">
                                {{ ucfirst($defaillance['gravite'] ?? 'Normale') }}
                            </span>
                        </td>
                        <td class="description-cell">
                            <div class="description-text" title="{{ $defaillance['description'] }}">
                                {{ Str::limit($defaillance['description'], 50) }}
                            </div>
                        </td>
                        <td>
                            <span class="status-badge {{ $defaillance['status'] == 'Résolu' ? 'conforme' : 'non-resolu' }}">
                                {{ $defaillance['status'] }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action view" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($defaillance['status'] != 'Résolu')
                                <button class="btn-action edit" title="Marquer comme résolu">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Aucune défaillance enregistrée pour cette période
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Résumé par Département -->
    <div class="departements-summary">
        <div class="section-header">
            <h3>Résumé par Département</h3>
        </div>
        <div class="departements-grid">
            @foreach($region->departements as $departement)
            <div class="departement-card">
                <div class="departement-header">
                    <h4>{{ $departement->nom }}</h4>
                    <span class="communes-count">{{ $departement->communes->count() }} communes</span>
                </div>
                <div class="departement-stats">
                    <div class="stat-item">
                        <span class="stat-label">Taux moyen</span>
                        <span class="stat-value">
                            {{ number_format($departement->communes->avg(function($c) use ($annee) {
                                return $c->tauxRealisations->where('annee_exercice', $annee)->first()?->pourcentage ?? 0;
                            }), 1) }}%
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Communes conformes</span>
                        <span class="stat-value">
                            {{ $departement->communes->filter(function($c) use ($annee) {
                                $taux = $c->tauxRealisations->where('annee_exercice', $annee)->first();
                                return $taux && $taux->pourcentage >= 90;
                            })->count() }}
                        </span>
                    </div>
                </div>
                <div class="departement-actions">
                    <a href="{{ route('departements.show', $departement->id) }}" class="btn btn-outline">
                        Voir détails
                    </a>
                </div>
            </div>
            @endforeach
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
@endsection 