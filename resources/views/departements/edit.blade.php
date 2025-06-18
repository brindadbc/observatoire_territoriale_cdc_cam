@extends('layouts.app')

@section('title', 'Modifier Département - Observatoire des Collectivités')
@section('page-title', 'Modifier le Département')

@section('content')
<div class="departement-edit">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('dashboard.index') }}">Tableau de bord</a>
        <span>/</span>
        <a href="{{ route('departements.index') }}">Départements</a>
        <span>/</span>
        <a href="{{ route('departements.show', $departement) }}">{{ $departement->nom }}</a>
        <span>/</span>
        <span>Modifier</span>
    </div>

    <!-- Messages d'erreur -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Erreurs de validation :</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Formulaire -->
    <div class="form-container">
        <div class="form-header">
            <h2>Modification du département</h2>
            <p>Modifiez les informations du département <strong>{{ $departement->nom }}</strong></p>
        </div>

        <form action="{{ route('departements.update', $departement) }}" method="POST" class="departement-form">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <!-- Informations principales -->
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Informations principales</h3>
                    
                    <div class="form-group">
                        <label for="nom" class="required">Nom du département</label>
                        <input type="text" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom', $departement->nom) }}" 
                               required
                               class="form-control @error('nom') is-invalid @enderror"
                               placeholder="Ex: Fako">
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- <div class="form-group">
                        <label for="code" class="required">Code du département</label>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               value="{{ old('code', $departement->code) }}" 
                               required
                               class="form-control @error('code') is-invalid @enderror"
                               placeholder="Ex: FK"
                               maxlength="10">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    <div class="form-group">
                        <label for="region_id" class="required">Région</label>
                        <select id="region_id" 
                                name="region_id" 
                                required
                                class="form-control @error('region_id') is-invalid @enderror">
                            <option value="">Sélectionnez une région</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" 
                                        {{ old('region_id', $departement->region_id) == $region->id ? 'selected' : '' }}>
                                    {{ $region->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('region_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- <div class="form-group">
                        <label for="chef_lieu">Chef-lieu</label>
                        <input type="text" 
                               id="chef_lieu" 
                               name="chef_lieu" 
                               value="{{ old('chef_lieu', $departement->chef_lieu) }}" 
                               class="form-control @error('chef_lieu') is-invalid @enderror"
                               placeholder="Ex: Limbe">
                        @error('chef_lieu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div> --}}

                <!-- Informations complémentaires -->
                {{-- <div class="form-section">
                    <h3><i class="fas fa-chart-bar"></i> Informations complémentaires</h3>
                    
                    <div class="form-group">
                        <label for="superficie">Superficie (km²)</label>
                        <input type="number" 
                               id="superficie" 
                               name="superficie" 
                               value="{{ old('superficie', $departement->superficie) }}" 
                               step="0.01"
                               min="0"
                               class="form-control @error('superficie') is-invalid @enderror"
                               placeholder="Ex: 2093.2">
                        @error('superficie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="population">Population</label>
                        <input type="number" 
                               id="population" 
                               name="population" 
                               value="{{ old('population', $departement->population) }}" 
                               min="0"
                               class="form-control @error('population') is-invalid @enderror"
                               placeholder="Ex: 534854">
                        @error('population')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Description du département...">{{ old('description', $departement->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div> --}}

            <!-- Informations sur les modifications -->
            <div class="modification-info">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Créé le :</span>
                        <span class="value">{{ $departement->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Dernière modification :</span>
                        <span class="value">{{ $departement->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Nombre de communes :</span>
                        <span class="value">{{ $departement->communes()->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <a href="{{ route('departements.show', $departement) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i>
                    Voir détails
                </a>
                <a href="{{ route('departements.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.departement-edit {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.form-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
}

.form-header {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    color: white;
    padding: 30px;
    text-align: center;
}

.form-header h2 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 600;
}

.form-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 16px;
}

.departement-form {
    padding: 40px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
}

.form-section {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 10px;
    border: 2px solid #e9ecef;
}

.form-section h3 {
    margin: 0 0 25px 0;
    color: #495057;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 15px;
    border-bottom: 2px solid #dee2e6;
}

.form-section h3 i {
    color: #f39c12;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.form-group label.required::after {
    content: " *";
    color: #e74c3c;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #f39c12;
    box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
}

.form-control.is-invalid {
    border-color: #e74c3c;
}

.invalid-feedback {
    display: block;
    color: #e74c3c;
    font-size: 12px;
    margin-top: 5px;
}

.modification-info {
    background: #e8f4f8;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 30px;
    border-left: 5px solid #17a2b8;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-item .label {
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
}

.info-item .value {
    font-size: 14px;
    color: #495057;
    font-weight: 500;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px solid #e9ecef;
}

.btn {
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(243, 156, 18, 0.4);
}

.btn-secondary {
    background: #17a2b8;
    color: white;
}

.btn-secondary:hover {
    background: #138496;
    transform: translateY(-2px);
}

.btn-outline {
    background: transparent;
    color: #6c757d;
    border: 2px solid #6c757d;
}

.btn-outline:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
}

.alert {
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 2px solid #f5c6cb;
}

.alert ul {
    margin: 0;
    padding-left: 20px;
}

.alert li {
    margin-bottom: 5px;
}

.breadcrumb {
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}

.breadcrumb a {
    color: #f39c12;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb span {
    color: #6c757d;
}

/* Loader pour les requêtes AJAX */
.loading {
    opacity: 0.6;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #f39c12;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Animations d'entrée */
.form-container {
    animation: slideInUp 0.5s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Améliorations pour mobile */
@media (max-width: 576px) {
    .departement-edit {
        padding: 10px;
    }
    
    .departement-form {
        padding: 20px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation en temps réel
    const form = document.querySelector('.departement-form');
    const inputs = form.querySelectorAll('.form-control');
    
    // Validation des champs obligatoires
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            // Retirer la classe d'erreur lors de la saisie
            this.classList.remove('is-invalid');
            const feedback = this.parentElement.querySelector('.invalid-feedback');
            if (feedback && !feedback.textContent.trim()) {
                feedback.style.display = 'none';
            }
        });
    });
    
    // Validation du formulaire avant soumission
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            // Scroll vers le premier champ en erreur
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
            return false;
        }
        
        // Afficher le loader
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
    });
    
    // Fonction de validation des champs
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';
        
        // Validation des champs obligatoires
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'Ce champ est obligatoire.';
        }
        
        // Validation spécifique par type de champ
        if (value && isValid) {
            switch (field.name) {
                case 'nom':
                    if (value.length < 2) {
                        isValid = false;
                        errorMessage = 'Le nom doit contenir au moins 2 caractères.';
                    } else if (value.length > 255) {
                        isValid = false;
                        errorMessage = 'Le nom ne peut pas dépasser 255 caractères.';
                    }
                    break;
                    
                case 'code':
                    if (value.length < 2) {
                        isValid = false;
                        errorMessage = 'Le code doit contenir au moins 2 caractères.';
                    } else if (value.length > 10) {
                        isValid = false;
                        errorMessage = 'Le code ne peut pas dépasser 10 caractères.';
                    } else if (!/^[A-Z0-9]+$/i.test(value)) {
                        isValid = false;
                        errorMessage = 'Le code ne peut contenir que des lettres et des chiffres.';
                    }
                    break;
                    
                case 'superficie':
                    if (parseFloat(value) < 0) {
                        isValid = false;
                        errorMessage = 'La superficie ne peut pas être négative.';
                    }
                    break;
                    
                case 'population':
                    if (parseInt(value) < 0) {
                        isValid = false;
                        errorMessage = 'La population ne peut pas être négative.';
                    }
                    break;
            }
        }
        
        // Afficher/masquer les erreurs
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            
            // Créer ou mettre à jour le message d'erreur
            let feedback = field.parentElement.querySelector('.invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                field.parentElement.appendChild(feedback);
            }
            feedback.textContent = errorMessage;
            feedback.style.display = 'block';
        }
        
        return isValid;
    }
    
    // Animation des boutons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Formatage automatique du code (en majuscules)
    const codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Formatage automatique des nombres
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Supprimer les caractères non numériques (sauf le point décimal)
            if (this.name === 'superficie') {
                this.value = this.value.replace(/[^\d.]/g, '');
            } else if (this.name === 'population') {
                this.value = this.value.replace(/[^\d]/g, '');
            }
        });
    });
    
    // Confirmation avant annulation si des modifications ont été apportées
    const originalValues = {};
    inputs.forEach(input => {
        originalValues[input.name] = input.value;
    });
    
    const cancelBtn = document.querySelector('.btn-outline');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            let hasChanges = false;
            inputs.forEach(input => {
                if (input.value !== originalValues[input.name]) {
                    hasChanges = true;
                }
            });
            
            if (hasChanges) {
                if (!confirm('Vous avez des modifications non sauvegardées. Êtes-vous sûr de vouloir quitter ?')) {
                    e.preventDefault();
                }
            }
        });
    }
    
    // Auto-save en local storage (optionnel)
    const autoSaveKey = `departement_edit_${document.querySelector('input[name="nom"]').value}`;
    
    // Charger les données sauvegardées
    const savedData = localStorage.getItem(autoSaveKey);
    if (savedData) {
        try {
            const data = JSON.parse(savedData);
            Object.keys(data).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input && input.value === originalValues[key]) {
                    input.value = data[key];
                }
            });
        } catch (e) {
            console.error('Erreur lors du chargement des données sauvegardées:', e);
        }
    }
    
    // Sauvegarder les modifications
    let saveTimeout;
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                const formData = {};
                inputs.forEach(inp => {
                    if (inp.value !== originalValues[inp.name]) {
                        formData[inp.name] = inp.value;
                    }
                });
                
                if (Object.keys(formData).length > 0) {
                    localStorage.setItem(autoSaveKey, JSON.stringify(formData));
                }
            }, 1000);
        });
    });
    
    // Nettoyer le localStorage après soumission réussie
    form.addEventListener('submit', function() {
        localStorage.removeItem(autoSaveKey);
    });
});
</script>
@endpush