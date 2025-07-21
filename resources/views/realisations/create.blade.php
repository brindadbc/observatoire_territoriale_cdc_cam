@extends('layouts.app')

@section('title', 'Nouvelle Réalisation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Nouvelle Réalisation</h3>
                    <a href="{{ route('realisations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('realisations.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="annee_exercice" class="form-label">Année d'exercice <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('annee_exercice') is-invalid @enderror" 
                                           id="annee_exercice" 
                                           name="annee_exercice" 
                                           value="{{ old('annee_exercice', date('Y')) }}"
                                           min="2000" 
                                           max="{{ date('Y') + 1 }}"
                                           required>
                                    @error('annee_exercice')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_realisation" class="form-label">Date de réalisation <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('date_realisation') is-invalid @enderror" 
                                           id="date_realisation" 
                                           name="date_realisation" 
                                           value="{{ old('date_realisation') }}"
                                           required>
                                    @error('date_realisation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="commune_id" class="form-label">Commune <span class="text-danger">*</span></label>
                                    <select class="form-select @error('commune_id') is-invalid @enderror" 
                                            id="commune_id" 
                                            name="commune_id" 
                                            required>
                                        <option value="">Sélectionnez une commune</option>
                                        @foreach($communes as $commune)
                                            <option value="{{ $commune->id }}" 
                                                    {{ old('commune_id') == $commune->id ? 'selected' : '' }}
                                                    data-departement="{{ $commune->departement->nom }}"
                                                    data-region="{{ $commune->departement->region->nom }}">
                                                {{ $commune->nom }} ({{ $commune->departement->nom }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('commune_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prevision_id" class="form-label">Prévision associée</label>
                                    <select class="form-select @error('prevision_id') is-invalid @enderror" 
                                            id="prevision_id" 
                                            name="prevision_id">
                                        <option value="">Aucune prévision</option>
                                        <!-- Les options seront chargées dynamiquement -->
                                    </select>
                                    @error('prevision_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> 
                                        Sélectionnez d'abord une commune et une année pour voir les prévisions disponibles
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="montant" class="form-label">Montant (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('montant') is-invalid @enderror" 
                                           id="montant" 
                                           name="montant" 
                                           value="{{ old('montant') }}"
                                           min="0" 
                                           step="0.01"
                                           required>
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Informations sur la prévision</label>
                                    <div class="alert alert-info" id="prevision-info" style="display: none;">
                                        <strong>Prévision sélectionnée:</strong><br>
                                        <span id="prevision-details"></span>
                                    </div>
                                    <div class="alert alert-warning" id="no-prevision-warning" style="display: none;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Aucune prévision trouvée</strong><br>
                                        <span id="no-prevision-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      maxlength="500"
                                      placeholder="Description optionnelle de la réalisation...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum 500 caractères</div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('realisations.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Enregistrer
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

{{-- Informations sur les prévisions disponibles --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Prévisions disponibles par commune</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="previsions-table">
                        <thead>
                            <tr>
                                <th>Commune</th>
                                <th>Année</th>
                                <th>Montant prévu</th>
                                <th>Réalisé</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($previsions as $prevision)
<tr data-commune="{{ $prevision->commune_id }}" 
    data-annee="{{ $prevision->annee_exercice }}"
    data-id="{{ $prevision->id }}">
    <td>{{ $prevision->commune->nom }}</td>
    <td>{{ $prevision->annee_exercice }}</td>
    <td>{{ number_format($prevision->montant, 0, ',', ' ') }} FCFA</td>
    <td>
        @php
            $realise = $prevision->realisations->sum('montant');
            $pourcentage = $prevision->montant > 0 ? ($realise / $prevision->montant) * 100 : 0;
        @endphp
        {{ number_format($realise, 0, ',', ' ') }} FCFA
        <small class="text-muted">({{ number_format($pourcentage, 1) }}%)</small>
    </td>
    <td>
        @if($pourcentage >= 100)
            <span class="badge bg-success">Terminé</span>
        @elseif($pourcentage >= 50)
            <span class="badge bg-warning">En cours</span>
        @else
            <span class="badge bg-info">Disponible</span>
        @endif
    </td>
</tr>
@empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Aucune prévision disponible</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const communeSelect = document.getElementById('commune_id');
    const anneeInput = document.getElementById('annee_exercice');
    const previsionSelect = document.getElementById('prevision_id');
    const previsionInfo = document.getElementById('prevision-info');
    const previsionDetails = document.getElementById('prevision-details');
    const noPrevisionWarning = document.getElementById('no-prevision-warning');
    const noPrevisionMessage = document.getElementById('no-prevision-message');

    // Récupérer les anciennes valeurs pour maintenir la sélection après validation échouée
    const oldPrevisionId = "{{ old('prevision_id') }}";
    const oldCommuneId = "{{ old('commune_id') }}";
    const oldAnnee = "{{ old('annee_exercice') }}";

    function updatePrevisions() {
        const selectedCommune = communeSelect.value;
        const selectedAnnee = anneeInput.value;

        console.log('Updating previsions:', { selectedCommune, selectedAnnee });

        // Réinitialiser les informations
        previsionSelect.innerHTML = '<option value="">Aucune prévision</option>';
        previsionInfo.style.display = 'none';
        previsionDetails.innerHTML = '';
        noPrevisionWarning.style.display = 'none';
        noPrevisionMessage.innerHTML = '';

        if (selectedCommune && selectedAnnee) {
            let found = false;
            document.querySelectorAll('#previsions-table tbody tr').forEach(row => {
                const rowCommune = row.getAttribute('data-commune');
                const rowAnnee = row.getAttribute('data-annee');
                const previsionId = row.getAttribute('data-id');

                console.log('Checking row:', { rowCommune, rowAnnee, previsionId });

                // Conversion en entier pour comparaison stricte
                if (parseInt(rowCommune) === parseInt(selectedCommune) && 
                    parseInt(rowAnnee) === parseInt(selectedAnnee)) {
                    
                    const montant = row.cells[2].innerText.trim();
                    const realise = row.cells[3].innerText.trim();
                    const statut = row.cells[4].innerText.trim();

                    const option = document.createElement('option');
                    option.value = previsionId;
                    option.text = `Prévision ${selectedAnnee} - ${montant}`;
                    
                    // Maintenir la sélection après validation échouée
                    if (oldPrevisionId && oldPrevisionId === previsionId) {
                        option.selected = true;
                    }
                    
                    previsionSelect.appendChild(option);

                    // Afficher les détails de la prévision
                    previsionDetails.innerHTML = `
                        <strong>Montant prévu :</strong> ${montant}<br>
                        <strong>Montant réalisé :</strong> ${realise}<br>
                        <strong>Statut :</strong> ${statut}
                    `;
                    previsionInfo.style.display = 'block';
                    found = true;

                    console.log('Match found:', { previsionId, montant });
                }
            });

            if (!found) {
                noPrevisionWarning.style.display = 'block';
                noPrevisionMessage.textContent = 'Aucune prévision correspondante trouvée pour cette commune et cette année.';
                console.log('No prevision found for:', { selectedCommune, selectedAnnee });
            }
        }
    }

    // Écouteurs d'événements
    communeSelect.addEventListener('change', updatePrevisions);
    anneeInput.addEventListener('input', updatePrevisions);

    // Appeler la fonction au chargement pour restaurer l'état après validation échouée
    if (oldCommuneId && oldAnnee) {
        communeSelect.value = oldCommuneId;
        anneeInput.value = oldAnnee;
        updatePrevisions();
    }

    // Appeler aussi au chargement initial
    updatePrevisions();
});
</script>
@endpush