{{-- @extends('layouts.app')

@section('title', 'Nouvelle Dette CNPS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-plus-circle"></i> Nouvelle Dette CNPS
                </h1>
                <a href="{{ route('dettes-cnps.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-form"></i> Formulaire de Création
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('dettes-cnps.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="commune_id" class="font-weight-bold">
                                        Commune <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('commune_id') is-invalid @enderror" 
                                            id="commune_id" name="commune_id" required>
                                        <option value="">Sélectionnez une commune</option>
                                        @foreach($communes->groupBy('departement.region.nom') as $regionNom => $communesRegion)
                                            <optgroup label="{{ $regionNom }}">
                                                @foreach($communesRegion->groupBy('departement.nom') as $departementNom => $communesDept)
                                                    <optgroup label="&nbsp;&nbsp;{{ $departementNom }}">
                                                        @foreach($communesDept as $commune)
                                                            <option value="{{ $commune->id }}" 
                                                                    {{ old('commune_id') == $commune->id ? 'selected' : '' }}>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;{{ $commune->nom }} ({{ $commune->code }})
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
                                    <small class="form-text text-muted">
                                        Choisissez la commune concernée par cette dette CNPS
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="montant" class="font-weight-bold">
                                        Montant de la Dette <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('montant') is-invalid @enderror" 
                                               id="montant" name="montant" 
                                               value="{{ old('montant') }}" 
                                               placeholder="0.00" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">FCFA</span>
                                        </div>
                                        @error('montant')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">
                                        Montant en francs CFA (ne peut pas être négatif)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_evaluation" class="font-weight-bold">
                                        Date d'Évaluation <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('date_evaluation') is-invalid @enderror" 
                                           id="date_evaluation" name="date_evaluation" 
                                           value="{{ old('date_evaluation', date('Y-m-d')) }}" 
                                           required>
                                    @error('date_evaluation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Date à laquelle cette dette a été évaluée
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="font-weight-bold">
                                Description <span class="text-muted">(Optionnel)</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Détails sur cette dette CNPS, contexte, remarques...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Informations complémentaires sur cette dette (maximum 500 caractères)
                            </small>
                        </div>

                        <hr class="my-4">

                        <!-- Aperçu des informations -->
                        <div class="row" id="commune-info" style="display: none;">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Informations sur la commune sélectionnée :</h6>
                                    <div id="commune-details"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Enregistrer la Dette
                            </button>
                            <a href="{{ route('dettes-cnps.index') }}" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Recherche dynamique dans le select des communes
    $('#commune_id').select2({
        placeholder: 'Rechercher une commune...',
        allowClear: true,
        theme: 'bootstrap4'
    });

    // Affichage des informations de la commune sélectionnée
    $('#commune_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const communeText = selectedOption.text().trim();
        
        if (communeText && communeText !== 'Sélectionnez une commune') {
            // Extraire les informations depuis le texte de l'option
            const optgroup = selectedOption.parent('optgroup');
            const departement = optgroup.attr('label');
            const region = optgroup.parent().prev('optgroup').attr('label') || 
                          optgroup.parent().parent().find('optgroup').first().attr('label');
            
            $('#commune-details').html(`
                <div class="row">
                    <div class="col-md-4">
                        <strong>Commune :</strong><br>
                        ${communeText}
                    </div>
                    <div class="col-md-4">
                        <strong>Département :</strong><br>
                        ${departement}
                    </div>
                    <div class="col-md-4">
                        <strong>Région :</strong><br>
                        ${region}
                    </div>
                </div>
            `);
            $('#commune-info').show();
        } else {
            $('#commune-info').hide();
        }
    });

    // Formatage automatique du montant
    $('#montant').on('input', function() {
        const value = $(this).val();
        if (value) {
            // Formatage visuel du montant
            const formatted = new Intl.NumberFormat('fr-FR').format(value);
            $(this).attr('title', `${formatted} FCFA`);
        }
    });

    // Validation en temps réel
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Vérification du montant
        const montant = parseFloat($('#montant').val());
        if (!montant || montant < 0) {
            isValid = false;
            $('#montant').addClass('is-invalid');
        } else {
            $('#montant').removeClass('is-invalid');
        }

        // Vérification de la commune
        if (!$('#commune_id').val()) {
            isValid = false;
            $('#commune_id').addClass('is-invalid');
        } else {
            $('#commune_id').removeClass('is-invalid');
        }

        // Vérification de la date
        if (!$('#date_evaluation').val()) {
            isValid = false;
            $('#date_evaluation').addClass('is-invalid');
        } else {
            $('#date_evaluation').removeClass('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
            toastr.error('Veuillez corriger les erreurs dans le formulaire');
        }
    });
});
</script>
@endpush

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif
@endsection --}}



@extends('layouts.app')

@section('title', 'Nouvelle Dette CNPS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-plus-circle"></i> Nouvelle Dette CNPS
                </h1>
                <a href="{{ route('dettes-cnps.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-form"></i> Formulaire de Création
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('dettes-cnps.store') }}" method="POST" id="detteForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="commune_id" class="font-weight-bold">
                                        Commune <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('commune_id') is-invalid @enderror" 
                                            id="commune_id" name="commune_id" required>
                                        <option value="">Sélectionnez une commune</option>
                                        @foreach($communes->groupBy('departement.region.nom') as $regionNom => $communesRegion)
                                            <optgroup label="{{ $regionNom }}">
                                                @foreach($communesRegion->groupBy('departement.nom') as $departementNom => $communesDept)
                                                    <optgroup label="&nbsp;&nbsp;{{ $departementNom }}">
                                                        @foreach($communesDept as $commune)
                                                            <option value="{{ $commune->id }}" 
                                                                    {{ old('commune_id') == $commune->id ? 'selected' : '' }}>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;{{ $commune->nom }} ({{ $commune->code }})
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
                                    <small class="form-text text-muted">
                                        Choisissez la commune concernée par cette dette CNPS
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="montant" class="font-weight-bold">
                                        Montant de la Dette <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control @error('montant') is-invalid @enderror" 
                                               id="montant" 
                                               name="montant" 
                                               value="{{ old('montant') }}" 
                                               placeholder="Ex: 1 500 000 000"
                                               autocomplete="off"
                                               required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">FCFA</span>
                                        </div>
                                        @error('montant')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">
                                        Montant en francs CFA. Vous pouvez saisir jusqu'au milliard. 
                                        <br><strong>Exemples:</strong> 1 500 000 000 ou 1500000000
                                    </small>
                                    <div id="montant-preview" class="mt-2" style="display: none;">
                                        <div class="alert alert-info py-2">
                                            <strong>Montant saisi:</strong> <span id="montant-formate"></span> FCFA
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_evaluation" class="font-weight-bold">
                                        Date d'Évaluation <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('date_evaluation') is-invalid @enderror" 
                                           id="date_evaluation" name="date_evaluation" 
                                           value="{{ old('date_evaluation', date('Y-m-d')) }}" 
                                           required>
                                    @error('date_evaluation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Date à laquelle cette dette a été évaluée
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="font-weight-bold">
                                Description <span class="text-muted">(Optionnel)</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      maxlength="1000"
                                      placeholder="Détails sur cette dette CNPS, contexte, remarques...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Informations complémentaires sur cette dette (maximum 1000 caractères)
                            </small>
                            <div class="text-right">
                                <small class="text-muted" id="description-counter">0/1000 caractères</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Aperçu des informations -->
                        <div class="row" id="commune-info" style="display: none;">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Informations sur la commune sélectionnée :</h6>
                                    <div id="commune-details"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Exemples de montants pour aide -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body py-3">
                                        <h6 class="card-title mb-2">
                                            <i class="fas fa-lightbulb text-warning"></i> Exemples de saisie de montants
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-unstyled mb-0 small">
                                                    <li><strong>1 million:</strong> 1 000 000 ou 1000000</li>
                                                    <li><strong>10 millions:</strong> 10 000 000 ou 10000000</li>
                                                    <li><strong>100 millions:</strong> 100 000 000 ou 100000000</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-unstyled mb-0 small">
                                                    <li><strong>1 milliard:</strong> 1 000 000 000 ou 1000000000</li>
                                                    <li><strong>5 milliards:</strong> 5 000 000 000 ou 5000000000</li>
                                                    <li><strong>10 milliards:</strong> 10 000 000 000 ou 10000000000</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-save"></i> Enregistrer la Dette
                            </button>
                            <a href="{{ route('dettes-cnps.index') }}" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Recherche dynamique dans le select des communes
    $('#commune_id').select2({
        placeholder: 'Rechercher une commune...',
        allowClear: true,
        theme: 'bootstrap4'
    });

    // Affichage des informations de la commune sélectionnée
    $('#commune_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const communeText = selectedOption.text().trim();
        
        if (communeText && communeText !== 'Sélectionnez une commune') {
            // Extraire les informations depuis le texte de l'option
            const optgroup = selectedOption.parent('optgroup');
            const departement = optgroup.attr('label').replace(/&nbsp;&nbsp;/, '');
            
            // Trouver la région
            let region = '';
            const parentOptgroup = optgroup.prevAll('optgroup').first();
            if (parentOptgroup.length) {
                region = parentOptgroup.attr('label');
            } else {
                // Si pas de parent optgroup précédent, c'est le premier niveau
                region = optgroup.parent().children('optgroup').first().attr('label');
            }
            
            $('#commune-details').html(`
                <div class="row">
                    <div class="col-md-4">
                        <strong>Commune :</strong><br>
                        ${communeText}
                    </div>
                    <div class="col-md-4">
                        <strong>Département :</strong><br>
                        ${departement}
                    </div>
                    <div class="col-md-4">
                        <strong>Région :</strong><br>
                        ${region}
                    </div>
                </div>
            `);
            $('#commune-info').show();
        } else {
            $('#commune-info').hide();
        }
    });

    // Gestion intelligente du montant
    let montantInput = $('#montant');
    let montantPreview = $('#montant-preview');
    let montantFormate = $('#montant-formate');

    // Fonction pour nettoyer et valider le montant
    function nettoyerMontant(valeur) {
        // Supprimer tout ce qui n'est pas un chiffre
        return valeur.replace(/[^\d]/g, '');
    }

    // Fonction pour formater le montant avec des espaces
    function formaterMontant(nombre) {
        if (!nombre) return '';
        return parseInt(nombre).toLocaleString('fr-FR');
    }

    // Fonction pour valider si le montant est dans les limites
    function validerMontant(nombre) {
        const maxValue = 999999999999999999999; // Limite du contrôleur
        return nombre <= maxValue;
    }

    // Événement sur la saisie du montant
    montantInput.on('input', function() {
        let valeur = $(this).val();
        let montantNettoye = nettoyerMontant(valeur);
        
        if (montantNettoye) {
            let montantNombre = parseInt(montantNettoye);
            
            if (validerMontant(montantNombre)) {
                // Afficher l'aperçu formaté
                montantFormate.text(formaterMontant(montantNettoye));
                montantPreview.show();
                $(this).removeClass('is-invalid');
                
                // Mettre à jour la valeur cachée pour l'envoi
                $(this).attr('data-raw-value', montantNettoye);
            } else {
                $(this).addClass('is-invalid');
                montantPreview.hide();
                toastr.warning('Le montant saisi dépasse la limite autorisée');
            }
        } else {
            montantPreview.hide();
            $(this).removeClass('is-invalid');
        }
    });

    // Compteur de caractères pour la description
    $('#description').on('input', function() {
        const longueur = $(this).val().length;
        const max = 1000;
        $('#description-counter').text(`${longueur}/${max} caractères`);
        
        if (longueur > max * 0.9) {
            $('#description-counter').removeClass('text-muted').addClass('text-warning');
        } else if (longueur === max) {
            $('#description-counter').removeClass('text-warning').addClass('text-danger');
        } else {
            $('#description-counter').removeClass('text-warning text-danger').addClass('text-muted');
        }
    });

    // Initialiser le compteur
    $('#description').trigger('input');

    // Validation avant soumission
    $('#detteForm').on('submit', function(e) {
        let isValid = true;
        let errors = [];

        // Nettoyer et valider le montant avant soumission
        let montantValeur = montantInput.val();
        let montantNettoye = nettoyerMontant(montantValeur);
        
        if (!montantNettoye || montantNettoye === '0') {
            isValid = false;
            errors.push('Le montant est obligatoire et doit être supérieur à 0');
            montantInput.addClass('is-invalid');
        } else {
            let montantNombre = parseInt(montantNettoye);
            if (!validerMontant(montantNombre)) {
                isValid = false;
                errors.push('Le montant saisi dépasse la limite autorisée');
                montantInput.addClass('is-invalid');
            } else {
                // Mettre à jour la valeur du champ avec le montant nettoyé
                montantInput.val(montantNettoye);
                montantInput.removeClass('is-invalid');
            }
        }

        // Vérification de la commune
        if (!$('#commune_id').val()) {
            isValid = false;
            errors.push('Vous devez sélectionner une commune');
            $('#commune_id').addClass('is-invalid');
        } else {
            $('#commune_id').removeClass('is-invalid');
        }

        // Vérification de la date
        if (!$('#date_evaluation').val()) {
            isValid = false;
            errors.push('La date d\'évaluation est obligatoire');
            $('#date_evaluation').addClass('is-invalid');
        } else {
            $('#date_evaluation').removeClass('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
            let errorMessage = 'Veuillez corriger les erreurs suivantes :<br>';
            errorMessage += errors.map(error => `• ${error}`).join('<br>');
            
            toastr.error(errorMessage, 'Erreurs de validation', {
                timeOut: 8000,
                extendedTimeOut: 2000,
                allowHtml: true
            });
            
            // Faire défiler vers la première erreur
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        } else {
            // Désactiver le bouton de soumission pour éviter les doubles clics
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enregistrement...');
        }
    });

    // Permettre de coller des montants avec séparateurs
    montantInput.on('paste', function(e) {
        setTimeout(() => {
            $(this).trigger('input');
        }, 10);
    });

    // Auto-focus sur le premier champ
    $('#commune_id').focus();
});
</script>
@endpush

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif
@endsection