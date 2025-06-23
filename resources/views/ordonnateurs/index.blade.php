@extends('layouts.app')

@section('title', 'Gestion des Ordonnateurs')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3 mb-0">Gestion des Ordonnateurs</h1>
                <a href="{{ route('ordonnateurs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouvel Ordonnateur
                </a>
            </div>

            <!-- Statistiques -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total</h6>
                                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Assignés</h6>
                                    <h3 class="mb-0">{{ $stats['assignes'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Libres</h6>
                                    <h3 class="mb-0">{{ $stats['libres'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-times fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Communes sans ordonnateur</h6>
                                    <h3 class="mb-0">{{ $stats['communes_sans_ordonnateur'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ordonnateurs.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Nom, fonction, téléphone...">
                </div>

                <div class="col-md-3">
                    <label for="departement_id" class="form-label">Département</label>
                    <select class="form-select" id="departement_id" name="departement_id">
                        <option value="">Tous les départements</option>
                        @foreach($departements as $departement)
                            <option value="{{ $departement->id }}"
                                    {{ request('departement_id') == $departement->id ? 'selected' : '' }}>
                                {{ $departement->nom }} ({{ $departement->region->nom }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="commune_id" class="form-label">Commune</label>
                    <select class="form-select" id="commune_id" name="commune_id">
                        <option value="">Toutes les communes</option>
                        @foreach($communes as $commune)
                            <option value="{{ $commune->id }}"
                                    data-departement="{{ $commune->departement_id }}"
                                    {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                                {{ $commune->nom }} ({{ $commune->departement->nom }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous</option>
                        <option value="assigne" {{ request('status') == 'assigne' ? 'selected' : '' }}>Assignés</option>
                        <option value="libre" {{ request('status') == 'libre' ? 'selected' : '' }}>Libres</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('ordonnateurs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des ordonnateurs -->
    <div class="card">
        <div class="card-body">
            @if($ordonnateurs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nom', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-white text-decoration-none">
                                        Nom
                                        @if(request('sort_by') === 'nom')
                                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Fonction</th>
                                <th>Téléphone</th>
                                <th>Commune</th>
                                <th>Département</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'date_prise_fonction', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-white text-decoration-none">
                                        Date prise fonction
                                        @if(request('sort_by') === 'date_prise_fonction')
                                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ordonnateurs as $ordonnateur)
                                <tr>
                                    <td>
                                        <strong>{{ $ordonnateur->nom }}</strong>
                                    </td>
                                    <td>{{ $ordonnateur->fonction }}</td>
                                    <td>
                                        @if($ordonnateur->telephone)
                                            <a href="tel:{{ $ordonnateur->telephone }}" class="text-decoration-none">
                                                {{ $ordonnateur->telephone }}
                                            </a>
                                        @else
                                            <span class="text-muted">Non renseigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ordonnateur->commune)
                                            <span class="badge bg-success">{{ $ordonnateur->commune->nom }}</span>
                                        @else
                                            <span class="badge bg-secondary">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ordonnateur->commune)
                                            {{ $ordonnateur->commune->departement->nom }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($ordonnateur->date_prise_fonction)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($ordonnateur->commune_id)
                                            <span class="badge bg-success">Assigné</span>
                                        @else
                                            <span class="badge bg-warning">Libre</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('ordonnateurs.show', $ordonnateur) }}"
                                               class="btn btn-sm btn-outline-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('ordonnateurs.edit', $ordonnateur) }}"
                                               class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmerSuppression({{ $ordonnateur->id }})" title="Supprimer">
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
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $ordonnateurs->firstItem() }} à {{ $ordonnateurs->lastItem() }}
                        sur {{ $ordonnateurs->total() }} résultats
                    </div>
                    {{ $ordonnateurs->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucun ordonnateur trouvé</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'commune_id', 'departement_id', 'status']))
                            Aucun ordonnateur ne correspond aux critères de recherche.
                        @else
                            Commencez par ajouter des ordonnateurs.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'commune_id', 'departement_id', 'status']))
                        <a href="{{ route('ordonnateurs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Ajouter un ordonnateur
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cet ordonnateur ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmerSuppression(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/ordonnateurs/${id}`;
    new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
}

// Filtrage des communes par département
document.getElementById('departement_id').addEventListener('change', function() {
    const departementId = this.value;
    const communeSelect = document.getElementById('commune_id');
    const options = communeSelect.querySelectorAll('option');

    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
        } else {
            const optionDepartement = option.getAttribute('data-departement');
            option.style.display = (!departementId || optionDepartement === departementId) ? 'block' : 'none';
        }
    });

    // Réinitialiser la sélection de commune si elle n'est plus visible
    if (communeSelect.value && communeSelect.querySelector(`option[value="${communeSelect.value}"]`).style.display === 'none') {
        communeSelect.value = '';
    }
});

// Initialiser le filtrage au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const departementSelect = document.getElementById('departement_id');
    if (departementSelect.value) {
        departementSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
