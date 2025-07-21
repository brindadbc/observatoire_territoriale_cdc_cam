{{-- @extends('layouts.app')

@section('title', 'Gestion des Réalisations')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Réalisations {{ $annee }}</h1>
            <p class="text-muted">Gestion et suivi des réalisations budgétaires</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('realisations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Réalisation
            </a>
             <a href="{{ route('realisations.statistiques') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> rapport
            </a>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('realisations.export', array_merge(request()->query(), ['format' => 'excel'])) }}">
                        <i class="fas fa-file-excel text-success"></i> Excel
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('realisations.export', array_merge(request()->query(), ['format' => 'pdf'])) }}">
                        <i class="fas fa-file-pdf text-danger"></i> PDF
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('realisations.export', array_merge(request()->query(), ['format' => 'csv'])) }}">
                        <i class="fas fa-file-csv text-info"></i> CSV
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <h5 class="card-title">{{ number_format($stats['nb_realisations']) }}</h5>
                    <p class="card-text text-muted small">Réalisations</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                    <h5 class="card-title">{{ number_format($stats['montant_total_realisations'], 0, ',', ' ') }} FCFA</h5>
                    <p class="card-text text-muted small">Montant Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-percentage fa-2x"></i>
                    </div>
                    <h5 class="card-title">{{ number_format($stats['taux_realisation_global'], 1) }}%</h5>
                    <p class="card-text text-muted small">Taux de Réalisation</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-map-marker-alt fa-2x"></i>
                    </div>
                    <h5 class="card-title">{{ $stats['nb_communes_realisatrices'] }}</h5>
                    <p class="card-text text-muted small">Communes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-filter"></i> Filtres de recherche
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('realisations.index') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Année</label>
                        <select name="annee" class="form-select">
                            @foreach($anneesDisponibles as $anneeDisp)
                                <option value="{{ $anneeDisp }}" {{ $annee == $anneeDisp ? 'selected' : '' }}>
                                    {{ $anneeDisp }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Commune</label>
                        <select name="commune_id" class="form-select">
                            <option value="">Toutes les communes</option>
                            @foreach($communes as $commune)
                                <option value="{{ $commune->id }}" {{ $communeId == $commune->id ? 'selected' : '' }}>
                                    {{ $commune->nom }} ({{ $commune->departement->nom }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Département</label>
                        <select name="departement_id" class="form-select">
                            <option value="">Tous</option>
                            @foreach($departements as $departement)
                                <option value="{{ $departement->id }}" {{ $departementId == $departement->id ? 'selected' : '' }}>
                                    {{ $departement->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Recherche</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Nom ou code commune..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('realisations.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des réalisations -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Liste des Réalisations</h6>
            <small class="text-muted">{{ $realisations->total() }} résultats</small>
        </div>
        <div class="card-body p-0">
            @if($realisations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'date_realisation', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Date
                                        @if(request('sort_by') === 'date_realisation')
                                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Commune</th>
                                <th>Département</th>
                                <th class="text-end">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'montant', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Montant
                                        @if(request('sort_by') === 'montant')
                                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-end">Prévision</th>
                                <th class="text-center">Taux</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($realisations as $realisation)
                                <tr>
                                    <td>{{ $realisation->date_realisation->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="fw-medium">{{ $realisation->commune->nom }}</div>
                                        <small class="text-muted">{{ $realisation->commune->code }}</small>
                                    </td>
                                    <td>{{ $realisation->commune->departement->nom }}</td>
                                    <td class="text-end">
                                        <strong>{{ number_format($realisation->montant, 0, ',', ' ') }}</strong>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>
                                    <td class="text-end">
                                        @if($realisation->prevision)
                                            {{ number_format($realisation->prevision->montant, 0, ',', ' ') }}
                                            <small class="text-muted d-block">FCFA</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($realisation->pourcentage_prevision)
                                            <span class="badge bg-{{ $realisation->pourcentage_prevision >= 100 ? 'success' : ($realisation->pourcentage_prevision >= 75 ? 'warning' : 'danger') }}">
                                                {{ number_format($realisation->pourcentage_prevision, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $realisation->status_realisation === 'Objectif atteint' ? 'success' : ($realisation->status_realisation === 'Bon' ? 'info' : ($realisation->status_realisation === 'Moyen' ? 'warning' : 'secondary')) }}">
                                            {{ $realisation->status_realisation }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('realisations.show', $realisation) }}" 
                                               class="btn btn-outline-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('realisations.edit', $realisation) }}" 
                                               class="btn btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('realisations.destroy', $realisation) }}" 
                                                  class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réalisation ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer">
                    {{ $realisations->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5>Aucune réalisation trouvée</h5>
                    <p class="text-muted">Aucune réalisation ne correspond à vos critères de recherche.</p>
                    <a href="{{ route('realisations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Créer une réalisation
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-submit du formulaire de filtre lors du changement d'année
    document.querySelector('select[name="annee"]').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endpush

@endsection --}}




@extends('layouts.app')

@section('title', 'Gestion des Réalisations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestion des Réalisations</h3>
                    <div>
                        <a href="{{ route('realisations.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Réalisation
                        </a>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-download"></i> Exporter
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Statistiques générales --}}
                    @if($statistiques)
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Réalisations</h5>
                                    <h3>{{ $statistiques['total_realisations'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Montant Total</h5>
                                    <h3>{{ number_format($statistiques['montant_total'], 0, ',', ' ') }} FCFA</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Taux Moyen</h5>
                                    <h3>{{ number_format($statistiques['taux_moyen'], 1) }}%</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Communes Excellentes</h5>
                                    <h3>{{ $statistiques['communes_excellentes'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Filtres --}}
                    <form method="GET" action="{{ route('realisations.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="annee" class="form-label">Année</label>
                                <select name="annee" id="annee" class="form-select">
                                    <option value="">Toutes les années</option>
                                    @foreach($annees as $annee)
                                        <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>
                                            {{ $annee }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="commune_id" class="form-label">Commune</label>
                                <select name="commune_id" id="commune_id" class="form-select">
                                    <option value="">Toutes les communes</option>
                                    @foreach($communes as $commune)
                                        <option value="{{ $commune->id }}" {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                                            {{ $commune->nom }} ({{ $commune->departement->nom }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Recherche</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       value="{{ request('search') }}" placeholder="Nom ou code commune">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filtrer
                                    </button>
                                    <a href="{{ route('realisations.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Tableau des réalisations --}}
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'annee_exercice', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}">
                                            Année
                                            @if(request('sort_by') === 'annee_exercice')
                                                <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Commune</th>
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
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'date_realisation', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}">
                                            Date Réalisation
                                            @if(request('sort_by') === 'date_realisation')
                                                <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Prévision</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($realisations as $realisation)
                                <tr>
                                    <td>{{ $realisation->annee_exercice }}</td>
                                    <td>{{ $realisation->commune->nom }}</td>
                                    <td>{{ $realisation->commune->departement->nom }}</td>
                                    <td>{{ $realisation->commune->departement->region->nom }}</td>
                                    <td>{{ number_format($realisation->montant, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ \Carbon\Carbon::parse($realisation->date_realisation)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($realisation->prevision)
                                            {{ number_format($realisation->prevision->montant, 0, ',', ' ') }} FCFA
                                        @else
                                            <span class="text-muted">Non liée</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('realisations.show', $realisation) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('realisations.edit', $realisation) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('realisations.destroy', $realisation) }}" 
                                                  style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réalisation ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Aucune réalisation trouvée</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    {{ $realisations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal d'export --}}
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exporter les réalisations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('realisations.export') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_annee" class="form-label">Année</label>
                        <select name="annee" id="export_annee" class="form-select" required>
                            @foreach($annees as $annee)
                                <option value="{{ $annee }}" {{ $annee == date('Y') ? 'selected' : '' }}>
                                    {{ $annee }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_format" class="form-label">Format</label>
                        <select name="format" id="export_format" class="form-select">
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Exporter</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    document.getElementById('annee').addEventListener('change', function() {
        this.form.submit();
    });
    
    document.getElementById('commune_id').addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
@endpush