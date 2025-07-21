@extends('layouts.app')

@section('title', 'Gestion des Dettes FEICOM')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Gestion des Dettes FEICOM</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">Dettes FEICOM</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase mb-1">Total des Dettes</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_dettes'] ?? 0, 0, ',', ' ') }} FCFA</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-currency-usd font-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase mb-1">Communes Concernées</h6>
                            <h4 class="mb-0">{{ $stats['nb_communes_concernees'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-city font-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase mb-1">Dette Moyenne</h6>
                            <h4 class="mb-0">{{ number_format($stats['dette_moyenne'] ?? 0, 0, ',', ' ') }} FCFA</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-trending-up font-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase mb-1">Dette Maximale</h6>
                            <h4 class="mb-0">{{ number_format($stats['dette_max'] ?? 0, 0, ',', ' ') }} FCFA</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-alert-circle font-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Liste des Dettes FEICOM</h5>
                    <div class="btn-group">
      
    <a href="{{ route('dettes-feicom.create') }}" class="btn btn-primary">
        <i class="mdi mdi-plus"></i> Nouvelle Dette
    </a>
    
    <!-- Bouton Export avec Dropdown -->
    <div class="btn-group" role="group">
        <button 
            type="button" 
            class="btn btn-success dropdown-toggle" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            id="exportDropdown">
            <i class="mdi mdi-download"></i> Exporter
        </button>
        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
            <li>
                <a class="dropdown-item" href="{{ route('dettes-feicom.export', ['format' => 'excel']) }}">
                    <i class="mdi mdi-file-excel"></i> Excel
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('dettes-feicom.export', ['format' => 'pdf']) }}">
                    <i class="mdi mdi-file-pdf"></i> PDF
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('dettes-feicom.export', ['format' => 'csv']) }}">
                    <i class="mdi mdi-file-delimited"></i> CSV
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Bouton Rapports avec Dropdown -->
    <div class="btn-group" role="group">
        <button 
            type="button" 
            class="btn btn-info dropdown-toggle" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            id="rapportsDropdown">
            <i class="mdi mdi-chart-line"></i> Rapports
        </button>
        <ul class="dropdown-menu" aria-labelledby="rapportsDropdown">
            <li>
                <a class="dropdown-item" href="{{ route('dettes-feicom.rapport-regions') }}">
                    <i class="mdi mdi-map"></i> Par Région
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('dettes-feicom.rapport-departements') }}">
                    <i class="mdi mdi-city"></i> Par Département
                </a>
            </li>
        </ul>
    </div>
</div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Formulaire de filtres -->
                    <form method="GET" action="{{ route('dettes-feicom.index') }}" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Rechercher une commune</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Nom ou code commune">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="region_id" class="form-label">Région</label>
                            <select name="region_id" id="region_id" class="form-select">
                                <option value="">Toutes les régions</option>
                                @foreach($regions ?? [] as $region)
                                    <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="departement_id" class="form-label">Département</label>
                            <select name="departement_id" id="departement_id" class="form-select">
                                <option value="">Tous les départements</option>
                                @foreach($departements ?? [] as $departement)
                                    <option value="{{ $departement->id }}" 
                                            data-region="{{ $departement->region_id }}"
                                            {{ request('departement_id') == $departement->id ? 'selected' : '' }}>
                                        {{ $departement->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="commune_id" class="form-label">Commune</label>
                            <select name="commune_id" id="commune_id" class="form-select">
                                <option value="">Toutes les communes</option>
                                @foreach($communes ?? [] as $commune)
                                    <option value="{{ $commune->id }}" 
                                            data-departement="{{ $commune->departement_id }}"
                                            {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                                        {{ $commune->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="date_debut" class="form-label">Date début</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_debut" 
                                   name="date_debut" 
                                   value="{{ request('date_debut') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="date_fin" class="form-label">Date fin</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_fin" 
                                   name="date_fin" 
                                   value="{{ request('date_fin') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="montant_min" class="form-label">Montant min.</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="montant_min" 
                                   name="montant_min" 
                                   value="{{ request('montant_min') }}" 
                                   placeholder="0">
                        </div>

                        <div class="col-md-2">
                            <label for="montant_max" class="form-label">Montant max.</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="montant_max" 
                                   name="montant_max" 
                                   value="{{ request('montant_max') }}" 
                                   placeholder="Illimité">
                        </div>

                        <div class="col-md-2">
                            <label for="sort_by" class="form-label">Trier par</label>
                            <select name="sort_by" id="sort_by" class="form-select">
                                <option value="date_evaluation" {{ request('sort_by') == 'date_evaluation' ? 'selected' : '' }}>Date d'évaluation</option>
                                <option value="montant" {{ request('sort_by') == 'montant' ? 'selected' : '' }}>Montant</option>
                                <option value="commune_id" {{ request('sort_by') == 'commune_id' ? 'selected' : '' }}>Commune</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="sort_direction" class="form-label">Ordre</label>
                            <select name="sort_direction" id="sort_direction" class="form-select">
                                <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>Décroissant</option>
                                <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Croissant</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter"></i> Filtrer
                                </button>
                                <a href="{{ route('dettes-feicom.index') }}" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Tableau des dettes -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Commune</th>
                                    <th>Département</th>
                                    <th>Région</th>
                                    <th>Montant (FCFA)</th>
                                    <th>Date d'évaluation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dettes ?? [] as $dette)
                                <tr>
                                    <td>
                                        <strong>{{ $dette->commune->nom ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $dette->commune->code ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $dette->commune->departement->nom ?? 'N/A' }}</td>
                                    <td>{{ $dette->commune->departement->region->nom ?? 'N/A' }}</td>
                                    <td class="text-end">
                                        <strong class="text-primary">
                                            {{ number_format($dette->montant ?? 0, 0, ',', ' ') }}
                                        </strong>
                                    </td>
                                    <td>
                                        @if($dette->date_evaluation)
                                            @if(is_string($dette->date_evaluation))
                                                {{ \Carbon\Carbon::parse($dette->date_evaluation)->format('d/m/Y') }}
                                            @else
                                                {{ $dette->date_evaluation->format('d/m/Y') }}
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('dettes-feicom.show', $dette) }}" 
                                               class="btn btn-info" 
                                               title="Voir les détails">
                                                <i class="mdi mdi-eye"></i>voir
                                            </a>
                                            <a href="{{ route('dettes-feicom.edit', $dette) }}" 
                                               class="btn btn-warning" 
                                               title="Modifier">
                                                <i class="mdi mdi-pencil"></i>modifier
                                            </a>
                                            {{-- <button type="button" 
                                                    class="btn btn-danger" 
                                                    title="Supprimer"
                                                    onclick="confirmDelete({{ $dette->id }})">
                                                <i class="mdi mdi-delete"></i>supprimer
                                            </button> --}}

                                        <form method="POST" action="{{ route('dettes-feicom.destroy', $dette) }}" class="d-inline" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette dette ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="mdi mdi-information-outline font-24 mb-2"></i>
                                            <p>Aucune dette FEICOM trouvée</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($dettes) && method_exists($dettes, 'links'))
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                @if($dettes->total() > 0)
                                    Affichage de {{ $dettes->firstItem() }} à {{ $dettes->lastItem() }} 
                                    sur {{ $dettes->total() }} résultats
                                @else
                                    Aucun résultat trouvé
                                @endif
                            </small>
                        </div>
                        <div>
                            {{ $dettes->withQueryString()->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette dette FEICOM ?</p>
                <p class="text-danger"><strong>Cette action est irréversible.</strong></p>
                <div id="deleteInfo" class="alert alert-warning d-none">
                    <!-- Les informations seront injectées par JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="mdi mdi-delete"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div> --}}

{{-- <div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette dette FEICOM ?</p>
                <p class="text-danger"><strong>Cette action est irréversible.</strong></p>
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
</div> --}}
@endsection

@push('scripts')
<script>
// function confirmDelete(detteId) {
//     const form = document.getElementById('deleteForm');
//     form.action = `/dettes/feicom/${detteId}`;
    
//     const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
//     modal.show();
// }

// function confirmDelete(detteId, communeNom = '', montant = '', dateEvaluation = '') {
//     const form = document.getElementById('deleteForm');
//     const confirmBtn = document.getElementById('confirmDeleteBtn');
//     const deleteInfo = document.getElementById('deleteInfo');
    
//     // Définir l'action du formulaire
//     form.action = `/dettes/feicom/${detteId}`;
    
$(document).ready(function() {
    // Auto-submit form on select change
    $('#region_id, #commune_id').change(function() {
        $(this).closest('form').submit();
    });
    // Afficher les informations si disponibles
    if (communeNom && montant && dateEvaluation) {
        deleteInfo.innerHTML = `
            <strong>Détails de la dette :</strong><br>
            Commune: <strong>${communeNom}</strong><br>
            Montant: <strong>${montant} FCFA</strong><br>
            Date: <strong>${dateEvaluation}</strong>
        `;
        deleteInfo.classList.remove('d-none');
    } else {
        deleteInfo.classList.add('d-none');
    }
    
    // Gérer la soumission du formulaire
    form.onsubmit = function(e) {
        e.preventDefault();
        
        // Désactiver le bouton pour éviter les doubles clics
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Suppression...';
        
        // Créer une requête AJAX pour éviter les problèmes de modal
        fetch(form.action, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                // Fermer le modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                modal.hide();
                
                // Recharger la page avec un message de succès
                window.location.reload();
            } else {
                throw new Error('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression. Veuillez réessayer.');
            
            // Réactiver le bouton
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="mdi mdi-delete"></i> Supprimer';
        });
    };
    
    // Afficher le modal
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Nettoyer le formulaire quand le modal se ferme
document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('deleteForm');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    
    form.onsubmit = null;
    confirmBtn.disabled = false;
    confirmBtn.innerHTML = '<i class="mdi mdi-delete"></i> Supprimer';
});

// Filtrage en cascade des sélecteurs
document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('region_id');
    const departementSelect = document.getElementById('departement_id');
    const communeSelect = document.getElementById('commune_id');
    
    function filterDepartements() {
        const selectedRegion = regionSelect.value;
        const departementOptions = departementSelect.querySelectorAll('option');
        
        departementOptions.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            
            const optionRegion = option.getAttribute('data-region');
            option.style.display = (!selectedRegion || optionRegion === selectedRegion) ? 'block' : 'none';
        });
        
        if (selectedRegion) {
            departementSelect.value = '';
        }
        filterCommunes();
    }
    
    function filterCommunes() {
        const selectedDepartement = departementSelect.value;
        const communeOptions = communeSelect.querySelectorAll('option');
        
        communeOptions.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            
            const optionDepartement = option.getAttribute('data-departement');
            option.style.display = (!selectedDepartement || optionDepartement === selectedDepartement) ? 'block' : 'none';
        });
        
        if (selectedDepartement) {
            communeSelect.value = '';
        }
    }
    
    regionSelect.addEventListener('change', filterDepartements);
    departementSelect.addEventListener('change', filterCommunes);
    
    // Initialiser les filtres
    filterDepartements();
});
</script>
@endpush