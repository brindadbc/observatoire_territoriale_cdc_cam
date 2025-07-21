@extends('layouts.app')

@section('title', 'Gestion des Dépôts de Comptes')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Gestion des Dépôts de Comptes</h1>
                <div class="btn-toolbar">
                    <a href="{{ route('depot-comptes.create') }}" class="btn btn-primary me-2">
                        <i class="fas fa-plus"></i> Nouveau Dépôt
                    </a>
                    <a href="{{ route('depot-comptes.rapport') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Rapport
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <div class="text-primary">
                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                    </div>
                    <h4 class="card-title">{{ $stats['total_depots'] }}</h4>
                    <p class="card-text text-muted">Total Dépôts</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                    </div>
                    <h4 class="card-title">{{ $stats['depots_valides'] }}</h4>
                    <p class="card-text text-muted">Dépôts Validés</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <div class="text-danger">
                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                    </div>
                    <h4 class="card-title">{{ $stats['depots_invalides'] }}</h4>
                    <p class="card-text text-muted">Dépôts Non Validés</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <div class="text-info">
                        <i class="fas fa-percentage fa-2x mb-2"></i>
                    </div>
                    <h4 class="card-title">{{ $stats['taux_validation'] }}%</h4>
                    <p class="card-text text-muted">Taux de Validation</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('depot-comptes.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Commune, receveur, année...">
                    </div>
                    <div class="col-md-2">
                        <label for="annee_exercice" class="form-label">Année</label>
                        <select class="form-select" id="annee_exercice" name="annee_exercice">
                            <option value="">Toutes</option>
                            @foreach($anneesExercice as $annee)
                                <option value="{{ $annee }}" {{ request('annee_exercice') == $annee ? 'selected' : '' }}>
                                    {{ $annee }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="commune_id" class="form-label">Commune</label>
                        <select class="form-select" id="commune_id" name="commune_id">
                            <option value="">Toutes les communes</option>
                            @foreach($communes as $commune)
                                <option value="{{ $commune->id }}" {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                                    {{ $commune->nom }} ({{ $commune->departement->nom }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="validation" class="form-label">Statut</label>
                        <select class="form-select" id="validation" name="validation">
                            <option value="">Tous</option>
                            <option value="1" {{ request('validation') === '1' ? 'selected' : '' }}>Validé</option>
                            <option value="0" {{ request('validation') === '0' ? 'selected' : '' }}>Non Validé</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tableau des dépôts -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Dépôts de Comptes</h5>
            <small class="text-muted">{{ $depotComptes->total() }} résultat(s)</small>
        </div>
        <div class="card-body p-0">
            @if($depotComptes->count() > 0)
                <!-- Actions de masse -->
                <div class="p-3 border-bottom">
                    <form id="bulk-action-form" action="{{ route('depot-comptes.bulk-validation') }}" method="POST">
                        @csrf
                        <div class="row align-items-end g-2">
                            <div class="col-auto">
                                <label class="form-label small">Actions sur la sélection :</label>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="action" value="validate" class="btn btn-sm btn-success" disabled id="validate-btn">
                                    <i class="fas fa-check"></i> Valider
                                </button>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="action" value="invalidate" class="btn btn-sm btn-warning" disabled id="invalidate-btn">
                                    <i class="fas fa-times"></i> Invalider
                                </button>
                            </div>
                            <div class="col-auto">
                                <small class="text-muted">
                                    <span id="selected-count">0</span> élément(s) sélectionné(s)
                                </small>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'date_depot', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none">
                                        Date Dépôt
                                        @if(request('sort_by') === 'date_depot')
                                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Commune</th>
                                <th>Département</th>
                                <th>Receveur</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'annee_exercice', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none">
                                        Année
                                        @if(request('sort_by') === 'annee_exercice')
                                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($depotComptes as $depot)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="depot_ids[]" value="{{ $depot->id }}" 
                                               class="form-check-input depot-checkbox">
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ \Carbon\Carbon::parse($depot->date_depot)->format('d/m/Y') }}</span>
                                        <small class="d-block text-muted">{{ \Carbon\Carbon::parse($depot->date_depot)->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-city text-primary"></i>
                                            </div>
                                            <div>
                                                <span class="fw-medium">{{ $depot->commune->nom }}</span>
                                                <small class="d-block text-muted">Code: {{ $depot->commune->code ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $depot->commune->departement->nom }}</span>
                                        <small class="d-block text-muted mt-1">{{ $depot->commune->departement->region->nom }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-info"></i>
                                            </div>
                                            <span>{{ $depot->receveur->nom }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary fs-6">{{ $depot->annee_exercice }}</span>
                                    </td>
                                    <td>
                                        @if($depot->validation)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Validé
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>En attente
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('depot-comptes.show', $depot) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('depot-comptes.edit', $depot) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Supprimer"
                                                    onclick="confirmDelete('{{ $depot->id }}', '{{ $depot->commune->nom }} - {{ $depot->annee_exercice }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer">
                    {{ $depotComptes->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-inbox fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Aucun dépôt de compte trouvé</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'annee_exercice', 'commune_id', 'validation']))
                            Aucun résultat ne correspond à vos critères de recherche.
                            <br>
                            <a href="{{ route('depot-comptes.index') }}" class="btn btn-link">Réinitialiser les filtres</a>
                        @else
                            Commencez par créer votre premier dépôt de compte.
                            <br>
                            <a href="{{ route('depot-comptes.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus"></i> Nouveau Dépôt
                            </a>
                        @endif
                    </p>
                </div>
            @endif
        </div>
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
                <p>Êtes-vous sûr de vouloir supprimer le dépôt de compte de <strong id="depot-info"></strong> ?</p>
                <p class="text-danger small">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la sélection multiple
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.depot-checkbox');
    const selectedCount = document.getElementById('selected-count');
    const validateBtn = document.getElementById('validate-btn');
    const invalidateBtn = document.getElementById('invalidate-btn');
    const bulkForm = document.getElementById('bulk-action-form');

    function updateSelection() {
        const selected = document.querySelectorAll('.depot-checkbox:checked');
        selectedCount.textContent = selected.length;
        
        const hasSelection = selected.length > 0;
        validateBtn.disabled = !hasSelection;
        invalidateBtn.disabled = !hasSelection;
        
        // Mettre à jour les IDs sélectionnés dans le formulaire
        const existingInputs = bulkForm.querySelectorAll('input[name="depot_ids[]"]');
        existingInputs.forEach(input => input.remove());
        
        selected.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'depot_ids[]';
            input.value = checkbox.value;
            bulkForm.appendChild(input);
        });
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelection();
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const someChecked = Array.from(checkboxes).some(cb => cb.checked);
            
            selectAll.checked = allChecked;
            selectAll.indeterminate = someChecked && !allChecked;
            
            updateSelection();
        });
    });

    // Confirmation des actions de masse
    validateBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const count = document.querySelectorAll('.depot-checkbox:checked').length;
        if (confirm(`Êtes-vous sûr de vouloir valider ${count} dépôt(s) de compte ?`)) {
            bulkForm.submit();
        }
    });

    invalidateBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const count = document.querySelectorAll('.depot-checkbox:checked').length;
        if (confirm(`Êtes-vous sûr de vouloir invalider ${count} dépôt(s) de compte ?`)) {
            bulkForm.submit();
        }
    });
});

// Fonction pour confirmer la suppression
function confirmDelete(depotId, depotInfo) {
    document.getElementById('depot-info').textContent = depotInfo;
    document.getElementById('delete-form').action = `/depot-comptes/${depotId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush