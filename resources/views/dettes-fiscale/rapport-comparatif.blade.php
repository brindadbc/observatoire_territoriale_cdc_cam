@extends('layouts.app')

@section('title', 'Rapport Comparatif - Dettes Fiscales')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Rapport Comparatif des Dettes Fiscales</h1>
                    <p class="text-muted mb-0">Analyse comparative pour l'année {{ $annee }}</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- Sélecteur d'année -->
                    <form method="GET" class="d-flex align-items-center">
                        <select name="annee" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                            @for($i = date('Y'); $i >= date('Y') - 10; $i--)
                                <option value="{{ $i }}" {{ $annee == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </form>
                    
                    <!-- Boutons d'export -->
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i> Exporter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dettes-fiscale.export-comparatif', ['format' => 'excel', 'annee' => $annee]) }}">
                                <i class="fas fa-file-excel text-success me-2"></i>Excel
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('dettes-fiscale.export-comparatif', ['format' => 'pdf', 'annee' => $annee]) }}">
                                <i class="fas fa-file-pdf text-danger me-2"></i>PDF
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('dettes-fiscale.export-comparatif', ['format' => 'csv', 'annee' => $annee]) }}">
                                <i class="fas fa-file-csv text-info me-2"></i>CSV
                            </a></li>
                        </ul>
                    </div>
                    
                    <a href="{{ route('dettes-fiscale.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparaison avec l'année précédente -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Comparaison {{ $annee }} vs {{ $anneePrecedente }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Évolution du montant total -->
                        <div class="col-md-4">
                            <div class="text-center p-3 border-end">
                                <h6 class="text-muted mb-2">Montant Total des Dettes</h6>
                                <div class="d-flex justify-content-center align-items-center">
                                    @if($comparaison['evolution_montant'] > 0)
                                        <i class="fas fa-arrow-up text-danger me-2"></i>
                                        <span class="text-danger fw-bold">+{{ $comparaison['evolution_montant'] }}%</span>
                                    @elseif($comparaison['evolution_montant'] < 0)
                                        <i class="fas fa-arrow-down text-success me-2"></i>
                                        <span class="text-success fw-bold">{{ $comparaison['evolution_montant'] }}%</span>
                                    @else
                                        <i class="fas fa-minus text-secondary me-2"></i>
                                        <span class="text-secondary fw-bold">0%</span>
                                    @endif
                                </div>
                                <small class="text-muted">par rapport à {{ $anneePrecedente }}</small>
                            </div>
                        </div>

                        <!-- Évolution du nombre de dettes -->
                        <div class="col-md-4">
                            <div class="text-center p-3 border-end">
                                <h6 class="text-muted mb-2">Nombre de Dettes</h6>
                                <div class="d-flex justify-content-center align-items-center">
                                    @if($comparaison['evolution_nb_dettes'] > 0)
                                        <i class="fas fa-arrow-up text-danger me-2"></i>
                                        <span class="text-danger fw-bold">+{{ $comparaison['evolution_nb_dettes'] }}%</span>
                                    @elseif($comparaison['evolution_nb_dettes'] < 0)
                                        <i class="fas fa-arrow-down text-success me-2"></i>
                                        <span class="text-success fw-bold">{{ $comparaison['evolution_nb_dettes'] }}%</span>
                                    @else
                                        <i class="fas fa-minus text-secondary me-2"></i>
                                        <span class="text-secondary fw-bold">0%</span>
                                    @endif
                                </div>
                                <small class="text-muted">par rapport à {{ $anneePrecedente }}</small>
                            </div>
                        </div>

                        <!-- Évolution du nombre de communes -->
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <h6 class="text-muted mb-2">Communes Concernées</h6>
                                <div class="d-flex justify-content-center align-items-center">
                                    @if($comparaison['evolution_nb_communes'] > 0)
                                        <i class="fas fa-arrow-up text-danger me-2"></i>
                                        <span class="text-danger fw-bold">+{{ $comparaison['evolution_nb_communes'] }}%</span>
                                    @elseif($comparaison['evolution_nb_communes'] < 0)
                                        <i class="fas fa-arrow-down text-success me-2"></i>
                                        <span class="text-success fw-bold">{{ $comparaison['evolution_nb_communes'] }}%</span>
                                    @else
                                        <i class="fas fa-minus text-secondary me-2"></i>
                                        <span class="text-secondary fw-bold">0%</span>
                                    @endif
                                </div>
                                <small class="text-muted">par rapport à {{ $anneePrecedente }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Évolution sur 5 ans -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area me-2"></i>
                        Évolution sur 5 ans
                    </h5>
                </div>
                <div class="card-body">
                    @if($evolutionCinqAns->count() > 0)
                        <canvas id="evolutionChart" width="400" height="200"></canvas>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune donnée disponible pour l'évolution sur 5 ans</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Synthèse des tendances -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Analyse des Tendances
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-bold">Situation Générale</h6>
                        @if($comparaison['evolution_montant'] > 10)
                            <div class="alert alert-danger py-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <small>Forte augmentation des dettes fiscales</small>
                            </div>
                        @elseif($comparaison['evolution_montant'] > 0)
                            <div class="alert alert-warning py-2">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <small>Légère augmentation des dettes</small>
                            </div>
                        @elseif($comparaison['evolution_montant'] < -10)
                            <div class="alert alert-success py-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <small>Nette amélioration de la situation</small>
                            </div>
                        @else
                            <div class="alert alert-info py-2">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Situation relativement stable</small>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold">Points d'Attention</h6>
                        <ul class="list-unstyled small">
                            @if($comparaison['evolution_nb_communes'] > 0)
                                <li class="mb-1">
                                    <i class="fas fa-circle text-warning me-2" style="font-size: 0.5rem;"></i>
                                    Plus de communes concernées
                                </li>
                            @endif
                            @if($comparaison['evolution_montant'] > $comparaison['evolution_nb_dettes'])
                                <li class="mb-1">
                                    <i class="fas fa-circle text-danger me-2" style="font-size: 0.5rem;"></i>
                                    Augmentation du montant moyen
                                </li>
                            @endif
                            @if($evolutionCinqAns->count() >= 3)
                                @php
                                    $tendance = $evolutionCinqAns->last()->total > $evolutionCinqAns->first()->total;
                                @endphp
                                <li class="mb-1">
                                    <i class="fas fa-circle {{ $tendance ? 'text-danger' : 'text-success' }} me-2" style="font-size: 0.5rem;"></i>
                                    Tendance {{ $tendance ? 'haussière' : 'baissière' }} sur 5 ans
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analyse par département -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Analyse par Département ({{ $annee }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($analyseDepartements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Département</th>
                                        <th class="text-end">Montant Total</th>
                                        <th class="text-center">Nb Communes</th>
                                        <th class="text-end">Moyenne par Dette</th>
                                        <th class="text-center">Part du Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalGeneral = $analyseDepartements->sum('total'); @endphp
                                    @foreach($analyseDepartements as $analyse)
                                        <tr>
                                            <td>
                                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                                {{ $analyse['departement'] }}
                                            </td>
                                            <td class="text-end fw-bold">
                                                {{ number_format($analyse['total'], 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $analyse['nb_communes'] }}</span>
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($analyse['moyenne'], 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="text-center">
                                                @php $pourcentage = $totalGeneral > 0 ? ($analyse['total'] / $totalGeneral) * 100 : 0; @endphp
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                        <div class="progress-bar bg-primary" style="width: {{ $pourcentage }}%"></div>
                                                    </div>
                                                    <small>{{ number_format($pourcentage, 1) }}%</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td>TOTAL</td>
                                        <td class="text-end">{{ number_format($totalGeneral, 0, ',', ' ') }} FCFA</td>
                                        <td class="text-center">{{ $analyseDepartements->sum('nb_communes') }}</td>
                                        <td class="text-end">{{ number_format($analyseDepartements->avg('moyenne'), 0, ',', ' ') }} FCFA</td>
                                        <td class="text-center">100%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune donnée disponible pour l'analyse par département</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($evolutionCinqAns->count() > 0)
    // Graphique d'évolution sur 5 ans
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($evolutionCinqAns->pluck('annee')->toArray()) !!},
            datasets: [{
                label: 'Montant Total (FCFA)',
                data: {!! json_encode($evolutionCinqAns->pluck('total')->toArray()) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Nombre de Communes',
                data: {!! json_encode($evolutionCinqAns->pluck('nb_communes')->toArray()) !!},
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
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
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Montant (FCFA)'
                    },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value);
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Nombre de Communes'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return 'Montant: ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                            } else {
                                return 'Communes: ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        }
    });
    @endif
</script>
@endpush

@push('styles')
<style>
    .progress {
        min-width: 60px;
    }
    
    .card-header.bg-primary {
        background-color: #0d6efd !important;
    }
    
    .alert {
        border-left: 4px solid;
    }
    
    .alert-danger {
        border-left-color: #dc3545;
    }
    
    .alert-warning {
        border-left-color: #ffc107;
    }
    
    .alert-success {
        border-left-color: #198754;
    }
    
    .alert-info {
        border-left-color: #0dcaf0;
    }
</style>
@endpush