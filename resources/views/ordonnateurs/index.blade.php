{{-- @extends('layouts.app')

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
@endpush --}}




@extends('layouts.app')

@section('title', 'Gestion des Ordonnateurs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Gestion des Ordonnateurs</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ordonnateurs</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('ordonnateurs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvel ordonnateur
                    </a>
                </div>
            </div>

            <!-- Messages de feedback -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Ordonnateurs</h6>
                                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
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
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Libres</h6>
                                    <h3 class="mb-0">{{ $stats['libres'] }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-minus fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
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

            <!-- Filtres et recherche -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter"></i> Filtres et recherche
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('ordonnateurs.index') }}" id="filterForm">
                        <div class="row">
                            <!-- Recherche -->
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="search" class="form-label">Recherche</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Nom, fonction, téléphone...">
                            </div>

                            <!-- Filtre par département -->
                            <div class="col-lg-3 col-md-6 mb-3">
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

                            <!-- Filtre par commune -->
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="commune_id" class="form-label">Commune</label>
                                <select class="form-select" id="commune_id" name="commune_id">
                                    <option value="">Toutes les communes</option>
                                    @foreach($communes as $commune)
                                        <option value="{{ $commune->id }}" 
                                                data-departement="{{ $commune->departement_id }}"
                                                {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                                            {{ $commune->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre par statut -->
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="status" class="form-label">Statut</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Tous les statuts</option>
                                    <option value="assigne" {{ request('status') == 'assigne' ? 'selected' : '' }}>Assignés</option>
                                    <option value="libre" {{ request('status') == 'libre' ? 'selected' : '' }}>Libres</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                                <a href="{{ route('ordonnateurs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des ordonnateurs -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Liste des ordonnateurs ({{ $ordonnateurs->total() }})
                    </h5>
                    <div class="btn-group" role="group">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nom', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                           class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sort-alpha-{{ request('sort_direction') === 'desc' ? 'up' : 'down' }}"></i> Nom
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'date_prise_fonction', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                           class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sort-numeric-{{ request('sort_direction') === 'desc' ? 'up' : 'down' }}"></i> Date
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($ordonnateurs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ordonnateur</th>
                                        <th>Fonction</th>
                                        <th>Commune assignée</th>
                                        <th>Date de prise de fonction</th>
                                        <th>Téléphone</th>
                                        <th>Statut</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ordonnateurs as $ordonnateur)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ strtoupper(substr($ordonnateur->nom, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $ordonnateur->nom }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $ordonnateur->fonction }}</span>
                                            </td>
                                            <td>
                                                @if($ordonnateur->commune)
                                                    <div>
                                                        <strong>{{ $ordonnateur->commune->nom }}</strong><br>
                                                        <small class="text-muted">
                                                            {{ $ordonnateur->commune->departement->nom }}
                                                        </small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Non assigné</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($ordonnateur->date_prise_fonction)->format('d/m/Y') }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($ordonnateur->date_prise_fonction)->diffForHumans() }}
                                                </small>
                                            </td>
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
                                                    <span class="badge bg-success">Assigné</span>
                                                @else
                                                    <span class="badge bg-warning">Libre</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('ordonnateurs.show', $ordonnateur) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Voir les détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('ordonnateurs.edit', $ordonnateur) }}" 
                                                       class="btn btn-sm btn-outline-secondary" 
                                                       title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal{{ $ordonnateur->id }}" 
                                                            title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal de suppression pour chaque ordonnateur -->
                                        <div class="modal fade" id="deleteModal{{ $ordonnateur->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-exclamation-triangle"></i> Confirmer la suppression
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Êtes-vous sûr de vouloir supprimer l'ordonnateur <strong>{{ $ordonnateur->nom }}</strong> ?</p>
                                                        @if($ordonnateur->commune)
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle"></i> 
                                                                Cet ordonnateur est actuellement assigné à la commune <strong>{{ $ordonnateur->commune->nom }}</strong>.
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('ordonnateurs.destroy', $ordonnateur) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-trash"></i> Supprimer
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0">
                                        Affichage de {{ $ordonnateurs->firstItem() }} à {{ $ordonnateurs->lastItem() }} 
                                        sur {{ $ordonnateurs->total() }} résultats
                                    </p>
                                </div>
                                <div>
                                    {{ $ordonnateurs->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun ordonnateur trouvé</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'commune_id', 'departement_id', 'status']))
                                    Aucun ordonnateur ne correspond à vos critères de recherche.
                                @else
                                    Commencez par ajouter votre premier ordonnateur.
                                @endif
                            </p>
                            <a href="{{ route('ordonnateurs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ajouter un ordonnateur
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtrage des communes par département
    const departementSelect = document.getElementById('departement_id');
    const communeSelect = document.getElementById('commune_id');
    const allCommuneOptions = Array.from(communeSelect.options);

    function filterCommunes() {
        const selectedDepartement = departementSelect.value;
        
        // Réinitialiser les options
        communeSelect.innerHTML = '<option value="">Toutes les communes</option>';
        
        // Ajouter les communes filtrées
        allCommuneOptions.forEach(option => {
            if (option.value === '' || selectedDepartement === '' || option.dataset.departement === selectedDepartement) {
                communeSelect.appendChild(option.cloneNode(true));
            }
        });
        
        // Maintenir la sélection si elle est toujours valide
        const currentCommune = '{{ request("commune_id") }}';
        if (currentCommune) {
            communeSelect.value = currentCommune;
        }
    }

    departementSelect.addEventListener('change', filterCommunes);

    // Soumission automatique du formulaire après changement de filtre
    const filterInputs = document.querySelectorAll('#filterForm select, #filterForm input');
    filterInputs.forEach(input => {
        if (input.id !== 'search') { // Pas de soumission auto pour la recherche
            input.addEventListener('change', function() {
                setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 100);
            });
        }
    });

    // Recherche avec délai
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 1000); // Attendre 1 seconde après la dernière frappe
    });
});
</script>
@endpush

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
    font-weight: bold;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75em;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.breadcrumb {
    background-color: transparent;
    padding: 0;
    margin: 0;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.modal-header.bg-danger {
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}
</style>
@endpush
