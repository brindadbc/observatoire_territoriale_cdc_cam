@extends('layouts.app')

@section('title', 'Modifier Département - Observatoire des Collectivités')
@section('page-title', 'Modifier le Département')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --warning-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ffa8a8 100%);
    --info-gradient: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
    
    --shadow-light: 0 2px 15px rgba(0,0,0,0.08);
    --shadow-medium: 0 8px 30px rgba(0,0,0,0.12);
    --shadow-heavy: 0 15px 40px rgba(0,0,0,0.15);
    
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

.departement-edit {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    animation: fadeInUp 0.6s ease-out;
}

/* Breadcrumb moderne */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-light);
    margin-bottom: 2rem;
    font-size: 0.875rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.breadcrumb a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
}

.breadcrumb a:hover {
    background: rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

.breadcrumb span {
    color: #64748b;
    font-weight: 400;
}

/* Alert moderne */
.alert {
    padding: 1.5rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    border: none;
    box-shadow: var(--shadow-medium);
    backdrop-filter: blur(10px);
    animation: slideInDown 0.5s ease-out;
}

.alert-danger {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%);
    border-left: 4px solid #ef4444;
    color: #dc2626;
}

.alert i {
    font-size: 1.25rem;
    margin-top: 0.125rem;
}

.alert ul {
    list-style: none;
    margin: 0.5rem 0 0 0;
}

.alert li {
    margin-bottom: 0.5rem;
    padding-left: 1rem;
    position: relative;
}

.alert li::before {
    content: '•';
    position: absolute;
    left: 0;
    color: #ef4444;
    font-weight: bold;
}

/* Container principal */
.form-container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-heavy);
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.3);
    animation: fadeInUp 0.6s ease-out 0.2s both;
}

.form-header {
    background: var(--primary-gradient);
    color: white;
    padding: 3rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.form-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.form-header h2 {
    margin: 0 0 1rem 0;
    font-size: 2rem;
    font-weight: 700;
    position: relative;
    z-index: 2;
}

.form-header p {
    margin: 0;
    opacity: 0.95;
    font-size: 1.125rem;
    font-weight: 400;
    position: relative;
    z-index: 2;
}

/* Formulaire */
.departement-form {
    padding: 3rem 2rem;
}

.form-section {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 2rem;
    border-radius: var(--border-radius);
    border: 2px solid rgba(102, 126, 234, 0.1);
    margin-bottom: 2rem;
    transition: var(--transition);
    animation: fadeInUp 0.6s ease-out;
}

.form-section:hover {
    border-color: rgba(102, 126, 234, 0.2);
    box-shadow: var(--shadow-light);
    transform: translateY(-2px);
}

.form-section h3 {
    margin: 0 0 2rem 0;
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(102, 126, 234, 0.1);
}

.form-section h3 i {
    color: #667eea;
    font-size: 1.125rem;
}

.form-group {
    margin-bottom: 2rem;
    position: relative;
}

.form-group label {
    display: block;
    margin-bottom: 0.75rem;
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-group label.required::after {
    content: " *";
    color: #ef4444;
    font-weight: 700;
}

.form-control {
    width: 100%;
    padding: 1rem 1.25rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: var(--transition);
    background: white;
    font-weight: 400;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1), 0 4px 12px rgba(102, 126, 234, 0.15);
    transform: translateY(-1px);
}

.form-control.is-invalid {
    border-color: #ef4444;
    background: rgba(239, 68, 68, 0.02);
}

.form-control.is-valid {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.02);
}

.invalid-feedback, .valid-feedback {
    display: block;
    font-size: 0.75rem;
    margin-top: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-weight: 500;
}

.invalid-feedback {
    color: #dc2626;
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.valid-feedback {
    color: #059669;
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

/* Textarea styling */
textarea.form-control {
    resize: vertical;
    min-height: 120px;
    line-height: 1.6;
}

/* Select styling */
select.form-control {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23667eea' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1rem;
    padding-right: 3rem;
}

/* Informations de modification */
.modification-info {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(147, 197, 253, 0.05) 100%);
    padding: 2rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    border: 2px solid rgba(59, 130, 246, 0.1);
    position: relative;
    animation: fadeInUp 0.6s ease-out 0.4s both;
}

.modification-info::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--info-gradient);
    border-radius: 2px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 12px;
    border: 1px solid rgba(59, 130, 246, 0.1);
    transition: var(--transition);
}

.info-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

.info-item .label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-item .value {
    font-size: 0.95rem;
    color: #1f2937;
    font-weight: 600;
}

/* Actions du formulaire */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 2px solid rgba(229, 231, 235, 0.5);
    animation: fadeInUp 0.6s ease-out 0.6s both;
}

.btn {
    padding: 0.875rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    min-width: 140px;
    justify-content: center;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.6s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: var(--primary-gradient);
    color: white;
    box-shadow: var(--shadow-medium);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-heavy);
}

.btn-secondary {
    background: var(--info-gradient);
    color: white;
    box-shadow: var(--shadow-medium);
}

.btn-secondary:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-heavy);
}

.btn-outline {
    background: transparent;
    color: #6b7280;
    border: 2px solid #d1d5db;
    box-shadow: var(--shadow-light);
}

.btn-outline:hover {
    background: #6b7280;
    color: white;
    border-color: #6b7280;
    transform: translateY(-3px);
    box-shadow: var(--shadow-medium);
}

/* États de chargement */
.btn.loading {
    pointer-events: none;
    position: relative;
    color: transparent !important;
}

.btn.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Validation visuelle en temps réel */
.form-group.validating .form-control {
    border-color: #f59e0b;
}

.form-group.valid .form-control {
    border-color: #10b981;
}

.form-group.invalid .form-control {
    border-color: #ef4444;
}

/* Progress indicator */
.form-progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: rgba(102, 126, 234, 0.1);
    z-index: 1000;
}

.form-progress-bar {
    height: 100%;
    background: var(--primary-gradient);
    width: 0%;
    transition: width 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .departement-edit {
        padding: 1rem;
    }
    
    .form-container {
        margin: 0;
    }
    
    .form-header {
        padding: 2rem 1.5rem;
    }
    
    .form-header h2 {
        font-size: 1.5rem;
    }
    
    .departement-form {
        padding: 2rem 1.5rem;
    }
    
    .form-section {
        padding: 1.5rem;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .btn {
        width: 100%;
        min-width: auto;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .breadcrumb {
        flex-wrap: wrap;
        padding: 0.75rem 1rem;
        font-size: 0.75rem;
    }
    
    .form-header {
        padding: 1.5rem 1rem;
    }
    
    .form-header h2 {
        font-size: 1.25rem;
    }
    
    .form-header p {
        font-size: 1rem;
    }
    
    .departement-form {
        padding: 1.5rem 1rem;
    }
    
    .form-section {
        padding: 1rem;
    }
    
    .form-control {
        padding: 0.875rem 1rem;
        font-size: 0.875rem;
    }
}

/* Floating labels effect */
.form-group-floating {
    position: relative;
}

.form-group-floating .form-control {
    padding-top: 1.5rem;
    padding-bottom: 0.5rem;
}

.form-group-floating label {
    position: absolute;
    top: 1rem;
    left: 1.25rem;
    transition: var(--transition);
    pointer-events: none;
    color: #9ca3af;
    background: white;
    padding: 0 0.25rem;
    font-size: 0.875rem;
    font-weight: 400;
    text-transform: none;
    letter-spacing: normal;
}

.form-group-floating .form-control:focus + label,
.form-group-floating .form-control:not(:placeholder-shown) + label {
    top: -0.5rem;
    left: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #667eea;
}

/* Custom checkbox/radio styles */
.custom-checkbox, .custom-radio {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    padding: 0.75rem;
    border-radius: 8px;
    transition: var(--transition);
}

.custom-checkbox:hover, .custom-radio:hover {
    background: rgba(102, 126, 234, 0.05);
}

.custom-checkbox input, .custom-radio input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.checkmark, .radiomark {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.radiomark {
    border-radius: 50%;
}

.custom-checkbox input:checked + .checkmark,
.custom-radio input:checked + .radiomark {
    background: var(--primary-gradient);
    border-color: #667eea;
}

.checkmark::after {
    content: '✓';
    color: white;
    font-size: 12px;
    font-weight: bold;
    opacity: 0;
    transition: var(--transition);
}

.radiomark::after {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
    opacity: 0;
    transition: var(--transition);
}

.custom-checkbox input:checked + .checkmark::after,
.custom-radio input:checked + .radiomark::after {
    opacity: 1;
}

/* Success message */
.success-message {
    position: fixed;
    top: 2rem;
    right: 2rem;
    background: var(--success-gradient);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: var(--shadow-heavy);
    z-index: 1000;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.success-message.show {
    transform: translateX(0);
}

.success-message i {
    margin-right: 0.5rem;
}
</style>
@endpush

@section('content')
<div class="departement-edit">
    <!-- Progress indicator -->
    <div class="form-progress">
        <div class="form-progress-bar" id="formProgress"></div>
    </div>

    <!-- Breadcrumb -->
    <div class="breadcrumb animate__animated animate__fadeInDown">
        <a href="{{ route('dashboard.index') }}">
            <i class="fas fa-home"></i> Tableau de bord
        </a>
        <span>/</span>
        <a href="{{ route('departements.index') }}">
            <i class="fas fa-map"></i> Départements
        </a>
        <span>/</span>
        <a href="{{ route('departements.show', $departement) }}">{{ $departement->nom }}</a>
        <span>/</span>
        <span><i class="fas fa-edit"></i> Modifier</span>
    </div>

    <!-- Messages d'erreur -->
    @if ($errors->any())
        <div class="alert alert-danger animate__animated animate__slideInDown">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Erreurs de validation détectées :</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Formulaire principal -->
    <div class="form-container">
        <div class="form-header">
            <h2>
                <i class="fas fa-edit"></i>
                Modification du département
            </h2>
            <p>Mettez à jour les informations du département <strong>{{ $departement->nom }}</strong></p>
        </div>

        <form action="{{ route('departements.update', $departement) }}" method="POST" class="departement-form" id="departementForm">
            @csrf
            @method('PUT')
            
            <!-- Informations principales -->
            <div class="form-section animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    Informations principales
                </h3>
                
                <div class="form-group">
                    <label for="nom" class="required">Nom du département</label>
                    <input type="text" 
                           id="nom" 
                           name="nom" 
                           value="{{ old('nom', $departement->nom) }}" 
                           required
                           class="form-control @error('nom') is-invalid @enderror"
                           placeholder="Entrez le nom du département"
                           autocomplete="organization">
                    @error('nom')
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="valid-feedback" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        Nom valide
                    </div>
                </div>

                <div class="form-group">
                    <label for="region_id" class="required">Région d'appartenance</label>
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
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="valid-feedback" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        Région sélectionnée
                    </div>
                </div>
            </div>

            <!-- Informations sur les modifications -->
            <div class="modification-info">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">
                            <i class="fas fa-calendar-edit"></i>
                            Dernière modification
                        </span>
                        <span class="value">{{ $departement->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">
                            <i class="fas fa-city"></i>
                            Nombre de communes
                        </span>
                        <span class="value">{{ $departement->communes()->count() }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">
                            <i class="fas fa-users"></i>
                            Statut du département
                        </span>
                        <span class="value">
                            <span style="color: #10b981; font-weight: 700;">
                                <i class="fas fa-check-circle"></i> Actif
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions du formulaire -->
            <div class="form-actions">
                <a href="{{ route('departements.show', $departement) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i>
                    Voir détails
                </a>
                <a href="{{ route('departements.index') }}" class="btn btn-outline" id="cancelBtn">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success Message Template -->
<div class="success-message" id="successMessage" style="display: none;">
    <i class="fas fa-check-circle"></i>
    Département mis à jour avec succès !
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const form = document.getElementById('departementForm');
    const inputs = form.querySelectorAll('.form-control');
    const submitBtn = document.getElementById('submitBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const progressBar = document.getElementById('formProgress');
    const successMessage = document.getElementById('successMessage');
    
    // Valeurs originales pour détecter les changements
    const originalValues = {};
    inputs.forEach(input => {
        originalValues[input.name] = input.value;
    });

    // Configuration de la validation
    const validationRules = {
        nom: {
            required: true,
            minLength: 2,
            maxLength: 255,
            pattern: /^[a-zA-ZÀ-ÿ\s\-']+$/,
            message: 'Le nom doit contenir entre 2 et 255 caractères (lettres, espaces, tirets et apostrophes uniquement)'
        },
        region_id: {
            required: true,
            message: 'Veuillez sélectionner une région'
        }
    };

    // Fonction de validation en temps réel
    function validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        const rules = validationRules[fieldName];
        const formGroup = field.closest('.form-group');
        
        if (!rules) return true;

        let isValid = true;
        let errorMessage = '';

        // Validation obligatoire
        if (rules.required && !value) {
            isValid = false;
            errorMessage = 'Ce champ est obligatoire';
        }
        
        // Validation spécifique si la valeur existe
        if (value && isValid) {
            // Longueur minimale
            if (rules.minLength && value.length < rules.minLength) {
                isValid = false;
                errorMessage = `Minimum ${rules.minLength} caractères requis`;
            }
            
            // Longueur maximale
            if (rules.maxLength && value.length > rules.maxLength) {
                isValid = false;
                errorMessage = `Maximum ${rules.maxLength} caractères autorisés`;
            }
            
            // Pattern de validation
            if (rules.pattern && !rules.pattern.test(value)) {
                isValid = false;
                errorMessage = rules.message || 'Format invalide';
            }
        }

        // Mise à jour de l'interface
        updateFieldValidation(formGroup, field, isValid, errorMessage);
        updateFormProgress();
        
        return isValid;
    }

    // Mise à jour de l'affichage de validation
    function updateFieldValidation(formGroup, field, isValid, errorMessage) {
        const invalidFeedback = formGroup.querySelector('.invalid-feedback');
        const validFeedback = formGroup.querySelector('.valid-feedback');
        
        // Réinitialisation des classes
        formGroup.classList.remove('valid', 'invalid', 'validating');
        field.classList.remove('is-valid', 'is-invalid');
        
        if (field.value.trim()) {
            if (isValid) {
                formGroup.classList.add('valid');
                field.classList.add('is-valid');
                if (validFeedback) validFeedback.style.display = 'block';
                if (invalidFeedback) invalidFeedback.style.display = 'none';
            } else {
                formGroup.classList.add('invalid');
                field.classList.add('is-invalid');
                if (invalidFeedback) {
                    invalidFeedback.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${errorMessage}`;
                    invalidFeedback.style.display = 'block';
                }
                if (validFeedback) validFeedback.style.display = 'none';
            }
        } else {
            if (invalidFeedback) invalidFeedback.style.display = 'none';
            if (validFeedback) validFeedback.style.display = 'none';
        }
    }

    // Mise à jour de la barre de progression
    function updateFormProgress() {
        const totalFields = inputs.length;
        const validFields = form.querySelectorAll('.form-control.is-valid').length;
        const progress = (validFields / totalFields) * 100;
        
        progressBar.style.width = `${progress}%`;
        
        if (progress === 100) {
            progressBar.style.background = 'var(--success-gradient)';
        } else if (progress >= 50) {
            progressBar.style.background = 'var(--warning-gradient)';
        } else {
            progressBar.style.background = 'var(--primary-gradient)';
        }
    }

    // Écouteurs d'événements pour la validation
    inputs.forEach(input => {
        // Validation en temps réel
        input.addEventListener('input', function() {
            clearTimeout(this.validationTimeout);
            const formGroup = this.closest('.form-group');
            formGroup.classList.add('validating');
            
            this.validationTimeout = setTimeout(() => {
                formGroup.classList.remove('validating');
                validateField(this);
            }, 500);
        });

        // Validation à la perte de focus
        input.addEventListener('blur', function() {
            validateField(this);
        });

        // Animation de focus
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-2px)';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateY(0)';
        });
    });

    // Validation complète du formulaire
    function validateForm() {
        let isFormValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isFormValid = false;
            }
        });
        
        return isFormValid;
    }

    // Gestion de la soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            // Scroll vers le premier champ en erreur
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                firstError.focus();
                
                // Animation d'alerte
                firstError.style.animation = 'shake 0.5s ease-in-out';
                setTimeout(() => {
                    firstError.style.animation = '';
                }, 500);
            }
            return false;
        }

        // Affichage du loader
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        progressBar.style.width = '100%';
        
        // Simulation de délai de traitement
        setTimeout(() => {
            // Soumission réelle du formulaire
            this.submit();
        }, 1000);
    });

    // Gestion de l'annulation avec confirmation
    cancelBtn.addEventListener('click', function(e) {
        let hasChanges = false;
        inputs.forEach(input => {
            if (input.value !== originalValues[input.name]) {
                hasChanges = true;
            }
        });

        if (hasChanges) {
            e.preventDefault();
            showConfirmDialog(
                'Modifications non sauvegardées',
                'Vous avez des modifications non sauvegardées. Êtes-vous sûr de vouloir quitter ?',
                () => {
                    window.location.href = this.href;
                }
            );
        }
    });

    // Dialog de confirmation personnalisé
    function showConfirmDialog(title, message, onConfirm) {
        const dialog = document.createElement('div');
        dialog.className = 'custom-dialog';
        dialog.innerHTML = `
            <div class="dialog-overlay">
                <div class="dialog-content animate__animated animate__zoomIn">
                    <div class="dialog-header">
                        <h3><i class="fas fa-exclamation-triangle"></i> ${title}</h3>
                    </div>
                    <div class="dialog-body">
                        <p>${message}</p>
                    </div>
                    <div class="dialog-actions">
                        <button class="btn btn-outline" onclick="closeDialog()">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <button class="btn btn-primary" onclick="confirmAction()">
                            <i class="fas fa-check"></i> Confirmer
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(dialog);

        // Fonctions du dialog
        window.closeDialog = function() {
            dialog.remove();
            delete window.closeDialog;
            delete window.confirmAction;
        };

        window.confirmAction = function() {
            onConfirm();
            closeDialog();
        };

        // Fermeture en cliquant sur l'overlay
        dialog.querySelector('.dialog-overlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDialog();
            }
        });
    }

    // Auto-sauvegarde locale (draft)
    const autosaveKey = `departement_edit_draft_${document.querySelector('input[name="nom"]').value}`;
    
    // Chargement du draft
    function loadDraft() {
        const draft = localStorage.getItem(autosaveKey);
        if (draft) {
            try {
                const data = JSON.parse(draft);
                Object.keys(data).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input && input.value === originalValues[key]) {
                        input.value = data[key];
                        validateField(input);
                    }
                });
                
                // Notification de draft chargé
                showNotification('Brouillon automatique restauré', 'info');
            } catch (e) {
                console.error('Erreur lors du chargement du brouillon:', e);
            }
        }
    }

    // Sauvegarde automatique
    let autosaveTimeout;
    function startAutosave() {
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autosaveTimeout);
                autosaveTimeout = setTimeout(() => {
                    const formData = {};
                    let hasChanges = false;
                    
                    inputs.forEach(inp => {
                        if (inp.value !== originalValues[inp.name]) {
                            formData[inp.name] = inp.value;
                            hasChanges = true;
                        }
                    });
                    
                    if (hasChanges) {
                        localStorage.setItem(autosaveKey, JSON.stringify(formData));
                    }
                }, 2000);
            });
        });
    }

    // Nettoyage après soumission réussie
    form.addEventListener('submit', function() {
        localStorage.removeItem(autosaveKey);
    });

    // Notification système
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            ${message}
        `;

        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 100);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Raccourcis clavier
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S pour sauvegarder
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (validateForm()) {
                form.submit();
            }
        }
        
        // Échap pour annuler
        if (e.key === 'Escape') {
            cancelBtn.click();
        }
    });

    // Animation des boutons
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Initialisation
    loadDraft();
    startAutosave();
    updateFormProgress();

    // Animation d'entrée retardée pour les éléments
    setTimeout(() => {
        document.querySelectorAll('.animate__animated').forEach((el, index) => {
            el.style.animationDelay = `${index * 0.1}s`;
        });
    }, 100);
});

// Styles pour les éléments dynamiques
const dynamicStyles = `
<style>
.custom-dialog {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
}

.dialog-overlay {
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.dialog-content {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-heavy);
    max-width: 500px;
    width: 100%;
    overflow: hidden;
}

.dialog-header {
    background: var(--warning-gradient);
    color: white;
    padding: 1.5rem 2rem;
}

.dialog-header h3 {
    margin: 0;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dialog-body {
    padding: 2rem;
}

.dialog-body p {
    margin: 0;
    color: #64748b;
    line-height: 1.6;
}

.dialog-actions {
    padding: 1.5rem 2rem;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    border-top: 1px solid #e5e7eb;
}

.notification {
    position: fixed;
    top: 2rem;
    right: 2rem;
    background: var(--success-gradient);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: var(--shadow-heavy);
    z-index: 1000;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.notification-info {
    background: var(--info-gradient);
}

.notification.show {
    transform: translateX(0);
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', dynamicStyles);
</script>
@endpush
