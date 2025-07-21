@extends('layouts.app')

@section('title', 'Modifier la dette CNPS')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Modifier la dette CNPS</h1>
                    <p class="text-muted">
                        Commune : {{ $detteCnps->commune->nom }} - {{ $detteCnps->commune->departement->nom }}
                    </p>
                </div>
                <a href="{{ route('dettes-cnps.show', $detteCnps) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit"></i> Formulaire de modification
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Erreurs de validation :</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('dettes-cnps.update', $detteCnps) }}" id="editDetteForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Montant -->
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">
                                    <i class="fas fa-money-bill-wave"></i> Montant <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control @error('montant') is-invalid @enderror" 
                                           id="montant" 
                                           name="montant" 
                                           value="{{ old('montant', number_format($detteCnps->montant, 0, ',', '')) }}" 
                                           placeholder="Ex: 1 500 000 000"
                                           autocomplete="off"
                                           required>
                                    <span class="input-group-text">FCFA</span>
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Montant actuel : {{ number_format($detteCnps->montant, 0, ',', ' ') }} FCFA
                                    <br>Vous pouvez saisir jusqu'au milliard et au-delà.
                                </small>
                                <div id="montant-preview" class="mt-2" style="display: none;">
                                    <div class="alert alert-info py-2">
                                        <strong>Nouveau montant:</strong> <span id="montant-formate"></span> FCFA
                                    </div>
                                </div>
                            </div>

                            <!-- Date d'évaluation -->
                            <div class="col-md-6 mb-3">
                                <label for="date_evaluation" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Date d'évaluation <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('date_evaluation') is-invalid @enderror" 
                                       id="date_evaluation" 
                                       name="date_evaluation" 
                                       value="{{ old('date_evaluation', $detteCnps->date_evaluation) }}" 
                                       required>
                                @error('date_evaluation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Commune -->
                        <div class="mb-3">
                            <label for="commune_id" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Commune <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('commune_id') is-invalid @enderror" 
                                    id="commune_id" 
                                    name="commune_id" 
                                    required>
                                <option value="">Sélectionnez une commune</option>
                                @foreach($communes->groupBy('departement.region.nom') as $region => $communesRegion)
                                    <optgroup label="{{ $region }}">
                                        @foreach($communesRegion->groupBy('departement.nom') as $departement => $communesDept)
                                            <optgroup label="&nbsp;&nbsp;{{ $departement }}">
                                                @foreach($communesDept as $commune)
                                                    <option value="{{ $commune->id }}" 
                                                            {{ old('commune_id', $detteCnps->commune_id) == $commune->id ? 'selected' : '' }}>
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

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-comment"></i> Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      maxlength="1000"
                                      placeholder="Description ou observations concernant cette dette...">{{ old('description', $detteCnps->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between">
                                <small class="form-text text-muted">Maximum 1000 caractères</small>
                                <small class="text-muted" id="description-counter">0/1000 caractères</small>
                            </div>
                        </div>

                        <!-- Exemples de montants pour aide lors de la modification -->
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
                                                    <li><strong>1 million:</strong> 1000000 ou 1 000 000</li>
                                                    <li><strong>10 millions:</strong> 10000000 ou 10 000 000</li>
                                                    <li><strong>100 millions:</strong> 100000000 ou 100 000 000</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-unstyled mb-0 small">
                                                    <li><strong>1 milliard:</strong> 1000000000 ou 1 000 000 000</li>
                                                    <li><strong>5 milliards:</strong> 5000000000 ou 5 000 000 000</li>
                                                    <li><strong>10 milliards:</strong> 10000000000 ou 10 000 000 000</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations sur la modification -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Informations actuelles</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Montant actuel :</strong> 
                                        <span class="badge bg-secondary fs-6">{{ number_format($detteCnps->montant, 0, ',', ' ') }} FCFA</span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Date actuelle :</strong> 
                                        {{ \Carbon\Carbon::parse($detteCnps->date_evaluation)->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Commune actuelle :</strong> 
                                        {{ $detteCnps->commune->nom }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Département :</strong> 
                                        {{ $detteCnps->commune->departement->nom }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dettes-cnps.show', $detteCnps) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Historique récent -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-history"></i> Historique récent de cette commune
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $historiqueRecent = App\Models\dette_cnps::where('commune_id', $detteCnps->commune_id)
                            ->where('id', '!=', $detteCnps->id)
                            ->orderByDesc('date_evaluation')
                            ->limit(3)
                            ->get();
                    @endphp

                    @if($historiqueRecent->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historiqueRecent as $dette)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($dette->date_evaluation)->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ number_format($dette->montant, 0, ',', ' ') }} FCFA
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($dette->description, 40) ?: '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
{{-- <div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Modifier la dette CNPS</h1>
                    <p class="text-muted">
                        Commune : {{ $detteCnps->commune->nom }} - {{ $detteCnps->commune->departement->nom }}
                    </p>
                </div>
                <a href="{{ route('dettes-cnps.show', $detteCnps) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit"></i> Formulaire de modification
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Erreurs de validation :</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('dettes-cnps.update', $detteCnps) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Montant -->
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">
                                    <i class="fas fa-money-bill-wave"></i> Montant <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('montant') is-invalid @enderror" 
                                           id="montant" 
                                           name="montant" 
                                           value="{{ old('montant', $detteCnps->montant) }}" 
                                           step="0.01" 
                                           min="0"
                                           placeholder="Montant de la dette"
                                           required>
                                    <span class="input-group-text">FCFA</span>
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Montant actuel : {{ number_format($detteCnps->montant, 0, ',', ' ') }} FCFA
                                </small>
                            </div>

                            <!-- Date d'évaluation -->
                            <div class="col-md-6 mb-3">
                                <label for="date_evaluation" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Date d'évaluation <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('date_evaluation') is-invalid @enderror" 
                                       id="date_evaluation" 
                                       name="date_evaluation" 
                                       value="{{ old('date_evaluation', $detteCnps->date_evaluation) }}" 
                                       required>
                                @error('date_evaluation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Commune -->
                        <div class="mb-3">
                            <label for="commune_id" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Commune <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('commune_id') is-invalid @enderror" 
                                    id="commune_id" 
                                    name="commune_id" 
                                    required>
                                <option value="">Sélectionnez une commune</option>
                                @foreach($communes->groupBy('departement.region.nom') as $region => $communesRegion)
                                    <optgroup label="{{ $region }}">
                                        @foreach($communesRegion->groupBy('departement.nom') as $departement => $communesDept)
                                            <optgroup label="&nbsp;&nbsp;{{ $departement }}">
                                                @foreach($communesDept as $commune)
                                                    <option value="{{ $commune->id }}" 
                                                            {{ old('commune_id', $detteCnps->commune_id) == $commune->id ? 'selected' : '' }}>
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

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-comment"></i> Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      maxlength="500"
                                      placeholder="Description ou observations concernant cette dette...">{{ old('description', $detteCnps->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Maximum 500 caractères</small>
                        </div>

                        <!-- Informations sur la modification -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Informations actuelles</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Montant actuel :</strong> 
                                        {{ number_format($detteCnps->montant, 0, ',', ' ') }} FCFA
                                    </p>
                                    <p class="mb-1">
                                        <strong>Date actuelle :</strong> 
                                        {{ \Carbon\Carbon::parse($detteCnps->date_evaluation)->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Commune actuelle :</strong> 
                                        {{ $detteCnps->commune->nom }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Département :</strong> 
                                        {{ $detteCnps->commune->departement->nom }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dettes-cnps.show', $detteCnps) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Historique récent -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-history"></i> Historique récent de cette commune
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $historiqueRecent = App\Models\dette_cnps::where('commune_id', $detteCnps->commune_id)
                            ->where('id', '!=', $detteCnps->id)
                            ->orderByDesc('date_evaluation')
                            ->limit(3)
                            ->get();
                    @endphp

                    @if($historiqueRecent->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historiqueRecent as $dette)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($dette->date_evaluation)->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ number_format($dette->montant, 0, ',', ' ') }} FCFA
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($dette->description, 40) ?: '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody> --}}
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucun historique disponible pour cette commune.</p>
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
        montantInput.addEventListener('input', function(e) {
            // Optionnel : formatage en temps réel
        });
    }

    // Limite de caractères pour la description
    const descriptionTextarea = document.getElementById('description');
    if (descriptionTextarea) {
        const maxLength = 500;
        const counterDiv = document.createElement('div');
        counterDiv.className = 'text-muted small mt-1';
        descriptionTextarea.parentNode.appendChild(counterDiv);

        function updateCounter() {
            const remaining = maxLength - descriptionTextarea.value.length;
            counterDiv.textContent = `${descriptionTextarea.value.length}/${maxLength} caractères`;
            
            if (remaining < 50) {
                counterDiv.className = 'text-warning small mt-1';
            } else {
                counterDiv.className = 'text-muted small mt-1';
            }
        }

        descriptionTextarea.addEventListener('input', updateCounter);
        updateCounter(); // Initialisation
    }
});
</script>
@endpush
@endsection