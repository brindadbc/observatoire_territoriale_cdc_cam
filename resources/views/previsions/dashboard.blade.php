@extends('layouts.app')

@section('title', 'Tableau de Bord - Prévisions et Réalisations')

@section('content')
<div class="container-fluid">
    <!-- En-tête du Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Tableau de Bord</h1>
                    <p class="text-muted">Vue d'ensemble des prévisions et réalisations - Année {{ $anneeActuelle }}</p>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select" id="annee-selector" onchange="changerAnnee(this.value)">
                        @foreach($anneesDisponibles as $annee)
                            <option value="{{ $annee }}" {{ $annee == $anneeActuelle ? 'selected' : '' }}>
                                {{ $annee }}
                            </option>
                        @endforeach
                    </select>
                    <button class="btn btn-outline-primary" onclick="actualiserDashboard()">
                        <i class="fas fa-sync-alt"></i> Actualiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Prévisions Totales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statsGenerales['nb_previsions']) }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ number_format($statsGenerales['montant_total_previsions'], 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Réalisations Totales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statsGenerales['nb_realisations']) }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ number_format($statsGenerales['montant_total_realisations'], 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Taux de Réalisation
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ number_format($statsGenerales['taux_realisation_global'], 1) }}%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                             style="width: {{ min($statsGenerales['taux_realisation_global'], 100) }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Communes Actives
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statsGenerales['nb_communes_actives'] }}
                            </div>
                            <div class="text-xs text-muted">
                                sur {{ $statsGenerales['nb_communes_total'] }} communes
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et tableaux -->
    <div class="row">
        <!-- Graphique d'évolution -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Évolution Mensuelle {{ $anneeActuelle }}</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="exporterGraphique('evolution')">Exporter</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chartEvolution"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 5 des départements -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 5 Départements</h6>
                </div>
                <div class="card-body">
                    @foreach($topDepartements as $dept)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="font-weight-bold">{{ $dept['nom'] }}</span>
                            <span class="text-primary">{{ number_format($dept['taux_realisation'], 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar 
                                @if($dept['taux_realisation'] >= 80) bg-success
                                @elseif($dept['taux_realisation'] >= 60) bg-warning
                                @else bg-danger
                                @endif" 
                                role="progressbar" 
                                style="width: {{ min($dept['taux_realisation'], 100) }}%">
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ number_format($dept['montant_realise'], 0, ',', ' ') }} / 
                            {{ number_format($dept['montant_prevision'], 0, ',', ' ') }} FCFA
                        </small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux de données -->
    <div class="row">
        <!-- Dernières prévisions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Dernières Prévisions</h6>
                    <a href="{{ route('previsions.index') }}" class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Commune</th>
                                    <th>Montant</th>
                                    <th>Réalisé</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dernieresPrevisions as $prevision)
                                <tr>
                                    <td>
                                        <a href="{{ route('previsions.show', $prevision) }}" 
                                           class="text-decoration-none">
                                            {{ $prevision->commune->nom }}
                                        </a>
                                    </td>
                                    <td>{{ number_format($prevision->montant, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($prevision->montant_realise, 0, ',', ' ') }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($prevision->taux_realisation >= 80) badge-success
                                            @elseif($prevision->taux_realisation >= 60) badge-warning
                                            @else badge-danger
                                            @endif">
                                            {{ number_format($prevision->taux_realisation, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernières réalisations -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Dernières Réalisations</h6>
                    <a href="{{ route('realisations.index') }}" class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Commune</th>
                                    <th>Montant</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dernieresRealisations as $realisation)
                                <tr>
                                    <td>
                                        <a href="{{ route('realisations.show', $realisation) }}" 
                                           class="text-decoration-none">
                                            {{ $realisation->commune->nom }}
                                        </a>
                                    </td>
                                    <td>{{ number_format($realisation->montant, 0, ',', ' ') }}</td>
                                    <td>{{ $realisation->date_realisation->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($realisation->status_realisation == 'Objectif atteint') badge-success
                                            @elseif($realisation->status_realisation == 'Bon') badge-info
                                            @elseif($realisation->status_realisation == 'Moyen') badge-warning
                                            @else badge-secondary
                                            @endif">
                                            {{ $realisation->status_realisation }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes et notifications -->
    @if(count($alertes) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle"></i> Alertes et Notifications
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($alertes as $alerte)
                    <div class="alert alert-{{ $alerte['type'] }} alert-dismissible fade show" role="alert">
                        <strong>{{ $alerte['titre'] }}</strong> {{ $alerte['message'] }}
                        @if(isset($alerte['action']))
                        <a href="{{ $alerte['action']['url'] }}" class="alert-link">{{ $alerte['action']['text'] }}</a>
                        @endif
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.progress-sm {
    height: 0.5rem;
}
.chart-area {
    position: relative;
    height: 400px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique d'évolution
    const ctx = document.getElementById('chartEvolution').getContext('2d');
    const chartEvolution = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($evolutionMensuelle['mois']),
            datasets: [{
                label: 'Prévisions',
                data: @json($evolutionMensuelle['previsions']),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true
            }, {
                label: 'Réalisations',
                data: @json($evolutionMensuelle['realisations']),
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderWidth: 2,
                fill: true
            }]
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
            },
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + 
                                   context.parsed.y.toLocaleString() + ' FCFA';
                        }
                    }
                }
            }
        }
    });
});

function changerAnnee(annee) {
    window.location.href = '{{ route("dashboard") }}?annee=' + annee;
}

function actualiserDashboard() {
    window.location.reload();
}

function exporterGraphique(type) {
    // Implémentation de l'export selon le type
    console.log('Export du graphique:', type);
}
</script>
@endpush
@endsection