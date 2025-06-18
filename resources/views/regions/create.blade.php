@extends('layouts.app')

@section('title', 'Créer une région')
@section('page-title', 'GESTION DES RÉGIONS')

@section('content')
<div class="form-container">
    <div class="row">
        <div class="col-6 mx-auto">
            <div class="card-header mb-4">
                <h4><i class="fas fa-plus-circle"></i> Créer une nouvelle région</h4>
            </div>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('regions.store') }}" method="POST" id="regionForm">
                @csrf
                
                <div class="form-group">
                    <label for="nom" class="form-label">Nom de la région <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('nom') is-invalid @enderror" 
                           id="nom" 
                           name="nom" 
                           value="{{ old('nom') }}" 
                           placeholder="Saisissez le nom de la région"
                           required
                           autofocus>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Le nom doit être unique et contenir au moins 3 caractères
                    </small>
                </div>

                <div class="form-group d-flex justify-content-between">
                    <a href="{{ route('regions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Créer la région
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Styles spécifiques pour la page de création */
.form-container {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    margin-bottom: 30px;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 2rem;
}

.card-header h4 {
    margin: 0;
    font-weight: 600;
    font-size: 1.2rem;
    color: white;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.75rem;
    font-size: 1rem;
}

.form-control {
    border: 2px solid #e9ecef;
    padding: 0.875rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 1rem;
    background-color: #fff;
}

.form-control:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    outline: none;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.form-control.is-valid {
    border-color: #28a745;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 0.95rem;
    border: none;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
}

.btn-primary:disabled {
    opacity: 0.6;
    transform: none;
}

.btn-secondary {
    background-color: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background-color: #7f8c8d;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.text-danger {
    color: #e74c3c !important;
}

.alert-danger {
    background-color: rgba(231, 76, 60, 0.1);
    border: 1px solid rgba(231, 76, 60, 0.2);
    color: #e74c3c;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    padding: 15px 20px;
}

.invalid-feedback {
    display: block;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    color: #e74c3c;
}

.form-text {
    font-size: 0.825rem;
    margin-top: 0.5rem;
    color: #6c757d;
}

/* Animation pour le bouton de soumission */
.btn-primary.loading {
    pointer-events: none;
}

.btn-primary.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Style pour le focus sur le formulaire */
.form-group:focus-within .form-label {
    color: #3498db;
}

/* Responsive */
@media (max-width: 768px) {
    .col-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .form-container {
        padding: 20px;
        margin: 0 10px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .card-header {
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .btn {
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('regionForm');
    const nomInput = document.getElementById('nom');
    const submitBtn = document.getElementById('submitBtn');
    const submitIcon = submitBtn.querySelector('i');
    
    // Validation en temps réel
    nomInput.addEventListener('input', function() {
        const nom = this.value.trim();
        
        // Supprimer les classes d'erreur précédentes
        this.classList.remove('is-invalid', 'is-valid');
        
        if (nom.length >= 3) {
            this.classList.add('is-valid');
            submitBtn.disabled = false;
        } else if (nom.length > 0) {
            submitBtn.disabled = true;
        }
    });
    
    // Validation et animation lors de la soumission
    form.addEventListener('submit', function(e) {
        const nom = nomInput.value.trim();
        
        // Validation côté client
        if (!nom) {
            e.preventDefault();
            nomInput.classList.add('is-invalid');
            nomInput.focus();
            showAlert('Le nom de la région est obligatoire.', 'danger');
            return false;
        }
        
        if (nom.length < 3) {
            e.preventDefault();
            nomInput.classList.add('is-invalid');
            nomInput.focus();
            showAlert('Le nom de la région doit contenir au moins 3 caractères.', 'danger');
            return false;
        }
        
        // Animation du bouton pendant l'envoi
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitIcon.className = 'fas fa-spinner';
        submitBtn.innerHTML = '<i class="fas fa-spinner"></i> Création en cours...';
    });
    
    // Fonction pour afficher les alertes
    function showAlert(message, type) {
        // Supprimer les alertes existantes
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Créer la nouvelle alerte
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = message;
        
        // Insérer l'alerte au début du formulaire
        const formContainer = document.querySelector('.form-container');
        const cardHeader = document.querySelector('.card-header');
        formContainer.insertBefore(alertDiv, cardHeader.nextSibling);
        
        // Auto-suppression après 5 secondes
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // Réinitialiser le bouton si erreur de validation côté serveur
    if (document.querySelector('.alert-danger')) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('loading');
        submitIcon.className = 'fas fa-save';
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Créer la région';
    }
});
</script>
@endpush
@endsection