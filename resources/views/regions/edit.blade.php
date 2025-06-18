@extends('layouts.app')

@section('title', 'Modifier la Région')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Modifier la Région</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('regions.index') }}">Régions</a></li>
                        <li class="breadcrumb-item active">Modifier</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="bx bx-edit me-2"></i>
                                Modification de la région "{{ $region->nom }}"
                            </h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('regions.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Affichage des erreurs --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">
                                <i class="bx bx-error-circle me-2"></i>
                                Erreurs de validation
                            </h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Messages de session --}}
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="bx bx-error-circle me-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('regions.update', $region) }}" method="POST" id="editRegionForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">
                                        Nom de la région <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" 
                                           name="nom" 
                                           value="{{ old('nom', $region->nom) }}"
                                           placeholder="Entrez le nom de la région..."
                                           required>
                                    @error('nom')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        Le nom doit être unique et ne pas dépasser 255 caractères.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Informations sur la région --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light border-0 mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted mb-3">
                                            <i class="bx bx-info-circle me-2"></i>
                                            Informations actuelles
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="bx bx-buildings text-primary me-2"></i>
                                                    <span class="fw-medium">Départements :</span>
                                                    <span class="ms-2 badge bg-info">{{ $region->departements->count() }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="bx bx-home text-warning me-2"></i>
                                                    <span class="fw-medium">Communes :</span>
                                                    <span class="ms-2 badge bg-warning">
                                                        {{ $region->departements->sum(function($dept) { return $dept->communes->count(); }) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        @if($region->departements->count() > 0)
                                            <div class="mt-3">
                                                <h6 class="text-muted mb-2">Départements associés :</h6>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($region->departements as $departement)
                                                        <span class="badge bg-secondary">{{ $departement->nom }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-light" onclick="window.history.back()">
                                        <i class="bx bx-x me-1"></i> Annuler
                                    </button>
                                    <div>
                                        <button type="button" class="btn btn-warning me-2" onclick="resetForm()">
                                            <i class="bx bx-reset me-1"></i> Réinitialiser
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-save me-1"></i> Modifier la région
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
@endsection

@push('scripts')
<script>
    // Validation côté client
    document.getElementById('editRegionForm').addEventListener('submit', function(e) {
        const nom = document.getElementById('nom').value.trim();
        
        if (nom === '') {
            e.preventDefault();
            showAlert('Le nom de la région est obligatoire.', 'danger');
            document.getElementById('nom').focus();
            return false;
        }
        
        if (nom.length > 255) {
            e.preventDefault();
            showAlert('Le nom de la région ne peut pas dépasser 255 caractères.', 'danger');
            document.getElementById('nom').focus();
            return false;
        }
        
        // Affichage du loader pendant la soumission
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Modification en cours...';
        submitBtn.disabled = true;
    });

    // Fonction pour réinitialiser le formulaire
    function resetForm() {
        if (confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ?')) {
            document.getElementById('nom').value = '{{ $region->nom }}';
            document.getElementById('nom').classList.remove('is-invalid');
            
            // Supprimer les messages d'erreur
            const errorElements = document.querySelectorAll('.invalid-feedback');
            errorElements.forEach(element => element.style.display = 'none');
        }
    }

    // Fonction pour afficher les alertes
    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bx bx-${type === 'danger' ? 'error-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.card-body');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-suppression après 5 secondes
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Validation en temps réel du nom
    document.getElementById('nom').addEventListener('input', function() {
        const value = this.value.trim();
        const feedback = this.nextElementSibling;
        
        if (value.length > 255) {
            this.classList.add('is-invalid');
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = 'Le nom ne peut pas dépasser 255 caractères.';
            }
        } else {
            this.classList.remove('is-invalid');
        }
    });
</script>
@endpush