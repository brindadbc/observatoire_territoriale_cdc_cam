@extends('layouts.app')

@section('title', 'Nouveau Dépôt de Compte')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Nouveau Dépôt de Compte</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('depot-comptes.index') }}">Dépôts de Comptes</a></li>
                    <li class="breadcrumb-item active">Nouveau</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('depot-comptes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du Dépôt</h6>
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

                    <form action="{{ route('depot-comptes.store') }}" method="POST">
                        @csrf
                        
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
                                                                {{ old('commune_id') == $commune->id ? 'selected' : '' }}
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
                                                {{ old('receveur_id') == $receveur->id ? 'selected' : '' }}>
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
                                       value="{{ old('date_depot', date('Y-m-d')) }}"
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
                                                {{ old('annee_exercice', date('Y')) == $year ? 'selected' : '' }}>
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
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="validation" 
                                       name="validation" 
                                       value="1"
                                       {{ old('validation') ? 'checked' : '' }}>
                                <label class="form-check-label" for="validation">
                                    Marquer comme validé
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Cochez cette case si le dépôt de compte a été validé lors de l'enregistrement.
                            </small>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('depot-comptes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aide</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle"></i> Information</strong>
                        <hr>
                        <ul class="mb-0 small">
                            <li>La date de dépôt ne peut pas être dans le futur</li>
                            <li>Un seul dépôt par commune et par année d'exercice est autorisé</li>
                            <li>Le receveur doit être associé à la commune sélectionnée</li>
                            <li>L'année d'exercice peut être l'année courante ou suivante</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques Rapides</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h4 mb-1 text-primary">{{ $communes->count() }}</div>
                                <div class="small text-muted">Communes</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-1 text-success">{{ $receveurs->count() }}</div>
                            <div class="small text-muted">Receveurs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialiser Select2 pour une meilleure UX
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
    $('#commune_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const receveurIds = selectedOption.data('receveurs');
        
        if (selectedOption.val() && receveurIds) {
            // Filtrer les receveurs
            $('#receveur_id option').each(function() {
                const receveurId = parseInt($(this).val());
                if ($(this).val() === '' || receveurIds.includes(receveurId)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            // Afficher tous les receveurs
            $('#receveur_id option').show();
        }
        
        // Réinitialiser la sélection du receveur
        $('#receveur_id').val('').trigger('change');
    });

    // Validation en temps réel
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Vérifier les champs requis
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
@endsection