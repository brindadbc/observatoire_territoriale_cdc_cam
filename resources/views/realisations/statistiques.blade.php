@extends('layouts.app')

@section('title', 'Tableau de bord - Réalisations')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Tableau de bord des Réalisations</h1>
            <p class="text-muted">Analyse et statistiques des réalisations {{ $annee }}</p>
        </div>
        <div class="d-flex gap-2">
            <select id="annee-selector" class="form-select" style="width: auto;">
                @foreach($anneesDisponibles as $year)
                    <option value="{{ $year }}" {{ $year == $annee ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('realisations.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-list"></i> Liste complète
            </a>
        </div>
    </div>

    <!-- Cartes de statistiques principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-50 small">Total Réalisations</div>
                            <div class="text-lg fw-bold">{{ number_format($stats['nb_realisations']) }}</div>
                        </div>
                        <div class="text-white-25">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <span>{{ $stats['nb_communes_realisatrices'] }} communes</span>
                    <div class="text-white-50">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-50 small">Montant Total</div>
                            <div class="text-lg fw-bold">{{ number_format($stats['montant_total_realisations'], 0, ',', ' ') }}</div>
                            <small>FCFA</small>
                        </div>
                        <div class="text-white-25">
                            <i class="fas fa-coins fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <span>Moyenne: {{ number_format($stats['realisation_moyenne'], 0, ',', ' ') }} FCFA</span>
                    <div class="text-white-50">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-50 small">Taux de Réalisation</div>
                            <div class="text-lg fw-bold">{{ number_format($stats['taux_realisation_global'], 1) }}%</div>
                        </div>
                        <div class="text-white-25">
                            <i class="fas fa-percentage fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <span>vs Prévisions</span>
                    <div class="text-white-50">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-50 small">Avec Prévision</div>
                            <div class="text-lg fw-bold">{{ $stats['nb_avec_prevision'] }}/{{ $stats['nb_realisations'] }}</div>
                        </div>
                        <div class="text-white-25">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <span>{{ number_format(($stats['nb_avec_prevision'] / max($stats['nb_realisations'], 1)) * 100, 1) }}% liées</span>
                    <div class="text-white-50">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Graphique évolution mensuelle -->
        <div class="col-xl-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-area"></i> Évolution des Réalisations par Mois
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>

        <!-- Top communes -->
        <div class="col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-trophy"></i> Top 10 Communes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($topCommunes->take(10) as $index => $commune)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong>#{{ $index + 1 }}</strong>
                                    <span class="ms-2">{{ $commune['nom'] }}</span>
                                    <br>
                                    <small class="text-muted">{{ $commune['departement'] }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">{{ number_format($commune['montant_total'], 0, ',', ' ') }}</div>
                                    <small class="text-muted">FCFA</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Répartition par département -->
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Répartition par Département
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="departementChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>

        <!-- Analyse des performances -->
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Performance vs Prévisions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="text-success">
                                <h4>{{ $performanceStats['objectif_atteint'] }}</h4>
                                <small>Objectif atteint<br>(≥100%)</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-info">
                                <h4>{{ $performanceStats['bon_niveau'] }}</h4>
                                <small>Bon niveau<br>(75-99%)</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-warning">
                                <h4>{{ $performanceStats['niveau_moyen'] }}</h4>
                                <small>Niveau moyen<br>(50-74%)</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-danger">
                                <h4>{{ $performanceStats['niveau_faible'] }}</h4>
                                <small>Niveau faible<br>(<50%)</small>
                            </div>
                        </div>
                    </div>
                    <canvas id="performanceChart" width="100%" height="30" class="mt-3"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau récapitulatif par région -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-table"></i> Récapitulatif par Région
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Région</th>
                                    <th>Nb. Communes</th>
                                    <th>Nb. Réalisations</th>
                                    <th>Montant Total</th>
                                    <th>Montant Moyen</th>
                                    <th>Taux de Réalisation</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statsParRegion as $region)
                                    <tr>
                                        <td>
                                            <strong>{{ $region['nom'] }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $region['nb_communes'] }}</span>
                                        </td>
                                        <td>{{ $region['nb_realisations'] }}</td>
                                        <td>{{ number_format($region['montant_total'], 0, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($region['montant_moyen'], 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">{{ number_format($region['taux_realisation'], 1) }}%</div>
                                                <div class="progress flex-fill" style="height: 6px;">
                                                    <div class="progress-bar 
                                                        @if($region['taux_realisation'] >= 100) bg-success
                                                        @elseif($region['taux_realisation'] >= 75) bg-info
                                                        @elseif($region['taux_realisation'] >= 50) bg-warning
                                                        @else bg-danger
                                                        @endif" 
                                                        style="width: {{ min($region['taux_realisation'], 100) }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($region['taux_realisation'] >= 100)
                                                <span class="badge bg-success">Excellent</span>
                                            @elseif($region['taux_realisation'] >= 75)
                                                <span class="badge bg-info">Bon</span>
                                            @elseif($region['taux_realisation'] >= 50)
                                                <span class="badge bg-warning">Moyen</span>
                                            @else
                                                <span class="badge bg-danger">Faible</span>
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
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Changement d'année
    document.getElementById('annee-selector').addEventListener('change', function() {
        window.location.href = '{{ url()->current() }}?annee=' + this.value;
    });

    // Données pour les graphiques
    const evolutionData = @json($evolutionMensuelle);
    const departementData = @json($repartitionDepartement);
    const performanceData = @json($performanceStats);

    // Graphique évolution mensuelle
    const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: evolutionData.labels,
            datasets: [{
                label: 'Réalisations',
                data: evolutionData.realisations,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.1
            }, {
                label: 'Prévisions',
                data: evolutionData.previsions,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                borderDash: [5, 5]
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,