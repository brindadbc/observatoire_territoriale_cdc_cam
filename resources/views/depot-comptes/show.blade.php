@extends('layouts.app')

@section('title', 'Détails du Dépôt de Compte')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Détails du Dépôt de Compte</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('depot-comptes.index') }}">Dépôts de Comptes</a></li>
                    <li class="breadcrumb-item active">Détails</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('depot-comptes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="{{ route('depot-comptes.edit', $depotCompte) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <!-- Informations du dépôt -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du Dépôt</h6>
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
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Commune:</td>
                                    <td>{{ $depotCompte->commune->nom }} ({{ $depotCompte->commune->code }})</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Département:</td>
                                    <td>{{ $depotCompte->commune->departement->nom }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Région:</td>
                                    <td>{{ $depotCompte->commune->departement->region->nom }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Receveur:</td>
                                    <td>{{ $depotCompte->receveur->nom }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Date de Dépôt:</td>
                                    <td>{{ \Carbon\Carbon::parse($depotCompte->date_depot)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Année d'Exercice:</td>
                                    <td>{{ $depotCompte->annee_exercice }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Créé le:</td>
                                    <td>{{ $depotCompte->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Dernière modification:</td>
                                    <td>{{ $depotCompte->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Données financières -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Données Financières - {{ $depotCompte->annee_exercice }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small">Prévision</div>
                                            <div class="h5 mb-0">{{ number_format($donneesFinancieres['prevision'], 0, ',', ' ') }} FCFA</div>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chart-line fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small">Réalisation</div>
                                            <div class="h5 mb-0">{{ number_format($donneesFinancieres['realisation_total'], 0, ',', ' ') }} FCFA</div>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chart-bar fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small">Taux de Réalisation</div>
                                            <div class="h5 mb-0">{{ number_format($donneesFinancieres['taux_realisation'], 2) }}%</div>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-percentage fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small">Évaluation</div>
                                            <div class="h6 mb-0">{{ $donneesFinancieres['evaluation'] }}</div>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-star fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphique de performance -->
                    <div class="mt-4">
                        <canvas id="performanceChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Historique des dépôts -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Historique des Dépôts de {{ $depotCompte->commune->nom }}</h6>
                </div>
                <div class="card-body">
                    @if($historiqueDepots->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Année</th>
                                        <th>Date de Dépôt</th>
                                        <th>Receveur</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historiqueDepots as $depot)
                                        <tr class="{{ $depot->id == $depotCompte->id ? 'table-active' : '' }}">
                                            <td>{{ $depot->annee_exercice }}</td>
                                            <td>{{ \Carbon\Carbon::parse($depot->date_depot)->format('d/m/Y') }}</td>
                                            <td>{{ $depot->receveur->nom }}</td>
                                            <td>
                                                @if($depot->validation)
                                                    <span class="badge bg-success">Validé</span>
                                                @else
                                                    <span class="badge bg-warning">En attente</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($depot->id != $depotCompte->id)
                                                    <a href="{{ route('depot-comptes.show', $depot) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span class="badge bg-info">Actuel</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun historique de dépôt disponible.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Actions rapides -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions Rapides</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$depotCompte->validation)
                            <form action="{{ route('depot-comptes.bulk-validation') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="depot_ids[]" value="{{ $depotCompte->id }}">
                                <input type="hidden" name="action" value="validate">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-check"></i> Valider ce dépôt
                                </button>
                            </form>
                        @else
                            <form action="{{ route('depot-comptes.bulk-validation') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="depot_ids[]" value="{{ $depotCompte->id }}">
                                <input type="hidden" name="action" value="invalidate">
                                <button type="submit" class="btn btn-warning btn-sm w-100">
                                    <i class="fas fa-times"></i> Invalider ce dépôt
                                </button>
                            </form>
                        @endif
                        
                        <button type="button" class="btn btn-info btn-sm" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                        
                        <a href="{{ route('depot-comptes.rapport', ['commune_id' => $depotCompte->commune_id]) }}" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-chart-bar"></i> Rapport de la commune
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informations complémentaires -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations Complémentaires</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-3">
                            <strong>Délai de dépôt:</strong><br>
                            @php
                                $delai = \Carbon\Carbon::parse($depotCompte->date_depot)->diffForHumans();
                                $isLate = \Carbon\Carbon::parse($depotCompte->date_depot)->year > $depotCompte->annee_exercice;
                            @endphp
                            <span class="{{ $isLate ? 'text-danger' : 'text-success' }}">
                                {{ $delai }}
                                @if($isLate)
                                    <i class="fas fa-exclamation-triangle"></i>
                                @endif
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Nombre de dépôts de cette commune:</strong><br>
                            {{ $historiqueDepots->count() }} dépôt(s)
                        </div>
                        
                        <div class="mb-3">
                            <strong>Taux de validation de la commune:</strong><br>
                            @php
                                $validated = $historiqueDepots->where('validation', true)->count();
                                $total = $historiqueDepots->count();
                                $rate = $total > 0 ? ($validated / $total) * 100 : 0;
                            @endphp
                            {{ number_format($rate, 1) }}% ({{ $validated }}/{{ $total }})
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce dépôt de compte ?</p>
                <div class="alert alert-warning">
                    <strong>Attention :</strong> Cette action est irréversible.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('depot-comptes.destroy', $depotCompte) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique de performance
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const performanceChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Réalisé', 'Non réalisé'],
            datasets: [{
                data: [
                    {{ $donneesFinancieres['taux_realisation'] }},
                    {{ 100 - $donneesFinancieres['taux_realisation'] }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#e3e6f0'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection