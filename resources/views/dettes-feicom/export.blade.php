@extends('layouts.app')

@section('title', 'Export Dettes FEICOM')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Export des Dettes FEICOM</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dettes-feicom.index') }}">Dettes FEICOM</a></li>
                        <li class="breadcrumb-item active">Export</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Paramètres d'Export</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dettes-feicom.export') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="format" class="form-label">Format d'export</label>
                                    <select name="format" id="format" class="form-select" required>
                                        <option value="">Sélectionner le format</option>
                                        <option value="excel" {{ request('format') == 'excel' ? 'selected' : '' }}>
                                            Excel (.xlsx)
                                        </option>
                                        <option value="pdf" {{ request('format') == 'pdf' ? 'selected' : '' }}>
                                            PDF
                                        </option>
                                        <option value="csv" {{ request('format') == 'csv' ? 'selected' : '' }}>
                                            CSV
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="annee" class="form-label">Année</label>
                                    <select name="annee" id="annee" class="form-select">
                                        @for($i = date('Y'); $i >= 2020; $i--)
                                            <option value="{{ $i }}" {{ (request('annee', date('Y')) == $i) ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type d'export</label>
                                    <select name="type" id="type" class="form-select">
                                        <option value="global" {{ request('type') == 'global' ? 'selected' : '' }}>
                                            Export global
                                        </option>
                                        <option value="regions" {{ request('type') == 'regions' ? 'selected' : '' }}>
                                            Par région
                                        </option>
                                        <option value="departements" {{ request('type') == 'departements' ? 'selected' : '' }}>
                                            Par département
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="region_id" class="form-label">Région (optionnel)</label>
                                    <select name="region_id" id="region_id" class="form-select">
                                        <option value="">Toutes les régions</option>
                                        @foreach(\App\Models\Region::orderBy('nom')->get() as $region)
                                            <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                                {{ $region->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_debut" class="form-label">Date de début (optionnel)</label>
                                    <input type="date" name="date_debut" id="date_debut" class="form-control" 
                                           value="{{ request('date_debut') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_fin" class="form-label">Date de fin (optionnel)</label>
                                    <input type="date" name="date_fin" id="date_fin" class="form-control" 
                                           value="{{ request('date_fin') }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_stats" id="include_stats" 
                                       {{ request('include_stats') ? 'checked' : '' }}>
                                <label class="form-check-label" for="include_stats">
                                    Inclure les statistiques
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_charts" id="include_charts" 
                                       {{ request('include_charts') ? 'checked' : '' }}>
                                <label class="form-check-label" for="include_charts">
                                    Inclure les graphiques (PDF uniquement)
                                </label>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('dettes-feicom.index') }}" class="btn btn-secondary me-2">
                                <i class="fe-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fe-download"></i> Télécharger
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Aperçu des formats -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Aperçu des formats</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="avatar-lg bg-soft-success text-success rounded-circle mx-auto mb-3">
                                    <i class="fe-file-text avatar-title font-22"></i>
                                </div>
                                <h5>Excel</h5>
                                <p class="text-muted">Format tableur avec formules et graphiques</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="avatar-lg bg-soft-danger text-danger rounded-circle mx-auto mb-3">
                                    <i class="fe-file avatar-title font-22"></i>
                                </div>
                                <h5>PDF</h5>
                                <p class="text-muted">Document formaté prêt à imprimer</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="avatar-lg bg-soft-info text-info rounded-circle mx-auto mb-3">
                                    <i class="fe-database avatar-title font-22"></i>
                                </div>
                                <h5>CSV</h5>
                                <p class="text-muted">Données brutes compatibles avec tous les systèmes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des exports récents -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Exports récents</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Format</th>
                                    <th>Type</th>
                                    <th>Utilisateur</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ now()->format('d/m/Y H:i') }}</td>
                                    <td><span class="badge bg-success">Excel</span></td>
                                    <td>Global</td>
                                    <td>{{ auth()->user()->name ?? 'Utilisateur' }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-outline-primary" disabled>
                                            <i class="fe-download"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        Aucun export récent
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
    // Gestion conditionnelle des options
    $('#format').on('change', function() {
        const format = $(this).val();
        const chartsCheckbox = $('#include_charts');
        
        if (format === 'pdf') {
            chartsCheckbox.prop('disabled', false);
            chartsCheckbox.parent().removeClass('text-muted');
        } else {
            chartsCheckbox.prop('disabled', true);
            chartsCheckbox.prop('checked', false);
            chartsCheckbox.parent().addClass('text-muted');
        }
    });

    // Validation des dates
    $('#date_debut, #date_fin').on('change', function() {
        const dateDebut = $('#date_debut').val();
        const dateFin = $('#date_fin').val();
        
        if (dateDebut && dateFin && dateDebut > dateFin) {
            alert('La date de début ne peut pas être supérieure à la date de fin');
            $(this).val('');
        }
    });

    // Initialisation
    $('#format').trigger('change');
</script>
@endpush
@endsection