@extends('layouts.app')

@section('title', 'Nouveau Département - Observatoire des Collectivités')
@section('page-title', 'Créer un Département')

@section('content')
<div class="departement-create">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('dashboard.index') }}">Tableau de bord</a>
        <span>/</span>
        <a href="{{ route('departements.index') }}">Départements</a>
        <span>/</span>
        <span>Nouveau</span>
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
            <h2>Informations du département</h2>
            <p>Renseignez les informations du nouveau département</p>
        </div>

        <form action="{{ route('departements.store') }}" method="POST" class="departement-form">
            @csrf
            
            <div class="form-grid">
                <!-- Informations principales -->
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Informations principales</h3>
                    
                    <div class="form-group">
                        <label for="nom" class="required">Nom du département</label>
                        <input type="text" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom') }}" 
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
                               value="{{ old('code') }}" 
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
                                <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
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
                               value="{{ old('chef_lieu') }}" 
                               class="form-control @error('chef_lieu') is-invalid @enderror"
                               placeholder="Ex: Limbe">
                        @error('chef_lieu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Informations complémentaires -->
                <div class="form-section">
                    <h3><i class="fas fa-chart-bar"></i> Informations complémentaires</h3>
                    
                    <div class="form-group">
                        <label for="superficie">Superficie (km²)</label>
                        <input type="number" 
                               id="superficie" 
                               name="superficie" 
                               value="{{ old('superficie') }}" 
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
                               value="{{ old('population') }}" 
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
                                  placeholder="Description du département...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div> --}}

            <!-- Actions -->
            <div class="form-actions">
                <a href="{{ route('departements.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.departement-create {
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    color: #667eea;
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
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
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
    color: #667eea;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb span {
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-génération du code basé sur le nom
    const nomInput = document.getElementById('nom');
    const codeInput = document.getElementById('code');
    
    nomInput.addEventListener('input', function() {
        if (!codeInput.value) {
            const nom = this.value.trim();
            if (nom) {
                // Prendre les 2-3 premières lettres et les mettre en majuscules
                const code = nom.substring(0, 3).toUpperCase();
                codeInput.value = code;
            }
        }
    });
    
    // Validation en temps réel
    const form = document.querySelector('.departement-form');
    const inputs = form.querySelectorAll('.form-control');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required');
        
        // Supprimer les classes d'erreur existantes
        field.classList.remove('is-invalid');
        
        if (isRequired && !value) {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Validation spécifique pour le code
        if (field.name === 'code' && value && value.length > 10) {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Validation pour les nombres
        if (field.type === 'number' && value && parseFloat(value) < 0) {
            field.classList.add('is-invalid');
            return false;
        }
        
        return true;
    }
    
    // Validation avant soumission
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Veuillez corriger les erreurs dans le formulaire.');
        }
    });
});
</script>
@endpush
@endsection