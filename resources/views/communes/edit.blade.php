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
@endpush