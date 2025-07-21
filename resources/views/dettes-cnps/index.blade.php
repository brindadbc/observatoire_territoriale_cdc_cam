@extends('layouts.app')

@section('title', 'Gestion des Dettes CNPS')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3 mb-0 text-gray-800">Gestion des Dettes CNPS</h1>
                <div class="btn-group">
                    <a href="{{ route('dettes-cnps.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle Dette
                    </a>
                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-download"></i> Exporter
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('dettes-cnps.export', array_merge(request()->all(), ['format' => 'excel'])) }}">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                        <a class="dropdown-item" href="{{ route('dettes-cnps.export', array_merge(request()->all(), ['format' => 'pdf'])) }}">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        <a class="dropdown-item" href="{{ route('dettes-cnps.export', array_merge(request()->all(), ['format' => 'csv'])) }}">
                            <i class="fas fa-file-csv"></i> CSV
                        </a>
                    </div>
                    <a href="{{ route('dettes-cnps.rapport') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Rapport
                    </a>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total des Dettes
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($stats['total_montant'], 0, ',', ' ') }} FCFA
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Nombre de Dettes
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($stats['nombre_dettes']) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Montant Moyen
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($stats['montant_moyen'], 0, ',', ' ') }} FCFA
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calculator fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Communes Concernées
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($stats['communes_concernees']) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-city fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Filtres de Recherche</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dettes-cnps.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Recherche Commune</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Nom ou code commune">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="region_id">Région</label>
                            <select class="form-control" id="region_id" name="region_id">
                                <option value="">Toutes les régions</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="departement_id">Département</label>
                            <select class="form-control" id="departement_id" name="departement_id">
                                <option value="">Tous les départements</option>
                                @foreach($departements as $departement)
                                    <option value="{{ $departement->id }}" {{ request('departement_id') == $departement->id ? 'selected' : '' }}>
                                        {{ $departement->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="annee">Année</label>
                            <select class="form-control" id="annee" name="annee">
                                <option value="">Toutes les années</option>
                                @foreach($annees as $annee)
                                    <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>
                                        {{ $annee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="montant_min">Montant Min</label>
                                    <input type="number" class="form-control" id="montant_min" name="montant_min" 
                                           value="{{ request('montant_min') }}" placeholder="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="montant_max">Montant Max</label>
                                    <input type="number" class="form-control" id="montant_max" name="montant_max" 
                                           value="{{ request('montant_max') }}" placeholder="Max">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        <a href="{{ route('dettes-cnps.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table des dettes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Dettes CNPS</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'commune', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}">
                                    Commune
                                    @if(request('sort_by') === 'commune')
                                        <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Département</th>
                            <th>Région</th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'montant', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}">
                                    Montant
                                    @if(request('sort_by') === 'montant')
                                        <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'date_evaluation', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}">
                                    Date d'Évaluation
                                    @if(request('sort_by') === 'date_evaluation')
                                        <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dettes as $dette)
                            <tr>
                                <td>
                                    <strong>{{ $dette->commune->nom }}</strong><br>
                                    <small class="text-muted">{{ $dette->commune->code }}</small>
                                </td>
                                <td>{{ $dette->commune->departement->nom }}</td>
                                <td>{{ $dette->commune->departement->region->nom }}</td>
                                <td>
                                    <span class="font-weight-bold text-danger">
                                        {{ number_format($dette->montant, 0, ',', ' ') }} FCFA
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($dette->date_evaluation)->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('dettes-cnps.show', $dette) }}" class="btn btn-info btn-sm" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('dettes-cnps.edit', $dette) }}" class="btn btn-warning btn-sm" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('dettes-cnps.destroy', $dette) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Supprimer"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette dette ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-gray-500">Aucune dette CNPS trouvée</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="row">
                <div class="col-sm-12 col-md-5">
                    <div class="dataTables_info">
                        Affichage de {{ $dettes->firstItem() ?? 0 }} à {{ $dettes->lastItem() ?? 0 }} 
                        sur {{ $dettes->total() }} entrées
                    </div>
                </div>
                <div class="col-sm-12 col-md-7">
                    {{ $dettes->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif
@endsection