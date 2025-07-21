@extends('layouts.app')

@section('title', 'Modifier un ordonnateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Modifier l'ordonnateur</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('ordonnateurs.index') }}">Ordonnateurs</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('ordonnateurs.show', $ordonnateur) }}">{{ $ordonnateur->nom }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Modifier</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('ordonnateurs.show', $ordonnateur) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
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

            <!-- Formulaire -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit"></i> Informations de l'ordonnateur
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('ordonnateurs.update', $ordonnateur) }}" method="POST" id="editOrdonnateurForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Informations personnelles -->
                            <div class="col-lg-6">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-user"></i> Informations personnelles
                                </h6>
                                
                                <!-- Nom -->
                                <div class="mb-3">
                                    <label for="nom" class="form-label">
                                        Nom complet <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" 
                                           name="nom" 
                                           value="{{ old('nom', $ordonnateur->nom) }}" 
                                           required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Fonction -->
                                <div class="mb-3">
                                    <label for="fonction" class="form-label">
                                        Fonction <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('fonction') is-invalid @enderror" 
                                            id="fonction" 
                                            name="fonction" 
                                            required>
                                        <option value="">Sélectionner une fonction</option>
                                        <option value="Maire" {{ old('fonction', $ordonnateur->fonction) == 'Maire' ? 'selected' : '' }}>Maire</option>
                                        <option value="Maire adjoint" {{ old('fonction', $ordonnateur->fonction) == 'Maire adjoint' ? 'selected' : '' }}>Maire adjoint</option>
                                        <option value="Délégué du gouvernement" {{ old('fonction', $ordonnateur->fonction) == 'Délégué du gouvernement' ? 'selected' : '' }}>Délégué du gouvernement</option>
                                        <option value="Délégué du gouvernement adjoint" {{ old('fonction', $ordonnateur->fonction) == 'Délégué du gouvernement adjoint' ? 'selected' : '' }}>Délégué du gouvernement adjoint</option>
                                        <option value="Autre" {{ old('fonction', $ordonnateur->fonction) == 'Autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    @error('fonction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Date de prise de fonction -->
                                <div class="mb-3">
                                    <label for="date_prise_fonction" class="form-label">
                                        Date de prise de fonction <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('date_prise_fonction') is-invalid @enderror" 
                                           id="date_prise_fonction" 
                                           name="date_prise_fonction" 
                                           value="{{ old('date_prise_fonction', $ordonnateur->date_prise_fonction) }}" 
                                           required>
                                    @error('date_prise_fonction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Téléphone -->
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" 
                                           class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" 
                                           name="telephone" 
                                           value="{{ old('telephone', $ordonnateur->telephone) }}" 
                                           placeholder="Ex: +237 6XX XXX XXX">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Assignation à une commune -->
                            <div class="col-lg-6">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-map-marker-alt"></i> Assignation
                                </h6>
                                
                                <!-- Commune actuelle -->
                                @if($ordonnateur->commune)
                                <div class="alert alert-info mb-3">
                                    <h6 class="alert-heading">Commune actuelle</h6>
                                    <p class="mb-0">
                                        <strong>{{ $ordonnateur->commune->nom }}</strong><br>
                                        <small class="text-muted">
                                            {{ $ordonnateur->commune->departement->nom }} - {{ $ordonnateur->commune->departement->region->nom }}
                                        </small>
                                    </p>
                                </div>
                                @endif

                                <!-- Sélection de commune -->
                                <div class="mb-3">
                                    <label for="commune_id" class="form-label">Commune d'assignation</label>
                                    <select class="form-select @error('commune_id') is-invalid @enderror" 
                                            id="commune_id" 
                                            name="commune_id">
                                        <option value="">Aucune assignation (Ordonnateur libre)</option>
                                        @foreach($communes as $commune)
                                            <option value="{{ $commune->id }}" 
                                                    data-departement="{{ $commune->departement->nom }}"
                                                    data-region="{{ $commune->departement->region->nom }}"
                                                    {{ old('commune_id', $ordonnateur->commune_id) == $commune->id ? 'selected' : '' }}>
                                                {{ $commune->nom }} ({{ $commune->departement->nom }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('commune_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Laissez vide si l'ordonnateur n'est pas assigné à une commune spécifique
                                    </div>
                                </div>

                                <!-- Informations sur la commune sélectionnée -->
                                <div id="commune-info" class="alert alert-light d-none">
                                    <h6 class="alert-heading">Informations sur la commune</h6>
                                    <p class="mb-0">
                                        <strong id="commune-nom"></strong><br>
                                        <small class="text-muted">
                                            <span id="commune-departement"></span> - <span id="commune-region"></span>
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="fas fa-trash"></i> Supprimer l'ordonnateur
                                        </button>
                                    </div>
                                    <div>
                                        <a href="{{ route('ordonnateurs.show', $ordonnateur) }}" class="btn btn-secondary me-2">
                                            <i class="fas fa-times"></i> Annuler
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Enregistrer les modifications
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'ordonnateur <strong>{{ $ordonnateur->nom }}</strong> ?</p>
                <p class="text-muted">Cette action est irréversible.</p>
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
    const communeSelect = document.getElementById('commune_id');
    const communeInfo = document.getElementById('commune-info');
    const communeNom = document.getElementById('commune-nom');
    const communeDepartement = document.getElementById('commune-departement');
    const communeRegion = document.getElementById('commune-region');

    // Afficher les informations de la commune sélectionnée
    function showCommuneInfo() {
        const selectedOption = communeSelect.options[communeSelect.selectedIndex];
        
        if (selectedOption.value && selectedOption.value !== '') {
            communeNom.textContent = selectedOption.textContent.split(' (')[0];
            communeDepartement.textContent = selectedOption.dataset.departement;
            communeRegion.textContent = selectedOption.dataset.region;
            communeInfo.classList.remove('d-none');
        } else {
            communeInfo.classList.add('d-none');
        }
    }

    // Écouter les changements de sélection
    communeSelect.addEventListener('change', showCommuneInfo);

    // Afficher les informations au chargement si une commune est déjà sélectionnée
    showCommuneInfo();

    // Validation du formulaire
    const form = document.getElementById('editOrdonnateurForm');
    form.addEventListener('submit', function(e) {
        const nom = document.getElementById('nom').value.trim();
        const fonction = document.getElementById('fonction').value;
        const datePriseFonction = document.getElementById('date_prise_fonction').value;

        if (!nom || !fonction || !datePriseFonction) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }

        // Validation de la date (ne doit pas être dans le futur)
        const dateInputValue = new Date(datePriseFonction);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (dateInputValue > today) {
            e.preventDefault();
            alert('La date de prise de fonction ne peut pas être dans le futur.');
            document.getElementById('date_prise_fonction').focus();
            return false;
        }
    });

    // Formater automatiquement le numéro de téléphone
    const telephoneInput = document.getElementById('telephone');
    telephoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Supprimer tous les non-chiffres
        
        if (value.length > 0) {
            if (value.startsWith('237')) {
                value = '+' + value;
            } else if (value.startsWith('6') || value.startsWith('2')) {
                value = '+237' + value;
            }
        }
        
        e.target.value = value;
    });
});
</script>
@endpush

@push('styles')
<style>
.alert-heading {
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.form-label {
    font-weight: 500;
}

.text-danger {
    font-weight: bold;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

.breadcrumb {
    background-color: transparent;
    padding: 0;
    margin: 0;
}

#commune-info {
    border-left: 4px solid #17a2b8;
}

.modal-header.bg-danger {
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}
</style>
@endpush