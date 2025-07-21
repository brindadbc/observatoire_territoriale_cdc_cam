@extends('layouts.app')

@section('title', 'Analyses des tendances')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        Analyses des tendances
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('previsions.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour aux prévisions
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <form method="GET" action="{{ route('previsions.analyses.tendances') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="region_id">Région</label>
                                    <select name="region_id" id="region_id" class="form-control select2" onchange="loadDepartements()">
                                        <option value="">Toutes les régions</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region->id }}" {{ $regionId == $region->id ? 'selected' : '' }}>
                                                {{ $region->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="departement_id">Département</label>
                                    <select name="departement_id" id="departement_id" class="form-control select2" onchange="loadCommunes()">
                                        <option value="">Tous les départements</option>
                                        @foreach($departements as $dept)
                                            <option value="{{ $dept->id }}" 
                                                    data-region="{{ $dept->region_id }}"
                                                    {{ $departementId == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="commune_id">Commune</label>
                                    <select name="commune_id" id="commune_id" class="form-control select2">
                                        <option value="">Toutes les communes</option>
                                        @foreach($communes as $commune)
                                            <option value="{{ $commune->id }}" 
                                                    data-departement="{{ $commune->departement_id }}"
                                                    {{ $communeId == $commune->id ? 'selected' : '' }}>
                                                {{ $commune->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-info form-control">
                                        <i class="fas fa-search"></i> Analyser
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="annee_debut">Année de début</label>
                                    <input type="number" name="annee_debut" id="annee_debut" class="form-control" 
                                           value="{{ $anneeDebut }}" min="2000" max="{{ date('Y') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="annee_fin">Année de fin</label>
                                    <input type="number" name="annee_fin" id="annee_fin" class="form-control" 
                                           value="{{ $anneeFin }}" min="2000" max="{{ date('Y') }}">
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Statistiques globales -->
                    @if(isset($tendancesData['statistiques_globales']))
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h4>Statistiques globales ({{ $anneeDebut }} - {{ $anneeFin }})</h4>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-calendar"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Années analysées</span>
                                        <span class="info-box-number">{{ $tendancesData['statistiques_globales']['annees_analysees'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-euro-sign"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Montant total prévu</span>
                                        <span class="info-box-number">{{ number_format($tendancesData['statistiques_globales']['montant_total_prevu'], 0, ',', ' ') }} FCFA</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Montant total réalisé</span>
                                        <span class="info-box-number">{{ number_format($tendancesData['statistiques_globales']['montant_total_realise'], 0, ',', ' ') }} FCFA</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-percentage"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Taux global</span>
                                        <span class="info-box-number">{{ number_format($tendancesData['statistiques_globales']['taux_realisation_global'], 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Meilleures et moins bonnes années -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Meilleure année</h3>
                                    </div>
                                    <div class="card-body">
                                        @if($tendancesData['statistiques_globales']['meilleure_annee'])
                                            <h4>{{ $tendancesData['statistiques_globales']['meilleure_annee']['annee'] }}</h4>
                                            <p>Taux de réalisation : <strong>{{ number_format($tendancesData['statistiques_globales']['meilleure_annee']['taux_realisation_moyen'], 1) }}%</strong></p>
                                            <p>Montant réalisé : {{ number_format($tendancesData['statistiques_globales']['meilleure_annee']['montant_realise'], 0, ',', ' ') }} FCFA</p>
                                        @else
                                            <p>Aucune donnée disponible</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <h3 class="card-title">Année la moins performante</h3>
                                    </div>
                                    <div class="card-body">
                                        @if($tendancesData['statistiques_globales']['moins_bonne_annee'])
                                            <h4>{{ $tendancesData['statistiques_globales']['moins_bonne_annee']['annee'] }}</h4>
                                            <p>Taux de réalisation : <strong>{{ number_format($tendancesData['statistiques_globales']['moins_bonne_annee']['taux_realisation_moyen'], 1) }}%</strong></p>
                                            <p>Montant réalisé : {{ number_format($tendancesData['statistiques_globales']['moins_bonne_annee']['montant_realise'], 0, ',', ' ') }} FCFA</p>
                                        @else
                                            <p>Aucune donnée disponible</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Graphique d'évolution -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Évolution des montants et taux de réalisation</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="evolutionChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau détaillé -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Détail par année</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Année</th>
                                                    <th>Nb prévisions</th>
                                                    <th>Montant prévu</th>
                                                    <th>Montant réalisé</th>
                                                    <th>Taux réalisation</th>
                                                    <th>Croissance prévision</th>
                                                    <th>Croissance réalisation</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($tendancesData['evolution_annuelle']))
                                                    @foreach($tendancesData['evolution_annuelle'] as $annee)
                                                        <tr>
                                                            <td><strong>{{ $annee['annee'] }}</strong></td>
                                                            <td>{{ $annee['nb_previsions'] }}</td>
                                                            <td>{{ number_format($annee['montant_prevu'], 0, ',', ' ') }} FCFA</td>
                                                            <td>{{ number_format($annee['montant_realise'], 0, ',', ' ') }} FCFA</td>
                                                            <td>
                                                                <span class="badge badge-{{ $annee['taux_realisation_moyen'] >= 75 ? 'success' : ($annee['taux_realisation_moyen'] >= 50 ? 'warning' : 'danger') }}">
                                                                    {{ number_format($annee['taux_realisation_moyen'], 1) }}%
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($annee['croissance_prevision'] != 0)
                                                                    <span class="badge badge-{{ $annee['croissance_prevision'] > 0 ? 'success' : 'danger' }}">
                                                                        {{ $annee['croissance_prevision'] > 0 ? '+' : '' }}{{ number_format($annee['croissance_prevision'], 1) }}%
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($annee['croissance_realisation'] != 0)
                                                                    <span class="badge badge-{{ $annee['croissance_realisation'] > 0 ? 'success' : 'danger' }}">
                                                                        {{ $annee['croissance_realisation'] > 0 ? '+' : '' }}{{ number_format($annee['croissance_realisation'], 1) }}%
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="7" class="text-center">Aucune donnée disponible pour la période sélectionnée</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Données pour le graphique
const evolutionData = @json($tendancesData['evolution_annuelle'] ?? []);

// Configuration du graphique
const ctx = document.getElementById('evolutionChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: evolutionData.map(item => item.annee),
        datasets: [{
            label: 'Montant prévu (fcfa)',
            data: evolutionData.map(item => item.montant_prevu),
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            yAxisID: 'y'
        }, {
            label: 'Montant réalisé (fcfa)',
            data: evolutionData.map(item => item.montant_realise),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            yAxisID: 'y'
        }, {
            label: 'Taux réalisation (%)',
            data: evolutionData.map(item => item.taux_realisation_moyen),
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            type: 'line',
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            x: {
                display: true,
                title: {
                    display: true,
                    text: 'Année'
                }
            },
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Montant (fcfa)'
                },
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('fr-FR').format(value) + ' fcfa';
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Taux (%)'
                },
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        if (context.dataset.label.includes('Montant')) {
                            return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' fcfa';
                        } else {
                            return context.dataset.label + ': ' + context.parsed.y + '%';
                        }
                    }
                }
            }
        }
    }
});

// Fonctions pour les filtres en cascade
function loadDepartements() {
    const regionId = document.getElementById('region_id').value;
    const departementSelect = document.getElementById('departement_id');
    const communeSelect = document.getElementById('commune_id');
    
    // Réinitialiser les sélections
    departementSelect.value = '';
    communeSelect.value = '';
    
    // Afficher/masquer les départements selon la région
    const options = departementSelect.querySelectorAll('option');
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
        } else {
            const optionRegion = option.dataset.region;
            option.style.display = (!regionId || optionRegion === regionId) ? 'block' : 'none';
        }
    });
    
    loadCommunes();
}

function loadCommunes() {
    const departementId = document.getElementById('departement_id').value;
    const communeSelect = document.getElementById('commune_id');
    
    // Réinitialiser la sélection
    communeSelect.value = '';
    
    // Afficher/masquer les communes selon le département
    const options = communeSelect.querySelectorAll('option');
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
        } else {
            const optionDepartement = option.dataset.departement;
            option.style.display = (!departementId || optionDepartement === departementId) ? 'block' : 'none';
        }
    });
}

// Initialiser les filtres au chargement
document.addEventListener('DOMContentLoaded', function() {
    loadDepartements();
});
</script>
@endsection