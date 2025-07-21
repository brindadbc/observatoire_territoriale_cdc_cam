@extends('layouts.app')

@section('title', 'Nouvelle Dette Fiscale')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 text-gray-800">Nouvelle Dette Fiscale</h1>
            <p class="text-muted">Enregistrer une nouvelle dette fiscale pour une commune</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('dettes-fiscale.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Informations de la Dette</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dettes-fiscale.store') }}">
                        @csrf
                        
                        <div class="form-group">
                            <label for="commune_id">Commune *</label>
                            <select class="form-control @error('commune_id') is-invalid @enderror" 
                                    id="commune_id" name="commune_id" required>
                                <option value="">Sélectionnez une commune</option>
                                @foreach($communes->groupBy('departement.region.nom') as $regionNom => $communesRegion)
                                    <optgroup label="{{ $regionNom }}">
                                        @foreach($communesRegion->groupBy('departement.nom') as $departementNom => $communesDept)
                                            @foreach($communesDept as $commune)
                                                <option value="{{ $commune->id }}" 
                                                        {{ old('commune_id') == $commune->id ? 'selected' : '' }}>
                                                    {{ $commune->nom }} - {{ $departementNom }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('commune_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Recherchez et sélectionnez la commune concernée par cette dette fiscale
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="montant">Montant de la Dette (FCFA) *</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="999999999999999999.99"
                                       class="form-control @error('montant') is-invalid @enderror" 
                                       id="montant" name="montant" value="{{ old('montant') }}" 
                                       placeholder="0.00" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Montant de la dette fiscale (maximum: 999,999,999,999,999,999.99 FCFA)
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="date_evaluation">Date d'Évaluation *</label>
                            <input type="date" class="form-control @error('date_evaluation') is-invalid @enderror" 
                                   id="date_evaluation" name="date_evaluation" 
                                   value="{{ old('date_evaluation', date('Y-m-d')) }}" 
                                   max="{{ date('Y-m-d') }}" required>
                            @error('date_evaluation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Date à laquelle cette dette a été évaluée (ne peut pas être dans le futur)
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirmation" required>
                                <label class="custom-control-label" for="confirmation">
                                    Je confirme que les informations saisies sont exactes
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer la Dette
                            </button>
                            <a href="{{ route('dettes-fiscale.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Aide et conseils -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle"></i> Aide
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">Conseils de saisie :</h6>
                    <ul class="small">
                        <li>Vérifiez que la commune sélectionnée est correcte</li>
                        <li>Le montant doit être saisi en FCFA</li>
                        <li>La date d'évaluation ne peut pas être dans le futur</li>
                        <li>Tous les champs marqués d'un * sont obligatoires</li>
                    </ul>
                    
                    <hr>
                    
                    <h6 class="font-weight-bold">Types de dettes fiscales :</h6>
                    <ul class="small">
                        <li>Impôts locaux</li>
                        <li>Taxes communales</li>
                        <li>Amendes fiscales</li>
                        <li>Autres dettes envers l'État</li>
                    </ul>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-line"></i> Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="small text-muted mb-2">Nombre de communes avec dettes</div>
                        <div class="h4 font-weight-bold text-primary" id="stats-communes">-</div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="small text-muted mb-2">Dette fiscale moyenne</div>
                        <div class="h6 font-weight-bold text-success" id="stats-moyenne">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Formatage du montant en temps réel
    $('#montant').on('input', function() {
        let value = $(this).val();
        if (value) {
            let formatted = new Intl.NumberFormat('fr-FR').format(value);
            $(this).next('.input-group-append').find('.input-group-text').text(formatted + ' FCFA');
        } else {
            $(this).next('.input-group-append').find('.input-group-text').text('FCFA');
        }
    });

    // Sélection commune avec recherche
    $('#commune_id').select2({
        placeholder: "Rechercher une commune...",
        allowClear: true,
        width: '100%'
    });

    // Chargement des statistiques
    loadStats();

    function loadStats() {
        $.get('/api/dettes/fiscale/stats', function(data) {
            $('#stats-communes').text(data.nb_communes || 0);
            $('#stats-moyenne').text(formatMoney(data.moyenne || 0) + ' FCFA');
        }).fail(function() {
            $('#stats-communes').text('N/A');
            $('#stats-moyenne').text('N/A');
        });
    }

    function formatMoney(amount) {
        return new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    // Validation en temps réel
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Vérifier la commune
        if (!$('#commune_id').val()) {
            isValid = false;
            $('#commune_id').addClass('is-invalid');
        } else {
            $('#commune_id').removeClass('is-invalid');
        }
        
        // Vérifier le montant
        let montant = parseFloat($('#montant').val());
        if (!montant || montant <= 0 || montant > 999999999.99) {
            isValid = false;
            $('#montant').addClass('is-invalid');
        } else {
            $('#montant').removeClass('is-invalid');
        }
        
        // Vérifier la date
        let dateEval = new Date($('#date_evaluation').val());
        let today = new Date();
        if (!$('#date_evaluation').val() || dateEval > today) {
            isValid = false;
            $('#date_evaluation').addClass('is-invalid');
        } else {
            $('#date_evaluation').removeClass('is-invalid');
        }
        
        // Vérifier la confirmation
        if (!$('#confirmation').is(':checked')) {
            isValid = false;
            alert('Veuillez confirmer que les informations sont exactes.');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single {
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    border: 1px solid #d1d3e2;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
@endsection