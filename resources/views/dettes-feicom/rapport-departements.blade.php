@extends('layouts.app')

@section('title', 'Rapport Dettes FEICOM par Département')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Rapport des Dettes FEICOM par Département</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dettes-feicom.index') }}">Dettes FEICOM</a></li>
                        <li class="breadcrumb-item active">Rapport par Département</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('dettes-feicom.rapport-departements') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="annee" class="form-label">Année</label>
                            <select name="annee" id="annee" class="form-select">
                                @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}" {{ $annee == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="region_id" class="form-label">Région</label>
                            <select name="region_id" id="region_id" class="form-select">
                                <option value="">Toutes les régions</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ $regionId == $region->id ? 'selected' : '' }}>
                                        {{ $region->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block">
                                <i class="fe-filter"></i> Filtrer
                            </button>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="dropdown d-block">
                                <button class="btn btn-success dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                    <i class="fe-download"></i> Exporter
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('dettes-feicom.export', ['format' => 'excel', 'annee' => $annee, 'type' => 'departements']) }}">Excel</a></li>
                                    <li><a class="dropdown-item" href="{{ route('dettes-feicom.export', ['format' => 'pdf', 'annee' => $annee, 'type' => 'departements']) }}">PDF</a></li>
                                    <li><a class="dropdown-item" href="{{ route('dettes-feicom.export', ['format' => 'csv', 'annee' => $annee, 'type' => 'departements']) }}">CSV</a></li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate">Total Dettes</h5>
                            <h3 class="my-2 py-1">{{ number_format($departements->sum('total_dette'), 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="avatar-sm bg-light rounded-circle">
                                    <i class="fe-credit-card avatar-title font-22 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate">Départements</h5>
                            <h3 class="my-2 py-1">{{ $departements->count() }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-success me-2">{{ $departements->where('total_dette', '>', 0)->count() }}</span>
                                avec dettes
                            </p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="avatar-sm bg-light rounded-circle">
                                    <i class="fe-map avatar-title font-22 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate">Communes Concernées</h5>
                            <h3 class="my-2 py-1">{{ $departements->sum('nb_communes_concernees') }}</h3>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="avatar-sm bg-light rounded-circle">
                                    <i class="fe-home avatar-title font-22 text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate">Dette Moyenne</h5>
                            <h3 class="my-2 py-1">{{ number_format($departements->avg('dette_moyenne'), 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="avatar-sm bg-light rounded-circle">
                                    <i class="fe-trending-up avatar-title font-22 text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des départements -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Dettes FEICOM par Département - {{ $annee }}</h4>
                </div>
                <div class="card-body">
                    @if($departements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Rang</th>
                                        <th>Département</th>
                                        <th>Région</th>
                                        <th>Total Dette</th>
                                        <th>Nb Communes</th>
                                        <th>Dette Moyenne</th>
                                        <th>Pourcentage</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $rang = 1; $totalGeneral = $departements->sum('total_dette'); @endphp
                                    @foreach($departements as $departement)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary rounded-pill">{{ $rang++ }}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{ $departement['departement'] }}</h5>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $departement['region'] }}</span>
                                            </td>
                                            <td>
                                                <span class="text-dark fw-bold">
                                                    {{ number_format($departement['total_dette'], 0, ',', ' ') }} FCFA
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-soft-info text-info">
                                                    {{ $departement['nb_communes_concernees'] }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ number_format($departement['dette_moyenne'], 0, ',', ' ') }} FCFA
                                            </td>
                                            <td>
                                                @if($totalGeneral > 0)
                                                    @php $pourcentage = ($departement['total_dette'] / $totalGeneral) * 100 @endphp
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar" role="progressbar" 
                                                             style="width: {{ $pourcentage }}%" 
                                                             aria-valuenow="{{ $pourcentage }}" 
                                                             aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">{{ number_format($pourcentage, 1) }}%</small>
                                                @else
                                                    0%
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('dettes-feicom.index', ['departement_id' => $departement['departement']]) }}" 
                                                   class="btn btn-xs btn-light" title="Voir détails">
                                                    <i class="fe-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <img src="{{ asset('assets/images/no-data.svg') }}" alt="No data" class="img-fluid" style="max-height: 200px;">
                            <h4 class="mt-3">Aucune donnée disponible</h4>
                            <p class="text-muted">Aucune dette FEICOM trouvée pour l'année {{ $annee }}.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="row">
        <div class="col-12">
            <div class="text-center">
                <a href="{{ route('dettes-feicom.rapport-regions') }}" class="btn btn-outline-primary me-2">
                    <i class="fe-bar-chart-2"></i> Rapport par Région
                </a>
                <a href="{{ route('dettes-feicom.index') }}" class="btn btn-outline-secondary">
                    <i class="fe-list"></i> Liste des Dettes
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-submit form when filters change
    $('#annee, #region_id').on('change', function() {
        $(this).closest('form').submit();
    });
</script>
@endpush
@endsection