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


// Gestion des filtres avec rechargement automatique
document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('.filter-select');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            const filterType = this.name;
            const filterValue = this.value;
            
            // Construire l'URL avec les paramètres
            const url = new URL(window.location.href);
            
            // Mettre à jour le paramètre selon le type de filtre
            if (filterType === 'annee') {
                url.searchParams.set('annee', filterValue);
            } else if (filterType === 'departement') {
                url.searchParams.set('departement', filterValue);
            } else if (filterType === 'statut') {
                url.searchParams.set('statut', filterValue);
            }
            
            // Recharger la page avec les nouveaux paramètres
            window.location.href = url.toString();
        });
    });
    
    // Fonction pour afficher un loader pendant le rechargement
    function showLoader() {
        const loader = document.createElement('div');
        loader.id = 'page-loader';
        loader.innerHTML = `
            <div style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255,255,255,0.9);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            ">
                <div style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 1rem;
                ">
                    <div style="
                        width: 40px;
                        height: 40px;
                        border: 4px solid #e2e8f0;
                        border-top: 4px solid #667eea;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                    "></div>
                    <span style="color: #4a5568; font-weight: 600;">Chargement des données...</span>
                </div>
            </div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        `;
        document.body.appendChild(loader);
    }
    
    // Ajouter le loader lors du changement de filtre
    filterSelects.forEach(select => {
        select.addEventListener('change', showLoader);
    });
    
    // Gestion des actions sur les défaillances
    const actionButtons = document.querySelectorAll('.btn-action');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.classList.contains('view') ? 'view' : 'resolve';
            const row = this.closest('tr');
            const commune = row.querySelector('td:first-child').textContent.trim();
            
            if (action === 'view') {
                // Afficher les détails de la défaillance
                alert(`Affichage des détails de la défaillance pour ${commune}`);
            } else {
                // Marquer comme résolu
                if (confirm(`Marquer cette défaillance comme résolue pour ${commune} ?`)) {
                    // Ici vous pouvez ajouter la logique AJAX pour marquer comme résolu
                    alert('Défaillance marquée comme résolue');
                }
            }
        });
    });
    
    // Animation des barres de progression
    const tauxBars = document.querySelectorAll('.taux-progress');
    tauxBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
});
</script> 

{{-- <style>
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
</style> --}}
<style>
    * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #2d3748;
        }

        .region-dashboard {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header avec titre stylé */
        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .region-title {
            font-size: 3rem;
            font-weight: 900;
            color: white;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
            margin-bottom: 1rem;
        }

        .region-subtitle {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.9);
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Filtres modernes */
        .dashboard-filters {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .filter-row {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .filter-select {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            color: #4a5568;
            min-width: 200px;
            cursor: pointer;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 3rem;
        }

        .filter-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .filter-select:hover {
            border-color: #cbd5e0;
            transform: translateY(-1px);
        }

        /* Cards de statistiques avec animations */
        .region-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .region-stat-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255,255,255,0.2);
            cursor: pointer;
        }

        .region-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: height 0.3s ease;
        }

        .region-stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }

        .region-stat-card:hover::before {
            height: 8px;
        }

        .region-stat-card.success::before {
            background: linear-gradient(90deg, #48bb78, #38a169);
        }

        .region-stat-card.danger::before {
            background: linear-gradient(90deg, #f56565, #e53e3e);
        }

        .region-stat-card.warning::before {
            background: linear-gradient(90deg, #ed8936, #dd6b20);
        }

        .stat-content h4 {
            font-size: 1rem;
            color: #718096;
            margin-bottom: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2d3748;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .region-stat-card.success .stat-number {
            color: #38a169;
        }

        .region-stat-card.danger .stat-number {
            color: #e53e3e;
        }

        .region-stat-card.warning .stat-number {
            color: #dd6b20;
        }

        /* Section des graphiques */
        .region-charts-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .chart-container {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .chart-header {
            margin-bottom: 2rem;
        }

        .chart-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .chart-content {
            height: 300px;
            position: relative;
        }

        .chart-legend {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 50%;
        }

        .legend-color.green {
            background: #48bb78;
        }

        .legend-color.yellow {
            background: #ed8936;
        }

        .legend-color.red {
            background: #f56565;
        }

        /* Tables modernes */
        .table-section {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            margin-bottom: 3rem;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .table-header {
            padding: 2rem;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
        }

        .table-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
        }

        .btn-outline:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #f8fafc;
            padding: 1.5rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }

        .data-table td {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .data-table tr:hover {
            background-color: #f8fafc;
        }

        /* Status badges modernes */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.conforme {
            background: rgba(72, 187, 120, 0.1);
            color: #2f855a;
            border: 1px solid rgba(72, 187, 120, 0.2);
        }

        .status-badge.moyen {
            background: rgba(237, 137, 54, 0.1);
            color: #c05621;
            border: 1px solid rgba(237, 137, 54, 0.2);
        }

        .status-badge.non-conforme {
            background: rgba(245, 101, 101, 0.1);
            color: #c53030;
            border: 1px solid rgba(245, 101, 101, 0.2);
        }

        /* Taux de réalisation avec barre de progression */
        .taux-container {
            min-width: 140px;
        }

        .taux-value {
            font-weight: 600;
            color: #2d3748;
        }

        .taux-bar {
            width: 100%;
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .taux-progress {
            height: 100%;
            border-radius: 3px;
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(90deg, #f56565 0%, #ed8936 50%, #48bb78 100%);
        }

        /* Liens de communes */
        .commune-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .commune-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .commune-code {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            background: #f1f5f9;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #4a5568;
        }

        /* Actions sur les défaillances */
        .btn-action {
            background: none;
            border: none;
            padding: 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-action.view {
            color: #667eea;
        }

        .btn-action.edit {
            color: #48bb78;
        }

        .btn-action:hover {
            background: rgba(0,0,0,0.05);
            transform: scale(1.1);
        }

        /* Résumé des départements */
        .departements-summary {
            margin-top: 3rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .section-header h3 {
            font-size: 2rem;
            font-weight: 800;
            color: white;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .departements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }

        .departement-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .departement-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.2);
        }

        .departement-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .departement-header h4 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2d3748;
        }

        .communes-count {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .departement-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 12px;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #718096;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #2d3748;
        }

        /* Pagination */
        .table-pagination {
            padding: 1.5rem 2rem;
            background: #f8fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e2e8f0;
        }

        .pagination-info {
            color: #718096;
            font-size: 0.9rem;
        }

        .pagination-controls {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .pagination-btn {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #4a5568;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Animations d'entrée */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .region-stat-card {
            animation: slideInUp 0.6s ease-out forwards;
        }

        .region-stat-card:nth-child(2) {
            animation-delay: 0.1s;
        }

        .region-stat-card:nth-child(3) {
            animation-delay: 0.2s;
        }

        .region-stat-card:nth-child(4) {
            animation-delay: 0.3s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .region-dashboard {
                padding: 1rem;
            }

            .region-title {
                font-size: 2rem;
            }

            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-select {
                min-width: unset;
            }

            .region-charts-section {
                grid-template-columns: 1fr;
            }

            .chart-legend {
                flex-direction: column;
                gap: 1rem;
                align-items: center;
            }

            .departement-stats {
                grid-template-columns: 1fr;
            }
        }
</style>
@endpush
@endsection 