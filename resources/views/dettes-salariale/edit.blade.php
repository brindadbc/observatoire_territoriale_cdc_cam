@extends('layouts.app')

@section('title', 'Modifier la Dette Salariale')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Modifier la Dette Salariale</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dettes-salariale.dashboard') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dettes-salariale.index') }}">Dettes Salariales</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dettes-salariale.show', $detteSalariale) }}">Détails</a></li>
                            <li class="breadcrumb-item active">Modifier</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('dettes-salariale.show', $detteSalariale) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <!-- Carte de modification -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit"></i> Modification de la dette salariale
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dettes-salariale.update', $detteSalariale) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Commune -->
                            <div class="col-md-6 mb-3">
                                <label for="commune_id" class="form-label required">Commune</label>
                                <select class="form-select @error('commune_id') is-invalid @enderror" 
                                        id="commune_id" 
                                        name="commune_id" 
                                        required>
                                    <option value="">Sélectionner une commune</option>
                                    @foreach($communes->groupBy('departement.region.nom') as $regionNom => $communesRegion)
                                        <optgroup label="{{ $regionNom }}">
                                            @foreach($communesRegion->groupBy('departement.nom') as $departementNom => $communesDepartement)
                                                <optgroup label="&nbsp;&nbsp;{{ $departementNom }}">
                                                    @foreach($communesDepartement as $commune)
                                                        <option value="{{ $commune->id }}" 
                                                                {{ old('commune_id', $detteSalariale->commune_id) == $commune->id ? 'selected' : '' }}>
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
                            </div>

                            <!-- Montant -->
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label required">Montant de la dette (FCFA)</label>
                                <input type="number" 
                                       class="form-control @error('montant') is-invalid @enderror" 
                                       id="montant" 
                                       name="montant" 
                                       value="{{ old('montant', $detteSalariale->montant) }}"
                                       min="0" 
                                       step="0.01"
                                       required>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Montant formaté: <span id="montant-formate">{{ number_format($detteSalariale->montant, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Date d'évaluation -->
                            <div class="col-md-6 mb-3">
                                <label for="date_evaluation" class="form-label required">Date d'évaluation</label>
                                <input type="date" 
                                       class="form-control @error('date_evaluation') is-invalid @enderror" 
                                       id="date_evaluation" 
                                       name="date_evaluation" 
                                       value="{{ old('date_evaluation', $detteSalariale->date_evaluation) }}"
                                       max="{{ date('Y-m-d') }}"
                                       required>
                                @error('date_evaluation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Informations actuelles -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Informations actuelles</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Commune actuelle:</strong><br>
                                            {{ $detteSalariale->commune->nom }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Montant actuel:</strong><br>
                                            {{ number_format($detteSalariale->montant, 0, ',', ' ') }} FCFA
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Date actuelle:</strong><br>
                                            {{ \Carbon\Carbon::parse($detteSalariale->date_evaluation)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('dettes-salariale.show', $detteSalariale) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Enregistrer les modifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const montantInput = document.getElementById('montant');
    const montantFormate = document.getElementById('montant-formate');
    
    // Formatage du montant en temps réel
    montantInput.addEventListener('input', function() {
        const value = parseFloat(this.value) || 0;
        montantFormate.textContent = new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
    });
    
    // Validation du formulaire
    document.getElementById('editForm').addEventListener('submit', function(e) {
        const montant = parseFloat(montantInput.value);
        
        if (montant <= 0) {
            e.preventDefault();
            alert('Le montant doit être supérieur à 0');
            montantInput.focus();
            return false;
        }
        
        if (montant > 999999999999999999.99) {
            e.preventDefault();
            alert('Le montant ne peut pas dépasser 999,999,999,999,999,999.99 FCFA');
            montantInput.focus();
            return false;
        }
        
        // Confirmation avant soumission
        if (!confirm('Êtes-vous sûr de vouloir modifier cette dette salariale ?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.required::after {
    content: " *";
    color: red;
}

.form-text {
    font-size: 0.875em;
    color: #6c757d;
}

.alert-info {
    border-left: 4px solid #0dcaf0;
}

.gap-2 {
    gap: 0.5rem;
}

optgroup {
    font-weight: bold;
}

optgroup option {
    font-weight: normal;
    padding-left: 20px;
}
</style>
@endpush
@endsection