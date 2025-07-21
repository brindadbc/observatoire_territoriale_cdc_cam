@extends('layouts.app')

@section('title', 'Rapport des dettes CNPS')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Rapport des dettes CNPS</h1>
            <p class="text-muted">Année {{ $annee }}</p>
        </div>
        <div>
            <a href="{{ route('dettes-cnps.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>

    <!-- Sélecteur d'année -->
    <div class="row mb-4">
        <div class="col-md-3">
            <form method="GET" action="{{ route('dettes-cnps.rapport') }}">
                <div class="input-group">
                    <select name="annee" class="form-select" onchange="this.form.submit()">
                        @foreach($annees as $year)
                            <option value="{{ $year }}" {{ $annee == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total des dettes</h6>
                            <h4 class="mb-0">{{ $rapport['global']['total_montant_formate'] }} FCFA</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Nombre de dettes</h6>
                            <h4 class="mb-0">{{ number_format($rapport['global']['nombre_dettes']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-list fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Montant moyen</h6>
                            <h4 class="mb-0">{{ $rapport['global']['montant_moyen_formate'] }} FCFA</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Communes concernées</h6>
                            <h4 class="mb-0">{{ number_format($rapport['global']['communes_concernees']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-map-marker-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques par région et département -->
    <div class="row mb-4">
        <!-- Par région -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map"></i> Répartition par région
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Région</th>
                                    <th>Total</th>
                                    <th>Nb dettes</th>
                                    <th>Communes</th>
                                    <th>Moyenne</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rapport['par_region'] as $region)
                                <tr>
                                    <td><strong>{{ $region->region }}</strong></td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ $region->total_montant_formate }} FCFA
                                        </span>
                                    </td>
                                    <td>{{ $region->nombre_dettes }}</td>
                                    <td>{{ $region->communes_concernees }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $region->montant_moyen_formate }} FCFA
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top départements -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy"></i> Top 10 départements
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Département</th>
                                    <th>Total</th>
                                    <th>Nb dettes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rapport['par_departement']->take(10) as $index => $dept)
                                <tr>
                                    <td>
                                        @if($index < 3)
                                            <i class="fas fa-medal text-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'warning') }}"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $dept->departement }}</strong>
                                        <br><small class="text-muted">{{ $dept->region }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ $dept->total_montant_formate }} FCFA
                                        </span>
                                    </td>
                                    <td>{{ $dept->nombre_dettes }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top communes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building"></i> Top 20 communes avec le plus de dettes CNPS
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rang</th>
                                    <th>Commune</th>
                                    <th>Code</th>
                                    <th>Département</th>
                                    <th>Région</th>
                                    <th>Total dette</th>
                                    <th>Nb évaluations</th>
                                    <th>Dette moyenne</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rapport['top_communes'] as $index => $commune)
                                <tr>
                                    <td>
                                        @if($index < 3)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-trophy"></i> {{ $index + 1 }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ $commune->commune }}</strong></td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $commune->code_commune }}</span>
                                    </td>
                                    <td>{{ $commune->departement }}</td>
                                    <td>{{ $commune->region }}</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ $commune->total_dette_formate }} FCFA
                                        </span>
                                    </td>
                                    <td>{{ $commune->nombre_evaluations }}</td>
                                    <td>
                                        @if($commune->nombre_evaluations > 0)
                                            {{ number_format($commune->total_dette / $commune->nombre_evaluations, 0, ',', ' ') }} FCFA
                                        @else
                                            0 FCFA
                                        @endif
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

    <!-- Évolution Annuelle - Détail -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt"></i> Évolution annuelle des dettes CNPS
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Année</th>
                                    <th>Montant total</th>
                                    <th>Nombre de dettes</th>
                                    <th>Communes concernées</th>
                                    <th>Montant moyen</th>
                                    <th>Évolution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $montantPrecedent = 0;
                                @endphp
                                @foreach($rapport['evolution_annuelle'] as $evolution)
                                <tr>
                                    <td><strong>{{ $evolution->annee }}</strong></td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">
                                            {{ $evolution->total_montant_formate }} FCFA
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $evolution->nombre_dettes }}</td>
                                    <td class="text-center">{{ $evolution->communes_concernees }}</td>
                                    <td class="text-end">
                                        {{ $evolution->montant_moyen_formate }} FCFA
                                    </td>
                                    <td>
                                        @if($montantPrecedent > 0)
                                            @php
                                                $variation = (($evolution->total_montant - $montantPrecedent) / $montantPrecedent) * 100;
                                            @endphp
                                            @if($variation > 0)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-arrow-up"></i> +{{ number_format($variation, 1) }}%
                                                </span>
                                            @elseif($variation < 0)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-arrow-down"></i> {{ number_format($variation, 1) }}%
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-minus"></i> 0%
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @php
                                    $montantPrecedent = $evolution->total_montant;
                                @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique d'évolution annuelle -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area"></i> Évolution graphique des dettes CNPS
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques complémentaires -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Informations complémentaires - {{ $annee }}
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Dette maximale :</dt>
                        <dd class="col-sm-6">
                            <span class="badge bg-danger">
                                {{ $rapport['global']['montant_max'] }} FCFA
                            </span>
                        </dd>
                        
                        <dt class="col-sm-6">Dette minimale :</dt>
                        <dd class="col-sm-6">
                            <span class="badge bg-success">
                                {{ $rapport['global']['montant_min'] }} FCFA
                            </span>
                        </dd>
                        
                        <dt class="col-sm-6">Nombre total de dettes :</dt>
                        <dd class="col-sm-6">
                            <span class="badge bg-info">
                                {{ number_format($rapport['global']['nombre_dettes']) }}
                            </span>
                        </dd>

                        <dt class="col-sm-6">Communes concernées :</dt>
                        <dd class="col-sm-6">
                            <span class="badge bg-warning text-dark">
                                {{ number_format($rapport['global']['communes_concernees']) }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-download"></i> Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('dettes-cnps.export', ['format' => 'pdf', 'annee' => $annee]) }}" 
                           class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> Exporter en PDF
                        </a>
                        <a href="{{ route('dettes-cnps.export', ['format' => 'excel', 'annee' => $annee]) }}" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Exporter en Excel
                        </a>
                        <a href="{{ route('dettes-cnps.export', ['format' => 'csv', 'annee' => $annee]) }}" 
                           class="btn btn-info btn-sm">
                            <i class="fas fa-file-csv"></i> Exporter en CSV
                        </a>
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
    // Graphique d'évolution annuelle
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    
    const evolutionData = @json($rapport['evolution_annuelle']);
    const labels = evolutionData.map(item => item.annee);
    const montants = evolutionData.map(item => parseFloat(item.total_montant));
    const nombreDettes = evolutionData.map(item => item.nombre_dettes);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Montant total (FCFA)',
                data: montants,
                borderColor: 'rgb(220, 53, 69)',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                yAxisID: 'y',
                tension: 0.1,
                fill: true
            }, {
                label: 'Nombre de dettes',
                data: nombreDettes,
                borderColor: 'rgb(13, 110, 253)',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                yAxisID: 'y1',
                tension: 0.1,
                fill: false
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
                        text: 'Années'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Montant (FCFA)'
                    },
                    ticks: {
                        callback: function(value, index, values) {
                            return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Nombre de dettes'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.datasetIndex === 0) {
                                label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                            } else {
                                label += context.parsed.y + ' dettes';
                            }
                            return label;
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
@media print {
    .btn, .no-print {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
        break-inside: avoid;
    }
    
    .card-header {
        background-color: #f8f9fa !important;
        border-bottom: 1px solid #dee2e6 !important;
    }
    
    .table {
        font-size: 11px;
    }
    
    .badge {
        border: 1px solid #000 !important;
        font-size: 10px;
    }
    
    .bg-danger {
        background-color: #dc3545 !important;
        color: white !important;
    }
    
    .bg-success {
        background-color: #198754 !important;
        color: white !important;
    }
    
    .bg-warning {
        background-color: #ffc107 !important;
        color: black !important;
    }
    
    .bg-info {
        background-color: #0dcaf0 !important;
        color: black !important;
    }

    .row {
        page-break-inside: avoid;
    }
    
    h1, h2, h3, h4, h5, h6 {
        page-break-after: avoid;
    }
}

.table-responsive {
    border-radius: 0.375rem;
}

.card-body {
    padding: 1.25rem;
}

.badge {
    font-size: 0.875em;
}

.fas {
    margin-right: 0.25rem;
}
</style>
@endpush
@endsection