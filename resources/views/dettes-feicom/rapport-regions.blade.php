@extends('layouts.app')

@section('title', 'Rapport Dettes FEICOM par Région')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Rapport des Dettes FEICOM par Région</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dettes-feicom.index') }}">Dettes FEICOM</a></li>
                        <li class="breadcrumb-item active">Rapport par Région</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-filter-outline me-2"></i>
                        Filtres
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="annee" class="form-label">Année</label>
                            <select class="form-select" id="annee" name="annee">
                                @for($year = date('Y'); $year >= 2020; $year--)
                                    <option value="{{ $year }}" {{ $annee == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="mdi mdi-magnify"></i> Filtrer
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                <i class="mdi mdi-printer"></i> Imprimer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0 me-3">
                            <span class="avatar-title bg-primary rounded-circle fs-3">
                                <i class="mdi mdi-cash-multiple"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Total Dettes</p>
                            <h4 class="mb-0">{{ number_format($regions->sum('total_dette'), 0, ',', ' ') }} FCFA</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0 me-3">
                            <span class="avatar-title bg-success rounded-circle fs-3">
                                <i class="mdi mdi-map-marker-multiple"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Régions Concernées</p>
                            <h4 class="mb-0">{{ $regions->where('total_dette', '>', 0)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0 me-3">
                            <span class="avatar-title bg-warning rounded-circle fs-3">
                                <i class="mdi mdi-city"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Communes Concernées</p>
                            <h4 class="mb-0">{{ $regions->sum('nb_communes_concernees') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0 me-3">
                            <span class="avatar-title bg-info rounded-circle fs-3">
                                <i class="mdi mdi-calculator"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Dette Moyenne</p>
                            <h4 class="mb-0">{{ number_format($regions->avg('dette_moyenne'), 0, ',', ' ') }} FCFA</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des données -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-table me-2"></i>
                        Dettes FEICOM par Région - Année {{ $annee }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Rang</th>
                                    <th>Région</th>
                                    <th class="text-end">Total Dettes</th>
                                    <th class="text-center">Communes Concernées</th>
                                    <th class="text-end">Dette Moyenne</th>
                                    <th class="text-center">Pourcentage</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalGeneral = $regions->sum('total_dette'); @endphp
                                @foreach($regions as $index => $region)
                                    @if($region['total_dette'] > 0)
                                    <tr>
                                        <td>
                                            <span class="badge 
                                                @if($index == 0) bg-danger
                                                @elseif($index == 1) bg-warning
                                                @elseif($index == 2) bg-info
                                                @else bg-secondary
                                                @endif">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $region['region'] }}</div>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-danger">
                                                {{ number_format($region['total_dette'], 0, ',', ' ') }} FCFA
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $region['nb_communes_concernees'] }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-muted">
                                                {{ number_format($region['dette_moyenne'], 0, ',', ' ') }} FCFA
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $pourcentage = $totalGeneral > 0 ? ($region['total_dette'] / $totalGeneral) * 100 : 0;
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                    <div class="progress-bar 
                                                        @if($pourcentage >= 30) bg-danger
                                                        @elseif($pourcentage >= 20) bg-warning
                                                        @else bg-info
                                                        @endif" 
                                                        style="width: {{ $pourcentage }}%">
                                                    </div>
                                                </div>
                                                <small class="fw-bold">{{ number_format($pourcentage, 1) }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('dettes-feicom.rapport-departements', ['region_id' => '', 'annee' => $annee]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="mdi mdi-eye"></i> Détails
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                                
                                @if($regions->where('total_dette', '>', 0)->count() == 0)
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="mdi mdi-information-outline fs-3 d-block mb-2"></i>
                                            Aucune dette FEICOM enregistrée pour l'année {{ $annee }}
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                            @if($regions->where('total_dette', '>', 0)->count() > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2">TOTAL GÉNÉRAL</th>
                                    <th class="text-end">{{ number_format($totalGeneral, 0, ',', ' ') }} FCFA</th>
                                    <th class="text-center">{{ $regions->sum('nb_communes_concernees') }}</th>
                                    <th class="text-end">{{ number_format($regions->avg('dette_moyenne'), 0, ',', ' ') }} FCFA</th>
                                    <th class="text-center">100%</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique (si vous voulez l'ajouter plus tard) -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-chart-pie me-2"></i>
                        Répartition des Dettes par Région
                    </h5>
                </div>
                <div class="card-body">
                    <div id="chart-container" style="height: 400px;">
                        <!-- Ici vous pouvez intégrer Chart.js ou un autre graphique -->
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                            <div class="text-center">
                                <i class="mdi mdi-chart-pie fs-1 d-block mb-2"></i>
                                Graphique à implémenter
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions d'export -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-export me-2"></i>
                        Export des Données
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('dettes-feicom.export', ['format' => 'excel', 'annee' => $annee]) }}" 
                           class="btn btn-success">
                            <i class="mdi mdi-file-excel"></i> Export Excel
                        </a>
                        <a href="{{ route('dettes-feicom.export', ['format' => 'pdf', 'annee' => $annee]) }}" 
                           class="btn btn-danger">
                            <i class="mdi mdi-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('dettes-feicom.export', ['format' => 'csv', 'annee' => $annee]) }}" 
                           class="btn btn-info">
                            <i class="mdi mdi-file-delimited"></i> Export CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style media="print">
    .btn, .card-header, .breadcrumb, .page-title-right {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
</style>
@endsection