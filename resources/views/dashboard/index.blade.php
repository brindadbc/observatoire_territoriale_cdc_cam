@extends('layouts.app')

@section('title', 'Tableau de Bord - Observatoire des Collectivités')
@section('page-title', 'Tableau de Bord')

@section('content')
<div class="dashboard-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <h3>Total des dépots de compte</h3>
                <div class="stat-icon yellow">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="stat-value">
                {{ number_format($stats['total_depots'], 1) }} Mds FCFA
            </div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                12,8% depuis 2023
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3>Communes Enregistrées</h3>
                <div class="stat-icon green">
                    <i class="fas fa-city"></i>
                </div>
            </div>
            <div class="stat-value">
                {{ $stats['communes_enregistrees'] }}
            </div>
            <div class="stat-change positive">
                <i class="fas fa-plus"></i>
                5 nouvelles en 2024
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3>Départements</h3>
                <div class="stat-icon blue">
                    <i class="fas fa-map"></i>
                </div>
            </div>
            <div class="stat-value">
                {{ $stats['departements'] }}
            </div>
            <div class="stat-subtitle">
                Données complètes
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3>Dette Moyenne CNPS</h3>
                <div class="stat-icon red">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
            <div class="stat-value">
                {{ number_format($stats['dette_moyenne_cnps'], 0) }}M FCFA
            </div>
            <div class="stat-change negative">
                <i class="fas fa-arrow-down"></i>
                8,3% depuis 2023
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="chart-container">
            <div class="chart-header">
                <h3>Évolution des dépots de compte (2018-2024)</h3>
                <div class="chart-filters">
                    <button class="filter-btn active">Annuel</button>
                </div>
            </div>
            <div class="chart-content">
                <canvas id="evolutionChart"></canvas>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color teal"></span>
                    <span>depots de compte</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color yellow"></span>
                    <span>Prévisions</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color red"></span>
                    <span>Dettes cnps</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color orange"></span>
                    <span>Dettes fiscale</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color purple"></span>
                    <span>Dettes feicom</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color pink"></span>
                    <span>Dettes salariale</span>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-header">
                <h3>Répartition par Catégorie</h3>
                <div class="chart-filters">
                    <button class="filter-btn active">2024</button>
                </div>
            </div>
            <div class="chart-content">
                <canvas id="repartitionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="map-section">
        <div class="map-header">
            <h3>Carte des dépots par Région</h3>
            <div class="map-filters">
                <button class="filter-btn active">2024</button>
            </div>
        </div>
        <div class="map-container">
            <div class="map-placeholder">
                <img src="{{ asset('images/cameroon-map.png') }}" alt="Carte du Cameroun" class="cameroon-map">
                <!-- Les régions seront ajoutées dynamiquement avec JavaScript -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Données pour les graphiques
const evolutionData = @json($stats['evolution_depots']);
const repartitionData = @json($stats['repartition_categories']);
const regionsData = @json($regions);

// Configuration du graphique d'évolution
const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
new Chart(evolutionCtx, {
    type: 'bar',
    data: {
        labels: evolutionData.map(item => item.annee),
        datasets: [{
            label: 'Dépôts de compte',
            data: evolutionData.map(item => item.total),
            backgroundColor: '#20B2AA',
            borderColor: '#20B2AA',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Configuration du graphique de répartition
const repartitionCtx = document.getElementById('repartitionChart').getContext('2d');
new Chart(repartitionCtx, {
    type: 'doughnut',
    data: {
        labels: ['Dépôts compte', 'Prévisions', 'Dettes CNPS', 'Dettes fiscale', 'Dettes feicom', 'Dettes salariale'],
        datasets: [{
            data: [
                repartitionData.depots_compte,
                repartitionData.previsions,
                repartitionData.dettes_cnps,
                repartitionData.dettes_fiscale,
                repartitionData.dettes_feicom,
                repartitionData.dettes_salariale
            ],
            backgroundColor: [
                '#20B2AA',
                '#FFD700',
                '#FF6B6B',
                '#FFA500',
                '#8A2BE2',
                '#FF69B4'
            ],
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
@endsection