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


{{-- @extends('layouts.app')

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
@endpush --}}






{{-- @extends('layouts.app')

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

    <!-- Alertes système -->
    @if(isset($alertes) && $alertes->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> Alertes importantes</h6>
                    @foreach($alertes as $alerte)
                        <div class="alert alert-{{ $alerte['niveau'] }} alert-sm mb-2">
                            <strong>{{ $alerte['type'] }}:</strong> {{ $alerte['message'] }}
                            @if(isset($alerte['action_recommandee']))
                                <br><small class="text-muted">Action recommandée: {{ $alerte['action_recommandee'] }}</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
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
                        
                        @if($commune->email)
                            <dt class="col-sm-5">Email:</dt>
                            <dd class="col-sm-7">{{ $commune->email }}</dd>
                        @endif
                        
                        @if($commune->population)
                            <dt class="col-sm-5">Population:</dt>
                            <dd class="col-sm-7">{{ $commune->population_formattee }}</dd>
                        @endif
                        
                        @if($commune->superficie)
                            <dt class="col-sm-5">Superficie:</dt>
                            <dd class="col-sm-7">{{ $commune->superficie_formattee }}</dd>
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
                                        @if($receveur->fonction ?? false)
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
                                        @if($ordonnateur->fonction ?? false)
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
                                <h3 class="text-primary">{{ number_format($donneesFinancieres['prevision'] ?? 0, 0, ',', ' ') }}</h3>
                                <p class="text-muted mb-0">Prévision (FCFA)</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-success">{{ number_format($donneesFinancieres['realisation_total'] ?? 0, 0, ',', ' ') }}</h3>
                                <p class="text-muted mb-0">Réalisation (FCFA)</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="
                                    @if(($donneesFinancieres['taux_realisation'] ?? 0) >= 80) text-success
                                    @elseif(($donneesFinancieres['taux_realisation'] ?? 0) >= 50) text-warning
                                    @else text-danger
                                    @endif
                                ">{{ number_format($donneesFinancieres['taux_realisation'] ?? 0, 1) }}%</h3>
                                <p class="text-muted mb-0">Taux de réalisation</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <span class="badge badge-lg 
                                    @if(($donneesFinancieres['evaluation'] ?? '') == 'Excellent') bg-success
                                    @elseif(($donneesFinancieres['evaluation'] ?? '') == 'Bien') bg-primary
                                    @elseif(($donneesFinancieres['evaluation'] ?? '') == 'Moyen') bg-warning
                                    @else bg-danger
                                    @endif
                                ">{{ $donneesFinancieres['evaluation'] ?? 'Non évalué' }}</span>
                                <p class="text-muted mb-0 mt-2">Évaluation</p>
                            </div>
                        </div>
                    </div>




    @php
    $hasDonneesPeriodiques = false;
    $countDonnees = 0;
    
    if (isset($donneesFinancieres['donnees_periodiques'])) {
        if (is_object($donneesFinancieres['donnees_periodiques']) && method_exists($donneesFinancieres['donnees_periodiques'], 'count')) {
            // C'est une collection Laravel
            $countDonnees = $donneesFinancieres['donnees_periodiques']->count();
            $hasDonneesPeriodiques = $countDonnees > 0;
        } elseif (is_array($donneesFinancieres['donnees_periodiques'])) {
            // C'est un tableau PHP
            $countDonnees = count($donneesFinancieres['donnees_periodiques']);
            $hasDonneesPeriodiques = $countDonnees > 0;
        }
    }
@endphp

@if($hasDonneesPeriodiques)
    <div class="mt-4">
        <h6>Évolution {{ $periode ?? 'annuelle' }} ({{ $countDonnees }} période(s))</h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Période</th>
                        <th>Montant</th>
                        <th>Opérations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donneesFinancieres['donnees_periodiques'] as $donnee)
                        @php
                            // Gestion universelle des données (objet ou tableau)
                            $periode = is_object($donnee) ? 
                                      ($donnee->periode ?? $donnee->mois ?? 'N/A') : 
                                      ($donnee['periode'] ?? $donnee['mois'] ?? 'N/A');
                                      
                            $montant = is_object($donnee) ? 
                                      ($donnee->montant_total ?? 0) : 
                                      ($donnee['montant_total'] ?? 0);
                                      
                            $operations = is_object($donnee) ? 
                                         ($donnee->nombre_operations ?? 0) : 
                                         ($donnee['nombre_operations'] ?? 0);
                        @endphp
                        <tr>
                            <td>{{ $periode }}</td>
                            <td>{{ number_format($montant, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $operations }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

    <!-- Indicateurs clés -->
    @if(isset($indicateurs))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Indicateurs clés {{ $annee }}</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="text-primary mb-1">Taux d'exécution</h6>
                                <h4 class="mb-0">{{ number_format($indicateurs['taux_execution'] ?? 0, 1) }}%</h4>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="text-info mb-1">Ratio dette/budget</h6>
                                <h4 class="mb-0">{{ number_format($indicateurs['ratio_dette_budget'] ?? 0, 1) }}%</h4>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="text-success mb-1">Projets actifs</h6>
                                <h4 class="mb-0">{{ $indicateurs['nombre_projets_actifs'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="text-warning mb-1">Défaillances</h6>
                                <h4 class="mb-0">{{ $indicateurs['nombre_defaillances'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="text-danger mb-1">Retards</h6>
                                <h4 class="mb-0">{{ $indicateurs['nombre_retards'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="text-dark mb-1">Total dettes</h6>
                                <h5 class="mb-0">{{ number_format($indicateurs['total_dettes'] ?? 0, 0, ',', ' ') }}</h5>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">

        <!-- Historique des performances -->
<div class="col-lg-6 mb-4">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Historique des performances</h5>
        </div>
        <div class="card-body">
            @if(isset($evolution) && count($evolution) > 0)
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
                            @foreach($evolution as $performance)
                                <tr class="{{ $performance['annee'] == $annee ? 'table-active' : '' }}">
                                    <td>{{ $performance['annee'] }}</td>
                                    <td>{{ number_format($performance['taux_realisation'], 1) }}%</td>
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
                        <p class="mb-0 fw-bold">{{ number_format($detailsDettes['cnps']['montant'] ?? 0, 0, ',', ' ') }}</p>
                        <small class="text-muted">FCFA</small>
                        <br><small class="badge bg-light text-dark">{{ $detailsDettes['cnps']['count'] ?? 0 }} dossier(s)</small>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="border rounded p-2">
                        <h6 class="text-warning mb-1">Fiscale</h6>
                        <p class="mb-0 fw-bold">{{ number_format($detailsDettes['fiscale']['montant'] ?? 0, 0, ',', ' ') }}</p>
                        <small class="text-muted">FCFA</small>
                        <br><small class="badge bg-light text-dark">{{ $detailsDettes['fiscale']['count'] ?? 0 }} dossier(s)</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-2">
                        <h6 class="text-info mb-1">FEICOM</h6>
                        <p class="mb-0 fw-bold">{{ number_format($detailsDettes['feicom']['montant'] ?? 0, 0, ',', ' ') }}</p>
                        <small class="text-muted">FCFA</small>
                        <br><small class="badge bg-light text-dark">{{ $detailsDettes['feicom']['count'] ?? 0 }} dossier(s)</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-2">
                        <h6 class="text-danger mb-1">Salariale</h6>
                        <p class="mb-0 fw-bold">{{ number_format($detailsDettes['salariale']['montant'] ?? 0, 0, ',', ' ') }}</p>
                        <small class="text-muted">FCFA</small>
                        <br><small class="badge bg-light text-dark">{{ $detailsDettes['salariale']['count'] ?? 0 }} dossier(s)</small>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3 pt-3 border-top">
                <h5 class="text-dark">Total: {{ number_format($totalDettes ?? 0, 0, ',', ' ') }} FCFA</h5>
                @if(isset($detailsDettes))
                    @php
                        $totalDossiers = ($detailsDettes['cnps']['count'] ?? 0) + 
                                       ($detailsDettes['fiscale']['count'] ?? 0) + 
                                       ($detailsDettes['feicom']['count'] ?? 0) + 
                                       ($detailsDettes['salariale']['count'] ?? 0);
                    @endphp
                    <small class="text-muted">{{ $totalDossiers }} dossier(s) au total</small>
                @endif
            </div>

            @if(isset($detailsDettes) && ($detailsDettes['cnps']['count'] > 0 || $detailsDettes['fiscale']['count'] > 0 || $detailsDettes['feicom']['count'] > 0 || $detailsDettes['salariale']['count'] > 0))
                <div class="mt-3">
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#detailDettes">
                        <i class="fas fa-eye"></i> Voir le détail
                    </button>
                    
                    <div class="collapse mt-2" id="detailDettes">
                        <div class="card card-body">
                            <ul class="nav nav-tabs" id="dettesTab">
                                @if($detailsDettes['cnps']['count'] > 0)
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cnps-tab">
                                            CNPS ({{ $detailsDettes['cnps']['count'] }})
                                        </button>
                                    </li>
                                @endif
                                @if($detailsDettes['fiscale']['count'] > 0)
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fiscale-tab">
                                            Fiscale ({{ $detailsDettes['fiscale']['count'] }})
                                        </button>
                                    </li>
                                @endif
                                @if($detailsDettes['feicom']['count'] > 0)
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#feicom-tab">
                                            FEICOM ({{ $detailsDettes['feicom']['count'] }})
                                        </button>
                                    </li>
                                @endif
                                @if($detailsDettes['salariale']['count'] > 0)
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#salariale-tab">
                                            Salariale ({{ $detailsDettes['salariale']['count'] }})
                                        </button>
                                    </li>
                                @endif
                            </ul>
                            
                            <div class="tab-content mt-2">
                                @if($detailsDettes['cnps']['count'] > 0)
                                    <div class="tab-pane fade show active" id="cnps-tab">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Montant</th>
                                                        <th>Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($detailsDettes['cnps']['details'] as $dette)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($dette->date_dette ?? $dette->created_at)->format('d/m/Y') }}</td>
                                                            <td>{{ number_format($dette->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                                                            <td>
                                                                <span class="badge {{ ($dette->est_payee ?? false) ? 'bg-success' : 'bg-danger' }}">
                                                                    {{ ($dette->est_payee ?? false) ? 'Payée' : 'En attente' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($detailsDettes['fiscale']['count'] > 0)
                                    <div class="tab-pane fade" id="fiscale-tab">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Montant</th>
                                                        <th>Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($detailsDettes['fiscale']['details'] as $dette)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($dette->date_dette ?? $dette->created_at)->format('d/m/Y') }}</td>
                                                            <td>{{ number_format($dette->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                                                            <td>
                                                                <span class="badge {{ ($dette->est_payee ?? false) ? 'bg-success' : 'bg-danger' }}">
                                                                    {{ ($dette->est_payee ?? false) ? 'Payée' : 'En attente' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($detailsDettes['feicom']['count'] > 0)
                                    <div class="tab-pane fade" id="feicom-tab">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Montant</th>
                                                        <th>Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($detailsDettes['feicom']['details'] as $dette)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($dette->date_dette ?? $dette->created_at)->format('d/m/Y') }}</td>
                                                            <td>{{ number_format($dette->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                                                            <td>
                                                                <span class="badge {{ ($dette->est_payee ?? false) ? 'bg-success' : 'bg-danger' }}">
                                                                    {{ ($dette->est_payee ?? false) ? 'Payée' : 'En attente' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($detailsDettes['salariale']['count'] > 0)
                                    <div class="tab-pane fade" id="salariale-tab">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Montant</th>
                                                        <th>Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($detailsDettes['salariale']['details'] as $dette)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($dette->date_dette ?? $dette->created_at)->format('d/m/Y') }}</td>
                                                            <td>{{ number_format($dette->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                                                            <td>
                                                                <span class="badge {{ ($dette->est_payee ?? false) ? 'bg-success' : 'bg-danger' }}">
                                                                    {{ ($dette->est_payee ?? false) ? 'Payée' : 'En attente' }}
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
            @endif
        </div>
    </div>
</div>

    <!-- Projets en cours -->
    @if(isset($projetsEnCours) && $projetsEnCours->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Projets en cours ({{ $projetsEnCours->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Projet</th>
                                    <th>Responsable</th>
                                    <th>Début</th>
                                    <th>Budget</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projetsEnCours as $projet)
                                    <tr>
                                        <td>{{ $projet->nom ?? 'N/A' }}</td>
                                        <td>{{ $projet->responsable->nom ?? 'Non assigné' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($projet->date_debut ?? now())->format('d/m/Y') }}</td>
                                        <td>{{ number_format($projet->budget ?? 0, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $projet->statut ?? 'En cours' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Problèmes et défaillances -->
    @php
        $defaillances = $commune->defaillances()->where('annee_exercice', $annee)->get();
        $retards = $commune->retards()->where('annee_exercice', $annee)->get();
    @endphp
    
    @if($defaillances->count() > 0 || $retards->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Problèmes identifiés {{ $annee }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Défaillances -->
                            @if($defaillances->count() > 0)
                                <div class="col-lg-6">
                                    <h6>Défaillances ({{ $defaillances->count() }})</h6>
                                    <div class="list-group list-group-flush">
                                        @foreach($defaillances as $defaillance)
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">{{ $defaillance->type ?? 'Type non spécifié' }}</h6>
                                                        <p class="mb-1">{{ $defaillance->description ?? 'Aucune description' }}</p>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($defaillance->date_constat ?? now())->format('d/m/Y') }}</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge {{ ($defaillance->est_grave ?? false) ? 'bg-danger' : 'bg-warning' }}">
                                                            {{ $defaillance->gravite ?? 'Normale' }}
                                                        </span>
                                                        @if($defaillance->est_resolue ?? false)
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
                            @if($retards->count() > 0)
                                <div class="col-lg-6">
                                    <h6>Retards ({{ $retards->count() }})</h6>
                                    <div class="list-group list-group-flush">
                                        @foreach($retards as $retard)
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">{{ $retard->type ?? 'Type non spécifié' }}</h6>
                                                        <p class="mb-1">{{ $retard->duree_jours ?? 0 }} jours de retard</p>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($retard->date_constat ?? now())->format('d/m/Y') }}</small>
                                                    </div>
                                                    <span class="badge 
                                                        @if(($retard->gravite ?? '') == 'Critique') bg-danger
                                                        @elseif(($retard->gravite ?? '') == 'Élevé') bg-warning
                                                        @else bg-info
                                                        @endif
                                                    ">
                                                        {{ $retard->gravite ?? 'Normal' }}
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

    <!-- Comparaisons régionales -->
    @if(isset($comparaisons))
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Comparaisons régionales</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h6>Position dans le département</h6>
                            <h3 class="text-primary">{{ $comparaisons['rang_departement'] ?? 'N/A' }}</h3>
                            <small class="text-muted">sur {{ $comparaisons['nombre_communes_departement'] ?? 'N/A' }} communes</small>
                        </div>
                        <div class="col-md-3">
                            <h6>Taux de réalisation</h6>
                            <h3 class="text-success">{{ number_format($comparaisons['taux_realisation_commune'] ?? 0, 1) }}%</h3>
                            <small class="text-muted">cette commune</small>
                        </div>
                        <div class="col-md-3">
                            <h6>Moyenne départementale</h6>
                            <h3 class="text-info">{{ number_format($comparaisons['moyenne_departement'] ?? 0, 1) }}%</h3>
                            <small class="text-muted">autres communes</small>
                        </div>
                        <div class="col-md-3">
                            <h6>Écart à la moyenne</h6>
                            @php
                                $ecart = ($comparaisons['taux_realisation_commune'] ?? 0) - ($comparaisons['moyenne_departement'] ?? 0);
                            @endphp
                            <h3 class="{{ $ecart >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $ecart > 0 ? '+' : '' }}{{ number_format($ecart, 1) }}%
                            </h3>
                            <small class="text-muted">par rapport à la moyenne</small>
                        </div>
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
    
    .alert-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush --}}



@extends('layouts.app')

@section('title', 'Commune ' . $commune->nom . ' - Observatoire des Collectivités')
@section('page-title', 'Commune de ' . $commune->nom)

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<style>
:root {
    --primary: #667eea;
    --primary-light: #8b9afc;
    --primary-dark: #4c63d2;
    --secondary: #64748b;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --light: #f8fafc;
    --white: #ffffff;
    --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --radius: 6px;
    --radius-lg: 8px;
    --transition: all 0.15s ease;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    font-size: 14px;
    line-height: 1.5;
}

.commune-dashboard {
    padding: 1rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header compact */
.commune-header {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    margin-bottom: 1rem;
    box-shadow: var(--shadow-sm);
    border-left: 3px solid var(--primary);
}

.commune-header-content {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 1rem;
    align-items: start;
}

.commune-title-section h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
}

.commune-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    font-size: 0.8rem;
    color: var(--secondary);
}

.commune-meta span {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    white-space: nowrap;
}

.commune-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.badge-commune {
    padding: 0.25rem 0.6rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.badge-excellent {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.badge-conforme {
    background: rgba(59, 130, 246, 0.1);
    color: var(--info);
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.badge-moyen {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.badge-non-conforme {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.commune-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    padding: 0.5rem 0.875rem;
    border-radius: var(--radius);
    border: none;
    cursor: pointer;
    font-weight: 500;
    font-size: 0.8rem;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.375rem;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    color: white;
}

.btn-secondary {
    background: #e2e8f0;
    color: #475569;
}

.btn-secondary:hover {
    background: #cbd5e1;
    color: #475569;
}

/* Breadcrumb compact */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow-xs);
    margin-bottom: 1rem;
    font-size: 0.8rem;
}

.breadcrumb a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.breadcrumb a:hover {
    background: rgba(102, 126, 234, 0.1);
}

.breadcrumb span {
    color: var(--secondary);
}

/* Stats Cards compactes */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 1rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    position: relative;
    border-left: 3px solid transparent;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.stat-card .stat-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
    color: white;
}

.stat-card .stat-icon.blue { 
    background: linear-gradient(135deg, var(--info), #2563eb);
    border-left-color: var(--info);
}
.stat-card .stat-icon.green { 
    background: linear-gradient(135deg, var(--success), #059669);
    border-left-color: var(--success);
}
.stat-card .stat-icon.red { 
    background: linear-gradient(135deg, var(--danger), #dc2626);
    border-left-color: var(--danger);
}
.stat-card .stat-icon.orange { 
    background: linear-gradient(135deg, var(--warning), #d97706);
    border-left-color: var(--warning);
}

.stat-card:hover {
    border-left-color: var(--primary);
}

.stat-card .stat-content h4 {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--secondary);
    text-transform: uppercase;
    letter-spacing: 0.025em;
    margin-bottom: 0.25rem;
}

.stat-card .stat-number {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
}

/* Sections de contenu compactes */
.content-section {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1rem;
    overflow: hidden;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
}

.section-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-header .section-icon {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    background: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
}

.section-content {
    padding: 1rem 1.25rem;
}

/* Données financières compactes */
.financial-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.financial-item {
    text-align: center;
    padding: 1rem 0.75rem;
    background: var(--light);
    border-radius: var(--radius);
    border: 1px solid #e2e8f0;
    transition: var(--transition);
}

.financial-item:hover {
    background: var(--white);
    box-shadow: var(--shadow-xs);
    transform: translateY(-1px);
}

.financial-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.financial-label {
    font-size: 0.75rem;
    color: var(--secondary);
    font-weight: 500;
}

/* Dettes compactes */
.dettes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
}

.dette-card {
    background: var(--light);
    border-radius: var(--radius);
    padding: 1rem;
    border: 1px solid #e2e8f0;
    transition: var(--transition);
}

.dette-card:hover {
    background: var(--white);
    box-shadow: var(--shadow-xs);
    transform: translateY(-1px);
}

.dette-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.dette-type {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.9rem;
}

.dette-icon {
    width: 32px;
    height: 32px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}

.dette-icon.cnps { background: var(--info); }
.dette-icon.fiscale { background: var(--danger); }
.dette-icon.feicom { background: var(--warning); }
.dette-icon.salariale { background: var(--success); }

.dette-montant {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
}

.dette-details {
    margin-top: 0.5rem;
    font-size: 0.75rem;
    color: var(--secondary);
}

/* Responsables compacts */
.responsables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 0.75rem;
}

.responsable-card {
    background: var(--light);
    border-radius: var(--radius);
    padding: 1rem;
    border: 1px solid #e2e8f0;
    transition: var(--transition);
}

.responsable-card:hover {
    background: var(--white);
    box-shadow: var(--shadow-xs);
    transform: translateY(-1px);
}

.responsable-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.responsable-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    flex-shrink: 0;
}

.responsable-info h4 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 0.125rem 0;
}

.responsable-role {
    font-size: 0.75rem;
    color: var(--secondary);
    font-weight: 500;
}

/* Issues compactes */
.issues-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.issue-card {
    background: var(--light);
    border-radius: var(--radius);
    padding: 1rem;
    border: 1px solid #e2e8f0;
}

.issue-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.issue-icon {
    width: 32px;
    height: 32px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.issue-icon.defaillances { background: var(--danger); }
.issue-icon.retards { background: var(--warning); }

.issue-count {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
}

.issue-label {
    font-size: 0.75rem;
    color: var(--secondary);
    font-weight: 500;
}

/* Table responsive compacte */
.table-container {
    overflow-x: auto;
    border-radius: var(--radius);
    border: 1px solid #e2e8f0;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.8rem;
}

.data-table th {
    background: var(--light);
    padding: 0.75rem 0.5rem;
    text-align: left;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    font-size: 0.7rem;
    border-bottom: 1px solid #e2e8f0;
}

.data-table td {
    padding: 0.75rem 0.5rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.data-table tr:hover td {
    background: rgba(102, 126, 234, 0.02);
}

/* Chart compact */
.chart-container {
    position: relative;
    height: 250px;
    margin-top: 0.5rem;
}

/* Filtres compacts */
.filter-section {
    background: var(--white);
    padding: 0.75rem 1rem;
    border-radius: var(--radius);
    margin-bottom: 1rem;
    box-shadow: var(--shadow-xs);
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.75rem;
    align-items: end;
}

.form-group label {
    display: block;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.375rem;
    font-size: 0.8rem;
}

.form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: var(--radius);
    font-size: 0.8rem;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

/* Progress bar compacte */
.progress-container {
    width: 100%;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 0.5rem;
}

.progress-bar {
    height: 100%;
    border-radius: 3px;
    transition: width 0.8s ease;
}

.progress-bar.excellent { background: linear-gradient(90deg, var(--success), #059669); }
.progress-bar.conforme { background: linear-gradient(90deg, var(--info), #2563eb); }
.progress-bar.moyen { background: linear-gradient(90deg, var(--warning), #d97706); }
.progress-bar.non-conforme { background: linear-gradient(90deg, var(--danger), #dc2626); }

/* Info list compacte */
.info-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: var(--light);
    border-radius: var(--radius);
    border: 1px solid #e2e8f0;
    font-size: 0.8rem;
}

.info-item strong {
    color: #1e293b;
    font-weight: 600;
    min-width: 100px;
}

.info-item span {
    color: var(--secondary);
    text-align: right;
    flex: 1;
}

/* Responsive optimisé */
@media (max-width: 1024px) {
    .commune-header-content {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .commune-actions {
        justify-content: flex-start;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .commune-dashboard {
        padding: 0.75rem;
    }
    
    .commune-header {
        padding: 1rem;
    }
    
    .commune-title-section h1 {
        font-size: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .financial-grid,
    .dettes-grid,
    .responsables-grid {
        grid-template-columns: 1fr;
    }
    
    .issues-grid {
        grid-template-columns: 1fr;
    }
    
    .section-content {
        padding: 0.75rem 1rem;
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .commune-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .commune-actions {
        flex-direction: column;
        width: 100%;
    }
}

@media (max-width: 480px) {
    .commune-dashboard {
        padding: 0.5rem;
    }
    
    .stat-card {
        padding: 0.75rem;
    }
    
    .stat-card .stat-number {
        font-size: 1.5rem;
    }
    
    .section-header {
        padding: 0.75rem 1rem 0.5rem;
    }
    
    .section-content {
        padding: 0.75rem;
    }
}

/* Animation subtile */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-up {
    animation: slideUp 0.3s ease-out;
}

/* États d'évaluation */
.evaluation-excellent { color: var(--success); }
.evaluation-conforme { color: var(--info); }
.evaluation-moyen { color: var(--warning); }
.evaluation-non-conforme { color: var(--danger); }
</style>
@endpush

@section('content')
<div class="commune-dashboard">
    <!-- Breadcrumb -->
    <div class="breadcrumb animate__animated animate__fadeInDown">
        <a href="{{ route('dashboard.index') }}">
            <i class="fas fa-home"></i> Tableau de bord
        </a>
        <span>/</span>
        <a href="{{ route('communes.index') }}">Communes</a>
        <span>/</span>
        <a href="{{ route('regions.show', $commune->departement->region->id) }}">{{ $commune->departement->region->nom }}</a>
        <span>/</span>
        <a href="{{ route('departements.show', $commune->departement->id) }}">{{ $commune->departement->nom }}</a>
        <span>/</span>
        <span>{{ $commune->nom }}</span>
    </div>

    <!-- Header principal -->
    <div class="commune-header animate-slide-up">
        <div class="commune-header-content">
            <div class="commune-title-section">
                <h1>{{ $commune->nom }}</h1>
                
                <div class="commune-meta">
                    <span>
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $commune->departement->nom }}, {{ $commune->departement->region->nom }}
                    </span>
                    <span>
                        <i class="fas fa-code"></i>
                        Code: {{ $commune->code }}
                    </span>
                    @if($commune->population)
                        <span>
                            <i class="fas fa-users"></i>
                            {{ number_format($commune->population) }} habitants
                        </span>
                    @endif
                    @if($commune->superficie)
                        <span>
                            <i class="fas fa-expand-arrows-alt"></i>
                            {{ number_format($commune->superficie) }} km²
                        </span>
                    @endif
                </div>

                <div class="commune-badges">
                    @php
                        $evaluation = strtolower($stats['evaluation'] ?? 'non évalué');
                        $badgeClass = 'badge-non-conforme';
                        if (str_contains($evaluation, 'excellent')) $badgeClass = 'badge-excellent';
                        elseif (str_contains($evaluation, 'conforme')) $badgeClass = 'badge-conforme';
                        elseif (str_contains($evaluation, 'moyen')) $badgeClass = 'badge-moyen';
                    @endphp
                    <span class="badge-commune {{ $badgeClass }}">
                        <i class="fas fa-star"></i>
                        {{ $stats['evaluation'] }}
                    </span>
                    <span class="badge-commune badge-conforme">
                        <i class="fas fa-percentage"></i>
                        {{ number_format($stats['taux_realisation'], 1) }}% réalisé
                    </span>
                </div>
            </div>

            <div class="commune-actions">
                <a href="{{ route('communes.edit', $commune) }}" class="btn-action btn-primary">
                    <i class="fas fa-edit"></i>
                    Modifier
                </a>
                <button class="btn-action btn-secondary" onclick="exportCommune()">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>
        </div>
    </div>

    <!-- Filtre d'année -->
    <div class="filter-section animate-slide-up">
        <div class="filter-grid">
            <div class="form-group">
                <label for="annee-select">Année d'exercice</label>
                <select id="annee-select" class="form-control" onchange="changeAnnee(this.value)">
                    @foreach($anneesDisponibles as $anneeOption)
                        <option value="{{ $anneeOption }}" {{ $anneeOption == $annee ? 'selected' : '' }}>
                            {{ $anneeOption }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <button class="btn-action btn-primary" onclick="refreshData()">
                    <i class="fas fa-sync-alt"></i>
                    Actualiser
                </button>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="stats-grid">
        <div class="stat-card animate-slide-up">
            <div class="stat-icon blue">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-content">
                <h4>Budget Prévisionnel</h4>
                <div class="stat-number">{{ number_format($stats['budget_previsionnel'] / 1000000, 1) }}M</div>
            </div>
        </div>

        <div class="stat-card animate-slide-up">
            <div class="stat-icon green">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h4>Réalisation Totale</h4>
                <div class="stat-number">{{ number_format($stats['realisation_total'] / 1000000, 1) }}M</div>
            </div>
        </div>

        <div class="stat-card animate-slide-up">
            <div class="stat-icon orange">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-content">
                <h4>Taux de Réalisation</h4>
                <div class="stat-number">{{ number_format($stats['taux_realisation'], 1) }}%</div>
            </div>
        </div>

        <div class="stat-card animate-slide-up">
            <div class="stat-icon red">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h4>Total des Dettes</h4>
                <div class="stat-number">{{ number_format($stats['total_dettes'] / 1000000, 1) }}M</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-lg-8">
            <!-- Données financières détaillées -->
            <div class="content-section animate-slide-up">
                <div class="section-header">
                    <h3>
                        <div class="section-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        Données financières {{ $annee }}
                    </h3>
                </div>
                <div class="section-content">
                    <div class="financial-grid">
                        <div class="financial-item">
                            <div class="financial-value">{{ number_format($donneesFinancieres['prevision']?->montant ?? 0) }} FCFA</div>
                            <div class="financial-label">Budget Prévisionnel</div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-value">{{ number_format($donneesFinancieres['realisation_total']) }} FCFA</div>
                            <div class="financial-label">Réalisation Totale</div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-value 
                                {{ $donneesFinancieres['ecart'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $donneesFinancieres['ecart'] >= 0 ? '+' : '' }}{{ number_format($donneesFinancieres['ecart']) }} FCFA
                            </div>
                            <div class="financial-label">Écart Prév./Réal.</div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-value evaluation-{{ strtolower(str_replace(' ', '-', $donneesFinancieres['taux_realisation']?->evaluation ?? 'non-évalué')) }}">
                                {{ $donneesFinancieres['taux_realisation']?->evaluation ?? 'Non évalué' }}
                            </div>
                            <div class="financial-label">Évaluation</div>
                        </div>
                    </div>

                    @if($donneesFinancieres['taux_realisation'])
                        <div class="progress-container">
                            @php
                                $taux = $donneesFinancieres['taux_realisation']->pourcentage;
                                $progressClass = $taux >= 90 ? 'excellent' : ($taux >= 75 ? 'conforme' : ($taux >= 50 ? 'moyen' : 'non-conforme'));
                            @endphp
                            <div class="progress-bar {{ $progressClass }}" style="width: {{ $taux }}%"></div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Évolution des performances -->
            @if($evolutionPerformances->count() > 0)
            <div class="content-section animate-slide-up">
                <div class="section-header">
                    <h3>
                        <div class="section-icon">
                            <i class="fas fa-chart-area"></i>
                        </div>
                        Évolution des Performances
                    </h3>
                </div>
                <div class="section-content">
                    <div class="chart-container">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>
            </div>
            @endif

            <!-- Détail des réalisations -->
            @if($donneesFinancieres['realisations']->count() > 0)
            <div class="content-section animate-slide-up">
                <div class="section-header">
                    <h3>
                        <div class="section-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        Détail des Réalisations {{ $annee }}
                    </h3>
                </div>
                <div class="section-content">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Montant</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($donneesFinancieres['realisations'] as $realisation)
                                    <tr>
                                        <td>{{ $realisation->date_realisation ? $realisation->date_realisation->format('d/m/Y') : 'N/A' }}</td>
                                        <td>{{ $realisation->description ?? 'N/A' }}</td>
                                        <td class="fw-bold">{{ number_format($realisation->montant) }} FCFA</td>
                                        <td>{{ $realisation->type ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Colonne latérale -->
        <div class="col-lg-4">
            <!-- Responsables -->
            <div class="content-section animate-slide-up">
                <div class="section-header">
                    <h3>
                        <div class="section-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        Responsables
                    </h3>
                </div>
                <div class="section-content">
                    <div class="responsables-grid">
                        @if($commune->receveurs->count() > 0)
                            @foreach($commune->receveurs as $receveur)
                                <div class="responsable-card">
                                    <div class="responsable-header">
                                        <div class="responsable-avatar">
                                            {{ strtoupper(substr($receveur->nom, 0, 2)) }}
                                        </div>
                                        <div class="responsable-info">
                                            <h4>{{ $receveur->nom }}</h4>
                                            <div class="responsable-role">Receveur Municipal</div>
                                        </div>
                                    </div>
                                    @if($receveur->telephone)
                                        <div class="responsable-contact">
                                            <i class="fas fa-phone"></i> {{ $receveur->telephone }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        @if($commune->ordonnateurs->count() > 0)
                            @foreach($commune->ordonnateurs as $ordonnateur)
                                <div class="responsable-card">
                                    <div class="responsable-header">
                                        <div class="responsable-avatar">
                                            {{ strtoupper(substr($ordonnateur->nom, 0, 2)) }}
                                        </div>
                                        <div class="responsable-info">
                                            <h4>{{ $ordonnateur->nom }}</h4>
                                            <div class="responsable-role">Ordonnateur</div>
                                        </div>
                                    </div>
                                    @if($ordonnateur->telephone)
                                        <div class="responsable-contact">
                                            <i class="fas fa-phone"></i> {{ $ordonnateur->telephone }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        @if($commune->receveurs->count() == 0 && $commune->ordonnateurs->count() == 0)
                            <div class="responsable-card">
                                <div class="text-center text-muted">
                                    <i class="fas fa-user-slash fa-2x mb-2"></i>
                                    <p>Aucun responsable assigné</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dettes par type -->
            <div class="content-section animate-slide-up">
                <div class="section-header">
                    <h3>
                        <div class="section-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        Dettes {{ $annee }}
                    </h3>
                </div>
                <div class="section-content">
                    <div class="dettes-grid">
                        <div class="dette-card">
                            <div class="dette-card-header">
                                <div class="dette-type">CNPS</div>
                                <div class="dette-icon cnps">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                            </div>
                            <div class="dette-montant">{{ number_format($detailsDettes['cnps']['montant']) }} FCFA</div>
                            @if($detailsDettes['cnps']['details']->count() > 0)
                                <div class="dette-details">
                                    {{ $detailsDettes['cnps']['details']->count() }} dette(s) enregistrée(s)
                                </div>
                            @endif
                        </div>

                        <div class="dette-card">
                            <div class="dette-card-header">
                                <div class="dette-type">Fiscale</div>
                                <div class="dette-icon fiscale">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                            </div>
                            <div class="dette-montant">{{ number_format($detailsDettes['fiscale']['montant']) }} FCFA</div>
                            @if($detailsDettes['fiscale']['details']->count() > 0)
                                <div class="dette-details">
                                    {{ $detailsDettes['fiscale']['details']->count() }} dette(s) enregistrée(s)
                                </div>
                            @endif
                        </div>

                        <div class="dette-card">
                            <div class="dette-card-header">
                                <div class="dette-type">FEICOM</div>
                                <div class="dette-icon feicom">
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>
                            <div class="dette-montant">{{ number_format($detailsDettes['feicom']['montant']) }} FCFA</div>
                            @if($detailsDettes['feicom']['details']->count() > 0)
                                <div class="dette-details">
                                    {{ $detailsDettes['feicom']['details']->count() }} dette(s) enregistrée(s)
                                </div>
                            @endif
                        </div>

                        <div class="dette-card">
                            <div class="dette-card-header">
                                <div class="dette-type">Salariale</div>
                                <div class="dette-icon salariale">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="dette-montant">{{ number_format($detailsDettes['salariale']['montant']) }} FCFA</div>
                            @if($detailsDettes['salariale']['details']->count() > 0)
                                <div class="dette-details">
                                    {{ $detailsDettes['salariale']['details']->count() }} dette(s) enregistrée(s)
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Défaillances et retards -->
            <div class="content-section animate-slide-up">
                <div class="section-header">
                    <h3>
                        <div class="section-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        Alertes {{ $annee }}
                    </h3>
                </div>
                <div class="section-content">
                    <div class="issues-grid">
                        <div class="issue-card">
                            <div class="issue-header">
                                <div class="issue-icon defaillances">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <div class="issue-count">{{ $defaillancesRetards['nb_defaillances'] ?? 0 }}</div>
                                    <div class="issue-label">Défaillances</div>
                                </div>
                            </div>
                            @if(($defaillancesRetards['nb_defaillances'] ?? 0) > 0)
                                <div class="issue-details">
                                    Nécessitent une attention immédiate
                                </div>
                            @endif
                        </div>

                        <div class="issue-card">
                            <div class="issue-header">
                                <div class="issue-icon retards">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <div class="issue-count">{{ $defaillancesRetards['nb_retards'] ?? 0 }}</div>
                                    <div class="issue-label">Retards</div>
                                </div>
                            </div>
                            @if(($defaillancesRetards['nb_retards'] ?? 0) > 0)
                                <div class="issue-details">
                                    Procédures en retard
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparaisons avec le département -->
            <div class="content-section animate-slide-up">
                <div class="section-header">
                    <h3>
                        <div class="section-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        Comparaisons
                    </h3>
                </div>
                <div class="section-content">
                    <div class="financial-grid">
                        <div class="financial-item">
                            <div class="financial-value">{{ number_format($comparaisons['taux_commune'], 1) }}%</div>
                            <div class="financial-label">Taux Commune</div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-value">{{ number_format($comparaisons['taux_moyen_departement'], 1) }}%</div>
                            <div class="financial-label">Moy. Département</div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-value">{{ $comparaisons['rang_departement'] ?? 'N/A' }}</div>
                            <div class="financial-label">Rang Dépt.</div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-value">{{ $comparaisons['nombre_communes_departement'] }}</div>
                            <div class="financial-label">Total Communes</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations générales -->
            <div class="content-section animate-slide-up">
                <div class="section-header">
                    <h3>
                        <div class="section-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        Informations
                    </h3>
                </div>
                <div class="section-content">
                    <div class="info-list">
                        <div class="info-item">
                            <strong>Téléphone:</strong>
                            <span>{{ $commune->telephone ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong>
                            <span>{{ $commune->email ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Adresse:</strong>
                            <span>{{ $commune->adresse ?? 'Non renseignée' }}</span>
                        </div>
                        @if($commune->coordonnees_gps)
                            <div class="info-item">
                                <strong>Coordonnées GPS:</strong>
                                <span>{{ $commune->coordonnees_gps }}</span>
                            </div>
                        @endif
                        <div class="info-item">
                            <strong>Créée le:</strong>
                            <span>{{ $commune->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        @if($commune->updated_at != $commune->created_at)
                            <div class="info-item">
                                <strong>Modifiée le:</strong>
                                <span>{{ $commune->updated_at->format('d/m/Y à H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Modal d'export -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-download me-2"></i>
                    Exporter les données de {{ $commune->nom }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="export-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Format d'export</label>
                                <select name="format" class="form-control">
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel (XLSX)</option>
                                    <option value="csv">CSV</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Type de données</label>
                                <select name="type" class="form-control">
                                    <option value="complet">Rapport complet</option>
                                    <option value="financier">Données financières</option>
                                    <option value="dettes">Détail des dettes</option>
                                    <option value="synthese">Synthèse exécutive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Année d'exercice</label>
                        <select name="annee_export" class="form-control">
                            @foreach($anneesDisponibles as $anneeOption)
                                <option value="{{ $anneeOption }}" {{ $anneeOption == $annee ? 'selected' : '' }}>
                                    {{ $anneeOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="include-charts" name="include_charts" checked>
                            <label class="form-check-label" for="include-charts">
                                Inclure les graphiques et visualisations
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="executeExport()">
                    <i class="fas fa-download me-2"></i>
                    Exporter
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration du graphique d'évolution
    @if($evolutionPerformances->count() > 0)
    const ctx = document.getElementById('evolutionChart');
    if (ctx) {
        const evolutionData = @json($evolutionPerformances->pluck('pourcentage'));
        const evolutionLabels = @json($evolutionPerformances->pluck('annee_exercice'));
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: evolutionLabels,
                datasets: [{
                    label: 'Taux de Réalisation (%)',
                    data: evolutionData,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1e293b',
                        bodyColor: '#64748b',
                        borderColor: 'rgba(102, 126, 234, 0.2)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Taux: ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                weight: 500
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)',
                        },
                        ticks: {
                            color: '#64748b',
                            callback: function(value) {
                                return value + '%';
                            },
                            font: {
                                weight: 500
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
    @endif
    
    // Animation des progress bars
    setTimeout(() => {
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            
            setTimeout(() => {
                bar.style.width = width;
            }, 500);
        });
    }, 1000);
    
    // Animation des cartes au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '50px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.stat-card, .content-section').forEach(el => {
        observer.observe(el);
    });
});

// Fonctions JavaScript
function changeAnnee(annee) {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('annee', annee);
    window.location.href = currentUrl.toString();
}

function refreshData() {
    const btn = event.target;
    const icon = btn.querySelector('i');
    
    btn.disabled = true;
    icon.classList.add('fa-spin');
    
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

function exportCommune() {
    const modal = new bootstrap.Modal(document.getElementById('exportModal'));
    modal.show();
}

function executeExport() {
    const form = document.getElementById('export-form');
    const formData = new FormData(form);
    
    const params = new URLSearchParams();
    for (const [key, value] of formData.entries()) {
        params.append(key, value);
    }
    
    // Ajouter l'ID de la commune
    params.append('commune_id', '{{ $commune->id }}');
    
    window.location.href = `/communes/{{ $commune->id }}/export?${params.toString()}`;
    
    // Fermer le modal
    bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
}

// Gestion des erreurs
window.addEventListener('error', function(e) {
    console.error('Erreur JavaScript:', e.error);
    
    // Afficher une notification d'erreur
    showNotification('Une erreur inattendue s\'est produite', 'error');
});

// Système de notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification-toast`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        max-width: 400px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    `;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Suppression automatique
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}
</script>

<style>
.info-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.info-item strong {
    color: #1e293b;
    font-weight: 600;
    min-width: 120px;
}

.info-item span {
    color: #64748b;
    text-align: right;
    flex: 1;
}

.issue-details {
    margin-top: 0.5rem;
    font-size: 0.75rem;
    color: #64748b;
}

.notification-toast {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.notification-toast.alert-success {
    background: rgba(16, 185, 129, 0.9);
    color: white;
}

.notification-toast.alert-error {
    background: rgba(239, 68, 68, 0.9);
    color: white;
}

.notification-toast.alert-info {
    background: rgba(59, 130, 246, 0.9);
    color: white;
}

@media print {
    .commune-actions,
    .breadcrumb,
    .filter-section,
    .btn,
    .modal {
        display: none !important;
    }
    
    .commune-dashboard {
        padding: 1rem;
    }
    
    .content-section {
        break-inside: avoid;
        page-break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
        margin-bottom: 1rem;
    }
    
    .commune-header {
        background: #f8f9fa !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
    }
}
</style>
@endpush