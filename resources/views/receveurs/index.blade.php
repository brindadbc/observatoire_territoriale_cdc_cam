@extends('layouts.app')

@section('title', 'Liste des Receveurs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Gestion des Receveurs</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">Receveurs</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Messages de succès/erreur --}}
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

    {{-- Statistiques --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Receveurs</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stats['total'] }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-user text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Disponibles</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stats['disponibles'] }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-check-circle text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Assignés</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stats['assignes'] }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-map text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Actifs</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stats['actifs'] }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-user-check text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <div class="flex-grow-1">
                            <a href="{{ route('receveurs.create') }}" class="btn btn-primary add-btn">
                                <i class="ri-add-line align-bottom me-1"></i> Nouveau Receveur
                            </a>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="hstack text-nowrap gap-2">
                                <button class="btn btn-soft-success" onclick="exportData()">
                                    <i class="ri-file-excel-2-line align-bottom me-1"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Filtres --}}
                    <form method="GET" action="{{ route('receveurs.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-xl-3">
                                <div class="search-box">
                                    <input type="text" name="search" class="form-control search" 
                                           placeholder="Rechercher..." value="{{ request('search') }}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            
                            <div class="col-xl-2">
                                <select name="statut" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    @foreach($statuts as $statut)
                                        <option value="{{ $statut }}" {{ request('statut') == $statut ? 'selected' : '' }}>
                                            {{ $statut }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-xl-3">
                                <select name="commune_id" class="form-select">
                                    <option value="">Toutes les communes</option>
                                    @foreach($communes as $commune)
                                        <option value="{{ $commune->id }}" {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                                            {{ $commune->nom }} - {{ $commune->departement->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-xl-2">
                                <select name="disponibilite" class="form-select">
                                    <option value="">Tous</option>
                                    <option value="disponible" {{ request('disponibilite') == 'disponible' ? 'selected' : '' }}>
                                        Disponibles
                                    </option>
                                    <option value="assigne" {{ request('disponibilite') == 'assigne' ? 'selected' : '' }}>
                                        Assignés
                                    </option>
                                </select>
                            </div>

                            <div class="col-xl-2">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="ri-equalizer-fill me-1"></i> Filtrer
                                    </button>
                                    <a href="{{ route('receveurs.index') }}" class="btn btn-light">
                                        <i class="ri-refresh-line me-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Tableau --}}
                    <div class="table-responsive table-card mb-1">
                        <table class="table align-middle table-nowrap" id="receveurTable">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nom', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-dark">
                                            Nom 
                                            @if(request('sort_by') == 'nom')
                                                <i class="ri-arrow-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}-line"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col">Matricule</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Commune</th>
                                    <th scope="col">Département</th>
                                    <th scope="col">Région</th>
                                    <th scope="col">Date Fonction</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                @forelse($receveurs as $receveur)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 ms-2 name">
                                                <strong>{{ $receveur->nom }}</strong>
                                                @if($receveur->telephone)
                                                    <br><small class="text-muted">{{ $receveur->telephone }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $receveur->matricule }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $receveur->statut == 'Actif' ? 'success' : 
                                            ($receveur->statut == 'Inactif' ? 'danger' : 'warning') 
                                        }}">
                                            {{ $receveur->statut }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($receveur->commune)
                                            {{ $receveur->commune->nom }}
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($receveur->commune)
                                            {{ $receveur->commune->departement->nom }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($receveur->commune)
                                            {{ $receveur->commune->departement->region->nom }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($receveur->date_prise_fonction)->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="edit">
                                                <a href="{{ route('receveurs.show', $receveur) }}" 
                                                   class="btn btn-sm btn-primary edit-item-btn" 
                                                   title="Voir les détails">
                                                    <i class="ri-eye-line"></i>Voir
                                                </a>
                                            </div>
                                            <div class="edit">
                                                <a href="{{ route('receveurs.edit', $receveur) }}" 
                                                   class="btn btn-sm btn-success edit-item-btn"
                                                   title="Modifier">
                                                    <i class="ri-pencil-fill"></i>Modifier
                                                </a>
                                            </div>
                                            <div class="remove">
                                                <button class="btn btn-sm btn-danger remove-item-btn" 
                                                        onclick="confirmDelete({{ $receveur->id }})"
                                                        title="Supprimer">
                                                    <i class="ri-delete-bin-fill"></i>Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-center">
                                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                                colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px">
                                            </lord-icon>
                                            <h5 class="mt-2">Aucun receveur trouvé</h5>
                                            <p class="text-muted mb-0">Aucun receveur ne correspond à vos critères de recherche.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-end">
                        {{ $receveurs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmation de suppression --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce receveur ?</p>
                <p class="text-muted">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Extrait de la vue index pour montrer l'utilisation du composant AJAX -->
<div class="table-responsive">
    <table class="table table-striped table-hover">
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
                <th>Matricule</th>
                <th>Statut</th>
                <th>Commune assignée</th>
                <th>Date prise fonction</th>
                <th>Téléphone</th>
                <th class="text-center">Actions rapides</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receveurs as $receveur)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                {{ strtoupper(substr($receveur->nom, 0, 2)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $receveur->nom }}</div>
                                <small class="text-muted">
                                    Créé le {{ $receveur->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-primary">{{ $receveur->matricule }}</span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $receveur->statut === 'Actif' ? 'success' : ($receveur->statut === 'Inactif' ? 'danger' : 'warning') }}">
                            {{ $receveur->statut }}
                        </span>
                    </td>
                    <td>
                        @if($receveur->commune)
                            <div>
                                <div class="fw-bold">{{ $receveur->commune->nom }}</div>
                                <small class="text-muted">
                                    {{ $receveur->commune->departement->nom }}, 
                                    {{ $receveur->commune->departement->region->nom }}
                                </small>
                            </div>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-exclamation-triangle"></i> Non assigné
                            </span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($receveur->date_prise_fonction)->format('d/m/Y') }}</td>
                    <td>{{ $receveur->telephone ?? '-' }}</td>
                    <td class="text-center">
                        <!-- Inclure le composant AJAX -->
                        @include('receveurs.partials.ajax-controls', ['receveur' => $receveur, 'communes' => $communes])
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('receveurs.show', $receveur) }}" 
                               class="btn btn-outline-info" 
                               title="Voir les détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('receveurs.edit', $receveur) }}" 
                               class="btn btn-outline-warning" 
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($receveur->depotsComptes()->count() == 0)
                                <form method="POST" 
                                      action="{{ route('receveurs.destroy', $receveur) }}" 
                                      class="d-inline"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce receveur ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-outline-danger" 
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-outline-danger" 
                                        disabled 
                                        title="Suppression impossible (dépôts existants)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Aucun receveur trouvé</p>
                            @if(request()->hasAny(['search', 'statut', 'commune_id', 'disponibilite']))
                                <a href="{{ route('receveurs.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-times"></i> Effacer les filtres
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center">
    <div>
        <small class="text-muted">
            Affichage de {{ $receveurs->firstItem() ?? 0 }} à {{ $receveurs->lastItem() ?? 0 }} 
            sur {{ $receveurs->total() }} résultat(s)
        </small>
    </div>
    <div>
        {{ $receveurs->appends(request()->query())->links() }}
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
    font-weight: bold;
}
</style>
@endsection

@push('scripts')
<script>
function confirmDelete(receveurId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/receveurs/${receveurId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function exportData() {
    // Logique d'export Excel
    console.log('Export Excel en cours...');
}
</script>
@endpush