@extends('layouts.app')

@section('title', 'Rapport des Dépôts de Comptes')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Rapport des Dépôts de Comptes</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('depot-comptes.index') }}">Dépôts de Comptes</a></li>
                    <li class="breadcrumb-item active">Rapport {{ $annee }}</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('depot-comptes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <button type="button" class="btn btn-info" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
            <button type="button" class="btn btn-success" id="exportExcel">
                <i class="fas fa-file-excel"></i> Exporter Excel
            </button>
        </div>
    </div>

    {{-- <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres de Rapport</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('depot-comptes.rapport') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="annee" class="form-label">Année</label>
                        <select class="form-select" id="annee" name="annee">
                            @for($year = date('Y') + 1; $year >= 2000; $year--)
                                <option value="{{ $year }}" {{ request('annee', $annee) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="region_id" class="form-label">Région</label>
                        <select class="form-select" id="region_id" name="region_id">
                            <option value="">Toutes les régions</option>
                            <!-- Ajoutez les régions depuis le contrôleur -->
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="departement_id" class="form-label">Département</label>
                        <select class="form-select" id="departement_id" name="departement_id">
                            <option value="">Tous les départements</option>
                            <!-- Ajoutez les départements depuis le contrôleur -->
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div> --}}


    <!-- Filtres -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtres de Rapport</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('depot-comptes.rapport') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="annee" class="form-label">Année</label>
                    <select class="form-select" id="annee" name="annee">
                        @for($year = date('Y') + 1; $year >= 2000; $year--)
                            <option value="{{ $year }}" {{ request('annee', $annee) == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="region_id" class="form-label">Région</label>
                    <select class="form-select" id="region_id" name="region_id">
                        <option value="">Toutes les régions</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" 
                                {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                {{ $region->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="departement_id" class="form-label">Département</label>
                    <select class="form-select" id="departement_id" name="departement_id">
                        <option value="">Tous les départements</option>
                        @foreach($departements as $departement)
                            <option value="{{ $departement->id }}" 
                                data-region="{{ $departement->region_id }}"
                                {{ request('departement_id') == $departement->id ? 'selected' : '' }}>
                                {{ $departement->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des Dépôts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['total_depots'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Dépôts Validés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['depots_valides'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Dépôts en Attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['depots_invalides'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Taux de Validation
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ $statistiques['taux_validation'] }}%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                             style="width: {{ $statistiques['taux_validation'] }}%"
                                             aria-valuenow="{{ $statistiques['taux_validation'] }}" 
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Évolution des Dépôts par Mois</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition par Statut</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Communes sans dépôt -->
    @if($statistiques['communes_sans_depot']->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger">
                Communes sans Dépôt ({{ $statistiques['communes_sans_depot']->count() }})
            </h6>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Les communes suivantes n'ont pas encore effectué leur dépôt de compte pour l'année {{ $annee }} :
            </div>
            <div class="row">
                @foreach($statistiques['communes_sans_depot'] as $commune)
                    <div class="col-md-4 mb-2">
                        <span class="badge bg-light text-dark">
                            {{ $commune->nom }} ({{ $commune->departement->nom }})
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Liste des dépôts -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Dépôts - {{ $annee }}</h6>
        </div>
        <div class="card-body">
            @if($depots->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover" id="dataTable">
                        <thead>
                            <tr>
                                <th>Commune</th>
                                <th>Département</th>
                                <th>Région</th>
                                <th>Receveur</th>
                                <th>Date Dépôt</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($depots as $depot)
                                <tr>
                                    <td>
                                        <strong>{{ $depot->commune->nom }}</strong><br>
                                        <small class="text-muted">{{ $depot->commune->code }}</small>
                                    </td>
                                    <td>{{ $depot->commune->departement->nom }}</td>
                                    <td>{{ $depot->commune->departement->region->nom }}</td>
                                    <td>{{ $depot->receveur->nom }}</td>
                                    <td>{{ \Carbon\Carbon::parse($depot->date_depot)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($depot->validation)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Validé
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> En attente
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('depot-comptes.show', $depot) }}" 
                                               class="btn btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('depot-comptes.edit', $depot) }}" 
                                               class="btn btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun dépôt trouvé</h5>
                    <p class="text-muted">Il n'y a aucun dépôt de compte pour les critères sélectionnés.</p>
                    <a href="{{ route('depot-comptes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un dépôt
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .card.border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .card.border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .card.border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .card.border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .progress-sm {
        height: 0.5rem;
    }
    @media print {
        .btn-group, .card-header .btn, #exportExcel {
            display: none !important;
        }
        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour les graphiques
    const monthlyData = @json($statistiques['depots_par_mois']);
    const statusData = {
        valides: {{ $statistiques['depots_valides'] }},
        invalides: {{ $statistiques['depots_invalides'] }}
    };

    // Graphique des dépôts par mois
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Nombre de dépôts',
                data: [
                    monthlyData['01'] || 0, monthlyData['02'] || 0, monthlyData['03'] || 0,
                    monthlyData['04'] || 0, monthlyData['05'] || 0, monthlyData['06'] || 0,
                    monthlyData['07'] || 0, monthlyData['08'] || 0, monthlyData['09'] || 0,
                    monthlyData['10'] || 0, monthlyData['11'] || 0, monthlyData['12'] || 0
                ],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Graphique de répartition par statut
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Validés', 'En attente'],
            datasets: [{
                data: [statusData.valides, statusData.invalides],
                backgroundColor: ['#1cc88a', '#f6c23e'],
                hoverBackgroundColor: ['#17a673', '#dda20a'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Export Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        // Créer un tableau pour l'export
        const table = document.getElementById('dataTable');
        let csv = '';
        
        // En-têtes
        const headers = table.querySelectorAll('thead th');
        const headerRow = Array.from(headers).slice(0, -1).map(th => th.textContent.trim()).join(';');
        csv += headerRow + '\n';
        
        // Données
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const rowData = Array.from(cells).slice(0, -1).map(cell => {
                return cell.textContent.trim().replace(/\n/g, ' ').replace(/;/g, ',');
            }).join(';');
            csv += rowData + '\n';
        });
        
        // Télécharger le fichier
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'rapport_depots_{{ $annee }}.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // DataTable
    if (document.getElementById('dataTable')) {
        $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
            },
            pageLength: 25,
            order: [[4, 'desc']], // Trier par date de dépôt desc
            columnDefs: [
                { orderable: false, targets: -1 } // Désactiver le tri sur la colonne Actions
            ]
        });
    }
});


// Gestion de la dépendance région -> département
document.getElementById('region_id').addEventListener('change', function() {
    const regionId = this.value;
    const departementSelect = document.getElementById('departement_id');
    const departementOptions = departementSelect.querySelectorAll('option[data-region]');
    
    // Réinitialiser la sélection
    departementSelect.value = '';
    
    // Masquer/Afficher les départements selon la région sélectionnée
    departementOptions.forEach(option => {
        if (regionId === '' || option.dataset.region === regionId) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    });
    
    // Si une région est sélectionnée et qu'il n'y a qu'un seul département visible, le sélectionner automatiquement
    if (regionId !== '') {
        const visibleOptions = Array.from(departementOptions).filter(option => 
            option.dataset.region === regionId
        );
        if (visibleOptions.length === 1) {
            departementSelect.value = visibleOptions[0].value;
        }
    }
});

// Initialiser le filtre au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('region_id');
    if (regionSelect.value) {
        regionSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection