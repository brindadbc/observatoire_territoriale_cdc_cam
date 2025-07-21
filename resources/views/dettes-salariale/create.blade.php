@extends('layouts.app')

@section('title', 'Nouvelle Dette Salariale')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Nouvelle Dette Salariale</h1>
                    <p class="text-muted">Enregistrer une nouvelle dette salariale pour une commune</p>
                </div>
                <div>
                    <a href="{{ route('dettes-salariale.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle"></i> Informations de la Dette
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dettes-salariale.store') }}">
                        @csrf
                        
                        <!-- Sélection de la commune -->
                        <div class="form-group">
                            <label for="commune_id" class="font-weight-bold">
                                Commune <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('commune_id') is-invalid @enderror" 
                                    id="commune_id" name="commune_id" required>
                                <option value="">-- Sélectionner une commune --</option>
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
                                Recherchez en tapant le nom de la commune
                            </small>
                        </div>

                        <!-- Montant de la dette -->
                        <div class="form-group">
                            <label for="montant" class="font-weight-bold">
                                Montant de la Dette (FCFA) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('montant') is-invalid @enderror" 
                                       id="montant" 
                                       name="montant" 
                                       value="{{ old('montant') }}" 
                                       step="0.01" 
                                       min="0" 
                                       max="99999999999999.99"
                                       required
                                       placeholder="Ex: 5000000">
                                <div class="input-group-append">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Montant en francs CFA (maximum: 999,999,999,999,999,999.99)
                            </small>
                            <!-- Affichage du montant en lettres -->
                            <div id="montantEnLettres" class="mt-2 p-2 bg-light border rounded" style="display: none;">
                                <small class="text-muted">
                                    <strong>En lettres :</strong> <span id="texteMontant"></span>
                                </small>
                            </div>
                        </div>

                        <!-- Date d'évaluation -->
                        <div class="form-group">
                            <label for="date_evaluation" class="font-weight-bold">
                                Date d'Évaluation <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('date_evaluation') is-invalid @enderror" 
                                   id="date_evaluation" 
                                   name="date_evaluation" 
                                   value="{{ old('date_evaluation', date('Y-m-d')) }}" 
                                   max="{{ date('Y-m-d') }}"
                                   required>
                            @error('date_evaluation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Date à laquelle la dette a été évaluée (ne peut pas être dans le futur)
                            </small>
                        </div>

                        <!-- Informations de la commune sélectionnée -->
                        <div id="infosCommune" class="alert alert-info" style="display: none;">
                            <h6><i class="fas fa-info-circle"></i> Informations de la Commune</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Commune :</strong> <span id="nomCommune"></span><br>
                                    <strong>Code :</strong> <span id="codeCommune"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Département :</strong> <span id="nomDepartement"></span><br>
                                    <strong>Région :</strong> <span id="nomRegion"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Estimation du niveau de criticité -->
                        <div id="niveauCriticite" class="alert" style="display: none;">
                            <h6><i class="fas fa-exclamation-triangle"></i> Évaluation Préliminaire</h6>
                            <p class="mb-0">
                                <strong>Niveau de criticité :</strong> 
                                <span id="badgeCriticite" class="badge"></span>
                            </p>
                            <div id="recommandationsPrelim" class="mt-2"></div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="form-group mt-4">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('dettes-salariale.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Enregistrer la Dette
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Aide -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-question-circle"></i> Aide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Niveaux de Criticité</h6>
                            <ul class="list-unstyled">
                                <li><span class="badge badge-success">Faible</span> : Moins de 1 M FCFA</li>
                                <li><span class="badge badge-info">Moyen</span> : 1 M à 10 M FCFA</li>
                                <li><span class="badge badge-warning">Élevé</span> : 10 M à 50 M FCFA</li>
                                <li><span class="badge badge-danger">Critique</span> : Plus de 50 M FCFA</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Conseils</h6>
                            <ul class="small">
                                <li>Vérifiez les informations avant validation</li>
                                <li>La date ne peut pas être dans le futur</li>
                                <li>Le montant doit être en francs CFA</li>
                                <li>Consultez l'historique de la commune si nécessaire</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Données des communes pour JavaScript
const communesData = @json($communes->keyBy('id'));

// Affichage des informations de la commune sélectionnée
document.getElementById('commune_id').addEventListener('change', function() {
    const communeId = this.value;
    const infosDiv = document.getElementById('infosCommune');
    
    if (communeId && communesData[communeId]) {
        const commune = communesData[communeId];
        document.getElementById('nomCommune').textContent = commune.nom;
        document.getElementById('codeCommune').textContent = commune.code;
        document.getElementById('nomDepartement').textContent = commune.departement.nom;
        document.getElementById('nomRegion').textContent = commune.departement.region.nom;
        infosDiv.style.display = 'block';
    } else {
        infosDiv.style.display = 'none';
    }
});

// Conversion du montant en lettres et évaluation de criticité
document.getElementById('montant').addEventListener('input', function() {
    const montant = parseFloat(this.value);
    const montantEnLettresDiv = document.getElementById('montantEnLettres');
    const niveauCriticiteDiv = document.getElementById('niveauCriticite');
    
    if (montant && montant > 0) {
        // Affichage du montant formaté
        const montantFormate = new Intl.NumberFormat('fr-FR').format(montant);
        document.getElementById('texteMontant').textContent = montantFormate + ' francs CFA';
        montantEnLettresDiv.style.display = 'block';
        
        // Évaluation du niveau de criticité
        let niveau, classe, recommandations;
        
        if (montant >= 50000000) {
            niveau = 'Critique';
            classe = 'badge-danger';
            niveauCriticiteDiv.className = 'alert alert-danger';
            recommandations = [
                'Mise en place urgente d\'un plan de redressement',
                'Négociation d\'un échéancier avec les créanciers',
                'Audit complet de la masse salariale'
            ];
        } else if (montant >= 10000000) {
            niveau = 'Élevé';
            classe = 'badge-warning';
            niveauCriticiteDiv.className = 'alert alert-warning';
            recommandations = [
                'Révision de la politique salariale',
                'Optimisation des effectifs',
                'Surveillance renforcée'
            ];
        } else if (montant >= 1000000) {
            niveau = 'Moyen';
            classe = 'badge-info';
            niveauCriticiteDiv.className = 'alert alert-info';
            recommandations = [
                'Surveillance renforcée',
                'Amélioration des procédures de paiement'
            ];
        } else {
            niveau = 'Faible';
            classe = 'badge-success';
            niveauCriticiteDiv.className = 'alert alert-success';
            recommandations = [
                'Maintenir la vigilance',
                'Suivi régulier'
            ];
        }
        
        document.getElementById('badgeCriticite').textContent = niveau;
        document.getElementById('badgeCriticite').className = 'badge ' + classe;
        
        // Affichage des recommandations
        const recommandationsHtml = recommandations.map(rec => 
            '<small class="d-block"><i class="fas fa-arrow-right"></i> ' + rec + '</small>'
        ).join('');
        document.getElementById('recommandationsPrelim').innerHTML = recommandationsHtml;
        
        niveauCriticiteDiv.style.display = 'block';
    } else {
        montantEnLettresDiv.style.display = 'none';
        niveauCriticiteDiv.style.display = 'none';
    }
});

// Amélioration du sélecteur de commune avec recherche
$(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('#commune_id').select2({
            placeholder: '-- Sélectionner une commune --',
            allowClear: true,
            language: 'fr'
        });
    }
});

// Validation du formulaire côté client
document.querySelector('form').addEventListener('submit', function(e) {
    const communeId = document.getElementById('commune_id').value;
    const montant = document.getElementById('montant').value;
    const dateEvaluation = document.getElementById('date_evaluation').value;
    
    let erreurs = [];
    
    if (!communeId) {
        erreurs.push('Veuillez sélectionner une commune');
    }
    
    if (!montant || parseFloat(montant) <= 0) {
        erreurs.push('Le montant doit être supérieur à 0');
    }
    
    if (!dateEvaluation) {
        erreurs.push('Veuillez saisir une date d\'évaluation');
    } else {
        const today = new Date();
        const dateEval = new Date(dateEvaluation);
        if (dateEval > today) {
            erreurs.push('La date d\'évaluation ne peut pas être dans le futur');
        }
    }
    
    if (erreurs.length > 0) {
        e.preventDefault();
        alert('Erreurs détectées :\n- ' + erreurs.join('\n- '));
        return false;
    }
    
    // Confirmation pour les montants élevés
    if (parseFloat(montant) >= 50000000) {
        if (!confirm('Le montant saisi est très élevé (' + new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA). Confirmez-vous la saisie ?')) {
            e.preventDefault();
            return false;
        }
    }
});
</script>
@endsection