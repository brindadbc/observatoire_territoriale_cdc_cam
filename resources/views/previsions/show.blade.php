{{-- resources/views/previsions/show.blade.php
@extends('layouts.app')

@section('title', 'Détails Prévision - ' . $prevision->commune->nom)

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('previsions.index') }}" class="btn btn-outline-secondary me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="h3 mb-1">{{ $prevision->commune->nom }}</h2>
                <div class="text-muted">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    {{ $prevision->commune->departement->nom }}, {{ $prevision->commune->departement->region->nom }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-calendar me-1"></i>
                    Exercice {{ $prevision->annee_exercice }}
                </div>
            </div>
        </div>
        <div class="btn-group">
            <a href="{{ route('previsions.edit', $prevision) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                <i class="fas fa-trash me-2"></i>Supprimer
            </button>
        </div>
    </div>

    <!-- Messages d'alerte -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Statistiques principales -->
        <div class="col-lg-8">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-flex mb-3">
                                <i class="fas fa-bullseye text-primary fa-lg"></i>
                            </div>
                            <h6 class="text-muted mb-1">Montant Prévu</h6>
                            <h4 class="mb-0 text-primary">{{ number_format($stats['montant_prevision'], 0, ',', ' ') }}</h4>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-flex mb-3">
                                <i class="fas fa-coins text-success fa-lg"></i>
                            </div>
                            <h6 class="text-muted mb-1">Montant Réalisé</h6>
                            <h4 class="mb-0 text-success">{{ number_format($stats['montant_realise'], 0, ',', ' ') }}</h4>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle d-inline-flex mb-3">
                                <i class="fas fa-percentage text-info fa-lg"></i>
                            </div>
                            <h6 class="text-muted mb-1">Taux Réalisation</h6>
                            <h4 class="mb-0 text-info">{{ number_format($stats['taux_realisation'], 1) }}%</h4>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-info" style="width: {{ min($stats['taux_realisation'], 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="bg-{{ $stats['ecart'] >= 0 ? 'success' : 'warning' }} bg-opacity-10 p-3 rounded-circle d-inline-flex mb-3">
                                <i class="fas fa-{{ $stats['ecart'] >= 0 ? 'arrow-up' : 'arrow-down' }} text-{{ $stats['ecart'] >= 0 ? 'success' : 'warning' }} fa-lg"></i>
                            </div>
                            <h6 class="text-muted mb-1">Écart</h6>
                            <h4 class="mb-0 text-{{ $stats['ecart'] >= 0 ? 'success' : 'warning' }}">
                                {{ $stats['ecart'] >= 0 ? '+' : '' }}{{ number_format($stats['ecart'], 0, ',', ' ') }}
                            </h4>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphique d'évolution -->
            @if($evolutionRealisations->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Évolution des Réalisations
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" height="100"></canvas>
                </div>
            </div>
            @endif

            <!-- Tableau des réalisations -->
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Détail des Réalisations
                        <span class="badge bg-primary ms-2">{{ $stats['nb_realisations'] }}</span>
                    </h5>
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Nouvelle Réalisation
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Cumul</th>
                                <th>% Réalisation</th>
                                <th>Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $cumul = 0; @endphp
                            @forelse($prevision->realisations->sortBy('date_realisation') as $realisation)
                                @php $cumul += $realisation->montant; @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($realisation->date_realisation)->format('d/m/Y') }}</td>
                                    <td class="fw-medium">{{ number_format($realisation->montant, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ number_format($cumul, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        @php $pourcentage = $prevision->montant > 0 ? ($cumul / $prevision->montant) * 100 : 0; @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-{{ $pourcentage >= 80 ? 'success' : ($pourcentage >= 50 ? 'warning' : 'info') }}" 
                                                     style="width: {{ min($pourcentage, 100) }}%"></div>
                                            </div>
                                            <span class="small">{{ number_format($pourcentage, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($pourcentage >= 90)
                                            <span class="badge bg-success">Excellent</span>
                                        @elseif($pourcentage >= 70)
                                            <span class="badge bg-primary">Bon</span>
                                        @elseif($pourcentage >= 50)
                                            <span class="badge bg-warning">Moyen</span>
                                        @else
                                            <span class="badge bg-info">En cours</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">Aucune réalisation enregistrée</p>
                                        <small class="text-muted">Commencez par ajouter une première réalisation</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Panneau latéral -->
        <div class="col-lg-4">
            <!-- Informations de la commune -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations de la Commune
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Commune :</dt>
                        <dd class="col-sm-8">{{ $prevision->commune->nom }}</dd>
                        
                        <dt class="col-sm-4">Code :</dt>
                        <dd class="col-sm-8">{{ $prevision->commune->code ?? 'N/A' }}</dd>
                        
                        <dt class="col-sm-4">Département :</dt>
                        <dd class="col-sm-8">{{ $prevision->commune->departement->nom }}</dd>
                        
                        <dt class="col-sm-4">Région :</dt>
                        <dd class="col-sm-8">{{ $prevision->commune->departement->region->nom }}</dd>
                        
                        <dt class="col-sm-4">Exercice :</dt>
                        <dd class="col-sm-8">{{ $prevision->annee_exercice }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Résumé des réalisations -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Résumé des Réalisations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ $stats['nb_realisations'] }}</h4>
                                <small class="text-muted">Réalisations</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-1">
                                @if($stats['derniere_realisation'])
                                    {{ \Carbon\Carbon::parse($stats['derniere_realisation'])->format('d/m') }}
                                @else
                                    --/--
                                @endif
                            </h4>
                            <small class="text-muted">Dernière réalisation</small>
                        </div>
                    </div>
                    
                    @if($stats['nb_realisations'] > 0)
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Progression</span>
                            <span>{{ number_format($stats['taux_realisation'], 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-gradient" 
                                 style="width: {{ min($stats['taux_realisation'], 100) }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Comparaison avec le département -->
            @if($comparaison->count() > 1)
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-balance-scale me-2"></i>Comparaison Départementale
                    </h6>
                </div>
                <div class="card-body">
                    <small class="text-muted mb-3 d-block">
                        Classement par taux de réalisation dans {{ $prevision->commune->departement->nom }}
                    </small>
                    
                    @foreach($comparaison->take(5) as $index => $commune)
                        <div class="d-flex align-items-center mb-2 {{ $commune['est_actuelle'] ? 'bg-light rounded p-2' : '' }}">
                            <div class="flex-shrink-0 me-2">
                                <span class="badge bg-{{ $index < 3 ? 'primary' : 'secondary' }} rounded-pill">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium small {{ $commune['est_actuelle'] ? 'text-primary' : '' }}">
                                    {{ $commune['commune'] }}
                                    @if($commune['est_actuelle'])
                                        <i class="fas fa-arrow-left text-primary ms-1"></i>
                                    @endif
                                </div>
                                <div class="small text-muted">
                                    {{ number_format($commune['taux_realisation'], 1) }}% 
                                    ({{ number_format($commune['montant_realise'], 0, ',', ' ') }} FCFA)
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($comparaison->count() > 5)
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                ... et {{ $comparaison->count() - 5 }} autres communes
                            </small>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette prévision pour <strong>{{ $prevision->commune->nom }}</strong> ?</p>
                @if($stats['nb_realisations'] > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Cette prévision a {{ $stats['nb_realisations'] }} réalisation(s) associée(s).
                        La suppression n'est pas possible.
                    </div>
                @else
                    <p class="text-danger small">
                        <i class="fas fa-exclamation-triangle me-1"></i>Cette action est irréversible.
                    </p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                @if($stats['nb_realisations'] == 0)
                    <form action="{{ route('previsions.destroy', $prevision) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function confirmDelete() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

@if($evolutionRealisations->count() > 0)
// Graphique d'évolution
const ctx = document.getElementById('evolutionChart').getContext('2d');
const evolutionData = @json($evolutionRealisations);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: evolutionData.map(item => new Date(item.date).toLocaleDateString('fr-FR')),
        datasets: [{
            label: 'Montant Cumulé',
            data: evolutionData.map(item => item.montant_cumule),
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: '% Réalisation',
            data: evolutionData.map(item => item.pourcentage_cumule),
            borderColor: '#198754',
            backgroundColor: 'rgba(25, 135, 84, 0.1)',
            tension: 0.4,
            yAxisID: 'y1',
            fill: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Montant (FCFA)'
                },
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('fr-FR').format(value);
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Pourcentage (%)'
                },
                grid: {
                    drawOnChartArea: false,
                },
                max: 100
            }
        }
    }
});
@endif
</script>
@endpush
@endsection --}}


@extends('layouts.app')

@section('title', 'Détails de la prévision')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        Prévision {{ $prevision->annee_exercice }} - {{ $prevision->commune->nom }}
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('previsions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <a href="{{ route('previsions.edit', $prevision) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('previsions.analyses.tendances') }}?commune_id={{ $prevision->commune_id }}" class="btn btn-primary btn-block">
                                        <i class="fas fa-chart-line"></i> Analyses
                                    </a>
                        {{-- <button type="button" class="btn btn-info" data-toggle="modal" data-target="#duplicateModal">
                            <i class="fas fa-copy"></i> Dupliquer
                        </button> --}}
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informations générales -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Informations Générales</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Année d'exercice:</strong></td>
                                            <td>{{ $prevision->annee_exercice }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Commune:</strong></td>
                                            <td>{{ $prevision->commune->nom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Département:</strong></td>
                                            <td>{{ $prevision->commune->departement->nom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Région:</strong></td>
                                            <td>{{ $prevision->commune->departement->region->nom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date création:</strong></td>
                                            <td>{{ $prevision->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dernière modification:</strong></td>
                                            <td>{{ $prevision->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Résumé Financier</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-primary"><i class="fas fa-target"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Prévu</span>
                                                    <span class="info-box-number">{{ number_format($stats['montant_prevision'], 0, ',', ' ') }}</span>
                                                    <span class="info-box-text">FCFA</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Réalisé</span>
                                                    <span class="info-box-number">{{ number_format($stats['montant_realise'], 0, ',', ' ') }}</span>
                                                    <span class="info-box-text">FCFA</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning"><i class="fas fa-hourglass"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Restant</span>
                                                    <span class="info-box-number">{{ number_format($stats['montant_restant'], 0, ',', ' ') }}</span>
                                                    <span class="info-box-text">FCFA</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Indicateurs de performance -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Taux de Réalisation</h5>
                                </div>
                                <div class="card-body text-center">
                                    <div class="progress mb-3" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $stats['taux_realisation'] >= 90 ? 'success' : ($stats['taux_realisation'] >= 75 ? 'info' : ($stats['taux_realisation'] >= 50 ? 'warning' : 'danger')) }}" 
                                             role="progressbar" style="width: {{ $stats['taux_realisation'] }}%">
                                            {{ number_format($stats['taux_realisation'], 1) }}%
                                        </div>
                                    </div>
                                    <h4 class="text-{{ $stats['taux_realisation'] >= 90 ? 'success' : ($stats['taux_realisation'] >= 75 ? 'info' : ($stats['taux_realisation'] >= 50 ? 'warning' : 'danger')) }}">
                                        {{ $stats['evaluation'] }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Statistiques</h5>
                                </div>
                                {{-- <div class="card-body">
                                    <p><strong>Nombre de réalisations:</strong> {{ $stats['nb_realisations'] }}</p>
                                    <p><strong>Dernière réalisation:</strong> 
                                        {{ $stats['derniere_realisation'] ? $stats['derniere_realisation']->format('d/m/Y') : 'Aucune' }}
                                    </p>
                                    @if($tauxRealisation)
                                        <p><strong>Date calcul taux:</strong> {{ $tauxRealisation->date_calcul->format('d/m/Y H:i') }}</p>
                                        <p><strong>Écart:</strong> {{ number_format($tauxRealisation->ecart, 0, ',', ' ') }} FCFA</p>
                                    @endif
                                </div> --}}<div class="card-body">
    <p><strong>Nombre de réalisations:</strong> {{ $stats['nb_realisations'] }}</p>
    {{-- <p><strong>Dernière réalisation:</strong> 
        {{ $stats['derniere_realisation'] ? $stats['derniere_realisation']->format('d/m/Y') : 'Aucune' }}
    </p> --}}
    {{-- <p><strong>Dernière réalisation:</strong> 
    {{ $stats['derniere_realisation'] ? $stats['derniere_realisation']->format('d/m/Y') : 'Aucune' }}
</p>
    @if($tauxRealisationModel)
        <p><strong>Date calcul taux:</strong> {{ $tauxRealisationModel->date_calcul->format('d/m/Y H:i') }}</p>
        <p><strong>Écart:</strong> {{ number_format($tauxRealisationModel->ecart, 0, ',', ' ') }} FCFA</p>
    @endif --}}
    <p><strong>Dernière réalisation:</strong> 
    @if($stats['derniere_realisation'])
        {{ $stats['derniere_realisation']->format('d/m/Y') }}
    @else
        Aucune
    @endif
</p>

@if($tauxRealisationModel && $tauxRealisationModel->date_calcul)
    <p><strong>Date calcul taux:</strong> {{ $tauxRealisationModel->date_calcul->format('d/m/Y H:i') }}</p>
    <p><strong>Écart:</strong> {{ number_format($tauxRealisationModel->ecart, 0, ',', ' ') }} FCFA</p>
@endif
</div>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Actions Rapides</h5>
                                </div>
                                <div class="card-body">
                                    {{-- <a href="{{ route('realisations.create', ['prevision_id' => $prevision->id]) }}" class="btn btn-success btn-block">
                                        <i class="fas fa-plus"></i> Ajouter une réalisation
                                    </a>
                                    <a href="{{ route('realisations.index', ['prevision_id' => $prevision->id]) }}" class="btn btn-info btn-block">
                                        <i class="fas fa-list"></i> Voir les réalisations
                                    </a> --}}
                                    <a href="{{ route('previsions.analyses.tendances') }}?commune_id={{ $prevision->commune_id }}" class="btn btn-primary btn-block">
                                        <i class="fas fa-chart-line"></i> Analyses
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Réalisations récentes -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Réalisations Récentes</h5>
                        </div>
                        <div class="card-body">
                            @if($prevision->realisations->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Montant</th>
                                                <th>Description</th>
                                                {{-- <th>Actions</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($prevision->realisations->take(5) as $realisation)
                                            <tr>
                                                <td>{{ $realisation->date_realisation->format('d/m/Y') }}</td>
                                                <td>{{ number_format($realisation->montant, 0, ',', ' ') }} FCFA</td>
                                                <td>{{ Str::limit($realisation->description, 50) }}</td>
                                                {{-- <td>
                                                    <a href="{{ route('realisations.show', $realisation) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td> --}}
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($prevision->realisations->count() > 5)
                                    <div class="text-center">
                                        <a href="{{ route('realisations.index', ['prevision_id' => $prevision->id]) }}" class="btn btn-primary">
                                            Voir toutes les réalisations ({{ $prevision->realisations->count() }})
                                        </a>
                                    </div>
                                @endif
                            @else
                                <p class="text-muted text-center">Aucune réalisation enregistrée pour cette prévision.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Évolution annuelle -->
                    @if($evolutionAnnuelle->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Évolution Annuelle des Réalisations</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="evolutionChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    @endif

                    <!-- Comparaison avec le département -->
                    @if($comparaison->count() > 1)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Comparaison Départementale ({{ $prevision->annee_exercice }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Rang</th>
                                            <th>Commune</th>
                                            <th>Montant Prévu</th>
                                            <th>Montant Réalisé</th>
                                            <th>Taux Réalisation</th>
                                            <th>Évaluation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($comparaison->take(10) as $index => $commune)
                                        <tr class="{{ $commune['commune'] == $prevision->commune->nom ? 'table-warning' : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $commune['commune'] }}
                                                @if($commune['commune'] == $prevision->commune->nom)
                                                    <span class="badge badge-warning">Actuelle</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($commune['montant_prevu'], 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($commune['montant_realise'], 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($commune['taux_realisation'], 1) }}%</td>
                                            <td>
                                                <span class="badge badge-{{ $commune['evaluation'] == 'Excellent' ? 'success' : ($commune['evaluation'] == 'Bon' ? 'info' : ($commune['evaluation'] == 'Moyen' ? 'warning' : 'danger')) }}">
                                                    {{ $commune['evaluation'] }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Historique des prévisions -->
                    @if($historiquePrevisions->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Historique des Prévisions - {{ $prevision->commune->nom }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Année</th>
                                            <th>Montant Prévu</th>
                                            <th>Montant Réalisé</th>
                                            <th>Taux Réalisation</th>
                                            <th>Évaluation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($historiquePrevisions as $hist)
                                        <tr>
                                            <td>{{ $hist['annee'] }}</td>
                                            <td>{{ number_format($hist['montant_prevu'], 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($hist['montant_realise'], 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($hist['taux_realisation'], 1) }}%</td>
                                            <td>
                                                <span class="badge badge-{{ $hist['evaluation'] == 'Excellent' ? 'success' : ($hist['evaluation'] == 'Bon' ? 'info' : ($hist['evaluation'] == 'Moyen' ? 'warning' : 'danger')) }}">
                                                    {{ $hist['evaluation'] }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de duplication -->
<div class="modal fade" id="duplicateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dupliquer la prévision</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('previsions.duplicate', $prevision) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nouvelle_annee">Nouvelle année</label>
                        <input type="number" class="form-control" id="nouvelle_annee" name="nouvelle_annee" 
                               min="2000" max="{{ date('Y') + 10 }}" value="{{ $prevision->annee_exercice + 1 }}" required>
                    </div>
                    <div class="form-group">
                        <label for="ajustement_pourcentage">Ajustement (%)</label>
                        <input type="number" class="form-control" id="ajustement_pourcentage" name="ajustement_pourcentage" 
                               step="0.1" min="-100" max="1000" placeholder="0">
                        <small class="form-text text-muted">
                            Pourcentage d'ajustement du montant (ex: 5 pour +5%, -10 pour -10%)
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Dupliquer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique d'évolution annuelle
    @if($evolutionAnnuelle->count() > 0)
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($evolutionAnnuelle->pluck('annee')),
            datasets: [{
                label: 'Montant Réalisé',
                data: @json($evolutionAnnuelle->pluck('montant')),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Nombre de Réalisations',
                data: @json($evolutionAnnuelle->pluck('nb_realisations')),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left'
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
    @endif
</script>
@endsection