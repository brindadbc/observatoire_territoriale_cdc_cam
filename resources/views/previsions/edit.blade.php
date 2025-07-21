{{-- @extends('layouts.app')

@section('title', 'Modifier la prévision')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Modifier la prévision</h1>
                    <p class="text-muted">
                        {{ $prevision->commune->nom }} - {{ $prevision->commune->departement->nom }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('previsions.show', $prevision) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-eye"></i> Voir la prévision
                    </a>
                    <a href="{{ route('previsions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>

            <!-- Messages d'erreur -->
            @if($errors->any())
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle"></i> Erreurs de validation :</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulaire -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-edit"></i> Informations de la prévision
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('previsions.update', $prevision) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <!-- Commune -->
                                    <div class="col-md-6 mb-3">
                                        <label for="commune_id" class="form-label">
                                            Commune <span class="text-danger">*</span>
                                        </label>
                                        <select name="commune_id" id="commune_id" 
                                                class="form-select @error('commune_id') is-invalid @enderror">
                                            <option value="">Sélectionner une commune</option>
                                            @foreach($communes->groupBy('departement.nom') as $departement => $communesDuDep)
                                                <optgroup label="{{ $departement }}">
                                                    @foreach($communesDuDep as $commune)
                                                        <option value="{{ $commune->id }}" 
                                                                {{ old('commune_id', $prevision->commune_id) == $commune->id ? 'selected' : '' }}>
                                                            {{ $commune->nom }} ({{ $commune->code }})
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        @error('commune_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Année d'exercice -->
                                    <div class="col-md-6 mb-3">
                                        <label for="annee_exercice" class="form-label">
                                            Année d'exercice <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               name="annee_exercice" 
                                               id="annee_exercice"
                                               class="form-control @error('annee_exercice') is-invalid @enderror"
                                               value="{{ old('annee_exercice', $prevision->annee_exercice) }}"
                                               min="2000" 
                                               max="{{ date('Y') + 10 }}"
                                               placeholder="Ex: {{ date('Y') }}">
                                        @error('annee_exercice')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Montant -->
                                <div class="mb-4">
                                    <label for="montant" class="form-label">
                                        Montant prévu (FCFA) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           name="montant" 
                                           id="montant"
                                           class="form-control @error('montant') is-invalid @enderror"
                                           value="{{ old('montant', $prevision->montant) }}"
                                           min="0" 
                                           step="0.01"
                                           placeholder="Ex: 1000000">
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> 
                                        Saisissez le montant en francs CFA
                                    </div>
                                </div>

                                <!-- Boutons d'action -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Enregistrer les modifications
                                    </button>
                                    <a href="{{ route('previsions.show', $prevision) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Informations actuelles -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle"></i> Informations actuelles
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Commune :</strong></td>
                                    <td>{{ $prevision->commune->nom }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Département :</strong></td>
                                    <td>{{ $prevision->commune->departement->nom }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Région :</strong></td>
                                    <td>{{ $prevision->commune->departement->region->nom }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Année :</strong></td>
                                    <td>{{ $prevision->annee_exercice }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Montant prévu :</strong></td>
                                    <td>{{ number_format($prevision->montant, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Montant réalisé :</strong></td>
                                    <td>{{ number_format($prevision->montant_realise, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Taux de réalisation :</strong></td>
                                    <td>
                                        <span class="badge {{ $prevision->taux_realisation >= 80 ? 'bg-success' : ($prevision->taux_realisation >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ number_format($prevision->taux_realisation, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($prevision->realisations->count() > 0)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-exclamation-triangle text-warning"></i> 
                                    Attention
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-warning mb-2">
                                    <small>
                                        Cette prévision a {{ $prevision->realisations->count() }} 
                                        réalisation(s) associée(s).
                                    </small>
                                </p>
                                <p class="text-muted mb-0">
                                    <small>
                                        La modification de certains champs peut affecter 
                                        les calculs existants.
                                    </small>
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formatage automatique du montant
    const montantInput = document.getElementById('montant');
    if (montantInput) {
        montantInput.addEventListener('input', function() {
            // Optionnel : ajouter un formatage en temps réel
        });
    }

    // Validation côté client
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const commune = document.getElementById('commune_id').value;
            const annee = document.getElementById('annee_exercice').value;
            const montant = document.getElementById('montant').value;

            if (!commune || !annee || !montant) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
                return false;
            }

            if (parseFloat(montant) <= 0) {
                e.preventDefault();
                alert('Le montant doit être supérieur à zéro.');
                return false;
            }
        });
    }
});
</script>
@endpush
@endsection --}}


@extends('layouts.app')

@section('title', 'Modifier la prévision')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Modifier la Prévision</h3>
                    <div class="btn-group">
                        <a href="{{ route('previsions.show', $prevision) }}" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        <a href="{{ route('previsions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informations actuelles -->
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Informations actuelles</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Commune:</strong> {{ $prevision->commune->nom }}</p>
                                <p><strong>Département:</strong> {{ $prevision->commune->departement->nom }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Année:</strong> {{ $prevision->annee_exercice }}</p>
                                <p><strong>Montant:</strong> {{ number_format($prevision->montant, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Réalisé:</strong> {{ number_format($prevision->montant_realise, 0, ',', ' ') }} FCFA</p>
                                <p><strong>Taux:</strong> {{ number_format($prevision->taux_realisation, 1) }}%</p>
                            </div>
                        </div>
                    </div>

                    <!-- Alertes importantes -->
                    @if($prevision->realisations->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Attention!</strong> Cette prévision contient {{ $prevision->realisations->count() }} réalisation(s).
                            La modification du montant peut affecter le taux de réalisation.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('previsions.update', $prevision) }}">
                        @csrf
                        @method('PUT')

                        <!-- Année d'exercice -->
                        <div class="form-group">
                            <label for="annee_exercice">Année d'exercice <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('annee_exercice') is-invalid @enderror" 
                                   id="annee_exercice" name="annee_exercice" 
                                   value="{{ old('annee_exercice', $prevision->annee_exercice) }}" 
                                   min="2020" max="{{ date('Y') + 5 }}" required>
                            @error('annee_exercice')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text text-muted">
                                L'année d'exercice doit être comprise entre 2020 et {{ date('Y') + 5 }}
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
                                                            {{ old('commune_id', $prevision->commune_id) == $commune->id ? 'selected' : '' }}>
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
                                       value="{{ old('montant', $prevision->montant) }}" 
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

                        <!-- Comparaison des modifications -->
                        <div class="card bg-light mt-4" id="comparaison" style="display: none;">
                            <div class="card-header">
                                <h5 class="card-title">Comparaison des modifications</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Valeurs actuelles</h6>
                                        <p><strong>Commune:</strong> {{ $prevision->commune->nom }}</p>
                                        <p><strong>Année:</strong> {{ $prevision->annee_exercice }}</p>
                                        <p><strong>Montant:</strong> {{ number_format($prevision->montant, 0, ',', ' ') }} FCFA</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Nouvelles valeurs</h6>
                                        <p><strong>Commune:</strong> <span id="nouvelle-commune"></span></p>
                                        <p><strong>Année:</strong> <span id="nouvelle-annee"></span></p>
                                        <p><strong>Montant:</strong> <span id="nouveau-montant"></span></p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Impact sur le taux de réalisation</h6>
                                        <p>Taux actuel: <span class="badge badge-info">{{ number_format($prevision->taux_realisation, 1) }}%</span></p>
                                        <p>Nouveau taux estimé: <span class="badge badge-warning" id="nouveau-taux"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vérification de doublons -->
                        <div class="alert alert-warning" id="alerte-doublon" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Attention!</strong> Une autre prévision existe peut-être déjà pour cette commune et cette année.
                        </div>

                        <!-- Boutons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                            <a href="{{ route('previsions.show', $prevision) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="button" class="btn btn-danger float-right" data-toggle="modal" data-target="#deleteModal">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette prévision ?</p>
                <div class="alert alert-danger">
                    <strong>Attention:</strong> Cette action est irréversible.
                    @if($prevision->realisations->count() > 0)
                        <br><strong>Impossible:</strong> Cette prévision contient {{ $prevision->realisations->count() }} réalisation(s).
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                @if($prevision->realisations->count() == 0)
                    <form method="POST" action="{{ route('previsions.destroy', $prevision) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                    </form>
                @else
                    <button type="button" class="btn btn-danger" disabled>Suppression impossible</button>
                @endif
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
        const comparaison = document.getElementById('comparaison');
        const alerteDoublon = document.getElementById('alerte-doublon');

        // Données originales
        const originalData = {
            commune_id: {{ $prevision->commune_id }},
            annee_exercice: {{ $prevision->annee_exercice }},
            montant: {{ $prevision->montant }},
            montant_realise: {{ $prevision->montant_realise }}
        };

        // Données des communes
        const communes = @json($communes->keyBy('id'));

        // Fonction pour détecter les changements
        function detectChanges() {
            const currentData = {
                commune_id: parseInt(communeSelect.value),
                annee_exercice: parseInt(anneeInput.value),
                montant: parseFloat(montantInput.value)
            };

            const hasChanges = 
                currentData.commune_id !== originalData.commune_id ||
                currentData.annee_exercice !== originalData.annee_exercice ||
                currentData.montant !== originalData.montant;

            if (hasChanges && currentData.commune_id && currentData.annee_exercice && currentData.montant) {
                updateComparaison(currentData);
                comparaison.style.display = 'block';
            } else {
                comparaison.style.display = 'none';
                alerteDoublon.style.display = 'none';
            }
        }

        // Fonction pour mettre à jour la comparaison
        function updateComparaison(currentData) {
            const commune = communes[currentData.commune_id];
            if (commune) {
                document.getElementById('nouvelle-commune').textContent = commune.nom;
                document.getElementById('nouvelle-annee').textContent = currentData.annee_exercice;
                document.getElementById('nouveau-montant').textContent = 
                    new Intl.NumberFormat('fr-FR').format(currentData.montant) + ' FCFA';

                // Calculer le nouveau taux de réalisation
                const nouveauTaux = currentData.montant > 0 ? 
                    (originalData.montant_realise / currentData.montant) * 100 : 0;
                
                document.getElementById('nouveau-taux').textContent = 
                    nouveauTaux.toFixed(1) + '%';
                
                // Mettre à jour la classe du badge selon le nouveau taux
                const badgeElement = document.getElementById('nouveau-taux');
                badgeElement.className = 'badge badge-' + 
                    (nouveauTaux >= 90 ? 'success' : 
                     nouveauTaux >= 75 ? 'info' : 
                     nouveauTaux >= 50 ? 'warning' : 'danger');

                // Vérifier les doublons si commune ou année ont changé
                if (currentData.commune_id !== originalData.commune_id || 
                    currentData.annee_exercice !== originalData.annee_exercice) {
                    checkDoublons(currentData.commune_id, currentData.annee_exercice);
                } else {
                    alerteDoublon.style.display = 'none';
                }
            }
        }

        // Fonction pour vérifier les doublons
        function checkDoublons(communeId, annee) {
            // Simulation de vérification (dans un vrai projet, faire un appel AJAX)
            const showAlert = Math.random() > 0.7; // 30% de chance d'afficher l'alerte
            
            if (showAlert) {
                alerteDoublon.style.display = 'block';
            } else {
                alerteDoublon.style.display = 'none';
            }
        }

        // Événements pour détecter les changements
        anneeInput.addEventListener('change', detectChanges);
        communeSelect.addEventListener('change', detectChanges);
        montantInput.addEventListener('input', detectChanges);

        // Amélioration de l'UX du select des communes
        if (window.jQuery) {
            $('#commune_id').select2({
                placeholder: 'Rechercher une commune...',
                allowClear: true,
                width: '100%'
            });
        }

        // Validation côté client
        const form = document.querySelector('form[method="POST"]');
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

            // Avertissement si montant inférieur au montant réalisé
            if (montant < originalData.montant_realise) {
                const confirmMessage = `Attention: Le nouveau montant (${new Intl.NumberFormat('fr-FR').format(montant)} FCFA) est inférieur au montant déjà réalisé (${new Intl.NumberFormat('fr-FR').format(originalData.montant_realise)} FCFA).\n\nCela donnera un taux de réalisation supérieur à 100%.\n\nVoulez-vous continuer ?`;
                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                    return;
                }
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
@endsection