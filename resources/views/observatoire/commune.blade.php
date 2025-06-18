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
                    <i class="fas fa-users"></i>
                    {{ number_format($commune->population) }} habitants
                </span>
                <span class="meta-item">
                    <i class="fas fa-building"></i>
                    {{ $commune->type }}
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
@endsection