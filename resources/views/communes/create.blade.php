@extends('layouts.app')

@section('title', 'Nouvelle Commune - Observatoire des Collectivités')
@section('page-title', 'Nouvelle Commune')

@push('styles')
<style>
.commune-form-container {
    max-width: 1200px;
    margin: 0 auto;
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.form-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.form-section {
    padding: 2rem;
    border-bottom: 1px solid #e9ecef;
}

.form-section:last-child {
    border-bottom: none;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}

.form-group label.required::after {
    content: " *";
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.875rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.form-check-group {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.form-check {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.form-check:hover {
    background-color: #f8f9fa;
}

.form-check-input {
    margin-right: 0.5rem;
}

.form-check-label {
    margin: 0;
    font-weight: normal;
    cursor: pointer;
    flex: 1;
}

.form-actions {
    padding: 1.5rem 2rem;
    background: #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

.alert {
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 4px;
    border: 1px solid transparent;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1rem;
}

.breadcrumb-item {
    display: inline-block;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    margin: 0 0.5rem;
    color: #6c757d;
}

.breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
}

.breadcrumb-item.active {
    color: #6c757d;
}

.help-text {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.form-row {
    display: flex;
    gap: 1rem;
}

.form-row .form-group {
    flex: 1;
}

.loading {
    opacity: 0.6;
    pointer-events: none;
}

.spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@section('content')
<div class="commune-form-container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <span class="breadcrumb-item"><a href="{{ route('communes.index') }}">Communes</a></span>
        <span class="breadcrumb-item active">Nouvelle commune</span>
    </nav>

    <!-- En-tête -->
    <div class="form-header">
        <h2>Nouvelle Commune</h2>
        <a href="{{ route('communes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Retour à la liste
        </a>
    </div>

    <!-- Messages d'erreur -->
    @if($errors->any())
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle"></i> Erreurs de validation</h5>
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulaire -->
    <form method="POST" action="{{ route('communes.store') }}" id="commune-form">
        @csrf
        
        <div class="form-card">
            <!-- Informations de base -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Informations de base
                </h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nom" class="required">Nom de la commune</label>
                        <input 
                            type="text" 
                            id="nom" 
                            name="nom" 
                            class="form-control @error('nom') is-invalid @enderror" 
                            value="{{ old('nom') }}"
                            placeholder="Entrez le nom de la commune"
                            required
                        >
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="code" class="required">Code commune</label>
                        <input 
                            type="text" 
                            id="code" 
                            name="code" 
                            class="form-control @error('code') is-invalid @enderror" 
                            value="{{ old('code') }}"
                            placeholder="Ex: CM001"
                            required
                        >
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="help-text">Code unique d'identification de la commune</div>
                    </div>

                    <div class="form-group">
                        <label for="departement_id" class="required">Département</label>
                        <select 
                            id="departement_id" 
                            name="departement_id" 
                            class="form-control @error('departement_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Sélectionnez un département</option>
                            @foreach($departements as $departement)
                                <option value="{{ $departement->id }}" 
                                        {{ old('departement_id') == $departement->id ? 'selected' : '' }}>
                                    {{ $departement->nom }} ({{ $departement->region->nom }})
                                </option>
                            @endforeach
                        </select>
                        @error('departement_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informations de contact -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-address-book"></i>
                    Informations de contact
                </h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input 
                            type="tel" 
                            id="telephone" 
                            name="telephone" 
                            class="form-control @error('telephone') is-invalid @enderror" 
                            value="{{ old('telephone') }}"
                            placeholder="Ex: +237 6XX XXX XXX"
                        >
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                     {{-- <div class="form-group">
                        <label for="email">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            value="{{ old('email') }}"
                            placeholder="commune@example.com"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="adresse">Adresse</label>
                        <textarea 
                            id="adresse" 
                            name="adresse" 
                            class="form-control @error('adresse') is-invalid @enderror" 
                            rows="3"
                            placeholder="Adresse complète de la mairie"
                        >{{ old('adresse') }}</textarea>
                        @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}
                {{-- </div>
            </div> --}}

            <!-- Informations démographiques -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-chart-bar"></i>
                    Informations démographiques
                </h3>
                
                {{-- <div class="form-row">
                    <div class="form-group">
                        <label for="population">Population</label>
                        <input 
                            type="number" 
                            id="population" 
                            name="population" 
                            class="form-control @error('population') is-invalid @enderror" 
                            value="{{ old('population') }}"
                            min="0"
                            placeholder="Nombre d'habitants"
                        >
                        @error('population')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="superficie">Superficie (km²)</label>
                        <input 
                            type="number" 
                            id="superficie" 
                            name="superficie" 
                            class="form-control @error('superficie') is-invalid @enderror" 
                            value="{{ old('superficie') }}"
                            min="0"
                            step="0.01"
                            placeholder="Superficie en km²"
                        >
                        @error('superficie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}
                </div>
            </div> 

            <!-- Responsables -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-users"></i>
                    Responsables
                </h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="receveur_ids">Receveurs</label>
                        <div class="form-check-group">
                            @foreach($receveurs as $receveur)
                                <div class="form-check">
                                    <input 
                                        type="checkbox" 
                                        id="receveur_{{ $receveur->id }}" 
                                        name="receveur_ids[]" 
                                        value="{{ $receveur->id }}"
                                        class="form-check-input"
                                        {{ in_array($receveur->id, old('receveur_ids', [])) ? 'checked' : '' }}
                                    >
                                    <label for="receveur_{{ $receveur->id }}" class="form-check-label">
                                        {{ $receveur->nom }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('receveur_ids')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($receveurs->count() == 0)
                            <div class="help-text">Aucun receveur disponible</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="ordonnateur_ids">Ordonnateurs</label>
                        <div class="form-check-group">
                            @foreach($ordonnateurs as $ordonnateur)
                                <div class="form-check">
                                    <input 
                                        type="checkbox" 
                                        id="ordonnateur_{{ $ordonnateur->id }}" 
                                        name="ordonnateur_ids[]" 
                                        value="{{ $ordonnateur->id }}"
                                        class="form-check-input"
                                        {{ in_array($ordonnateur->id, old('ordonnateur_ids', [])) ? 'checked' : '' }}
                                    >
                                    <label for="ordonnateur_{{ $ordonnateur->id }}" class="form-check-label">
                                        {{ $ordonnateur->nom }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('ordonnateur_ids')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($ordonnateurs->count() == 0)
                            <div class="help-text">Aucun ordonnateur disponible</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <a href="{{ route('communes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
                
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <i class="fas fa-save"></i>
                    Enregistrer la commune
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('commune-form');
    const submitBtn = document.getElementById('submit-btn');
    
    // Gestion de la soumission du formulaire
    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Enregistrement...';
        
        // Réactiver le bouton après 5 secondes en cas de problème
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer la commune';
        }, 5000);
    });
    
    // Génération automatique du code (optionnel)
    const nomInput = document.getElementById('nom');
    const codeInput = document.getElementById('code');
    
    nomInput.addEventListener('input', function() {
        if (!codeInput.value) {
            // Générer un code basé sur le nom (simplifié)
            const nom = this.value.trim();
            if (nom) {
                const code = nom.toUpperCase()
                    .replace(/[^A-Z\s]/g, '')
                    .replace(/\s+/g, '')
                    .substring(0, 6);
                codeInput.value = code;
            }
        }
    });
});
</script>
@endpush
@endsection



