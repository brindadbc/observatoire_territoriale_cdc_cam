{{-- @extends('layouts.app')

@section('title', 'Modifier le Dépôt de Compte')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Modifier le Dépôt de Compte</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('depot-comptes.index') }}">Dépôts de Comptes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('depot-comptes.show', $depotCompte) }}">Détails</a></li>
                    <li class="breadcrumb-item active">Modifier</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('depot-comptes.show', $depotCompte) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux détails
            </a>
            <a href="{{ route('depot-comptes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> Liste
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Modifier les Informations</h6>
                    <div>
                        @if($depotCompte->validation)
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> Validé
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="fas fa-clock"></i> En attente
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('depot-comptes.update', $depotCompte) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Commune -->
                            <div class="col-md-6 mb-3">
                                <label for="commune_id" class="form-label">Commune <span class="text-danger">*</span></label>
                                <select class="form-select @error('commune_id') is-invalid @enderror" 
                                        id="commune_id" name="commune_id" required>
                                    <option value="">Sélectionner une commune</option>
                                    @foreach($communes->groupBy('departement.region.nom') as $regionNom => $communesRegion)
                                        <optgroup label="{{ $regionNom }}">
                                            @foreach($communesRegion->groupBy('departement.nom') as $departementNom => $communesDept)
                                                <optgroup label="&nbsp;&nbsp;{{ $departementNom }}">
                                                    @foreach($communesDept as $commune)
                                                        <option value="{{ $commune->id }}" 
                                                                {{ old('commune_id', $depotCompte->commune_id) == $commune->id ? 'selected' : '' }}
                                                                data-receveurs="{{ $commune->receveurs->pluck('id') }}">
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

                            <!-- Receveur -->
                            <div class="col-md-6 mb-3">
                                <label for="receveur_id" class="form-label">Receveur <span class="text-danger">*</span></label>
                                <select class="form-select @error('receveur_id') is-invalid @enderror" 
                                        id="receveur_id" name="receveur_id" required>
                                    <option value="">Sélectionner un receveur</option>
                                    @foreach($receveurs as $receveur)
                                        <option value="{{ $receveur->id }}" 
                                                {{ old('receveur_id', $depotCompte->receveur_id) == $receveur->id ? 'selected' : '' }}>
                                            {{ $receveur->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('receveur_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Date de dépôt -->
                            <div class="col-md-6 mb-3">
                                <label for="date_depot" class="form-label">Date de Dépôt <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('date_depot') is-invalid @enderror" 
                                       id="date_depot" 
                                       name="date_depot" 
                                       value="{{ old('date_depot', $depotCompte->date_depot) }}"
                                       max="{{ date('Y-m-d') }}"
                                       required>
                                @error('date_depot')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Année d'exercice -->
                            <div class="col-md-6 mb-3">
                                <label for="annee_exercice" class="form-label">Année d'Exercice <span class="text-danger">*</span></label>
                                <select class="form-select @error('annee_exercice') is-invalid @enderror" 
                                        id="annee_exercice" name="annee_exercice" required>
                                    <option value="">Sélectionner une année</option>
                                    @for($year = date('Y') + 1; $year >= 2000; $year--)
                                        <option value="{{ $year }}" 
                                                {{ old('annee_exercice', $depotCompte->annee_exercice) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                @error('annee_exercice')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Validation -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="validation" 
                                       name="validation" 
                                       value="1"
                                       {{ old('validation', $depotCompte->validation) ? 'checked' : '' }}>
                                <label class="form-check-label" for="validation">
                                    Marquer comme validé
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Cochez cette case si le dépôt de compte a été validé.
                            </small>
                        </div>

                        <!-- Zone de changements -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Informations sur les modifications</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small>
                                        <strong>Créé le:</strong> {{ $depotCompte->created_at->format('d/m/Y H:i') }}<br>
                                        <strong>Dernière modification:</strong> {{ $depotCompte->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small>
                                        <strong>Commune actuelle:</strong> {{ $depotCompte->commune->nom }}<br>
                                        <strong>Receveur actuel:</strong> {{ $depotCompte->receveur->nom }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('depot-comptes.show', $depotCompte) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Sauvegarder les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations -->
        <div class="col-lg-4">
            <!-- Informations actuelles -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations Actuelles</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">Commune:</td>
                                <td class="fw-bold">{{ $depotCompte->commune->nom }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Code:</td>
                                <td>{{ $depotCompte->commune->code }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Département:</td>
                                <td>{{ $depotCompte->commune->departement->nom }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Région:</td>
                                <td>{{ $depotCompte->commune->departement->region->nom }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Receveur:</td>
                                <td class="fw-bold">{{ $depotCompte->receveur->nom }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Date de dépôt:</td>
                                <td>{{ \Carbon\Carbon::parse($depotCompte->date_depot)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Année:</td>
                                <td class="fw-bold">{{ $depotCompte->annee_exercice }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Statut:</td>
                                <td>
                                    @if($depotCompte->validation)
                                        <span class="badge bg-success">Validé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Aide -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aide à la Modification</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong><i class="fas fa-exclamation-triangle"></i> Attention</strong>
                        <hr>
                        <ul class="mb-0 small">
                            <li>La modification d'une commune ou d'une année peut avoir des impacts sur les données financières associées</li>
                            <li>Vérifiez que le nouveau receveur est bien associé à la commune</li>
                            <li>La date de dépôt ne peut pas être dans le futur</li>
                            <li>Un seul dépôt par commune et par année est autorisé</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Historique rapide -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Modifications Récentes</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Création</h6>
                                <p class="timeline-text small text-muted">
                                    {{ $depotCompte->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                        @if($depotCompte->updated_at != $depotCompte->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Dernière modification</h6>
                                    <p class="timeline-text small text-muted">
                                        {{ $depotCompte->updated_at->format('d/m/Y H:i') }}
                                        <br><em>{{ $depotCompte->updated_at->diffForHumans() }}</em>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialiser Select2
    $('#commune_id').select2({
        placeholder: 'Rechercher une commune...',
        allowClear: true,
        width: '100%'
    });
    
    $('#receveur_id').select2({
        placeholder: 'Sélectionner un receveur...',
        allowClear: true,
        width: '100%'
    });

    // Filtrer les receveurs selon la commune sélectionnée
    function filterReceveurs() {
        const selectedOption = $('#commune_id').find('option:selected');
        const receveurIds = selectedOption.data('receveurs');
        const currentReceveurId = {{ $depotCompte->receveur_id }};
        
        if (selectedOption.val() && receveurIds) {
            $('#receveur_id option').each(function() {
                const receveurId = parseInt($(this).val());
                if ($(this).val() === '' || receveurIds.includes(receveurId)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $('#receveur_id option').show();
        }
        
        // Conserver la sélection actuelle si elle est valide
        if (receveurIds.includes(currentReceveurId) || !selectedOption.val()) {
            $('#receveur_id').val(currentReceveurId).trigger('change');
        } else {
            $('#receveur_id').val('').trigger('change');
        }
    }

    // Filtrer au chargement
    filterReceveurs();

    // Filtrer lors du changement de commune
    $('#commune_id').on('change', filterReceveurs);

    // Validation en temps réel
    $('form').on('submit', function(e) {
        let isValid = true;
        let firstInvalid = null;
        
        // Vérifier les champs requis
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
                if (!firstInvalid) firstInvalid = $(this);
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            if (firstInvalid) {
                $('html, body').animate({
                    scrollTop: firstInvalid.offset().top - 100
                }, 500);
                firstInvalid.focus();
            }
        }
    });

    // Confirmation des changements majeurs
    let originalValues = {
        commune_id: {{ $depotCompte->commune_id }},
        annee_exercice: {{ $depotCompte->annee_exercice }}
    };

    $('form').on('submit', function(e) {
        const newCommuneId = parseInt($('#commune_id').val());
        const newAnneeExercice = parseInt($('#annee_exercice').val());
        
        if (newCommuneId !== originalValues.commune_id || newAnneeExercice !== originalValues.annee_exercice) {
            if (!confirm('Vous modifiez des informations critiques (commune ou année d\'exercice). Êtes-vous sûr de vouloir continuer ?')) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -31px;
    top: 15px;
    width: 2px;
    height: calc(100% + 5px);
    background-color: #e3e6f0;
}

.timeline-title {
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.timeline-text {
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
@endsection --}}




@extends('layouts.app')

@section('title', 'Modifier le Dépôt de Compte')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Modifier le Dépôt de Compte</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('depot-comptes.index') }}">Dépôts de Comptes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('depot-comptes.show', $depotCompte) }}">Détails</a></li>
                    <li class="breadcrumb-item active">Modifier</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('depot-comptes.show', $depotCompte) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux détails
            </a>
            <a href="{{ route('depot-comptes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> Liste
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Modifier les Informations</h6>
                    <div>
                        @if($depotCompte->validation)
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> Validé
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="fas fa-clock"></i> En attente
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('depot-comptes.update', $depotCompte) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Commune -->
                            <div class="col-md-6 mb-3">
                                <label for="commune_id" class="form-label">Commune <span class="text-danger">*</span></label>
                                <select class="form-select @error('commune_id') is-invalid @enderror" 
                                        id="commune_id" name="commune_id" required>
                                    <option value="">Sélectionner une commune</option>
                                    @foreach($communes->groupBy('departement.region.nom') as $regionNom => $communesRegion)
                                        <optgroup label="{{ $regionNom }}">
                                            @foreach($communesRegion->groupBy('departement.nom') as $departementNom => $communesDept)
                                                <optgroup label="&nbsp;&nbsp;{{ $departementNom }}">
                                                    @foreach($communesDept as $commune)
                                                        <option value="{{ $commune->id }}" 
                                                                {{ old('commune_id', $depotCompte->commune_id) == $commune->id ? 'selected' : '' }}
                                                                data-receveurs="{{ $commune->receveurs->pluck('id') }}">
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

                            <!-- Receveur -->
                            <div class="col-md-6 mb-3">
                                <label for="receveur_id" class="form-label">Receveur</label>
                                <select class="form-select @error('receveur_id') is-invalid @enderror" 
                                        id="receveur_id" name="receveur_id">
                                    <option value="">Sélectionner un receveur</option>
                                    @foreach($receveurs as $receveur)
                                        <option value="{{ $receveur->id }}" 
                                                {{ old('receveur_id', $depotCompte->receveur_id) == $receveur->id ? 'selected' : '' }}>
                                            {{ $receveur->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('receveur_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Type de dépôt -->
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type de Dépôt <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="budget_primitif" {{ old('type', $depotCompte->type) == 'budget_primitif' ? 'selected' : '' }}>
                                        Budget Primitif
                                    </option>
                                    <option value="compte_administratif" {{ old('type', $depotCompte->type) == 'compte_administratif' ? 'selected' : '' }}>
                                        Compte Administratif
                                    </option>
                                    <option value="budget_supplementaire" {{ old('type', $depotCompte->type) == 'budget_supplementaire' ? 'selected' : '' }}>
                                        Budget Supplémentaire
                                    </option>
                                    <option value="decision_modificative" {{ old('type', $depotCompte->type) == 'decision_modificative' ? 'selected' : '' }}>
                                        Décision Modificative
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Statut -->
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                <select class="form-select @error('statut') is-invalid @enderror" 
                                        id="statut" name="statut" required>
                                    <option value="">Sélectionner un statut</option>
                                    <option value="depose" {{ old('statut', $depotCompte->statut) == 'depose' ? 'selected' : '' }}>
                                        Déposé
                                    </option>
                                    <option value="non_depose" {{ old('statut', $depotCompte->statut) == 'non_depose' ? 'selected' : '' }}>
                                        Non Déposé
                                    </option>
                                    <option value="en_attente" {{ old('statut', $depotCompte->statut) == 'en_attente' ? 'selected' : '' }}>
                                        En Attente
                                    </option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Année d'exercice -->
                            <div class="col-md-6 mb-3">
                                <label for="annee_exercice" class="form-label">Année d'Exercice <span class="text-danger">*</span></label>
                                <select class="form-select @error('annee_exercice') is-invalid @enderror" 
                                        id="annee_exercice" name="annee_exercice" required>
                                    <option value="">Sélectionner une année</option>
                                    @for($year = date('Y') + 1; $year >= 2000; $year--)
                                        <option value="{{ $year }}" 
                                                {{ old('annee_exercice', $depotCompte->annee_exercice) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                @error('annee_exercice')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date limite de dépôt -->
                            <div class="col-md-6 mb-3">
                                <label for="date_limite_depot" class="form-label">Date Limite de Dépôt <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('date_limite_depot') is-invalid @enderror" 
                                       id="date_limite_depot" 
                                       name="date_limite_depot" 
                                       value="{{ old('date_limite_depot', $depotCompte->date_limite_depot) }}"
                                       required>
                                @error('date_limite_depot')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Date limite réglementaire pour le dépôt.
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Date de dépôt -->
                            <div class="col-md-6 mb-3">
                                <label for="date_depot" class="form-label">Date de Dépôt <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('date_depot') is-invalid @enderror" 
                                       id="date_depot" 
                                       name="date_depot" 
                                       value="{{ old('date_depot', $depotCompte->date_depot) }}"
                                       max="{{ date('Y-m-d') }}"
                                       required>
                                @error('date_depot')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Jours de retard -->
                            <div class="col-md-6 mb-3">
                                <label for="jours_retard" class="form-label">Jours de Retard</label>
                                <input type="number" 
                                       class="form-control @error('jours_retard') is-invalid @enderror" 
                                       id="jours_retard" 
                                       name="jours_retard" 
                                       value="{{ old('jours_retard', $depotCompte->jours_retard) }}"
                                       min="0"
                                       readonly>
                                @error('jours_retard')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Calculé automatiquement selon les dates.
                                </small>
                            </div>
                        </div>

                        <!-- Observations -->
                        <div class="mb-3">
                            <label for="observations" class="form-label">Observations</label>
                            <textarea class="form-control @error('observations') is-invalid @enderror" 
                                      id="observations" 
                                      name="observations" 
                                      rows="3"
                                      maxlength="1000"
                                      placeholder="Observations ou remarques sur ce dépôt...">{{ old('observations', $depotCompte->observations) }}</textarea>
                            @error('observations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Maximum 1000 caractères.
                            </small>
                        </div>

                        <!-- Validation -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="validation" 
                                       name="validation" 
                                       value="1"
                                       {{ old('validation', $depotCompte->validation) ? 'checked' : '' }}>
                                <label class="form-check-label" for="validation">
                                    Marquer comme validé
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Cochez cette case si le dépôt de compte a été validé.
                            </small>
                        </div>

                        <!-- Zone de changements -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Informations sur les modifications</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small>
                                        <strong>Créé le:</strong> {{ $depotCompte->created_at->format('d/m/Y H:i') }}<br>
                                        <strong>Dernière modification:</strong> {{ $depotCompte->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small>
                                        <strong>Commune actuelle:</strong> {{ $depotCompte->commune->nom }}<br>
                                        <strong>Receveur actuel:</strong> {{ $depotCompte->receveur ? $depotCompte->receveur->nom : 'Non assigné' }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('depot-comptes.show', $depotCompte) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Sauvegarder les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations -->
        <div class="col-lg-4">
            <!-- Informations actuelles -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations Actuelles</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">Commune:</td>
                                <td class="fw-bold">{{ $depotCompte->commune->nom }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Code:</td>
                                <td>{{ $depotCompte->commune->code }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Département:</td>
                                <td>{{ $depotCompte->commune->departement->nom }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Région:</td>
                                <td>{{ $depotCompte->commune->departement->region->nom }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Receveur:</td>
                                <td class="fw-bold">{{ $depotCompte->receveur ? $depotCompte->receveur->nom : 'Non assigné' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Type:</td>
                                <td>
                                    @switch($depotCompte->type)
                                        @case('budget_primitif')
                                            Budget Primitif
                                            @break
                                        @case('compte_administratif')
                                            Compte Administratif
                                            @break
                                        @case('budget_supplementaire')
                                            Budget Supplémentaire
                                            @break
                                        @case('decision_modificative')
                                            Décision Modificative
                                            @break
                                        @default
                                            {{ $depotCompte->type }}
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Date de dépôt:</td>
                                <td>{{ \Carbon\Carbon::parse($depotCompte->date_depot)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Date limite:</td>
                                <td>{{ \Carbon\Carbon::parse($depotCompte->date_limite_depot)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Année:</td>
                                <td class="fw-bold">{{ $depotCompte->annee_exercice }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Statut:</td>
                                <td>
                                    @switch($depotCompte->statut)
                                        @case('depose')
                                            <span class="badge bg-success">Déposé</span>
                                            @break
                                        @case('non_depose')
                                            <span class="badge bg-danger">Non Déposé</span>
                                            @break
                                        @case('en_attente')
                                            <span class="badge bg-warning">En Attente</span>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Validation:</td>
                                <td>
                                    @if($depotCompte->validation)
                                        <span class="badge bg-success">Validé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Aide -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aide à la Modification</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong><i class="fas fa-exclamation-triangle"></i> Attention</strong>
                        <hr>
                        <ul class="mb-0 small">
                            <li>La modification d'une commune ou d'une année peut avoir des impacts sur les données financières associées</li>
                            <li>Vérifiez que le nouveau receveur est bien associé à la commune</li>
                            <li>La date de dépôt ne peut pas être dans le futur</li>
                            <li>Un seul dépôt par commune, par année et par type est autorisé</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <strong><i class="fas fa-calendar-alt"></i> Dates limites usuelles</strong>
                        <hr>
                        <ul class="mb-0 small">
                            <li><strong>Budget Primitif :</strong> 31 mars</li>
                            <li><strong>Compte Administratif :</strong> 30 juin (N+1)</li>
                            <li><strong>Budget Supplémentaire :</strong> 30 septembre</li>
                            <li><strong>Décision Modificative :</strong> 31 décembre</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Historique rapide -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Modifications Récentes</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Création</h6>
                                <p class="timeline-text small text-muted">
                                    {{ $depotCompte->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                        @if($depotCompte->updated_at != $depotCompte->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Dernière modification</h6>
                                    <p class="timeline-text small text-muted">
                                        {{ $depotCompte->updated_at->format('d/m/Y H:i') }}
                                        <br><em>{{ $depotCompte->updated_at->diffForHumans() }}</em>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialiser Select2
    $('#commune_id').select2({
        placeholder: 'Rechercher une commune...',
        allowClear: true,
        width: '100%'
    });
    
    $('#receveur_id').select2({
        placeholder: 'Sélectionner un receveur...',
        allowClear: true,
        width: '100%'
    });

    // Filtrer les receveurs selon la commune sélectionnée
    function filterReceveurs() {
        const selectedOption = $('#commune_id').find('option:selected');
        const receveurIds = selectedOption.data('receveurs');
        const currentReceveurId = {{ $depotCompte->receveur_id ?? 'null' }};
        
        if (selectedOption.val() && receveurIds) {
            $('#receveur_id option').each(function() {
                const receveurId = parseInt($(this).val());
                if ($(this).val() === '' || receveurIds.includes(receveurId)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $('#receveur_id option').show();
        }
        
        // Conserver la sélection actuelle si elle est valide
        if (currentReceveurId && (receveurIds.includes(currentReceveurId) || !selectedOption.val())) {
            $('#receveur_id').val(currentReceveurId).trigger('change');
        } else if (!selectedOption.val()) {
            $('#receveur_id').val(currentReceveurId).trigger('change');
        } else {
            $('#receveur_id').val('').trigger('change');
        }
    }

    // Filtrer au chargement
    filterReceveurs();

    // Filtrer lors du changement de commune
    $('#commune_id').on('change', filterReceveurs);

    // Calculer automatiquement les jours de retard
    function calculerRetard() {
        const dateDepot = $('#date_depot').val();
        const dateLimite = $('#date_limite_depot').val();
        
        if (dateDepot && dateLimite) {
            const depot = new Date(dateDepot);
            const limite = new Date(dateLimite);
            
            if (depot > limite) {
                const diffTime = Math.abs(depot - limite);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                $('#jours_retard').val(diffDays);
            } else {
                $('#jours_retard').val(0);
            }
        }
    }
    
    $('#date_depot, #date_limite_depot').on('change', calculerRetard);
    
    // Définir des dates limites par défaut selon le type
    $('#type').on('change', function() {
        const type = $(this).val();
        const annee = $('#annee_exercice').val() || new Date().getFullYear();
        let dateLimite = '';
        
        switch(type) {
            case 'budget_primitif':
                dateLimite = annee + '-03-31'; // 31 mars
                break;
            case 'compte_administratif':
                dateLimite = (parseInt(annee) + 1) + '-06-30'; // 30 juin de l'année suivante
                break;
            case 'budget_supplementaire':
                dateLimite = annee + '-09-30'; // 30 septembre
                break;
            case 'decision_modificative':
                dateLimite = annee + '-12-31'; // 31 décembre
                break;
        }
        
        // Ne pas écraser la date limite existante sauf si demandé explicitement
        if (dateLimite && confirm('Voulez-vous mettre à jour la date limite selon le type sélectionné ?')) {
            $('#date_limite_depot').val(dateLimite);
            calculerRetard();
        }
    });

    // Recalculer la date limite si l'année change
    $('#annee_exercice').on('change', function() {
        if ($('#type').val()) {
            const type = $('#type').val();
            const annee = $(this).val();
            let dateLimite = '';
            
            switch(type) {
                case 'budget_primitif':
                    dateLimite = annee + '-03-31';
                    break;
                case 'compte_administratif':
                    dateLimite = (parseInt(annee) + 1) + '-06-30';
                    break;
                case 'budget_supplementaire':
                    dateLimite = annee + '-09-30';
                    break;
                case 'decision_modificative':
                    dateLimite = annee + '-12-31';
                    break;
            }
            
            if (dateLimite && confirm('Voulez-vous mettre à jour la date limite selon l\'année sélectionnée ?')) {
                $('#date_limite_depot').val(dateLimite);
                calculerRetard();
            }
        }
    });

    // Validation en temps réel
    $('form').on('submit', function(e) {
        let isValid = true;
        let firstInvalid = null;
        
        // Vérifier les champs requis
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
                if (!firstInvalid) firstInvalid = $(this);
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            if (firstInvalid) {
                $('html, body').animate({
                    scrollTop: firstInvalid.offset().top - 100
                }, 500);
                firstInvalid.focus();
            }
        }
    });

    // Confirmation des changements majeurs
    let originalValues = {
        commune_id: {{ $depotCompte->commune_id }},
        annee_exercice: {{ $depotCompte->annee_exercice }}
    };

    $('form').on('submit', function(e) {
        const newCommuneId = parseInt($('#commune_id').val());
        const newAnneeExercice = parseInt($('#annee_exercice').val());
        
        if (newCommuneId !== originalValues.commune_id || newAnneeExercice !== originalValues.annee_exercice) {
            if (!confirm('Vous modifiez des informations critiques (commune ou année d\'exercice). Êtes-vous sûr de vouloir continuer ?')) {
                e.preventDefault();
            }
        }
    });

    // Calculer le retard au chargement initial
    calculerRetard();
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -31px;
    top: 15px;
    width: 2px;
    height: calc(100% + 5px);
    background-color: #e3e6f0;
}

.timeline-title {
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.timeline-text {
    margin-bottom: 0;
}

.gap-2 {
    gap: 0.5rem;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
@endsection