{{-- @extends('layouts.app')

@section('title', 'Liste des Prévisions')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0">Prévisions {{ $annee }}</h2>
            <p class="text-muted mb-0">Gestion des prévisions budgétaires communales</p>
        </div>
        <div>
            <a href="{{ route('previsions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Prévision
            </a>
        </div>
    </div>

    <!-- Messages d'alerte -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-chart-line text-primary fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Prévisions</h6>
                            <h4 class="mb-0">{{ number_format($stats['nb_previsions']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-money-bill-wave text-success fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Montant Prévu</h6>
                            <h4 class="mb-0">{{ number_format($stats['montant_total_previsions'], 0, ',', ' ') }} FCFA</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-coins text-info fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Montant Réalisé</h6>
                            <h4 class="mb-0">{{ number_format($stats['montant_total_realisations'], 0, ',', ' ') }} FCFA</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-percentage text-warning fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Taux Réalisation</h6>
                            <h4 class="mb-0">{{ number_format($stats['taux_realisation_moyen'], 1) }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('previsions.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label for="annee" class="form-label">Année</label>
                    <select name="annee" id="annee" class="form-select">
                        @foreach($anneesDisponibles as $year)
                            <option value="{{ $year }}" {{ $year == $annee ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="departement_id" class="form-label">Département</label>
                    <select name="departement_id" id="departement_id" class="form-select">
                        <option value="">Tous les départements</option>
                        @foreach($departements as $departement)
                            <option value="{{ $departement->id }}" {{ $departement->id == $departementId ? 'selected' : '' }}>
                                {{ $departement->nom }} ({{ $departement->region->nom }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="commune_id" class="form-label">Commune</label>
                    <select name="commune_id" id="commune_id" class="form-select">
                        <option value="">Toutes les communes</option>
                        @foreach($communes as $commune)
                            <option value="{{ $commune->id }}" {{ $commune->id == $communeId ? 'selected' : '' }}>
                                {{ $commune->nom }} ({{ $commune->departement->nom }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Nom ou code commune..." value="{{ request('search') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-outline-primary d-block">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Actions groupées -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="text-muted">{{ $previsions->total() }} prévision(s) trouvée(s)</span>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-download me-2"></i>Exporter
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('previsions.export', array_merge(request()->all(), ['format' => 'excel'])) }}">
                    <i class="fas fa-file-excel text-success me-2"></i>Excel
                </a></li>
                <li><a class="dropdown-item" href="{{ route('previsions.export', array_merge(request()->all(), ['format' => 'pdf'])) }}">
                    <i class="fas fa-file-pdf text-danger me-2"></i>PDF
                </a></li>
                <li><a class="dropdown-item" href="{{ route('previsions.export', array_merge(request()->all(), ['format' => 'csv'])) }}">
                    <i class="fas fa-file-csv text-info me-2"></i>CSV
                </a></li>
            </ul>
        </div>
    </div>

    <!-- Tableau des prévisions -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'commune', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" 
                               class="text-decoration-none text-dark">
                                Commune
                                @if(request('sort_by') == 'commune')
                                    <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </a>
                        </th>
                        <th>Département</th>
                        <th>Région</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'montant', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" 
                               class="text-decoration-none text-dark">
                                Montant Prévu
                                @if(request('sort_by') == 'montant')
                                    <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </a>
                        </th>
                        <th>Montant Réalisé</th>
                        <th>Taux Réalisation</th>
                        <th>Statut</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($previsions as $prevision)
                        <tr>
                            <td class="fw-medium">
                                <a href="{{ route('previsions.show', $prevision) }}" class="text-decoration-none">
                                    {{ $prevision->commune->nom }}
                                </a>
                            </td>
                            <td>{{ $prevision->commune->departement->nom }}</td>
                            <td>{{ $prevision->commune->departement->region->nom }}</td>
                            <td class="fw-medium">{{ number_format($prevision->montant, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($prevision->montant_realise, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 60px; height: 6px;">
                                        <div class="progress-bar bg-{{ $prevision->taux_realisation >= 80 ? 'success' : ($prevision->taux_realisation >= 50 ? 'warning' : 'danger') }}" 
                                             style="width: {{ min($prevision->taux_realisation, 100) }}%"></div>
                                    </div>
                                    <span class="small">{{ number_format($prevision->taux_realisation, 1) }}%</span>
                                </div>
                            </td>
                            <td>
                                @if($prevision->taux_realisation >= 90)
                                    <span class="badge bg-success">Excellent</span>
                                @elseif($prevision->taux_realisation >= 70)
                                    <span class="badge bg-primary">Bon</span>
                                @elseif($prevision->taux_realisation >= 50)
                                    <span class="badge bg-warning">Moyen</span>
                                @else
                                    <span class="badge bg-danger">Faible</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('previsions.show', $prevision) }}" class="btn btn-outline-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('previsions.edit', $prevision) }}" class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Supprimer" 
                                            onclick="confirmDelete({{ $prevision->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Aucune prévision trouvée pour les critères sélectionnés</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($previsions->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $previsions->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette prévision ?</p>
                <p class="text-danger small"><i class="fas fa-exclamation-triangle me-1"></i>Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    document.getElementById('deleteForm').action = `/previsions/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Filtrage dynamique des communes par département
document.getElementById('departement_id').addEventListener('change', function() {
    const departementId = this.value;
    const communeSelect = document.getElementById('commune_id');
    
    // Réinitialiser
    communeSelect.innerHTML = '<option value="">Toutes les communes</option>';
    
    if (departementId) {
        @json($communes).forEach(function(commune) {
            if (commune.departement_id == departementId) {
                const option = new Option(commune.nom, commune.id);
                communeSelect.add(option);
            }
        });
    } else {
        @json($communes).forEach(function(commune) {
            const option = new Option(`${commune.nom} (${commune.departement.nom})`, commune.id);
            communeSelect.add(option);
        });
    }
});
</script>
@endpush
@endsection --}}


@extends('layouts.app')

@section('title', 'Prévisions budgétaires')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestion des Prévisions Budgétaires</h3>
                    <a href="{{ route('previsions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle Prévision
                    </a>
                    
                </div>

                <div class="card-body">
                    <!-- Statistiques générales -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Prévisions</span>
                                    <span class="info-box-number">{{ $stats['total_previsions'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Montant Prévu</span>
                                    <span class="info-box-number">{{ number_format($stats['montant_total_prevu'], 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Montant Réalisé</span>
                                    <span class="info-box-number">{{ number_format($stats['montant_total_realise'], 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Taux Moyen</span>
                                    <span class="info-box-number">{{ number_format($stats['taux_moyen'], 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Filtres et Recherche</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('previsions.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="annee_exercice">Année d'exercice</label>
                                        <select name="annee_exercice" id="annee_exercice" class="form-control">
                                            <option value="">Toutes les années</option>
                                            @foreach($annees as $annee)
                                                <option value="{{ $annee }}" {{ request('annee_exercice') == $annee ? 'selected' : '' }}>
                                                    {{ $annee }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="commune_id">Commune</label>
                                        <select name="commune_id" id="commune_id" class="form-control">
                                            <option value="">Toutes les communes</option>
                                            @foreach($communes as $commune)
                                                <option value="{{ $commune->id }}" {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                                                    {{ $commune->nom }} ({{ $commune->departement->nom }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="search">Recherche</label>
                                        <input type="text" name="search" id="search" class="form-control" 
                                               value="{{ request('search') }}" placeholder="Nom ou code commune">
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Filtrer
                                            </button>
                                            <a href="{{ route('previsions.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Réinitialiser
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tableau des prévisions -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Année</th>
                                    <th>Commune</th>
                                    <th>Département</th>
                                    <th>Région</th>
                                    <th>Montant Prévu</th>
                                    <th>Montant Réalisé</th>
                                    <th>Taux Réalisation</th>
                                    <th>Évaluation</th>
                                    <th>Nb Réalisations</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($previsions as $prevision)
                                <tr>
                                    <td>{{ $prevision->annee_exercice }}</td>
                                    <td>{{ $prevision->commune->nom }}</td>
                                    <td>{{ $prevision->commune->departement->nom }}</td>
                                    <td>{{ $prevision->commune->departement->region->nom }}</td>
                                    <td>{{ number_format($prevision->montant, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ number_format($prevision->montant_realise, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="badge badge-{{ $prevision->taux_realisation >= 90 ? 'success' : ($prevision->taux_realisation >= 75 ? 'info' : ($prevision->taux_realisation >= 50 ? 'warning' : 'danger')) }}">
                                            {{ number_format($prevision->taux_realisation, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $prevision->evaluation == 'Excellent' ? 'success' : ($prevision->evaluation == 'Bon' ? 'info' : ($prevision->evaluation == 'Moyen' ? 'warning' : 'danger')) }}">
                                            {{ $prevision->evaluation }}
                                        </span>
                                    </td>
                                    <td>{{ $prevision->nb_realisations }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('previsions.show', $prevision) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('previsions.edit', $prevision) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('previsions.destroy', $prevision) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette prévision ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Aucune prévision trouvée</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $previsions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Script pour améliorer l'UX des filtres
    document.addEventListener('DOMContentLoaded', function() {
        const communeSelect = document.getElementById('commune_id');
        const searchInput = document.getElementById('search');
        
        // Auto-submit sur changement de commune
        communeSelect.addEventListener('change', function() {
            this.form.submit();
        });
        
        // Recherche en temps réel avec délai
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    });
</script>
@endsection