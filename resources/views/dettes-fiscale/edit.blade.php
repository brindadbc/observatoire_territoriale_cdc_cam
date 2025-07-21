@extends('layouts.app')

@section('title', 'Modifier la dette fiscale')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Modifier la dette fiscale</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dettes-fiscale.index') }}">Dettes fiscales</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dettes-fiscale.show', $detteFiscale) }}">Détails</a></li>
                            <li class="breadcrumb-item active">Modifier</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('dettes-fiscale.show', $detteFiscale) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit text-warning"></i>
                        Formulaire de modification
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dettes-fiscale.update', $detteFiscale) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Sélection de la commune -->
                            <div class="col-md-12 mb-3">
                                <label for="commune_id" class="form-label required">Commune</label>
                                <select name="commune_id" id="commune_id" class="form-select @error('commune_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez une commune</option>
                                    @foreach($communes->groupBy('departement.region.nom') as $regionNom => $communesRegion)
                                        <optgroup label="RÉGION {{ strtoupper($regionNom) }}">
                                            @foreach($communesRegion->groupBy('departement.nom') as $departementNom => $communesDept)
                                                <optgroup label="&nbsp;&nbsp;{{ $departementNom }}">
                                                    @foreach($communesDept as $commune)
                                                        <option value="{{ $commune->id }}" 
                                                                {{ old('commune_id', $detteFiscale->commune_id) == $commune->id ? 'selected' : '' }}>
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
                            </div>

                            <!-- Montant -->
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label required">Montant de la dette (FCFA)</label>
                                <div class="input-group">
                                    <input type="number" 
                                           name="montant" 
                                           id="montant" 
                                           class="form-control @error('montant') is-invalid @enderror"
                                           value="{{ old('montant', $detteFiscale->montant) }}"
                                           min="0" 
                                           max="999999999.99"
                                           step="0.01"
                                           required>
                                    <span class="input-group-text">FCFA</span>
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    Montant maximum : 999,999,999.99 FCFA
                                </div>
                            </div>

                            <!-- Date d'évaluation -->
                            <div class="col-md-6 mb-3">
                                <label for="date_evaluation" class="form-label required">Date d'évaluation</label>
                                <input type="date" 
                                       name="date_evaluation" 
                                       id="date_evaluation" 
                                       class="form-control @error('date_evaluation') is-invalid @enderror"
                                       value="{{ old('date_evaluation', $detteFiscale->date_evaluation) }}"
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

                        <!-- Aperçu du montant formaté -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong>Aperçu du montant :</strong>
                                    <span id="montant-preview">{{ number_format($detteFiscale->montant, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('dettes-fiscale.show', $detteFiscale) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations actuelles -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary"></i>
                        Informations actuelles
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="fw-bold">Commune actuelle :</td>
                            <td>{{ $detteFiscale->commune->nom }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Montant actuel :</td>
                            <td class="text-danger">{{ number_format($detteFiscale->montant, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Date actuelle :</td>
                            <td>{{ \Carbon\Carbon::parse($detteFiscale->date_evaluation)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Créé le :</td>
                            <td>{{ $detteFiscale->created_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Modifié le :</td>
                            <td>{{ $detteFiscale->updated_at->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Aide -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-question-circle text-info"></i>
                        Aide
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            Vérifiez que la commune sélectionnée est correcte
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            Le montant doit être positif et inférieur à 1 milliard
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            La date d'évaluation ne peut pas être dans le futur
                        </li>
                        <li>
                            <i class="fas fa-check text-success"></i>
                            Tous les champs marqués d'un * sont obligatoires
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const montantInput = document.getElementById('montant');
    const montantPreview = document.getElementById('montant-preview');

    function updateMontantPreview() {
        const montant = parseFloat(montantInput.value) || 0;
        montantPreview.textContent = new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
    }

    montantInput.addEventListener('input', updateMontantPreview);
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

optgroup {
    font-weight: bold;
}

optgroup option {
    font-weight: normal;
}
</style>
@endpush