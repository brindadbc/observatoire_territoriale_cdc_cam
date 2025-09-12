{{-- @extends('layouts.app')

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

                     
            <!-- Informations démographiques -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-chart-bar"></i>
                    Informations démographiques
                </h3>
                
                
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


 --}}




 @extends('layouts.app')

@section('title', 'Nouvelle Commune - Observatoire des Collectivités')
@section('page-title', 'Nouvelle Commune')

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        color: #333;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .breadcrumb {
        background: rgba(255, 255, 255, 0.1);
        padding: 12px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .breadcrumb a {
        color: #fff;
        text-decoration: none;
        opacity: 0.8;
        transition: opacity 0.3s;
    }

    .breadcrumb a:hover {
        opacity: 1;
    }

    .breadcrumb-separator {
        margin: 0 8px;
        opacity: 0.6;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        color: white;
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #4CAF50, #45a049);
        color: white;
        padding: 24px 32px;
        text-align: center;
    }

    .form-header h2 {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }

    .form-header p {
        opacity: 0.9;
    }

    .form-section {
        padding: 32px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 12px;
        border-bottom: 2px solid #4CAF50;
    }

    .section-title i {
        color: #4CAF50;
        font-size: 1.1rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 0.9rem;
    }

    .form-group label.required::after {
        content: " *";
        color: #e74c3c;
        font-weight: bold;
    }

    .form-control {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid #e0e6ed;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #fff;
    }

    .form-control:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        transform: translateY(-1px);
    }

    .form-control.is-invalid {
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .form-control.is-valid {
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }

    .invalid-feedback {
        display: block;
        color: #e74c3c;
        font-size: 0.85rem;
        margin-top: 6px;
        font-weight: 500;
    }

    .valid-feedback {
        display: block;
        color: #4CAF50;
        font-size: 0.85rem;
        margin-top: 6px;
        font-weight: 500;
    }

    .help-text {
        font-size: 0.85rem;
        color: #666;
        margin-top: 6px;
    }

    .form-check-group {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 12px;
        margin-top: 12px;
    }

    .form-check {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border: 2px solid #e0e6ed;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: #fff;
        cursor: pointer;
    }

    .form-check:hover {
        border-color: #4CAF50;
        background: #f8fffe;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.1);
    }

    .form-check-input {
        margin-right: 12px;
        transform: scale(1.1);
    }

    .form-check-label {
        margin: 0;
        font-weight: 500;
        cursor: pointer;
        flex: 1;
    }

    .form-check.selected {
        border-color: #4CAF50;
        background: linear-gradient(135deg, #f8fffe, #e8f5e8);
    }

    .btn {
        padding: 14px 28px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4CAF50, #45a049);
        color: white;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        transform: none;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .form-actions {
        padding: 32px;
        background: #f8fffe;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .alert {
        padding: 16px 20px;
        margin-bottom: 24px;
        border-radius: 12px;
        border: 1px solid transparent;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-danger {
        color: #721c24;
        background: linear-gradient(135deg, #f8d7da, #f5c6cb);
        border-color: #f1aeb5;
    }

    .alert-success {
        color: #155724;
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        border-color: #b8dacc;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-content {
        background: white;
        padding: 32px;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #4CAF50;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 16px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .container {
            padding: 12px;
        }
        
        .page-header {
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }
        
        .form-grid,
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column-reverse;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
    }

    .validation-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #4CAF50;
    }

    .input-group {
        position: relative;
    }

    .progress-bar {
        height: 4px;
        background: #e0e6ed;
        border-radius: 2px;
        margin-bottom: 24px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #4CAF50, #45a049);
        width: 0%;
        transition: width 0.3s ease;
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 24px;
        padding: 0 16px;
    }

    .step {
        flex: 1;
        text-align: center;
        position: relative;
    }

    .step::after {
        content: '';
        position: absolute;
        top: 50%;
        right: -50%;
        width: 100%;
        height: 2px;
        background: #e0e6ed;
        z-index: -1;
    }

    .step:last-child::after {
        display: none;
    }

    .step.active {
        color: #4CAF50;
        font-weight: 600;
    }

    .step.active::after {
        background: #4CAF50;
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="{{ route('communes.index') }}"><i class="fas fa-home"></i> Communes</a>
        <span class="breadcrumb-separator">›</span>
        <span>Nouvelle commune</span>
    </nav>

    <!-- En-tête de page -->
    <div class="page-header">
        <h1>
            <i class="fas fa-plus-circle"></i>
            Nouvelle Commune
        </h1>
        <a href="{{ route('communes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Retour à la liste
        </a>
    </div>

    <!-- Messages d'erreur globaux -->
    @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <h5>Erreurs de validation détectées</h5>
                <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Messages de succès -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Formulaire principal -->
    <div class="form-container">
        <div class="form-header">
            <h2>Créer une nouvelle commune</h2>
            <p>Remplissez les informations ci-dessous pour ajouter une nouvelle commune au système</p>
        </div>

        <!-- Indicateur de progression -->
        <div class="progress-bar">
            <div class="progress-fill" id="progress-fill"></div>
        </div>

        <form method="POST" action="{{ route('communes.store') }}" id="commune-form" novalidate>
            @csrf
            
            <!-- Section 1: Informations de base -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Informations de base
                </h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nom" class="required">Nom de la commune</label>
                        <div class="input-group">
                            <input 
                                type="text" 
                                id="nom" 
                                name="nom" 
                                class="form-control @error('nom') is-invalid @enderror" 
                                value="{{ old('nom') }}"
                                placeholder="Entrez le nom complet de la commune"
                                required
                                data-validate="nom"
                            >
                            <div class="validation-icon" id="nom-icon" style="display: none;">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="help-text">Le nom officiel de la commune tel qu'enregistré</div>
                    </div>

                    <div class="form-group">
                        <label for="code" class="required">Code commune</label>
                        <div class="input-group">
                            <input 
                                type="text" 
                                id="code" 
                                name="code" 
                                class="form-control @error('code') is-invalid @enderror" 
                                value="{{ old('code') }}"
                                placeholder="Ex: CM001, YDE001"
                                required
                                data-validate="code"
                                maxlength="10"
                            >
                            <div class="validation-icon" id="code-icon" style="display: none;">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="help-text">Code unique d'identification (généré automatiquement ou personnalisé)</div>
                    </div>

                    <div class="form-group">
                        <label for="departement_id" class="required">Département</label>
                        <select 
                            id="departement_id" 
                            name="departement_id" 
                            class="form-control @error('departement_id') is-invalid @enderror"
                            required
                            data-validate="departement"
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
                        <div class="help-text">Sélectionnez le département de rattachement</div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Informations de contact -->
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
                            data-validate="telephone"
                        >
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="help-text">Numéro de téléphone principal de la mairie (optionnel)</div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            value="{{ old('email') }}"
                            placeholder="commune@example.com"
                            data-validate="email"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="help-text">Adresse email officielle de la commune (optionnel)</div>
                    </div>

                    <div class="form-group">
                        <label for="adresse">Adresse complète</label>
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
                        <div class="help-text">Adresse physique de la mairie ou du siège administratif (optionnel)</div>
                    </div>

                    <div class="form-group">
                        <label for="coordonnees_gps">Coordonnées GPS</label>
                        <input 
                            type="text" 
                            id="coordonnees_gps" 
                            name="coordonnees_gps" 
                            class="form-control @error('coordonnees_gps') is-invalid @enderror" 
                            value="{{ old('coordonnees_gps') }}"
                            placeholder="Ex: 3.8480, 11.5021"
                        >
                        @error('coordonnees_gps')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="help-text">Coordonnées GPS au format latitude, longitude (optionnel)</div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Informations démographiques -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-chart-bar"></i>
                    Informations démographiques et géographiques
                </h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="population">Population</label>
                        <input 
                            type="number" 
                            id="population" 
                            name="population" 
                            class="form-control @error('population') is-invalid @enderror" 
                            value="{{ old('population') }}"
                            placeholder="Ex: 50000"
                            min="0"
                            max="10000000"
                            data-validate="population"
                        >
                        @error('population')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="help-text">Nombre d'habitants selon le dernier recensement (optionnel)</div>
                    </div>

                    <div class="form-group">
                        <label for="superficie">Superficie (km²)</label>
                        <input 
                            type="number" 
                            id="superficie" 
                            name="superficie" 
                            class="form-control @error('superficie') is-invalid @enderror" 
                            value="{{ old('superficie') }}"
                            placeholder="Ex: 125.5"
                            min="0"
                            step="0.1"
                            max="100000"
                            data-validate="superficie"
                        >
                        @error('superficie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="help-text">Superficie totale en kilomètres carrés (optionnel)</div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Responsables -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-users"></i>
                    Attribution des responsables
                </h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="receveur_ids">Receveurs municipaux</label>
                        <div class="form-check-group">
                            @forelse($receveurs as $receveur)
                                <div class="form-check" onclick="toggleCheck(this)">
                                    <input 
                                        type="checkbox" 
                                        id="receveur_{{ $receveur->id }}" 
                                        name="receveur_ids[]" 
                                        value="{{ $receveur->id }}"
                                        class="form-check-input"
                                        {{ in_array($receveur->id, old('receveur_ids', [])) ? 'checked' : '' }}
                                    >
                                    <label for="receveur_{{ $receveur->id }}" class="form-check-label">
                                        <strong>{{ $receveur->nom }}</strong>
                                        @if(isset($receveur->specialite))
                                            <br><small style="color: #666;">{{ $receveur->specialite }}</small>
                                        @endif
                                    </label>
                                </div>
                            @empty
                                <div class="help-text">
                                    <i class="fas fa-info-circle"></i>
                                    Aucun receveur disponible. <a href="{{ route('receveurs.create') }}">Créer un nouveau receveur</a>
                                </div>
                            @endforelse
                        </div>
                        @error('receveur_ids')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="help-text">Sélectionnez les receveurs qui seront affectés à cette commune (optionnel)</div>
                    </div>

                    <div class="form-group">
                        <label for="ordonnateur_ids">Ordonnateurs</label>
                        <div class="form-check-group">
                            @forelse($ordonnateurs as $ordonnateur)
                                <div class="form-check" onclick="toggleCheck(this)">
                                    <input 
                                        type="checkbox" 
                                        id="ordonnateur_{{ $ordonnateur->id }}" 
                                        name="ordonnateur_ids[]" 
                                        value="{{ $ordonnateur->id }}"
                                        class="form-check-input"
                                        {{ in_array($ordonnateur->id, old('ordonnateur_ids', [])) ? 'checked' : '' }}
                                    >
                                    <label for="ordonnateur_{{ $ordonnateur->id }}" class="form-check-label">
                                        <strong>{{ $ordonnateur->nom }}</strong>
                                        @if(isset($ordonnateur->fonction))
                                            <br><small style="color: #666;">{{ $ordonnateur->fonction }}</small>
                                        @endif
                                    </label>
                                </div>
                            @empty
                                <div class="help-text">
                                    <i class="fas fa-info-circle"></i>
                                    Aucun ordonnateur disponible. <a href="{{ route('ordonnateurs.create') }}">Créer un nouvel ordonnateur</a>
                                </div>
                            @endforelse
                        </div>
                        @error('ordonnateur_ids')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="help-text">Sélectionnez les ordonnateurs qui seront affectés à cette commune (optionnel)</div>
                    </div>
                </div>
            </div>

            <!-- Actions du formulaire -->
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
        </form>
    </div>
</div>

<!-- Overlay de chargement -->
<div class="loading-overlay" id="loading-overlay">
    <div class="loading-content">
        <div class="spinner"></div>
        <h3>Création en cours...</h3>
        <p>Veuillez patienter pendant l'enregistrement de la commune</p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('commune-form');
    const submitBtn = document.getElementById('submit-btn');
    const loadingOverlay = document.getElementById('loading-overlay');
    const progressFill = document.getElementById('progress-fill');
    
    // Variables pour la validation
    let validationTimeout;
    const validatedFields = new Set();
    
    // Auto-génération du code basé sur le nom
    const nomInput = document.getElementById('nom');
    const codeInput = document.getElementById('code');
    
    nomInput.addEventListener('input', function() {
        if (!codeInput.value || codeInput.dataset.autoGenerated === 'true') {
            const nom = this.value.trim();
            if (nom) {
                const code = nom.toUpperCase()
                    .replace(/[^A-Z\s]/g, '')
                    .replace(/\s+/g, '')
                    .substring(0, 6) + Math.floor(Math.random() * 100).toString().padStart(2, '0');
                
                codeInput.value = code;
                codeInput.dataset.autoGenerated = 'true';
            }
        }
    });
    
    // Désactiver l'auto-génération si l'utilisateur modifie manuellement le code
    codeInput.addEventListener('input', function() {
        this.dataset.autoGenerated = 'false';
    });
    
    // Validation en temps réel
    const validateField = async (field, value) => {
        const input = document.getElementById(field);
        const icon = document.getElementById(field + '-icon');
        
        // Supprimer les classes de validation existantes
        input.classList.remove('is-valid', 'is-invalid');
        if (icon) icon.style.display = 'none';
        
        if (!value) return;
        
        let isValid = true;
        let errorMessage = '';
        
        switch (field) {
            case 'nom':
                isValid = value.length >= 2;
                errorMessage = 'Le nom doit contenir au moins 2 caractères';
                break;
            case 'code':
                isValid = value.length >= 3 && /^[A-Z0-9]+$/.test(value);
                errorMessage = 'Le code doit contenir au moins 3 caractères alphanumériques';
                break;
            case 'email':
                isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                errorMessage = 'Format d\'email invalide';
                break;
            case 'telephone':
                isValid = /^[0-9+\-\s()]+$/.test(value);
                errorMessage = 'Format de téléphone invalide';
                break;
            case 'population':
                const pop = parseInt(value);
                isValid = !isNaN(pop) && pop >= 0 && pop <= 10000000;
                errorMessage = 'La population doit être un nombre entre 0 et 10,000,000';
                break;
            case 'superficie':
                const surf = parseFloat(value);
                isValid = !isNaN(surf) && surf >= 0 && surf <= 100000;
                errorMessage = 'La superficie doit être un nombre entre 0 et 100,000 km²';
                break;
        }
        
        if (isValid) {
            input.classList.add('is-valid');
            if (icon) {
                icon.style.display = 'block';
                icon.innerHTML = '<i class="fas fa-check"></i>';
            }
            validatedFields.add(field);
        } else {
            input.classList.add('is-invalid');
            if (icon) {
                icon.style.display = 'block';
                icon.innerHTML = '<i class="fas fa-times"></i>';
                icon.style.color = '#e74c3c';
            }
            validatedFields.delete(field);
            
            // Afficher le message d'erreur
            let feedback = input.parentNode.querySelector('.invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                input.parentNode.appendChild(feedback);
            }
            feedback.textContent = errorMessage;
        }
        
        updateProgress();
    };
    
    // Mise à jour de la barre de progression
    const updateProgress = () => {
        const totalFields = ['nom', 'code', 'departement_id'];
        const progress = (validatedFields.size / totalFields.length) * 100;
        progressFill.style.width = progress + '%';
    };
    
    // Écouteurs pour la validation en temps réel
    document.querySelectorAll('[data-validate]').forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(validationTimeout);
            validationTimeout = setTimeout(() => {
                validateField(this.dataset.validate, this.value);
            }, 500);
        });
        
        input.addEventListener('blur', function() {
            validateField(this.dataset.validate, this.value);
        });
    });
    
    // Validation du département
    document.getElementById('departement_id').addEventListener('change', function() {
        if (this.value) {
            this.classList.add('is-valid');
            validatedFields.add('departement');
        } else {
            this.classList.remove('is-valid');
            validatedFields.delete('departement');
        }
        updateProgress();
    });
    
    // Gestion des cases à cocher
    window.toggleCheck = function(element) {
        const checkbox = element.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
        
        if (checkbox.checked) {
            element.classList.add('selected');
        } else {
            element.classList.remove('selected');
        }
    };
    
    // Gestion de la soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validation finale
        let hasErrors = false;
        const requiredFields = ['nom', 'code', 'departement_id'];
        
        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                hasErrors = true;
            }
        });
        
        if (hasErrors) {
            // Scroll vers le premier champ avec erreur
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            // Afficher une alerte
            showAlert('Veuillez remplir tous les champs obligatoires (nom, code et département).', 'error');
            return;
        }
        
        // Afficher le loader
        loadingOverlay.style.display = 'flex';
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner" style="width: 20px; height: 20px; margin-right: 8px;"></span> Enregistrement...';
        
        // Soumettre le formulaire
        setTimeout(() => {
            form.submit();
        }, 1000);
    });
    
    // Fonction pour afficher des alertes
    const showAlert = (message, type = 'info') => {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'}`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'}"></i>
            <div>${message}</div>
        `;
        
        // Insérer l'alerte au début du formulaire
        const formContainer = document.querySelector('.form-container');
        formContainer.insertBefore(alertDiv, formContainer.firstChild);
        
        // Supprimer l'alerte après 5 secondes
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
        
        // Scroll vers l'alerte
        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };
    
    // Gestion de la géolocalisation
    const gpsInput = document.getElementById('coordonnees_gps');
    if (gpsInput && navigator.geolocation) {
        const gpsButton = document.createElement('button');
        gpsButton.type = 'button';
        gpsButton.className = 'btn btn-secondary';
        gpsButton.style.marginTop = '8px';
        gpsButton.innerHTML = '<i class="fas fa-map-marker-alt"></i> Utiliser ma position';
        
        gpsButton.addEventListener('click', function() {
            this.innerHTML = '<span class="spinner" style="width: 16px; height: 16px;"></span> Localisation...';
            this.disabled = true;
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude.toFixed(6);
                    const lon = position.coords.longitude.toFixed(6);
                    gpsInput.value = `${lat}, ${lon}`;
                    
                    this.innerHTML = '<i class="fas fa-check"></i> Position obtenue';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-map-marker-alt"></i> Utiliser ma position';
                        this.disabled = false;
                    }, 2000);
                },
                (error) => {
                    this.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Erreur';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-map-marker-alt"></i> Utiliser ma position';
                        this.disabled = false;
                    }, 2000);
                }
            );
        });
        
        gpsInput.parentNode.appendChild(gpsButton);
    }
    
    // Auto-complétion pour les codes de communes existants
    const existingCodes = @json($suggestions['codes_existants'] ?? []);
    const codeValidation = () => {
        const code = codeInput.value.toUpperCase();
        if (existingCodes.includes(code)) {
            codeInput.classList.add('is-invalid');
            let feedback = codeInput.parentNode.querySelector('.invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                codeInput.parentNode.appendChild(feedback);
            }
            feedback.textContent = 'Ce code est déjà utilisé par une autre commune';
            validatedFields.delete('code');
        }
    };
    
    codeInput.addEventListener('blur', codeValidation);
    
    // Formatage automatique du téléphone
    const phoneInput = document.getElementById('telephone');
    phoneInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.startsWith('237')) {
            value = '+237 ' + value.substring(3);
        } else if (value.startsWith('6') || value.startsWith('2')) {
            value = '+237 ' + value;
        }
        
        // Formatage avec espaces
        if (value.startsWith('+237 ')) {
            const number = value.substring(5);
            if (number.length > 0) {
                const formatted = number.match(/.{1,3}/g)?.join(' ') || number;
                value = '+237 ' + formatted;
            }
        }
        
        this.value = value;
    });
    
    // Calcul automatique de la densité de population
    const populationInput = document.getElementById('population');
    const superficieInput = document.getElementById('superficie');
    const densiteDisplay = document.createElement('div');
    densiteDisplay.className = 'help-text';
    densiteDisplay.style.fontWeight = '600';
    densiteDisplay.style.color = '#4CAF50';
    
    const updateDensity = () => {
        const pop = parseInt(populationInput.value) || 0;
        const surf = parseFloat(superficieInput.value) || 0;
        
        if (pop > 0 && surf > 0) {
            const density = (pop / surf).toFixed(2);
            densiteDisplay.textContent = `Densité: ${density} habitants/km²`;
            if (!superficieInput.parentNode.contains(densiteDisplay)) {
                superficieInput.parentNode.appendChild(densiteDisplay);
            }
        } else {
            if (superficieInput.parentNode.contains(densiteDisplay)) {
                densiteDisplay.remove();
            }
        }
    };
    
    populationInput.addEventListener('input', updateDensity);
    superficieInput.addEventListener('input', updateDensity);
    
    // Animation d'entrée des éléments
    const animateElements = () => {
        const sections = document.querySelectorAll('.form-section');
        sections.forEach((section, index) => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, index * 200);
        });
    };
    
    animateElements();
    
    // Sauvegarde automatique dans le localStorage (brouillon)
    const saveDraft = () => {
        const formData = new FormData(form);
        const draftData = {};
        
        for (let [key, value] of formData.entries()) {
            if (draftData[key]) {
                if (Array.isArray(draftData[key])) {
                    draftData[key].push(value);
                } else {
                    draftData[key] = [draftData[key], value];
                }
            } else {
                draftData[key] = value;
            }
        }
        
        try {
            localStorage.setItem('commune_draft', JSON.stringify(draftData));
        } catch (e) {
            console.log('Impossible de sauvegarder le brouillon');
        }
    };
    
    // Restaurer le brouillon
    const restoreDraft = () => {
        try {
            const draft = localStorage.getItem('commune_draft');
            if (draft) {
                const draftData = JSON.parse(draft);
                
                Object.keys(draftData).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            const values = Array.isArray(draftData[key]) ? draftData[key] : [draftData[key]];
                            document.querySelectorAll(`[name="${key}"]`).forEach(checkbox => {
                                checkbox.checked = values.includes(checkbox.value);
                                if (checkbox.checked) {
                                    checkbox.closest('.form-check').classList.add('selected');
                                }
                            });
                        } else {
                            input.value = draftData[key];
                        }
                    }
                });
                
                showAlert('Brouillon restauré automatiquement', 'info');
                
                // Supprimer le brouillon après restauration
                setTimeout(() => {
                    localStorage.removeItem('commune_draft');
                }, 1000);
            }
        } catch (e) {
            console.log('Impossible de restaurer le brouillon');
        }
    };
    
    // Sauvegarder toutes les 30 secondes
    setInterval(saveDraft, 30000);
    
    // Sauvegarder à chaque modification
    form.addEventListener('change', saveDraft);
    form.addEventListener('input', saveDraft);
    
    // Restaurer le brouillon au chargement (si pas de données old)
    const hasOldData = {{ old() ? 'true' : 'false' }};
    if (!hasOldData) {
        restoreDraft();
    }
    
    // Confirmation avant de quitter si le formulaire est modifié
    let formModified = false;
    form.addEventListener('change', () => formModified = true);
    form.addEventListener('input', () => formModified = true);
    
    window.addEventListener('beforeunload', (e) => {
        if (formModified) {
            e.preventDefault();
            e.returnValue = 'Vous avez des modifications non sauvegardées. Voulez-vous vraiment quitter?';
        }
    });
    
    // Supprimer l'avertissement lors de la soumission
    form.addEventListener('submit', () => {
        formModified = false;
        localStorage.removeItem('commune_draft');
    });
    
    console.log('Formulaire de création de commune initialisé avec succès');
});
</script>
@endpush
@endsection
