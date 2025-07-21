{{-- @extends('layouts.app')

@section('title', 'Tableau de Bord - Dettes Salariales')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-0">Tableau de Bord - Dettes Salariales</h1>
                    <p class="text-muted mb-0">Analyse complète des dettes salariales pour l'année {{ $annee }}</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- Filtre par année -->
                    <form method="GET" class="d-flex align-items-center">
                        <select name="annee" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                            @foreach($anneesDisponibles as $anneeDisponible)
                                <option value="{{ $anneeDisponible }}" {{ $annee == $anneeDisponible ? 'selected' : '' }}>
                                    {{ $anneeDisponible }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    
                    <!-- Boutons d'action -->
                    <div class="btn-group" role="group">
                        <a href="{{ route('dettes-salariale.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list"></i> Liste
                        </a>
                        <a href="{{ route('dettes-salariale.export', ['annee' => $annee]) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-download"></i> Export
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des Dettes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_dettes'], 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                Communes Concernées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['nb_communes_concernees'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-city fa-2x text-gray-300"></i>
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
                                Dette Moyenne
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['dette_moyenne'], 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Communes Critiques
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['communes_critiques'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicateurs supplémentaires -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Dette Médiane</h5>
                    <h3 class="text-info">{{ number_format($stats['dette_mediane'], 0, ',', ' ') }} FCFA</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Croissance Annuelle</h5>
                    <h3 class="{{ $stats['croissance_annuelle'] >= 0 ? 'text-danger' : 'text-success' }}">
                        {{ $stats['croissance_annuelle'] }}%
                        <i class="fas fa-arrow-{{ $stats['croissance_annuelle'] >= 0 ? 'up' : 'down' }}"></i>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Taux de Criticité</h5>
                    <h3 class="text-warning">
                        {{ $stats['nb_communes_concernees'] > 0 ? round(($stats['communes_critiques'] / $stats['nb_communes_concernees']) * 100, 1) : 0 }}%
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row mb-4">
        <!-- Évolution annuelle -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Évolution Annuelle des Dettes</h6>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>

        <!-- Répartition par tranches -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition par Tranches</h6>
                </div>
                <div class="card-body">
                    <canvas id="tranchesChart" width="100%" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Répartition régionale -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition par Région</h6>
                </div>
                <div class="card-body">
                    <canvas id="regionChart" width="100%" height="60"></canvas>
                </div>
            </div>
        </div>

        <!-- Comparatif types de dettes -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Comparatif Types de Dettes</h6>
                </div>
                <div class="card-body">
                    <canvas id="comparatifChart" width="100%" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top communes endettées -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 - Communes les Plus Endettées</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rang</th>
                                    <th>Commune</th>
                                    <th>Département</th>
                                    <th>Région</th>
                                    <th>Montant</th>
                                    <th>Date Évaluation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCommunes as $index => $dette)
                                    <tr>
                                        <td>
                                            <span class="badge badge-{{ $index < 3 ? 'danger' : ($index < 7 ? 'warning' : 'secondary') }}">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>{{ $dette->commune->nom }}</td>
                                        <td>{{ $dette->commune->departement->nom }}</td>
                                        <td>{{ $dette->commune->departement->region->nom }}</td>
                                        <td>
                                            <strong>{{ number_format($dette->montant, 0, ',', ' ') }} FCFA</strong>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($dette->date_evaluation)->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('dettes-salariale.show', $dette) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune donnée disponible</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration commune des graphiques
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    };

    // Graphique d'évolution annuelle
    const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: @json($evolutionAnnuelle->pluck('annee')),
            datasets: [{
                label: 'Montant Total (FCFA)',
                data: @json($evolutionAnnuelle->pluck('total')),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }, {
                label: 'Nombre de Dettes',
                data: @json($evolutionAnnuelle->pluck('nombre')),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (FCFA)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nombre de Dettes'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Graphique répartition par tranches
    const tranchesCtx = document.getElementById('tranchesChart').getContext('2d');
    new Chart(tranchesCtx, {
        type: 'doughnut',
        data: {
            
            labels: @json(collect($evolutionAnnuelle)->pluck('annee'))
            datasets: [{
                data: @json(collect($repartitionTranches)->pluck('nombre')),
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + ' communes';
                        }
                    }
                }
            }
        }
    });

    // Graphique répartition régionale
    const regionCtx = document.getElementById('regionChart').getContext('2d');
    new Chart(regionCtx, {
        type: 'bar',
        data: {
            labels: @json($stats['repartition_regionale']->pluck('region')),
            datasets: [{
                label: 'Montant Total (FCFA)',
                data: @json($stats['repartition_regionale']->pluck('total')),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (FCFA)'
                    }
                }
            },
            plugins: {
                ...commonOptions.plugins,
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

    // Graphique comparatif types de dettes
    const comparatifCtx = document.getElementById('comparatifChart').getContext('2d');
    new Chart(comparatifCtx, {
        type: 'bar',
        data: {
            labels: ['Salariale', 'CNPS', 'Fiscale', 'FEICOM'],
            datasets: [{
                label: 'Montant Total (FCFA)',
                data: [
                    @json($comparatifDettes['salariale']),
                    @json($comparatifDettes['cnps']),
                    @json($comparatifDettes['fiscale']),
                    @json($comparatifDettes['feicom'])
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (FCFA)'
                    }
                }
            },
            plugins: {
                ...commonOptions.plugins,
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
});
</script>
@endpush

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.text-xs {
    font-size: 0.7rem;
}

.shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.gap-2 {
    gap: 0.5rem;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.table th {
    background-color: #f8f9fc;
    font-weight: 600;
    color: #5a5c69;
}

.table tbody tr:hover {
    background-color: #f8f9fc;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

canvas {
    height: 300px !important;
}
</style>
@endpush
@endsection --}}



@extends('layouts.app')

@section('title', 'Tableau de Bord - Dettes Salariales')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-0">Tableau de Bord - Dettes Salariales</h1>
                    <p class="text-muted mb-0">Analyse complète des dettes salariales pour l'année {{ $annee }}</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- Filtre par année -->
                    <form method="GET" class="d-flex align-items-center">
                        <select name="annee" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                            @foreach($anneesDisponibles as $anneeDisponible)
                                <option value="{{ $anneeDisponible }}" {{ $annee == $anneeDisponible ? 'selected' : '' }}>
                                    {{ $anneeDisponible }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    
                    <!-- Boutons d'action -->
                    <div class="btn-group" role="group">
                        <a href="{{ route('dettes-salariale.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list"></i> Liste
                        </a>
                        <a href="{{ route('dettes-salariale.export', ['annee' => $annee]) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-download"></i> Export
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des Dettes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_dettes'], 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                Communes Concernées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['nb_communes_concernees'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-city fa-2x text-gray-300"></i>
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
                                Dette Moyenne
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['dette_moyenne'], 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Communes Critiques
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['communes_critiques'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicateurs supplémentaires -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Dette Médiane</h5>
                    <h3 class="text-info">{{ number_format($stats['dette_mediane'], 0, ',', ' ') }} FCFA</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Croissance Annuelle</h5>
                    <h3 class="{{ $stats['croissance_annuelle'] >= 0 ? 'text-danger' : 'text-success' }}">
                        {{ $stats['croissance_annuelle'] }}%
                        <i class="fas fa-arrow-{{ $stats['croissance_annuelle'] >= 0 ? 'up' : 'down' }}"></i>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Taux de Criticité</h5>
                    <h3 class="text-warning">
                        {{ $stats['nb_communes_concernees'] > 0 ? round(($stats['communes_critiques'] / $stats['nb_communes_concernees']) * 100, 1) : 0 }}%
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row mb-4">
        <!-- Évolution annuelle -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Évolution Annuelle des Dettes</h6>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>

        <!-- Répartition par tranches -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition par Tranches</h6>
                </div>
                <div class="card-body">
                    <canvas id="tranchesChart" width="100%" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Répartition régionale -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition par Région</h6>
                </div>
                <div class="card-body">
                    <canvas id="regionChart" width="100%" height="60"></canvas>
                </div>
            </div>
        </div>

        <!-- Comparatif types de dettes -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Comparatif Types de Dettes</h6>
                </div>
                <div class="card-body">
                    <canvas id="comparatifChart" width="100%" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top communes endettées -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 - Communes les Plus Endettées</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rang</th>
                                    <th>Commune</th>
                                    <th>Département</th>
                                    <th>Région</th>
                                    <th>Montant</th>
                                    <th>Date Évaluation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCommunes as $index => $dette)
                                    <tr>
                                        <td>
                                            <span class="badge badge-{{ $index < 3 ? 'danger' : ($index < 7 ? 'warning' : 'secondary') }}">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>{{ $dette->commune->nom }}</td>
                                        <td>{{ $dette->commune->departement->nom }}</td>
                                        <td>{{ $dette->commune->departement->region->nom }}</td>
                                        <td>
                                            <strong>{{ number_format($dette->montant, 0, ',', ' ') }} FCFA</strong>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($dette->date_evaluation)->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('dettes-salariale.show', $dette) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune donnée disponible</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @php
        // Conversion sécurisée des données pour JavaScript
        $evolutionLabels = [];
        $evolutionTotaux = [];
        $evolutionNombres = [];
        
        if (!empty($evolutionAnnuelle)) {
            if (is_array($evolutionAnnuelle)) {
                foreach ($evolutionAnnuelle as $item) {
                    $evolutionLabels[] = $item['annee'] ?? $item->annee ?? '';
                    $evolutionTotaux[] = $item['total'] ?? $item->total ?? 0;
                    $evolutionNombres[] = $item['nombre'] ?? $item->nombre ?? 0;
                }
            } else {
                // Si c'est une collection Laravel
                $evolutionLabels = $evolutionAnnuelle->pluck('annee')->toArray();
                $evolutionTotaux = $evolutionAnnuelle->pluck('total')->toArray();
                $evolutionNombres = $evolutionAnnuelle->pluck('nombre')->toArray();
            }
        }
        
        // Données pour les tranches
        $tranchesLabels = [];
        $tranchesData = [];
        
        if (!empty($repartitionTranches)) {
            if (is_array($repartitionTranches)) {
                foreach ($repartitionTranches as $item) {
                    $tranchesLabels[] = $item['tranche'] ?? $item->tranche ?? '';
                    $tranchesData[] = $item['nombre'] ?? $item->nombre ?? 0;
                }
            } else {
                $tranchesLabels = $repartitionTranches->pluck('tranche')->toArray();
                $tranchesData = $repartitionTranches->pluck('nombre')->toArray();
            }
        }
        
        // Données pour les régions
        $regionLabels = [];
        $regionTotaux = [];
        
        if (!empty($stats['repartition_regionale'])) {
            if (is_array($stats['repartition_regionale'])) {
                foreach ($stats['repartition_regionale'] as $item) {
                    $regionLabels[] = $item['region'] ?? $item->region ?? '';
                    $regionTotaux[] = $item['total'] ?? $item->total ?? 0;
                }
            } else {
                $regionLabels = $stats['repartition_regionale']->pluck('region')->toArray();
                $regionTotaux = $stats['repartition_regionale']->pluck('total')->toArray();
            }
        }
    @endphp

    // Configuration commune des graphiques
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    };

    // Graphique d'évolution annuelle
    const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: @json($evolutionLabels),
            datasets: [{
                label: 'Montant Total (FCFA)',
                data: @json($evolutionTotaux),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }, {
                label: 'Nombre de Dettes',
                data: @json($evolutionNombres),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (FCFA)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nombre de Dettes'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Graphique répartition par tranches
    const tranchesCtx = document.getElementById('tranchesChart').getContext('2d');
    new Chart(tranchesCtx, {
        type: 'doughnut',
        data: {
            labels: @json($tranchesLabels),
            datasets: [{
                data: @json($tranchesData),
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + ' communes';
                        }
                    }
                }
            }
        }
    });

    // Graphique répartition régionale
    const regionCtx = document.getElementById('regionChart').getContext('2d');
    new Chart(regionCtx, {
        type: 'bar',
        data: {
            labels: @json($regionLabels),
            datasets: [{
                label: 'Montant Total (FCFA)',
                data: @json($regionTotaux),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (FCFA)'
                    }
                }
            },
            plugins: {
                ...commonOptions.plugins,
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

    // Graphique comparatif types de dettes
    const comparatifCtx = document.getElementById('comparatifChart').getContext('2d');
    new Chart(comparatifCtx, {
        type: 'bar',
        data: {
            labels: ['Salariale', 'CNPS', 'Fiscale', 'FEICOM'],
            datasets: [{
                label: 'Montant Total (FCFA)',
                data: [
                    {{ isset($comparatifDettes['salariale']) ? $comparatifDettes['salariale'] : 0 }},
                    {{ isset($comparatifDettes['cnps']) ? $comparatifDettes['cnps'] : 0 }},
                    {{ isset($comparatifDettes['fiscale']) ? $comparatifDettes['fiscale'] : 0 }},
                    {{ isset($comparatifDettes['feicom']) ? $comparatifDettes['feicom'] : 0 }}
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (FCFA)'
                    }
                }
            },
            plugins: {
                ...commonOptions.plugins,
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
});
</script>
@endpush

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.text-xs {
    font-size: 0.7rem;
}

.shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.gap-2 {
    gap: 0.5rem;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.table th {
    background-color: #f8f9fc;
    font-weight: 600;
    color: #5a5c69;
}

.table tbody tr:hover {
    background-color: #f8f9fc;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

canvas {
    height: 300px !important;
}
</style>
@endpush
@endsection