@extends('layouts.app')

@section('title', 'Dettes Fiscales')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 text-gray-800">Gestion des Dettes Fiscales</h1>
            <p class="text-muted">Suivi et gestion des dettes fiscales des communes</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('dettes-fiscale.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Dette
            </a>
            <a href="{{ route('dettes-fiscale.rapport-comparatif') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> Rapport-comparatif
            </a>
           
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
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
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
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
                                Communes Concernées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['nb_communes_concernees'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
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
                                Dette Moyenne
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['dette_moyenne'], 0, ',', ' ') }} FCFA
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
                                Dette Maximale
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['dette_max'], 0, ',', ' ') }} FCFA
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

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres de Recherche</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dettes-fiscale.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Recherche</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Nom ou code commune">
                        </div>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="commune_id">Commune</label>
                            <select class="form-control" id="commune_id" name="commune_id">
                                <option value="">Toutes les communes</option>
                                @foreach($communes as $commune)
                                    <option value="{{ $commune->id }}" {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                                        {{ $commune->nom }} ({{ $commune->departement->nom }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_debut">Date début</label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                   value="{{ request('date_debut') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_fin">Date fin</label>
                            <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                   value="{{ request('date_fin') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="montant_min">Montant min</label>
                            <input type="number" class="form-control" id="montant_min" name="montant_min" 
                                   value="{{ request('montant_min') }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="montant_max">Montant max</label>
                            <input type="number" class="form-control" id="montant_max" name="montant_max" 
                                   value="{{ request('montant_max') }}" placeholder="999999999">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                                <a href="{{ route('dettes-fiscale.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des dettes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Dettes Fiscales</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Actions d'export:</div>
                    <a class="dropdown-item" href="{{ route('dettes-fiscale.export', ['format' => 'excel']) }}">
                        <i class="fas fa-file-excel fa-sm fa-fw mr-2 text-gray-400"></i>
                        Export Excel
                    </a>
                    <a class="dropdown-item" href="{{ route('dettes-fiscale.export', ['format' => 'pdf']) }}">
                        <i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-gray-400"></i>
                        Export PDF
                    </a>
                    <a class="dropdown-item" href="{{ route('dettes-fiscale.export', ['format' => 'csv']) }}">
                        <i class="fas fa-file-csv fa-sm fa-fw mr-2 text-gray-400"></i>
                        Export CSV
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'commune_id', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    Commune
                                    @if(request('sort_by') == 'commune_id')
                                        <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Département</th>
                            <th>Région</th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'montant', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    Montant
                                    @if(request('sort_by') == 'montant')
                                        <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'date_evaluation', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    Date d'évaluation
                                    @if(request('sort_by') == 'date_evaluation')
                                        <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
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
                                <span class="badge badge-{{ $dette->montant > 50000000 ? 'danger' : ($dette->montant > 10000000 ? 'warning' : 'success') }}">
                                    {{ number_format($dette->montant, 0, ',', ' ') }} FCFA
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($dette->date_evaluation)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('dettes-fiscale.show', $dette) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('dettes-fiscale.edit', $dette) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('dettes-fiscale.destroy', $dette) }}" class="d-inline" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette dette ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted">Aucune dette fiscale trouvée</p>
                                    <a href="{{ route('dettes-fiscale.create') }}" class="btn btn-primary">
                                        Ajouter une nouvelle dette
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $dettes->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form on select change
    $('#region_id, #commune_id').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush
@endsection




