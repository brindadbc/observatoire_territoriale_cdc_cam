@extends('layouts.app')

@section('title', 'Export des Réalisations')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Export des Réalisations</h1>
                    <p class="text-muted">Exportez les données selon vos critères</p>
                </div>
                <a href="{{ route('realisations.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>

            <!-- Formulaire d'export -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-filter"></i> Critères d'export
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('realisations.export') }}" id="exportForm">
                        <div class="row">
                            <!-- Année -->
                            <div class="col-md-6 mb-3">
                                <label for="annee" class="form-label">Année d'exercice</label>
                                <select name="annee" id="annee" class="form-select">
                                    @foreach(range(date('Y'), 2020) as $year)
                                        <option value="{{ $year }}" {{ request('annee', date('Y')) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Format d'export -->
                            <div class="col-md-6 mb-3">
                                <label for="format" class="form-label">Format d'export</label>
                                <select name="format" id="format" class="form-select">
                                    <option value="excel">Excel (.xlsx)</option>
                                    <option value="csv">CSV (.csv)</option>
                                    <option value="pdf">PDF (.pdf)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Commune -->
                            <div class="col-md-6 mb-3">
                                <label for="commune_id" class="form-label">Commune (optionnel)</label>
                                <select name="commune_id" id="commune_id" class="form-select">
                                    <option value="">Toutes les communes</option>
                                    @foreach($communes->groupBy('departement.nom') as $departement => $communesGroup)
                                        <optgroup label="{{ $departement }}">
                                            @foreach($communesGroup as $commune)
                                                <option value="{{ $commune->id }}" 
                                                        {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                                                    {{ $commune->nom }} ({{ $commune->code }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Département -->
                            <div class="col-md-6 mb-3">
                                <label for="departement_id" class="form-label">Département (optionnel)</label>
                                <select name="departement_id" id="departement_id" class="form-select">
                                    <option value="">Tous les départements</option>
                                    @foreach($departements->groupBy('region.nom') as $region => $departementsGroup)
                                        <optgroup label="{{ $region }}">
                                            @foreach($departementsGroup as $departement)
                                                <option value="{{ $departement->id }}" 
                                                        {{ request('departement_id') == $departement->id ? 'selected' : '' }}>
                                                    {{ $departement->nom }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Options d'export -->
                        <div class="mb-4">
                            <h6>Options d'inclusion</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_stats" name="include_stats" value="1" checked>
                                        <label class="form-check-label" for="include_stats">
                                            Inclure les statistiques générales
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_comparaisons" name="include_comparaisons" value="1" checked>
                                        <label class="form-check-label" for="include_comparaisons">
                                            Inclure les comparaisons entre communes
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_evolution" name="include_evolution" value="1">
                                        <label class="form-check-label" for="include_evolution">
                                            Inclure l'évolution annuelle
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_details" name="include_details" value="1" checked>
                                        <label class="form-check-label" for="include_details">
                                            Inclure les détails des prévisions
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aperçu des données -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Aperçu des données à exporter</h6>
                            <div id="preview-info">
                                <p class="mb-0">Cliquez sur "Aperçu" pour voir le nombre d'enregistrements qui seront exportés.</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <button type="button" id="preview-btn" class="btn btn-outline-info">
                                <i class="fas fa-eye"></i> Aperçu des données
                            </button>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i> Réinitialiser
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Télécharger l'export
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Historique des exports récents -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-history"></i> Exports récents
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date/Heure</th>
                                    <th>Critères</th>
                                    <th>Format</th>
                                    <th>Nb. enregistrements</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-muted">
                                    <td colspan="5" class="text-center py-3">
                                        <i class="fas fa-info-circle"></i> Aucun export récent
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewBtn = document.getElementById('preview-btn');
    const previewInfo = document.getElementById('preview-info');
    const form = document.getElementById('exportForm');

    previewBtn.addEventListener('click', function() {
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        params.append('preview', '1');

        fetch('{{ route('realisations.export') }}?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    previewInfo.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${data.count}</strong> réalisations seront exportées
                                <br><small class="text-muted">Montant total: ${data.montant_total} FCFA</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">${data.communes_count} communes concernées</small>
                            </div>
                        </div>
                    `;
                } else {
                    previewInfo.innerHTML = `
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle"></i> ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                previewInfo.innerHTML = `
                    <div class="text-danger">
                        <i class="fas fa-exclamation-circle"></i> Erreur lors de l'aperçu
                    </div>
                `;
            });
    });

    // Auto-preview lors du changement de critères
    form.addEventListener('change', function() {
        previewBtn.click();
    });

    // Preview initial
    previewBtn.click();
});
</script>
@endpush

@endsection