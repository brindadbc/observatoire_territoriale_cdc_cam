 @extends('layouts.app')

@section('title', 'Nouvel Ordonnateur')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>Nouvel Ordonnateur
                        </h4>
                        <a href="{{ route('ordonnateurs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('ordonnateurs.store') }}" novalidate>
                        @csrf

                        <div class="row">
                            <!-- Informations personnelles -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Informations personnelles
                                </h5>

                                <div class="mb-3">
                                    <label for="nom" class="form-label">
                                        Nom complet <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('nom') is-invalid @enderror"
                                           id="nom"
                                           name="nom"
                                           value="{{ old('nom') }}"
                                           required
                                           placeholder="Entrez le nom complet">
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="fonction" class="form-label">
                                        Fonction <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('fonction') is-invalid @enderror"
                                           id="fonction"
                                           name="fonction"
                                           value="{{ old('fonction') }}"
                                           required
                                           placeholder="Ex: Maire, Adjoint au Maire, etc.">
                                    @error('fonction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel"
                                           class="form-control @error('telephone') is-invalid @enderror"
                                           id="telephone"
                                           name="telephone"
                                           value="{{ old('telephone') }}"
                                           placeholder="Ex: +237 6XX XX XX XX">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="date_prise_fonction" class="form-label">
                                        Date de prise de fonction <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control @error('date_prise_fonction') is-invalid @enderror"
                                           id="date_prise_fonction"
                                           name="date_prise_fonction"
                                           value="{{ old('date_prise_fonction') }}"
                                           required>
                                    @error('date_prise_fonction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Assignation -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>Assignation
                                </h5>

                                <div class="mb-3">
                                    <label for="commune_id" class="form-label">Commune</label>
                                    <select class="form-select @error('commune_id') is-invalid @enderror"
                                            id="commune_id"
                                            name="commune_id">
                                        <option value="">Sélectionner une commune (optionnel)</option>
                                        @foreach($communes->groupBy('departement.region.nom') as $region => $communesRegion)
                                            <optgroup label="Région {{ $region }}">
                                                @foreach($communesRegion->groupBy('departement.nom') as $departement => $communesDepartement)
                                                    <optgroup label="── {{ $departement }}">
                                                        @foreach($communesDepartement as $commune)
                                                            <option value="{{ $commune->id }}"
                                                                    {{ old('commune_id') == $commune->id ? 'selected' : '' }}>
                                                                {{ $commune->nom }}
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
                                        <i class="fas fa-info-circle me-1"></i>
                                        Vous pouvez laisser ce champ vide et assigner l'ordonnateur plus tard.
                                    </div>
                                </div>

                                <!-- Informations sur la commune sélectionnée -->
                                <div id="commune-info" class="alert alert-info d-none">
                                    <h6><i class="fas fa-info-circle me-2"></i>Information sur la commune</h6>
                                    <div id="commune-details"></div>
                                </div>

                                <!-- Guide d'aide -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-lightbulb me-2 text-warning"></i>Conseils
                                        </h6>
                                        <ul class="small mb-0">
                                            <li>Assurez-vous que les informations saisies sont exactes</li>
                                            <li>La date de prise de fonction doit être réaliste</li>
                                            <li>Un ordonnateur peut être créé sans être assigné à une commune</li>
                                            <li>Le numéro de téléphone facilitera les communications</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('ordonnateurs.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <button type="reset" class="btn btn-outline-warning">
                                        <i class="fas fa-redo me-2"></i>Réinitialiser
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const communeSelect = document.getElementById('commune_id');
    const communeInfo = document.getElementById('commune-info');
    const communeDetails = document.getElementById('commune-details');

    // Données des communes (vous pouvez les passer depuis le contrôleur)
    // const communesData = @json($communes->keyBy('id')->map(function($commune) {
    //     return(
    //         'nom' => $commune->nom,
    //         'departement' => $commune->departement->nom,
    //         'region' => $commune->departement->region->nom,
    //         'has_ordonnateur' => $commune->ordonnateurs->count() > 0
    //      );
    // }));

    communeSelect.addEventListener('change', function() {
        const communeId = this.value;

        if (communeId && communesData[communeId]) {
            const commune = communesData[communeId];
            let html = `
                <div class="row">
                    <div class="col-6">
                        <strong>Département:</strong> ${commune.departement}
                    </div>
                    <div class="col-6">
                        <strong>Région:</strong> ${commune.region}
                    </div>
                </div>
            `;

            if (commune.has_ordonnateur) {
                html += `
                    <div class="mt-2">
                        <span class="badge bg-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Cette commune a déjà un ordonnateur
                        </span>
                    </div>
                `;
            }

            communeDetails.innerHTML = html;
            communeInfo.classList.remove('d-none');
        } else {
            communeInfo.classList.add('d-none');
        }
    });

    // Validation en temps réel
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required], select[required]');

    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });

    function validateField(field) {
        const isValid = field.checkValidity();
        field.classList.remove('is-valid', 'is-invalid');

        if (field.value.trim() !== '') {
            field.classList.add(isValid ? 'is-valid' : 'is-invalid');
        }
    }

    // Validation du formulaire avant soumission
    form.addEventListener('submit', function(e) {
        let isFormValid = true;

        inputs.forEach(input => {
            validateField(input);
            if (!input.checkValidity()) {
                isFormValid = false;
            }
        });

        if (!isFormValid) {
            e.preventDefault();

            // Faire défiler vers le premier champ invalide
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
        }
    });

    // Auto-focus sur le premier champ
    document.getElementById('nom').focus();
});
</script>
@endpush
