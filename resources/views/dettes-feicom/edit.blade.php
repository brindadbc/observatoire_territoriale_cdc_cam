@extends('layouts.app')

@section('title', 'Modifier Dette FEICOM')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Modifier la Dette FEICOM</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dettes-feicom.index') }}">Dettes FEICOM</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dettes-feicom.show', $detteFeicom) }}">Détails</a></li>
                        <li class="breadcrumb-item active">Modifier</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-pencil-outline me-2"></i>
                        Formulaire de Modification
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dettes-feicom.update', $detteFeicom) }}" method="POST" id="editDetteForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Sélection de la commune -->
                        <div class="mb-3">
                            <label for="commune_id" class="form-label">
                                Commune <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('commune_id') is-invalid @enderror" 
                                    id="commune_id" name="commune_id" required>
                                <option value="">Sélectionnez une commune...</option>
                                @foreach($communes->groupBy('departement.region.nom') as $regionNom => $communesRegion)
                                    <optgroup label="{{ $regionNom }}">
                                        @foreach($communesRegion->groupBy('departement.nom') as $departementNom => $communesDept)
                                            <optgroup label="-- {{ $departementNom }}">
                                                @foreach($communesDept as $commune)
                                                    <option value="{{ $commune->id }}" 
                                                            {{ old('commune_id', $detteFeicom->commune_id) == $commune->id ? 'selected' : '' }}
                                                            data-region="{{ $commune->departement->region->nom }}"
                                                            data-departement="{{ $commune->departement->nom }}">
                                                        {{ $commune->nom }} ({{ $commune->code }})
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
                            <div id="commune-info" class="mt-2" style="display: none;">
                                <small class="text-muted">
                                    <strong>Région:</strong> <span id="region-name"></span> | 
                                    <strong>Département:</strong> <span id="departement-name"></span>
                                </small>
                            </div>
                        </div>

                        <!-- Montant de la dette -->
                        <div class="mb-3">
                            <label for="montant" class="form-label">
                                Montant de la Dette (FCFA) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('montant') is-invalid @enderror" 
                                       id="montant" 
                                       name="montant" 
                                       value="{{ old('montant', $detteFeicom->montant) }}"
                                       step="0.01" 
                                       min="0" 
                                       max="999999999999999999999.99"
                                       required
                                       placeholder="Entrez le montant de la dette">
                                <span class="input-group-text">FCFA</span>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">
                                <span id="montant-format"></span>
                            </div>
                        </div>

                        <!-- Date d'évaluation -->
                        <div class="mb-3">
                            <label for="date_evaluation" class="form-label">
                                Date d'Évaluation <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('date_evaluation') is-invalid @enderror" 
                                   id="date_evaluation" 
                                   name="date_evaluation" 
                                   value="{{ old('date_evaluation', $detteFeicom->date_evaluation) }}"
                                   max="{{ date('Y-m-d') }}"
                                   required>
                            @error('date_evaluation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                La date d'évaluation ne peut pas être dans le futur.
                            </div>
                        </div>

                        <hr>

                        <!-- Boutons d'action -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Mettre à jour
                            </button>
                            <a href="{{ route('dettes-feicom.show', $detteFeicom) }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations actuelles -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-information-outline me-2"></i>
                        Informations Actuelles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted">Commune actuelle</label>
                        <div class="fw-bold">{{ $detteFeicom->commune->nom }}</div>
                        <small class="text-muted">{{ $detteFeicom->commune->departement->nom }}, {{ $detteFeicom->commune->departement->region->nom }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted">Montant actuel</label>
                        <div class="fw-bold text-danger fs-5">
                            {{ number_format($detteFeicom->montant, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted">Date d'évaluation actuelle</label>
                        <div class="fw-bold">
                            {{ \Carbon\Carbon::parse($detteFeicom->date_evaluation)->format('d/m/Y') }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted">Dernière modification</label>
                        <div class="fw-semibold">
                            {{ $detteFeicom->updated_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conseils -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-lightbulb-outline me-2"></i>
                        Conseils
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="mdi mdi-check-circle text-success me-2"></i>
                            Vérifiez bien la commune sélectionnée
                        </li>
                        <li class="mb-2">
                            <i class="mdi mdi-check-circle text-success me-2"></i>
                            Le montant doit être exact et en FCFA
                        </li>
                        <li class="mb-2">
                            <i class="mdi mdi-check-circle text-success me-2"></i>
                            La date d'évaluation doit être antérieure à aujourd'hui
                        </li>
                        <li>
                            <i class="mdi mdi-check-circle text-success me-2"></i>
                            Une fois modifiée, l'historique sera préservé
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const communeSelect = document.getElementById('commune_id');
    const montantInput = document.getElementById('montant');
    const communeInfo = document.getElementById('commune-info');
    const regionName = document.getElementById('region-name');
    const departementName = document.getElementById('departement-name');
    const montantFormat = document.getElementById('montant-format');

    // Gestion de l'affichage des informations de la commune
    communeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const region = selectedOption.getAttribute('data-region');
            const departement = selectedOption.getAttribute('data-departement');
            
            regionName.textContent = region;
            departementName.textContent = departement;
            communeInfo.style.display = 'block';
        } else {
            communeInfo.style.display = 'none';
        }
    });

    // Formatage du montant en temps réel
    montantInput.addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (!isNaN(value) && value > 0) {
            montantFormat.textContent = 'Soit: ' + value.toLocaleString('fr-FR') + ' FCFA';
            montantFormat.className = 'form-text text-primary';
        } else {
            montantFormat.textContent = '';
        }
    });

    // Déclenchement initial pour commune pré-sélectionnée
    if (communeSelect.value) {
        communeSelect.dispatchEvent(new Event('change'));
    }

    // Formatage initial du montant
    if (montantInput.value) {
        montantInput.dispatchEvent(new Event('input'));
    }

    // Validation du formulaire
    document.getElementById('editDetteForm').addEventListener('submit', function(e) {
        const montant = parseFloat(montantInput.value);
        const commune = communeSelect.value;
        const date = document.getElementById('date_evaluation').value;

        if (!commune) {
            e.preventDefault();
            alert('Veuillez sélectionner une commune.');
            communeSelect.focus();
            return;
        }

        if (!montant || montant <= 0) {
            e.preventDefault();
            alert('Veuillez entrer un montant valide supérieur à 0.');
            montantInput.focus();
            return;
        }

        if (!date) {
            e.preventDefault();
            alert('Veuillez sélectionner une date d\'évaluation.');
            document.getElementById('date_evaluation').focus();
            return;
        }

        // Confirmation de modification
        if (!confirm('Êtes-vous sûr de vouloir modifier cette dette FEICOM ?')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection