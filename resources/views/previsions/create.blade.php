
@extends('layouts.app')

@section('title', 'Nouvelle Prévision')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- En-tête -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('previsions.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="h3 mb-0">Nouvelle Prévision</h2>
                    <p class="text-muted mb-0">Créer une nouvelle prévision budgétaire</p>
                </div>
            </div>

            <!-- Messages d'erreur -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Erreurs détectées :</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Formulaire -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Informations de la prévision
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('previsions.store') }}" novalidate>
                        @csrf
                        
                        <div class="row">
                            <!-- Sélection de la commune -->
                            <div class="col-md-12 mb-4">
                                <label for="commune_id" class="form-label required">Commune</label>
                                <select name="commune_id" id="commune_id" 
                                        class="form-select @error('commune_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez une commune</option>
                                    @foreach($communes->groupBy('departement.region.nom') as $regionNom => $communesRegion)
                                        <optgroup label="{{ $regionNom }}">
                                            @foreach($communesRegion->groupBy('departement.nom') as $departementNom => $communesDept)
                                                <optgroup label="&nbsp;&nbsp;{{ $departementNom }}">
                                                    @foreach($communesDept as $commune)
                                                        <option value="{{ $commune->id }}" 
                                                                {{ old('commune_id') == $commune->id ? 'selected' : '' }}
                                                                data-departement="{{ $commune->departement->nom }}"
                                                                data-region="{{ $commune->departement->region->nom }}">
                                                            &nbsp;&nbsp;&nbsp;&nbsp;{{ $commune->nom }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('commune_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Choisissez la commune pour laquelle vous souhaitez créer une prévision</div>
                            </div>
                        </div>

                        <!-- Informations sélectionnées -->
                        <div id="commune-info" class="alert alert-info d-none mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Commune :</strong>
                                    <span id="selected-commune"></span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Département :</strong>
                                    <span id="selected-departement"></span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Région :</strong>
                                    <span id="selected-region"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Année d'exercice -->
                            <div class="col-md-6 mb-4">
                                <label for="annee_exercice" class="form-label required">Année d'exercice</label>
                                <select name="annee_exercice" id="annee_exercice" 
                                        class="form-select @error('annee_exercice') is-invalid @enderror" required>
                                    @for($year = 2000; $year <= (date('Y') + 10); $year++)
                                        <option value="{{ $year }}" 
                                                {{ (old('annee_exercice', $anneeDefaut) == $year) ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                @error('annee_exercice')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Année budgétaire pour cette prévision</div>
                            </div>

                            <!-- Montant -->
                            <div class="col-md-6 mb-4">
                                <label for="montant" class="form-label required">Montant de la prévision (FCFA)</label>
                                <div class="input-group">
                                    <input type="number" name="montant" id="montant" 
                                           class="form-control @error('montant') is-invalid @enderror"
                                           value="{{ old('montant') }}" 
                                           min="0" step="0.01" required
                                           placeholder="0.00">
                                    <span class="input-group-text">FCFA</span>
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Montant prévu en francs CFA</div>
                                <div id="montant-formatted" class="small text-muted mt-1"></div>
                            </div>
                        </div>

                        <!-- Vérification d'unicité -->
                        <div id="uniqueness-check" class="d-none">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Attention :</strong> Une prévision existe peut-être déjà pour cette commune et cette année.
                                Veuillez vérifier avant de continuer.
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                            <a href="{{ route('previsions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save me-2"></i>Créer la prévision
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Aide -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations importantes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Règles de validation</h6>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-check text-success me-2"></i>Une seule prévision par commune et par année</li>
                                <li><i class="fas fa-check text-success me-2"></i>Montant minimum : 0 FCFA</li>
                                <li><i class="fas fa-check text-success me-2"></i>Années disponibles : 2020 à {{ date('Y') + 5 }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Conseils</h6>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Vérifiez les données avant validation</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Les montants sont en francs CFA</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Utilisez des montants réalistes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.required::after {
    content: ' *';
    color: #dc3545;
}

#commune_id optgroup {
    font-weight: bold;
    color: #495057;
}

#commune_id optgroup option {
    font-weight: normal;
    padding-left: 20px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const communeSelect = document.getElementById('commune_id');
    const communeInfo = document.getElementById('commune-info');
    const montantInput = document.getElementById('montant');
    const montantFormatted = document.getElementById('montant-formatted');
    const anneeSelect = document.getElementById('annee_exercice');
    const uniquenessCheck = document.getElementById('uniqueness-check');

    // Recherche dans le select des communes
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control mb-2';
    searchInput.placeholder = 'Rechercher une commune...';
    
    // Insérer le champ de recherche avant le select
    communeSelect.parentNode.insertBefore(searchInput, communeSelect);
    
    const allOptions = Array.from(communeSelect.options);
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        // Effacer les options actuelles (sauf la première)
        communeSelect.innerHTML = '<option value="">Sélectionnez une commune</option>';
        
        if (searchTerm === '') {
            // Restaurer toutes les options
            allOptions.slice(1).forEach(option => {
                communeSelect.appendChild(option);
            });
        } else {
            // Filtrer les options
            allOptions.slice(1).forEach(option => {
                if (option.textContent.toLowerCase().includes(searchTerm)) {
                    communeSelect.appendChild(option);
                }
            });
        }
    });
});
//Gestion de l'affichage des informations de commune
    communeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const communeNom = selectedOption.textContent.trim();
            const departement = selectedOption.dataset.departement;
            const region = selectedOption.dataset.region;
            
            document.getElementById('selected-commune').textContent = communeNom;
            document.getElementById('selected-departement').textContent = departement;
            document.getElementById('selected-region').textContent = region;
            
            communeInfo.classList.remove('d-none');
            checkUniqueness();
        } else {
            communeInfo.classList.add('d-none');
            uniquenessCheck.classList.add('d-none');
        }
    });

    // Formatage du montant
    montantInput.addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (!isNaN(value) && value > 0) {
            montantFormatted.textContent = `Soit : ${new Intl.NumberFormat('fr-FR').format(value)} FCFA`;
        } else {
            montantFormatted.textContent = '';
        }
    });

    // Vérification d'unicité (simulation)
    function checkUniqueness() {
        if (communeSelect.value && anneeSelect.value) {
            // Ici vous pourriez faire un appel AJAX pour vérifier l'unicité
            // Pour la démo, on simule une vérification
            setTimeout(() => {
                // Simulation : 20% de chance qu'une prévision existe déjà
                if (Math.random() < 0.2) {
                    uniquenessCheck.classList.remove('d-none');
                } else {
                    uniquenessCheck.classList.add('d-none');
                }
            }, 500);
        }
    }

    anneeSelect.addEventListener('change', checkUniqueness);

    // Validation du formulaire
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Création en cours...';
    });
</script>
@endpush
@endsection 


    





     {{-- @extends('layouts.app')

@section('title', 'Nouvelle prévision')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Créer une Nouvelle Prévision</h3>
                    <a href="{{ route('previsions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('previsions.store') }}">
                        @csrf

                        <!-- Année d'exercice -->
                        <div class="form-group">
                            <label for="annee_exercice">Année d'exercice <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('annee_exercice') is-invalid @enderror" 
                                   id="annee_exercice" name="annee_exercice" 
                                   value="{{ old('annee_exercice', $anneeDefaut) }}" 
                                   min="2000" max="{{ date('Y') + 10 }}" required>
                            @error('annee_exercice')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text text-muted">
                                L'année d'exercice doit être comprise entre 2000 et {{ date('Y') + 10 }}
                            </small>
                        </div>

                        <!-- Commune -->
                        <div class="form-group">
                            <label for="commune_id">Commune <span class="text-danger">*</span></label>
                            <select class="form-control @error('commune_id') is-invalid @enderror" 
                                    id="commune_id" name="commune_id" required>
                                <option value="">Sélectionnez une commune</option>
                                @foreach($communes->groupBy('departement.region.nom') as $regionNom => $communesRegion)
                                    <optgroup label="{{ $regionNom }}">
                                        @foreach($communesRegion->groupBy('departement.nom') as $departementNom => $communesDepartement)
                                            <optgroup label="&nbsp;&nbsp;{{ $departementNom }}">
                                                @foreach($communesDepartement as $commune)
                                                    <option value="{{ $commune->id }}" 
                                                            {{ old('commune_id') == $commune->id ? 'selected' : '' }}>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;{{ $commune->nom }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('commune_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Montant -->
                        <div class="form-group">
                            <label for="montant">Montant prévu (FCFA) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('montant') is-invalid @enderror" 
                                       id="montant" name="montant" 
                                       value="{{ old('montant') }}" 
                                       min="0" step="0.01" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @error('montant')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Le montant doit être supérieur ou égal à 0
                            </small>
                        </div>

                        <!-- Aperçu des informations -->
                        <div class="card bg-light mt-4" id="apercu" style="display: none;">
                            <div class="card-header">
                                <h5 class="card-title">Aperçu de la prévision</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Commune:</strong> <span id="apercu-commune"></span></p>
                                        <p><strong>Département:</strong> <span id="apercu-departement"></span></p>
                                        <p><strong>Région:</strong> <span id="apercu-region"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Année:</strong> <span id="apercu-annee"></span></p>
                                        <p><strong>Montant:</strong> <span id="apercu-montant"></span></p>
                                        <p><strong>Montant formaté:</strong> <span id="apercu-montant-formate"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vérification de doublons -->
                        <div class="alert alert-warning" id="alerte-doublon" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Attention!</strong> Une prévision existe peut-être déjà pour cette commune et cette année.
                        </div>

                        <!-- Boutons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer la prévision
                            </button>
                            <a href="{{ route('previsions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const anneeInput = document.getElementById('annee_exercice');
        const communeSelect = document.getElementById('commune_id');
        const montantInput = document.getElementById('montant');
        const apercu = document.getElementById('apercu');
        const alerteDoublon = document.getElementById('alerte-doublon');

        // Données des communes (passées depuis le contrôleur)
        const communes = @json($communes->keyBy('id'));

        // Fonction pour mettre à jour l'aperçu
        function updateApercu() {
            const annee = anneeInput.value;
            const communeId = communeSelect.value;
            const montant = montantInput.value;

            if (annee && communeId && montant) {
                const commune = communes[communeId];
                if (commune) {
                    document.getElementById('apercu-commune').textContent = commune.nom;
                    document.getElementById('apercu-departement').textContent = commune.departement.nom;
                    document.getElementById('apercu-region').textContent = commune.departement.region.nom;
                    document.getElementById('apercu-annee').textContent = annee;
                    document.getElementById('apercu-montant').textContent = montant + ' FCFA';
                    document.getElementById('apercu-montant-formate').textContent = 
                        new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
                    
                    apercu.style.display = 'block';
                    
                    // Vérifier les doublons potentiels
                    checkDoublons(communeId, annee);
                } else {
                    apercu.style.display = 'none';
                }
            } else {
                apercu.style.display = 'none';
                alerteDoublon.style.display = 'none';
            }
        }

        // Fonction pour vérifier les doublons
        function checkDoublons(communeId, annee) {
            // Simulation de vérification (dans un vrai projet, faire un appel AJAX)
            // Pour cet exemple, on montre l'alerte de manière aléatoire
            const showAlert = Math.random() > 0.8; // 20% de chance d'afficher l'alerte
            
            if (showAlert) {
                alerteDoublon.style.display = 'block';
            } else {
                alerteDoublon.style.display = 'none';
            }
        }

        // Formatage du montant en temps réel
        montantInput.addEventListener('input', function() {
            updateApercu();
        });

        // Mise à jour de l'aperçu lors du changement des champs
        anneeInput.addEventListener('change', updateApercu);
        communeSelect.addEventListener('change', updateApercu);
        montantInput.addEventListener('input', updateApercu);

        // Amélioration de l'UX du select des communes
        if (window.jQuery) {
            $('#commune_id').select2({
                placeholder: 'Rechercher une commune...',
                allowClear: true,
                width: '100%'
            });
        }

        // Validation côté client
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const errors = [];

            // Validation de l'année
            const annee = parseInt(anneeInput.value);
            if (!annee || annee < 2020 || annee > {{ date('Y') + 5 }}) {
                errors.push('L\'année d\'exercice doit être comprise entre 2020 et {{ date('Y') + 5 }}');
                isValid = false;
            }

            // Validation du montant
            const montant = parseFloat(montantInput.value);
            if (!montant || montant < 0) {
                errors.push('Le montant doit être supérieur ou égal à 0');
                isValid = false;
            }

            // Validation de la commune
            if (!communeSelect.value) {
                errors.push('Vous devez sélectionner une commune');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                alert('Erreurs de validation:\n' + errors.join('\n'));
            }
        });
    });
</script>

<!-- Select2 pour améliorer l'UX -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection --}}