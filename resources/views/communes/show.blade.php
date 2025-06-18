{{-- @extends('layouts.app')

@section('title', 'Commune ' . $commune->nom . ' - Observatoire des Collectivités')
@section('page-title', 'Commune de ' . $commune->nom)

@section('content')
<div class="commune-dashboard">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('dashboard.index') }}">Tableau de bord</a>
        <span>/</span>
        <a href="{{ route('regions.show', $commune->departement->region->id) }}">{{ $commune->departement->region->nom }}</a>
        <span>/</span>
        <a href="{{ route('departements.show', $commune->departement->id) }}">{{ $commune->departement->nom }}</a>
        <span>/</span>
        <span>{{ $commune->nom }}</span>
    </div>

    <!-- Info Générale -->
    <div class="commune-header">
        <div class="commune-info">
            <h2>{{ $commune->nom }}</h2>
            <div class="commune-meta">
                <span class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    {{ $commune->departement->nom }}, {{ $commune->departement->region->nom }}
                </span>
                <span class="meta-item">
                    <i class="fas fa-code"></i>
                    Code: {{ $commune->code }}
                </span>
                <span class="meta-item">
                    <i class="fas fa-phone"></i>
                    {{ $commune->telephone ?? 'Non renseigné' }}
                </span>
            </div>
        </div>
        <div class="commune-actions">
            <button class="btn btn-export">
                <i class="fas fa-file-pdf"></i>
                Générer Rapport
            </button>
            <button class="btn btn-edit">
                <i class="fas fa-edit"></i>
                Modifier
            </button>
        </div>
    </div>

    <!-- Responsables -->
    <div class="responsables-section">
        <h3>Responsables</h3>
        <div class="responsables-grid">
            <div class="responsable-card">
                <div class="responsable-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="responsable-info">
                    <h4>Receveur Municipal</h4>
                    <p>{{ $commune->receveurs->first()?->nom ?? 'Non assigné' }}</p>
                </div>
            </div>
            <div class="responsable-card">
                <div class="responsable-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div class="responsable-info">
                    <h4>Ordonnateur</h4>
                    <p>{{ $commune->ordonnateurs->first()?->nom ?? 'Non assigné' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Données Financières -->
    <div class="finances-section">
        <h3>Données Financières {{ $annee }}</h3>
        <div class="finances-grid">
            <div class="finance-card budget">
                <div class="finance-header">
                    <h4>Budget Prévisionnel</h4>
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="finance-amount">
                    {{ number_format($donneesFinancieres['prevision']) }} FCFA
                </div>
            </div>
            
            <div class="finance-card realisation">
                <div class="finance-header">
                    <h4>Réalisation Totale</h4>
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="finance-amount">
                    {{ number_format($donneesFinancieres['realisation_total']) }} FCFA
                </div>
            </div>
            
            <div class="finance-card taux">
                <div class="finance-header">
                    <h4>Taux de Réalisation</h4>
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="finance-amount taux-value {{ $donneesFinancieres['taux_realisation'] >= 75 ? 'good' : ($donneesFinancieres['taux_realisation'] >= 50 ? 'medium' : 'bad') }}">
                    {{ number_format($donneesFinancieres['taux_realisation'], 2) }}%
                </div>
            </div>
            
            <div class="finance-card evaluation">
                <div class="finance-header">
                    <h4>Évaluation</h4>
                    <i class="fas fa-star"></i>
                </div>
                <div class="finance-evaluation">
                    {{ $donneesFinancieres['evaluation'] }}
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique Performance -->
    <div class="chart-section">
        <div class="chart-container">
            <div class="chart-header">
                <h3>Historique des Performances</h3>
            </div>
            <div class="chart-content">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Détails des Dettes -->
    <div class="dettes-section">
        <h3>Détail des Dettes {{ $annee }}</h3>
        <div class="dettes-grid">
            <div class="dette-card cnps">
                <div class="dette-header">
                    <h4>Dettes CNPS</h4>
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="dette-amount">
                    {{ number_format($detailsDettes['cnps']['montant']) }} FCFA
                </div>
                <div class="dette-count">
                    {{ $detailsDettes['cnps']['details']->count() }} dossier(s)
                </div>
            </div>
            
            <div class="dette-card fiscale">
                <div class="dette-header">
                    <h4>Dettes Fiscales</h4>
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="dette-amount">
                    {{ number_format($detailsDettes['fiscale']['montant']) }} FCFA
                </div>
                <div class="dette-count">
                    {{ $detailsDettes['fiscale']['details']->count() }} dossier(s)
                </div>
            </div>
            
            <div class="dette-card feicom">
                <div class="dette-header">
                    <h4>Dettes FEICOM</h4>
                    <i class="fas fa-building"></i>
                </div>
                <div class="dette-amount">
                    {{ number_format($detailsDettes['feicom']['montant']) }} FCFA
                </div>
                <div class="dette-count">
                    {{ $detailsDettes['feicom']['details']->count() }} dossier(s)
                </div>
            </div>
            
            <div class="dette-card salariale">
                <div class="dette-header">
                    <h4>Dettes Salariales</h4>
                    <i class="fas fa-users"></i>
                </div>
                <div class="dette-amount">
                    {{ number_format($detailsDettes['salariale']['montant']) }} FCFA
                </div>
                <div class="dette-count">
                    {{ $detailsDettes['salariale']['details']->count() }} dossier(s)
                </div>
            </div>
        </div>
    </div>

    <!-- Problèmes et Défaillances -->
    <div class="problemes-section">
        <div class="section-tabs">
            <button class="tab-btn active" data-tab="defaillances">
                Défaillances ({{ $problemes['defaillances']->count() }})
            </button>
            <button class="tab-btn" data-tab="retards">
                Retards ({{ $problemes['retards']->count() }})
            </button>
        </div>
        
        <div class="tab-content active" id="defaillances">
            <div class="problemes-list">
                @forelse($problemes['defaillances'] as $defaillance)
                <div class="probleme-item {{ $defaillance['est_grave'] ? 'grave' : 'normal' }} {{ $defaillance['est_resolue'] ? 'resolved' : 'pending' }}">
                    <div class="probleme-header">
                        <span class="probleme-type">{{ $defaillance['type'] }}</span>
                        <span class="probleme-date">{{ date('d/m/Y', strtotime($defaillance['date_constat'])) }}</span>
                        <span class="probleme-status {{ $defaillance['est_resolue'] ? 'resolved' : 'pending' }}">
                            {{ $defaillance['est_resolue'] ? 'Résolu' : 'En cours' }}
                        </span>
                    </div>
                    <div class="probleme-description">
                        {{ $defaillance['description'] }}
                    </div>
                    @if($defaillance['est_grave'])
                    <div class="probleme-gravite">
                        <i class="fas fa-exclamation-triangle"></i>
                        Défaillance grave
                    </div>
                    @endif
                </div>
                @empty
                <div class="no-problemes">
                    <i class="fas fa-check-circle"></i>
                    Aucune défaillance constatée
                </div>
                @endforelse
            </div>
        </div>
        
        <div class="tab-content" id="retards">
            <div class="problemes-list">
                @forelse($problemes['retards'] as $retard)
                <div class="probleme-item {{ $retard['gravite'] === 'grave' ? 'grave' : 'normal' }}">
                    <div class="probleme-header">
                        <span class="probleme-type">{{ $retard['type'] }}</span>
                        <span class="probleme-date">{{ date('d/m/Y', strtotime($retard['date_constat'])) }}</span>
                        <span class="retard-duree">{{ $retard['duree_jours'] }} jours</span>
                    </div>
                    <div class="probleme-gravite {{ $retard['gravite'] }}">
                        Gravité: {{ ucfirst($retard['gravite']) }}
                    </div>
                </div>
                @empty
                <div class="no-problemes">
                    <i class="fas fa-clock"></i>
                    Aucun retard constaté
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique historique des performances
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const historiqueData = @json($historiquePerformances);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: historiqueData.map(item => item.annee),
            datasets: [{
                label: 'Taux de Réalisation (%)',
                data: historiqueData.map(item => item.pourcentage),
                backgroundColor: historiqueData.map(item => {
                    if (item.pourcentage >= 75) return '#28a745';
                    if (item.pourcentage >= 50) return '#ffc107';
                    return '#dc3545';
                }),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Évolution des Performances par Année'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Gestion des tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            
            // Remove active classes
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active classes
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
});
</script>
@endpush
@endsection --}}




@extends('layouts.app')

@section('title', 'Commune - ' . $commune->nom)

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $commune->nom }}</h1>
            <p class="text-muted mb-0">
                Code: {{ $commune->code }} | 
                {{ $commune->departement->nom }} - {{ $commune->departement->region->nom }}
            </p>
        </div>
        <div>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    Année: {{ $annee }}
                </button>
                <ul class="dropdown-menu">
                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <li>
                            <a class="dropdown-item {{ $i == $annee ? 'active' : '' }}" 
                               href="{{ route('communes.show', ['commune' => $commune, 'annee' => $i]) }}">
                                {{ $i }}
                            </a>
                        </li>
                    @endfor
                </ul>
            </div>
            <a href="{{ route('communes.edit', $commune) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('communes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informations générales -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations générales</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Nom:</dt>
                        <dd class="col-sm-7">{{ $commune->nom }}</dd>
                        
                        <dt class="col-sm-5">Code:</dt>
                        <dd class="col-sm-7">{{ $commune->code }}</dd>
                        
                        <dt class="col-sm-5">Département:</dt>
                        <dd class="col-sm-7">{{ $commune->departement->nom }}</dd>
                        
                        <dt class="col-sm-5">Région:</dt>
                        <dd class="col-sm-7">{{ $commune->departement->region->nom }}</dd>
                        
                        @if($commune->telephone)
                            <dt class="col-sm-5">Téléphone:</dt>
                            <dd class="col-sm-7">{{ $commune->telephone }}</dd>
                        @endif
                    </dl>

                    <!-- Personnel -->
                    <div class="mt-4">
                        <h6>Receveurs ({{ $commune->receveurs->count() }})</h6>
                        @if($commune->receveurs->count() > 0)
                            <ul class="list-unstyled mb-3">
                                @foreach($commune->receveurs as $receveur)
                                    <li class="mb-1">
                                        <i class="fas fa-user text-primary"></i>
                                        {{ $receveur->nom }}
                                        @if($receveur->fonction)
                                            <small class="text-muted">({{ $receveur->fonction }})</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">Aucun receveur assigné</p>
                        @endif

                        <h6>Ordonnateurs ({{ $commune->ordonnateurs->count() }})</h6>
                        @if($commune->ordonnateurs->count() > 0)
                            <ul class="list-unstyled">
                                @foreach($commune->ordonnateurs as $ordonnateur)
                                    <li class="mb-1">
                                        <i class="fas fa-user-tie text-success"></i>
                                        {{ $ordonnateur->nom }}
                                        @if($ordonnateur->fonction)
                                            <small class="text-muted">({{ $ordonnateur->fonction }})</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">Aucun ordonnateur assigné</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Données financières -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Données financières {{ $annee }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-primary">{{ number_format($donneesFinancieres['prevision'], 0, ',', ' ') }}</h3>
                                <p class="text-muted mb-0">Prévision (FCFA)</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-success">{{ number_format($donneesFinancieres['realisation_total'], 0, ',', ' ') }}</h3>
                                <p class="text-muted mb-0">Réalisation (FCFA)</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="
                                    @if($donneesFinancieres['taux_realisation'] >= 80) text-success
                                    @elseif($donneesFinancieres['taux_realisation'] >= 50) text-warning
                                    @else text-danger
                                    @endif
                                ">{{ number_format($donneesFinancieres['taux_realisation'], 1) }}%</h3>
                                <p class="text-muted mb-0">Taux de réalisation</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <span class="badge badge-lg 
                                    @if($donneesFinancieres['evaluation'] == 'Excellent') bg-success
                                    @elseif($donneesFinancieres['evaluation'] == 'Bien') bg-primary
                                    @elseif($donneesFinancieres['evaluation'] == 'Moyen') bg-warning
                                    @else bg-danger
                                    @endif
                                ">{{ $donneesFinancieres['evaluation'] }}</span>
                                <p class="text-muted mb-0 mt-2">Évaluation</p>
                            </div>
                        </div>
                    </div>

                    @if($donneesFinancieres['realisations_detail']->count() > 0)
                        <div class="mt-4">
                            <h6>Détail des réalisations</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Montant</th>
                                            <th>Écart prévision</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($donneesFinancieres['realisations_detail'] as $realisation)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($realisation['date'])->format('d/m/Y') }}</td>
                                                <td>{{ number_format($realisation['montant'], 0, ',', ' ') }} FCFA</td>
                                                <td>
                                                    <span class="badge {{ $realisation['ecart_prevision'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $realisation['ecart_prevision'] > 0 ? '+' : '' }}{{ number_format($realisation['ecart_prevision'], 1) }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Historique des performances -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Historique des performances</h5>
                </div>
                <div class="card-body">
                    @if($historiquePerformances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Année</th>
                                        <th>Taux</th>
                                        <th>Évaluation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historiquePerformances as $performance)
                                        <tr class="{{ $performance['annee'] == $annee ? 'table-active' : '' }}">
                                            <td>{{ $performance['annee'] }}</td>
                                            <td>{{ number_format($performance['pourcentage'], 1) }}%</td>
                                            <td>
                                                <span class="badge 
                                                    @if($performance['evaluation'] == 'Excellent') bg-success
                                                    @elseif($performance['evaluation'] == 'Bien') bg-primary
                                                    @elseif($performance['evaluation'] == 'Moyen') bg-warning
                                                    @else bg-danger
                                                    @endif
                                                ">{{ $performance['evaluation'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Aucun historique de performance disponible</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Dettes -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">État des dettes {{ $annee }}</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-2">
                                <h6 class="text-primary mb-1">CNPS</h6>
                                <p class="mb-0 fw-bold">{{ number_format($detailsDettes['cnps']['montant'], 0, ',', ' ') }}</p>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-2">
                                <h6 class="text-warning mb-1">Fiscale</h6>
                                <p class="mb-0 fw-bold">{{ number_format($detailsDettes['fiscale']['montant'], 0, ',', ' ') }}</p>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h6 class="text-info mb-1">FEICOM</h6>
                                <p class="mb-0 fw-bold">{{ number_format($detailsDettes['feicom']['montant'], 0, ',', ' ') }}</p>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h6 class="text-danger mb-1">Salariale</h6>
                                <p class="mb-0 fw-bold">{{ number_format($detailsDettes['salariale']['montant'], 0, ',', ' ') }}</p>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $totalDettes = $detailsDettes['cnps']['montant'] + $detailsDettes['fiscale']['montant'] + 
                                      $detailsDettes['feicom']['montant'] + $detailsDettes['salariale']['montant'];
                    @endphp
                    
                    <div class="text-center mt-3 pt-3 border-top">
                        <h5 class="text-dark">Total: {{ number_format($totalDettes, 0, ',', ' ') }} FCFA</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Problèmes et défaillances -->
    @if($problemes['defaillances']->count() > 0 || $problemes['retards']->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Problèmes identifiés {{ $annee }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Défaillances -->
                            @if($problemes['defaillances']->count() > 0)
                                <div class="col-lg-6">
                                    <h6>Défaillances ({{ $problemes['defaillances']->count() }})</h6>
                                    <div class="list-group list-group-flush">
                                        @foreach($problemes['defaillances'] as $defaillance)
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">{{ $defaillance['type'] }}</h6>
                                                        <p class="mb-1">{{ $defaillance['description'] }}</p>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($defaillance['date_constat'])->format('d/m/Y') }}</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge {{ $defaillance['est_grave'] ? 'bg-danger' : 'bg-warning' }}">
                                                            {{ $defaillance['gravite'] }}
                                                        </span>
                                                        @if($defaillance['est_resolue'])
                                                            <br><span class="badge bg-success mt-1">Résolu</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Retards -->
                            @if($problemes['retards']->count() > 0)
                                <div class="col-lg-6">
                                    <h6>Retards ({{ $problemes['retards']->count() }})</h6>
                                    <div class="list-group list-group-flush">
                                        @foreach($problemes['retards'] as $retard)
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">{{ $retard['type'] }}</h6>
                                                        <p class="mb-1">{{ $retard['duree_jours'] }} jours de retard</p>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($retard['date_constat'])->format('d/m/Y') }}</small>
                                                    </div>
                                                    <span class="badge 
                                                        @if($retard['gravite'] == 'Critique') bg-danger
                                                        @elseif($retard['gravite'] == 'Élevé') bg-warning
                                                        @else bg-info
                                                        @endif
                                                    ">
                                                        {{ $retard['gravite'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .badge-lg {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
    
    .card-header h5 {
        color: #495057;
    }
    
    .border {
        border-color: #dee2e6 !important;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
    }
</style>
@endpush