{{-- @extends('layouts.app')

@section('title', 'Modifier la commune - ' . $commune->nom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Modifier la commune</h1>
                    <p class="text-muted">{{ $commune->nom }} ({{ $commune->code }})</p>
                </div>
                <div>
                    <a href="{{ route('communes.show', $commune) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                    <a href="{{ route('communes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <!-- Messages d'erreur/succès -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Formulaire -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations de la commune</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('communes.update', $commune) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Informations de base -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom de la commune <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" 
                                           name="nom" 
                                           value="{{ old('nom', $commune->nom) }}" 
                                           required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code commune <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           id="code" 
                                           name="code" 
                                           value="{{ old('code', $commune->code) }}" 
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="departement_id" class="form-label">Département <span class="text-danger">*</span></label>
                                    <select class="form-select @error('departement_id') is-invalid @enderror" 
                                            id="departement_id" 
                                            name="departement_id" 
                                            required>
                                        <option value="">Sélectionner un département</option>
                                        @foreach($departements as $departement)
                                            <option value="{{ $departement->id }}" 
                                                    {{ old('departement_id', $commune->departement_id) == $departement->id ? 'selected' : '' }}>
                                                {{ $departement->nom }} - {{ $departement->region->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('departement_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" 
                                           class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" 
                                           name="telephone" 
                                           value="{{ old('telephone', $commune->telephone) }}">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Receveurs -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Receveurs</label>
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                        @foreach($receveurs as $receveur)
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="receveur_ids[]" 
                                                       value="{{ $receveur->id }}" 
                                                       id="receveur_{{ $receveur->id }}"
                                                       {{ in_array($receveur->id, old('receveur_ids', $commune->receveurs->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="receveur_{{ $receveur->id }}">
                                                    {{ $receveur->nom }}
                                                    @if($receveur->fonction)
                                                        <small class="text-muted">({{ $receveur->fonction }})</small>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                        @if($receveurs->isEmpty())
                                            <p class="text-muted mb-0">Aucun receveur actif disponible</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Ordonnateurs -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ordonnateurs</label>
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                        @foreach($ordonnateurs as $ordonnateur)
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="ordonnateur_ids[]" 
                                                       value="{{ $ordonnateur->id }}" 
                                                       id="ordonnateur_{{ $ordonnateur->id }}"
                                                       {{ in_array($ordonnateur->id, old('ordonnateur_ids', $commune->ordonnateurs->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="ordonnateur_{{ $ordonnateur->id }}">
                                                    {{ $ordonnateur->nom }}
                                                    @if($ordonnateur->fonction)
                                                        <small class="text-muted">({{ $ordonnateur->fonction }})</small>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                        @if($ordonnateurs->isEmpty())
                                            <p class="text-muted mb-0">Aucun ordonnateur actif disponible</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Mettre à jour
                                </button>
                                <a href="{{ route('communes.show', $commune) }}" class="btn btn-outline-secondary">
                                    Annuler
                                </a>
                            </div>
                            
                            <!-- Bouton de suppression -->
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
                <p>Êtes-vous sûr de vouloir supprimer la commune <strong>{{ $commune->nom }}</strong> ?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cette action est irréversible et supprimera toutes les données associées.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('communes.destroy', $commune) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script pour améliorer l'expérience utilisateur
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmation avant suppression
        const deleteForm = document.querySelector('#deleteModal form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                if (!confirm('Êtes-vous absolument sûr ? Cette action ne peut pas être annulée.')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush --}}




@extends('layouts.app')

@section('title', 'Modifier la commune - ' . $commune->nom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Modifier la commune</h1>
                    <p class="text-muted">{{ $commune->nom }} ({{ $commune->code }})</p>
                </div>
                <div>
                    <a href="{{ route('communes.show', $commune) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                    <a href="{{ route('communes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <!-- Messages d'erreur/succès -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Formulaire -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations de la commune</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('communes.update', $commune) }}" method="POST" id="commune-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Informations de base -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom de la commune <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" 
                                           name="nom" 
                                           value="{{ old('nom', $commune->nom) }}" 
                                           required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code commune <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           id="code" 
                                           name="code" 
                                           value="{{ old('code', $commune->code) }}" 
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="departement_id" class="form-label">Département <span class="text-danger">*</span></label>
                                    <select class="form-select @error('departement_id') is-invalid @enderror" 
                                            id="departement_id" 
                                            name="departement_id" 
                                            required>
                                        <option value="">Sélectionner un département</option>
                                        @foreach($departements as $departement)
                                            <option value="{{ $departement->id }}" 
                                                    {{ old('departement_id', $commune->departement_id) == $departement->id ? 'selected' : '' }}>
                                                {{ $departement->nom }} - {{ $departement->region->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('departement_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" 
                                           class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" 
                                           name="telephone" 
                                           value="{{ old('telephone', $commune->telephone) }}">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section Responsables -->
                        <div class="row">
                            <!-- Receveurs -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        Receveurs 
                                        <small class="text-muted">(optionnel)</small>
                                    </label>
                                    
                                    <!-- Boutons d'action rapide -->
                                    <div class="mb-2">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" onclick="selectAllReceveurs()">
                                                <i class="fas fa-check-square"></i> Tout sélectionner
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="unselectAllReceveurs()">
                                                <i class="fas fa-square"></i> Tout désélectionner
                                            </button>
                                        </div>
                                    </div>

                                    <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                                        <!-- Option pour aucun receveur -->
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="no_receveur" 
                                                   onchange="toggleNoReceveur()">
                                            <label class="form-check-label text-muted fst-italic" for="no_receveur">
                                                <i class="fas fa-ban"></i> Aucun receveur assigné
                                            </label>
                                        </div>
                                        <hr class="my-2">

                                        @foreach($receveurs as $receveur)
                                            <div class="form-check">
                                                <input class="form-check-input receveur-checkbox" 
                                                       type="checkbox" 
                                                       name="receveur_ids[]" 
                                                       value="{{ $receveur->id }}" 
                                                       id="receveur_{{ $receveur->id }}"
                                                       {{ in_array($receveur->id, old('receveur_ids', $commune->receveurs->pluck('id')->toArray())) ? 'checked' : '' }}
                                                       onchange="updateNoReceveurState()">
                                                <label class="form-check-label" for="receveur_{{ $receveur->id }}">
                                                    {{ $receveur->nom }}
                                                    @if($receveur->fonction)
                                                        <small class="text-muted">({{ $receveur->fonction }})</small>
                                                    @endif
                                                    @if($receveur->commune_id && $receveur->commune_id != $commune->id)
                                                        <small class="badge bg-warning text-dark">Déjà assigné</small>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                        
                                        @if($receveurs->isEmpty())
                                            <div class="text-center py-3">
                                                <i class="fas fa-inbox text-muted"></i>
                                                <p class="text-muted mb-0">Aucun receveur disponible</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Résumé de sélection -->
                                    <small class="form-text text-muted mt-1">
                                        <span id="receveur-count">{{ $commune->receveurs->count() }}</span> receveur(s) actuellement assigné(s)
                                    </small>
                                </div>
                            </div>

                            <!-- Ordonnateurs -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        Ordonnateurs 
                                        <small class="text-muted">(optionnel)</small>
                                    </label>
                                    
                                    <!-- Boutons d'action rapide -->
                                    <div class="mb-2">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" onclick="selectAllOrdonnateurs()">
                                                <i class="fas fa-check-square"></i> Tout sélectionner
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="unselectAllOrdonnateurs()">
                                                <i class="fas fa-square"></i> Tout désélectionner
                                            </button>
                                        </div>
                                    </div>

                                    <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                                        <!-- Option pour aucun ordonnateur -->
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="no_ordonnateur" 
                                                   onchange="toggleNoOrdonnateur()">
                                            <label class="form-check-label text-muted fst-italic" for="no_ordonnateur">
                                                <i class="fas fa-ban"></i> Aucun ordonnateur assigné
                                            </label>
                                        </div>
                                        <hr class="my-2">

                                        @foreach($ordonnateurs as $ordonnateur)
                                            <div class="form-check">
                                                <input class="form-check-input ordonnateur-checkbox" 
                                                       type="checkbox" 
                                                       name="ordonnateur_ids[]" 
                                                       value="{{ $ordonnateur->id }}" 
                                                       id="ordonnateur_{{ $ordonnateur->id }}"
                                                       {{ in_array($ordonnateur->id, old('ordonnateur_ids', $commune->ordonnateurs->pluck('id')->toArray())) ? 'checked' : '' }}
                                                       onchange="updateNoOrdonnateurState()">
                                                <label class="form-check-label" for="ordonnateur_{{ $ordonnateur->id }}">
                                                    {{ $ordonnateur->nom }}
                                                    @if($ordonnateur->fonction)
                                                        <small class="text-muted">({{ $ordonnateur->fonction }})</small>
                                                    @endif
                                                    @if($ordonnateur->commune_id && $ordonnateur->commune_id != $commune->id)
                                                        <small class="badge bg-warning text-dark">Déjà assigné</small>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                        
                                        @if($ordonnateurs->isEmpty())
                                            <div class="text-center py-3">
                                                <i class="fas fa-inbox text-muted"></i>
                                                <p class="text-muted mb-0">Aucun ordonnateur disponible</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Résumé de sélection -->
                                    <small class="form-text text-muted mt-1">
                                        <span id="ordonnateur-count">{{ $commune->ordonnateurs->count() }}</span> ordonnateur(s) actuellement assigné(s)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Alerte d'information -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Information importante :</strong>
                            <ul class="mb-0 mt-2">
                                <li>Une commune peut fonctionner sans receveur ni ordonnateur assigné</li>
                                <li>La désassignation libère le responsable pour d'autres communes</li>
                                <li>Les responsables déjà assignés à d'autres communes sont marqués comme tels</li>
                                <li>Vous pouvez réassigner un responsable d'une commune à une autre</li>
                            </ul>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Mettre à jour
                                </button>
                                <a href="{{ route('communes.show', $commune) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            </div>
                            
                            <!-- Bouton de suppression -->
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
                <p>Êtes-vous sûr de vouloir supprimer la commune <strong>{{ $commune->nom }}</strong> ?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cette action est irréversible et supprimera toutes les données associées.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('communes.destroy', $commune) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

    
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser l'état des compteurs
    updateReceveurCount();
    updateOrdonnateurCount();
    updateNoReceveurState();
    updateNoOrdonnateurState();

    // Confirmation avant suppression
    const deleteForm = document.querySelector('#deleteModal form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous absolument sûr ? Cette action ne peut pas être annulée.')) {
                e.preventDefault();
            }
        });
    }

    // Confirmation pour les changements majeurs
    const form = document.getElementById('commune-form');
    form.addEventListener('submit', function(e) {
        const selectedReceveurs = document.querySelectorAll('input[name="receveur_ids[]"]:checked').length;
        const selectedOrdonnateurs = document.querySelectorAll('input[name="ordonnateur_ids[]"]:checked').length;
        const currentReceveurs = {{ $commune->receveurs->count() }};
        const currentOrdonnateurs = {{ $commune->ordonnateurs->count() }};

        if ((selectedReceveurs === 0 && currentReceveurs > 0) || 
            (selectedOrdonnateurs === 0 && currentOrdonnateurs > 0)) {
            if (!confirm('Vous êtes sur le point de désassigner tous les responsables de cette commune. Êtes-vous sûr de vouloir continuer ?')) {
                e.preventDefault();
            }
        }
    });
});

// Fonctions pour les receveurs
function selectAllReceveurs() {
    document.querySelectorAll('.receveur-checkbox').forEach(cb => {
        cb.checked = true;
    });
    document.getElementById('no_receveur').checked = false;
    updateReceveurCount();
}

function unselectAllReceveurs() {
    document.querySelectorAll('.receveur-checkbox').forEach(cb => {
        cb.checked = false;
    });
    document.getElementById('no_receveur').checked = true;
    updateReceveurCount();
}

function toggleNoReceveur() {
    const noReceveur = document.getElementById('no_receveur');
    const receveurCheckboxes = document.querySelectorAll('.receveur-checkbox');
    
    if (noReceveur.checked) {
        receveurCheckboxes.forEach(cb => cb.checked = false);
    }
    updateReceveurCount();
}

function updateNoReceveurState() {
    const checkedReceveurs = document.querySelectorAll('.receveur-checkbox:checked').length;
    const noReceveur = document.getElementById('no_receveur');
    
    noReceveur.checked = checkedReceveurs === 0;
    updateReceveurCount();
}

function updateReceveurCount() {
    const count = document.querySelectorAll('.receveur-checkbox:checked').length;
    document.getElementById('receveur-count').textContent = count;
}

// Fonctions pour les ordonnateurs
function selectAllOrdonnateurs() {
    document.querySelectorAll('.ordonnateur-checkbox').forEach(cb => {
        cb.checked = true;
    });
    document.getElementById('no_ordonnateur').checked = false;
    updateOrdonnateurCount();
}

function unselectAllOrdonnateurs() {
    document.querySelectorAll('.ordonnateur-checkbox').forEach(cb => {
        cb.checked = false;
    });
    document.getElementById('no_ordonnateur').checked = true;
    updateOrdonnateurCount();
}

function toggleNoOrdonnateur() {
    const noOrdonnateur = document.getElementById('no_ordonnateur');
    const ordonnateurCheckboxes = document.querySelectorAll('.ordonnateur-checkbox');
    
    if (noOrdonnateur.checked) {
        ordonnateurCheckboxes.forEach(cb => cb.checked = false);
    }
    updateOrdonnateurCount();
}

function updateNoOrdonnateurState() {
    const checkedOrdonnateurs = document.querySelectorAll('.ordonnateur-checkbox:checked').length;
    const noOrdonnateur = document.getElementById('no_ordonnateur');
    
    noOrdonnateur.checked = checkedOrdonnateurs === 0;
    updateOrdonnateurCount();
}

function updateOrdonnateurCount() {
    const count = document.querySelectorAll('.ordonnateur-checkbox:checked').length;
    document.getElementById('ordonnateur-count').textContent = count;
}
</script>
@endpush

@push('styles')
<style>
.form-check-input:checked + .form-check-label {
    font-weight: 500;
}

.badge.bg-warning {
    font-size: 0.7em;
}

.btn-group-sm .btn {
    font-size: 0.8rem;
}

.alert ul li {
    margin-bottom: 0.25rem;
}
</style>
@endpush