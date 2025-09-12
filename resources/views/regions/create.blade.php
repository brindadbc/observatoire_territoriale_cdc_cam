{{-- @extends('layouts.app')

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
@endsection --}}





@extends('layouts.app')

@section('title', 'Nouvelle Région - Observatoire des Collectivités Territoriales')
@section('page-title', 'Créer une nouvelle région')

@push('styles')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
    }

    .form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }

    .form-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f0f0f0;
    }

    .form-title {
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .form-subtitle {
        color: #6c757d;
        font-size: 14px;
    }

    .form-section {
        margin-bottom: 25px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-icon {
        width: 30px;
        height: 30px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .required {
        color: #dc3545;
    }

    .form-control {
        padding: 12px 15px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 0 0 3px rgba(44,82,130,0.1);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        background-color: rgba(220,53,69,0.05);
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 5px;
        font-size: 12px;
        color: #dc3545;
    }

    .input-group-text {
        background: #e9ecef;
        border: 1px solid #dee2e6;
        color: #6c757d;
        font-size: 12px;
        font-weight: 600;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-custom {
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: 120px;
        justify-content: center;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(44,82,130,0.3);
        color: white;
    }

    .btn-secondary-custom {
        background: #6c757d;
        color: white;
    }

    .btn-secondary-custom:hover {
        background: #5a6268;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108,117,125,0.3);
    }

    .help-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }

    .preview-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
        border: 1px solid #e9ecef;
    }

    .preview-title {
        font-size: 14px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .preview-content {
        font-size: 13px;
        color: #6c757d;
    }

    .loading-spinner {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .form-card.loading .loading-spinner {
        display: inline-block;
    }

    @media (max-width: 768px) {
        .form-card {
            padding: 20px;
            margin: 10px;
        }
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-custom {
            min-width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('regions.index') }}">Régions</a></li>
            <li class="breadcrumb-item active">Nouvelle région</li>
        </ol>
    </nav>

    <div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <h1 class="form-title">Créer une nouvelle région</h1>
                <p class="form-subtitle">Saisissez les informations de la région</p>
            </div>

            <form action="{{ route('regions.store') }}" method="POST" id="regionForm">
                @csrf
                
                <!-- Informations de base -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        Informations de base
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="nom">
                                Nom de la région <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom') }}"
                                   placeholder="Ex: Centre, Littoral..."
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Nom officiel de la région</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="code">
                                Code région <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('code') is-invalid @enderror" 
                                   id="code" 
                                   name="code" 
                                   value="{{ old('code') }}"
                                   placeholder="Ex: CE, LT..."
                                   maxlength="10"
                                   required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Code unique d'identification (max 10 caractères)</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="chef_lieu">
                            Chef-lieu <span class="required">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('chef_lieu') is-invalid @enderror" 
                               id="chef_lieu" 
                               name="chef_lieu" 
                               value="{{ old('chef_lieu') }}"
                               placeholder="Ex: Yaoundé, Douala..."
                               required>
                        @error('chef_lieu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="help-text">Ville principale de la région</div>
                    </div>
                </div>

                <!-- Données géographiques et démographiques -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        Données géographiques et démographiques
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="superficie">
                                Superficie
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('superficie') is-invalid @enderror" 
                                       id="superficie" 
                                       name="superficie" 
                                       value="{{ old('superficie') }}"
                                       placeholder="68953"
                                       min="0"
                                       step="0.01">
                                <span class="input-group-text">km²</span>
                            </div>
                            @error('superficie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Superficie totale de la région en km²</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="population">
                                Population
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('population') is-invalid @enderror" 
                                       id="population" 
                                       name="population" 
                                       value="{{ old('population') }}"
                                       placeholder="3500000"
                                       min="0">
                                <span class="input-group-text">hab.</span>
                            </div>
                            @error('population')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Population estimée de la région</div>
                        </div>
                    </div>
                </div>

                <!-- Aperçu -->
                <div class="preview-section" id="previewSection" style="display: none;">
                    <h4 class="preview-title">
                        <i class="fas fa-eye"></i>
                        Aperçu de la région
                    </h4>
                    <div class="preview-content" id="previewContent">
                        <!-- Le contenu sera généré dynamiquement -->
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="{{ route('regions.index') }}" class="btn-custom btn-secondary-custom">
                        <i class="fas fa-arrow-left"></i>
                        Annuler
                    </a>
                    <button type="submit" class="btn-custom btn-primary-custom" id="submitBtn">
                        <span class="loading-spinner"></span>
                        <i class="fas fa-save"></i>
                        Créer la région
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('regionForm');
        const submitBtn = document.getElementById('submitBtn');
        const previewSection = document.getElementById('previewSection');
        const previewContent = document.getElementById('previewContent');
        
        // Champs du formulaire
        const nomField = document.getElementById('nom');
        const codeField = document.getElementById('code');
        const chefLieuField = document.getElementById('chef_lieu');
        const superficieField = document.getElementById('superficie');
        const populationField = document.getElementById('population');

        // Génération automatique du code à partir du nom
        nomField.addEventListener('input', function() {
            if (this.value && !codeField.value) {
                const code = this.value.substring(0, 2).toUpperCase();
                codeField.value = code;
            }
            updatePreview();
        });

        // Mise à jour de l'aperçu en temps réel
        [nomField, codeField, chefLieuField, superficieField, populationField].forEach(field => {
            field.addEventListener('input', updatePreview);
        });

        function updatePreview() {
            const nom = nomField.value;
            const code = codeField.value;
            const chefLieu = chefLieuField.value;
            const superficie = superficieField.value;
            const population = populationField.value;

            if (nom || code || chefLieu) {
                previewSection.style.display = 'block';
                
                let preview = '';
                if (nom) preview += `<strong>Région:</strong> ${nom}<br>`;
                if (code) preview += `<strong>Code:</strong> ${code}<br>`;
                if (chefLieu) preview += `<strong>Chef-lieu:</strong> ${chefLieu}<br>`;
                if (superficie) preview += `<strong>Superficie:</strong> ${parseInt(superficie).toLocaleString()} km²<br>`;
                if (population) preview += `<strong>Population:</strong> ${parseInt(population).toLocaleString()} habitants<br>`;
                
                if (superficie && population) {
                    const densite = (population / superficie).toFixed(2);
                    preview += `<strong>Densité:</strong> ${densite} hab/km²<br>`;
                }

                previewContent.innerHTML = preview;
            } else {
                previewSection.style.display = 'none';
            }
        }

        // Validation en temps réel
        function validateField(field, rules) {
            const value = field.value.trim();
            let isValid = true;
            let message = '';

            rules.forEach(rule => {
                if (!isValid) return;

                switch (rule.type) {
                    case 'required':
                        if (!value) {
                            isValid = false;
                            message = rule.message || 'Ce champ est obligatoire';
                        }
                        break;
                    case 'maxLength':
                        if (value.length > rule.value) {
                            isValid = false;
                            message = rule.message || `Maximum ${rule.value} caractères`;
                        }
                        break;
                    case 'minLength':
                        if (value && value.length < rule.value) {
                            isValid = false;
                            message = rule.message || `Minimum ${rule.value} caractères`;
                        }
                        break;
                    case 'pattern':
                        if (value && !rule.value.test(value)) {
                            isValid = false;
                            message = rule.message || 'Format invalide';
                        }
                        break;
                }
            });

            // Appliquer les styles de validation
            if (value) {
                if (isValid) {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                } else {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                }

                // Afficher le message d'erreur
                let feedback = field.parentElement.querySelector('.invalid-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    field.parentElement.appendChild(feedback);
                }
                feedback.textContent = message;
            } else {
                field.classList.remove('is-valid', 'is-invalid');
            }

            return isValid;
        }

        // Règles de validation
        nomField.addEventListener('blur', function() {
            validateField(this, [
                { type: 'required', message: 'Le nom de la région est obligatoire' },
                { type: 'minLength', value: 2, message: 'Le nom doit contenir au moins 2 caractères' }
            ]);
        });

        codeField.addEventListener('blur', function() {
            validateField(this, [
                { type: 'required', message: 'Le code de la région est obligatoire' },
                { type: 'maxLength', value: 10, message: 'Le code ne peut pas dépasser 10 caractères' },
                { type: 'pattern', value: /^[A-Z0-9-]+$/, message: 'Le code ne peut contenir que des lettres majuscules, chiffres et tirets' }
            ]);
        });

        chefLieuField.addEventListener('blur', function() {
            validateField(this, [
                { type: 'required', message: 'Le chef-lieu est obligatoire' },
                { type: 'minLength', value: 2, message: 'Le chef-lieu doit contenir au moins 2 caractères' }
            ]);
        });

        // Formatage des nombres
        superficieField.addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
        });

        populationField.addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
        });

        // Soumission du formulaire
        form.addEventListener('submit', function(e) {
            // Ajouter la classe de chargement
            form.classList.add('loading');
            submitBtn.disabled = true;

            // Validation finale
            let isFormValid = true;
            
            isFormValid &= validateField(nomField, [
                { type: 'required' },
                { type: 'minLength', value: 2 }
            ]);
            
            isFormValid &= validateField(codeField, [
                { type: 'required' },
                { type: 'maxLength', value: 10 },
                { type: 'pattern', value: /^[A-Z0-9-]+$/ }
            ]);
            
            isFormValid &= validateField(chefLieuField, [
                { type: 'required' },
                { type: 'minLength', value: 2 }
            ]);

            if (!isFormValid) {
                e.preventDefault();
                form.classList.remove('loading');
                submitBtn.disabled = false;
                
                // Scroll vers la première erreur
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });

        // Animation d'entrée
        const formCard = document.querySelector('.form-card');
        formCard.style.opacity = '0';
        formCard.style.transform = 'translateY(30px)';
        formCard.style.transition = 'all 0.6s ease';

        setTimeout(() => {
            formCard.style.opacity = '1';
            formCard.style.transform = 'translateY(0)';
        }, 200);
    });
</script>
@endpush