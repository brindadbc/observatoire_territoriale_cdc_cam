@extends('layouts.app')

@section('title', 'Nouvelle Dette FEICOM')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Nouvelle Dette FEICOM</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dettes-feicom.index') }}">Dettes FEICOM</a></li>
                        <li class="breadcrumb-item active">Nouvelle Dette</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-plus-circle me-2"></i>
                        Enregistrer une nouvelle dette FEICOM
                    </h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('dettes-feicom.store') }}" id="detteForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Sélection de la commune -->
                            <div class="col-md-12 mb-3">
                                <label for="commune_id" class="form-label required">
                                    Commune <span class="text-danger">*</span>
                                </label>
                                <select name="commune_id" 
                                        id="commune_id" 
                                        class="form-select @error('commune_id') is-invalid @enderror"
                                        required>
                                    <option value="">-- Sélectionnez une commune --</option>
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
                                <div class="form-text">
                                    Choisissez la commune concernée par cette dette FEICOM
                                </div>
                            </div>

                            <!-- Montant de la dette -->
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label required">
                                    Montant de la dette (FCFA) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           name="montant" 
                                           id="montant" 
                                           class="form-control @error('montant') is-invalid @enderror" 
                                           value="{{ old('montant') }}" 
                                           step="0.01"
                                           min="0"
                                           max="9999999999999999.99"
                                           placeholder="Ex: 5000000"
                                           required>
                                    <span class="input-group-text">FCFA</span>
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    Montant maximum: 999,999,999,999,9999.99 FCFA
                                </div>
                                <div id="montantFormate" class="text-primary fw-bold mt-1"></div>
                            </div>

                            <!-- Date d'évaluation -->
                            <div class="col-md-6 mb-3">
                                <label for="date_evaluation" class="form-label required">
                                    Date d'évaluation <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="date_evaluation" 
                                       id="date_evaluation" 
                                       class="form-control @error('date_evaluation') is-invalid @enderror" 
                                       value="{{ old('date_evaluation', date('Y-m-d')) }}" 
                                       max="{{ date('Y-m-d') }}"
                                       required>
                                @error('date_evaluation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    La date ne peut pas être dans le futur
                                </div>
                            </div>
                        </div>

                        <!-- Informations sur la commune sélectionnée -->
                        <div id="communeInfo" class="alert alert-info" style="display: none;">
                            <h6><i class="mdi mdi-information"></i> Informations sur la commune</h6>
                            <div id="communeDetails"></div>
                            <div id="communeHistory" class="mt-3">
                                <h6>Historique des dettes FEICOM</h6>
                                <div id="historyContent"></div>
                            </div>
                        </div>

                        <!-- Aperçu des données saisies -->
                        <div class="card bg-light mt-4">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="mdi mdi-eye-outline"></i>
                                    Aperçu de la dette à enregistrer
                                </h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Commune:</strong>
                                        <div id="previewCommune" class="text-muted">Non sélectionnée</div>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Montant:</strong>
                                        <div id="previewMontant" class="text-muted">Non saisi</div>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Date d'évaluation:</strong>
                                        <div id="previewDate" class="text-muted">Non saisie</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('dettes-feicom.index') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left"></i> Retour à la liste
                                    </a>
                                    <div class="btn-group">
                                        <button type="reset" class="btn btn-outline-warning">
                                            <i class="mdi mdi-refresh"></i> Réinitialiser
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="mdi mdi-check"></i> Enregistrer la dette
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const communeSelect = document.getElementById('commune_id');
    const montantInput = document.getElementById('montant');
    const dateInput = document.getElementById('date_evaluation');
    const form = document.getElementById('detteForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Données des communes (passées depuis le contrôleur)
    const communesData = @json($communes->keyBy('id'));
    
    // Formatage du montant en temps réel
    montantInput.addEventListener('input', function() {
        const montant = parseFloat(this.value);
        const montantFormateDiv = document.getElementById('montantFormate');
        
        if (!isNaN(montant) && montant > 0) {
            montantFormateDiv.textContent = `Montant formaté: ${new Intl.NumberFormat('fr-FR').format(montant)} FCFA`;
        } else {
            montantFormateDiv.textContent = '';
        }
        
        updatePreview();
    });
    
    // Mise à jour des informations sur la commune
    communeSelect.addEventListener('change', function() {
        const communeId = this.value;
        const communeInfo = document.getElementById('communeInfo');
        const communeDetails = document.getElementById('communeDetails');
        
        if (communeId && communesData[communeId]) {
            const commune = communesData[communeId];
            communeDetails.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <strong>Nom:</strong> ${commune.nom}<br>
                        <strong>Code:</strong> ${commune.code}
                    </div>
                    <div class="col-md-4">
                        <strong>Département:</strong> ${commune.departement.nom}
                    </div>
                    <div class="col-md-4">
                        <strong>Région:</strong> ${commune.departement.region.nom}
                    </div>
                </div>
            `;
            communeInfo.style.display = 'block';
            
            // Charger l'historique des dettes pour cette commune
            loadCommuneHistory(communeId);
        } else {
            communeInfo.style.display = 'none';
        }
        
        updatePreview();
    });
    
    // Mise à jour de l'aperçu
    dateInput.addEventListener('change', updatePreview);
    
    function updatePreview() {
        const communeId = communeSelect.value;
        const montant = parseFloat(montantInput.value);
        const date = dateInput.value;
        
        // Commune
        const previewCommune = document.getElementById('previewCommune');
        if (communeId && communesData[communeId]) {
            const commune = communesData[communeId];
            previewCommune.innerHTML = `<span class="text-success">${commune.nom} (${commune.code})</span>`;
        } else {
            previewCommune.innerHTML = '<span class="text-muted">Non sélectionnée</span>';
        }
        
        // Montant
        const previewMontant = document.getElementById('previewMontant');
        if (!isNaN(montant) && montant > 0) {
            previewMontant.innerHTML = `<span class="text-success">${new Intl.NumberFormat('fr-FR').format(montant)} FCFA</span>`;
        } else {
            previewMontant.innerHTML = '<span class="text-muted">Non saisi</span>';
        }
        
        // Date
        const previewDate = document.getElementById('previewDate');
        if (date) {
            const formattedDate = new Date(date).toLocaleDateString('fr-FR');
            previewDate.innerHTML = `<span class="text-success">${formattedDate}</span>`;
        } else {
            previewDate.innerHTML = '<span class="text-muted">Non saisie</span>';
        }
        
        // Activer/désactiver le bouton de soumission
        const isValid = communeId && !isNaN(montant) && montant > 0 && date;
        submitBtn.disabled = !isValid;
    }
    
    // Fonction pour charger l'historique des dettes d'une commune
    function loadCommuneHistory(communeId) {
        const historyContent = document.getElementById('historyContent');
        historyContent.innerHTML = '<div class="text-center"><i class="mdi mdi-loading mdi-spin"></i> Chargement...</div>';
        
        fetch(`{{ route('dettes-feicom.getDettesByCommune') }}?commune_id=${communeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.dettes && data.dettes.length > 0) {
                    let historyHtml = `
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.dettes.slice(0, 5).forEach(dette => {
                        const formattedDate = new Date(dette.date_evaluation).toLocaleDateString('fr-FR');
                        const formattedMontant = new Intl.NumberFormat('fr-FR').format(dette.montant);
                        historyHtml += `
                            <tr>
                                <td>${formattedDate}</td>
                                <td>${formattedMontant} FCFA</td>
                            </tr>
                        `;
                    });
                    
                    historyHtml += `
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted">Total: ${new Intl.NumberFormat('fr-FR').format(data.total)} FCFA (${data.count} enregistrement(s))</small>
                    `;
                    
                    historyContent.innerHTML = historyHtml;
                } else {
                    historyContent.innerHTML = '<p class="text-muted">Aucune dette FEICOM enregistrée pour cette commune.</p>';
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement de l\'historique:', error);
                historyContent.innerHTML = '<p class="text-danger">Erreur lors du chargement de l\'historique.</p>';
            });
    }
    
    // Validation en temps réel
    form.addEventListener('input', function() {
        updatePreview();
    });
    
    // Réinitialisation du formulaire
    form.addEventListener('reset', function() {
        setTimeout(() => {
            document.getElementById('communeInfo').style.display = 'none';
            document.getElementById('montantFormate').textContent = '';
            updatePreview();
        }, 10);
    });
    
    // Initialisation
    updatePreview();
});
</script>
@endpush