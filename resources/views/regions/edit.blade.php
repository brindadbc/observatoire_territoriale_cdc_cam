@extends('layouts.app')

@section('title', 'Modifier ' . $region->nom . ' - Observatoire des Collectivités Territoriales')
@section('page-title', 'Modifier la région ' . $region->nom)

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
        background: linear-gradient(90deg, #f39c12, #f1c40f);
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

    .region-badge {
        background: linear-gradient(135deg, #f39c12, #f1c40f);
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 10px;
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
        background: linear-gradient(135deg, #f39c12, #f1c40f);
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
        border-color: #f39c12;
        background: white;
        box-shadow: 0 0 0 3px rgba(243,156,18,0.1);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        background-color: rgba(220,53,69,0.05);
    }

    .form-control.is-valid {
        border-color: #28a745;
        background-color: rgba(40,167,69,0.05);
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 5px;
        font-size: 12px;
        color: #dc3545;
    }

    .valid-feedback {
        display: block;
        width: 100%;
        margin-top: 5px;
        font-size: 12px;
        color: #28a745;
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

    .btn-warning-custom {
        background: linear-gradient(135deg, #f39c12, #f1c40f);
        color: white;
    }

    .btn-warning-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(243,156,18,0.3);
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

    .btn-danger-custom {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }

    .btn-danger-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220,53,69,0.3);
        color: white;
    }

    .help-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }

    .changes-indicator {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        display: none;
    }

    .changes-indicator.show {
        display: block;
    }

    .changes-title {
        font-size: 14px;
        font-weight: 600;
        color: #856404;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .changes-list {
        font-size: 12px;
        color: #856404;
        margin: 0;
        padding-left: 20px;
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
            <li class="breadcrumb-item"><a href="{{ route('regions.show', $region) }}">{{ $region->nom }}</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol>
    </nav>

    <div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="region-badge">{{ $region->code }}</div>
                <h1 class="form-title">Modifier la région {{ $region->nom }}</h1>
                <p class="form-subtitle">Mettez à jour les informations de la région</p>
            </div>

            <!-- Indicateur de modifications -->
            <div class="changes-indicator" id="changesIndicator">
                <div class="changes-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Modifications détectées
                </div>
                <ul class="changes-list" id="changesList">
                    <!-- Les modifications seront listées ici -->
                </ul>
            </div>

            <form action="{{ route('regions.update', $region) }}" method="POST" id="regionForm">
                @csrf
                @method('PUT')
                
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
                                   value="{{ old('nom', $region->nom) }}"
                                   data-original="{{ $region->nom }}"
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
                                   value="{{ old('code', $region->code) }}"
                                   data-original="{{ $region->code }}"
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
                               value="{{ old('chef_lieu', $region->chef_lieu) }}"
                               data-original="{{ $region->chef_lieu }}"
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
                                       value="{{ old('superficie', $region->superficie) }}"
                                       data-original="{{ $region->superficie }}"
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
                                       value="{{ old('population', $region->population) }}"
                                       data-original="{{ $region->population }}"
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

                <!-- Informations sur les départements et communes -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        Informations actuelles
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Départements</label>
                            <div class="form-control" style="background: #f8f9fa; color: #6c757d;">
                                {{ $region->nombre_departements }} département(s)
                            </div>
                            <div class="help-text">Nombre de départements dans cette région</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Communes</label>
                            <div class="form-control" style="background: #f8f9fa; color: #6c757d;">
                                {{ $region->nombre_communes }} commune(s)
                            </div>
                            <div class="help-text">Nombre total de communes dans cette région</div>
                        </div>
                    </div>

                    @if($region->superficie && $region->population)
                    <div class="form-group">
                        <label class="form-label">Densité de population</label>
                        <div class="form-control" style="background: #f8f9fa; color: #6c757d;">
                            {{ number_format($region->population / $region->superficie, 2) }} hab/km²
                        </div>
                        <div class="help-text">Calculée automatiquement</div>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="{{ route('regions.show', $region) }}" class="btn-custom btn-secondary-custom">
                        <i class="fas fa-arrow-left"></i>
                        Annuler
                    </a>
                    <button type="button" class="btn-custom btn-danger-custom" onclick="confirmDelete()">
                        <i class="fas fa-trash"></i>
                        Supprimer
                    </button>
                    <button type="submit" class="btn-custom btn-warning-custom" id="submitBtn">
                        <span class="loading-spinner"></span>
                        <i class="fas fa-save"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
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
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 48px;"></i>
                </div>
                <p class="text-center">Êtes-vous sûr de vouloir supprimer définitivement la région <strong>{{ $region->nom }}</strong> ?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-warning me-2"></i>
                    <strong>Attention :</strong> Cette action est irréversible et ne peut être effectuée que si la région ne contient aucun département.
                </div>
                @if($region->nombre_departements > 0)
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    Cette région contient {{ $region->nombre_departements }} département(s) et ne peut pas être supprimée.
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                @if($region->nombre_departements == 0)
                <form action="{{ route('regions.destroy', $region) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer définitivement
                    </button>
                </form>
                @else
                <button type="button" class="btn btn-danger" disabled>
                    <i class="fas fa-lock"></i> Suppression impossible
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('regionForm');
        const submitBtn = document.getElementById('submitBtn');
        const changesIndicator = document.getElementById('changesIndicator');
        const changesList = document.getElementById('changesList');
        
        // Champs du formulaire
        const fields = [
            { element: document.getElementById('nom'), label: 'Nom' },
            { element: document.getElementById('code'), label: 'Code' },
            { element: document.getElementById('chef_lieu'), label: 'Chef-lieu' },
            { element: document.getElementById('superficie'), label: 'Superficie' },
            { element: document.getElementById('population'), label: 'Population' }
        ];

        let hasChanges = false;

        // Surveillance des modifications
        function checkChanges() {
            const changes = [];
            hasChanges = false;

            fields.forEach(field => {
                const currentValue = field.element.value.trim();
                const originalValue = field.element.getAttribute('data-original') || '';
                
                if (currentValue !== originalValue) {
                    hasChanges = true;
                    changes.push({
                        field: field.label,
                        from: originalValue || '(vide)',
                        to: currentValue || '(vide)'
                    });
                }
            });

            // Afficher/masquer l'indicateur de modifications
            if (hasChanges) {
                changesIndicator.classList.add('show');
                changesList.innerHTML = changes.map(change => 
                    `<li><strong>${change.field}:</strong> "${change.from}" → "${change.to}"</li>`
                ).join('');
            } else {
                changesIndicator.classList.remove('show');
            }

            // Activer/désactiver le bouton de soumission
            submitBtn.disabled = !hasChanges;
            if (hasChanges) {
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer les modifications';
            } else {
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Aucune modification';
            }
        }

        // Ajouter les écouteurs d'événements
        fields.forEach(field => {
            field.element.addEventListener('input', checkChanges);
            field.element.addEventListener('blur', function() {
                validateField(this);
            });
        });

        // Validation des champs
        function validateField(field) {
            const value = field.value.trim();
            let isValid = true;
            let message = '';

            // Validation selon le type de champ
            switch (field.id) {
                case 'nom':
                    if (!value) {
                        isValid = false;
                        message = 'Le nom de la région est obligatoire';
                    } else if (value.length < 2) {
                        isValid = false;
                        message = 'Le nom doit contenir au moins 2 caractères';
                    }
                    break;

                case 'code':
                    if (!value) {
                        isValid = false;
                        message = 'Le code de la région est obligatoire';
                    } else if (value.length > 10) {
                        isValid = false;
                        message = 'Le code ne peut pas dépasser 10 caractères';
                    } else if (!/^[A-Z0-9-]+$/.test(value)) {
                        isValid = false;
                        message = 'Le code ne peut contenir que des lettres majuscules, chiffres et tirets';
                    }
                    break;

                case 'chef_lieu':
                    if (!value) {
                        isValid = false;
                        message = 'Le chef-lieu est obligatoire';
                    } else if (value.length < 2) {
                        isValid = false;
                        message = 'Le chef-lieu doit contenir au moins 2 caractères';
                    }
                    break;

                case 'superficie':
                    if (value && parseFloat(value) < 0) {
                        isValid = false;
                        message = 'La superficie ne peut pas être négative';
                    }
                    break;

                case 'population':
                    if (value && parseInt(value) < 0) {
                        isValid = false;
                        message = 'La population ne peut pas être négative';
                    }
                    break;
            }

            // Appliquer les styles de validation
            if (value) {
                if (isValid) {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                } else {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                }

                // Afficher le message d'erreur ou de succès
                let feedback = field.parentElement.querySelector('.invalid-feedback');
                if (!feedback && !isValid) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    field.parentElement.appendChild(feedback);
                }
                if (feedback) {
                    feedback.textContent = message;
                    feedback.style.display = isValid ? 'none' : 'block';
                }
            } else {
                field.classList.remove('is-valid', 'is-invalid');
            }

            return isValid;
        }

        // Vérification initiale
        checkChanges();

        // Formatage automatique du code en majuscules
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Formatage des nombres
        document.getElementById('superficie').addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
        });

        document.getElementById('population').addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
        });

        // Soumission du formulaire
        form.addEventListener('submit', function(e) {
            if (!hasChanges) {
                e.preventDefault();
                return;
            }

            // Validation finale
            let isFormValid = true;
            fields.forEach(field => {
                if (!validateField(field.element)) {
                    isFormValid = false;
                }
            });

            if (!isFormValid) {
                e.preventDefault();
                
                // Scroll vers la première erreur
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
                return;
            }

            // Ajouter la classe de chargement
            form.classList.add('loading');
            submitBtn.disabled = true;
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

    // Fonction de confirmation de suppression
    function confirmDelete() {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // Avertissement avant de quitter la page si des modifications non sauvegardées existent
    window.addEventListener('beforeunload', function(e) {
        const changesIndicator = document.getElementById('changesIndicator');
        if (changesIndicator.classList.contains('show')) {
            e.preventDefault();
            e.returnValue = 'Vous avez des modifications non sauvegardées. Êtes-vous sûr de vouloir quitter cette page ?';
        }
    });
</script>
@endpush