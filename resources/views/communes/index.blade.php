@extends('layouts.app')

@section('title', 'Gestion des Communes - Observatoire des Collectivités')
@section('page-title', 'Communes du Cameroun')

@push('styles')
<style>
/* Variables CSS pour la cohérence */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --card-shadow: 0 4px 25px rgba(0,0,0,0.1);
    --card-shadow-hover: 0 8px 40px rgba(0,0,0,0.15);
    --border-radius: 12px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Layout principal */
.communes-dashboard {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.dashboard-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border-radius: var(--border-radius);
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
}

.header-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    background: linear-gradient(45deg, #fff, #e2e8f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.header-subtitle {
    font-size: 1.1rem;
    opacity: 0.8;
    margin: 0;
}

.quick-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn-glass {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    text-decoration: none;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
}

.btn-glass:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    color: white;
}

.btn-primary-gradient {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary-gradient:hover {
    transform: translateY(-2px);
    box-shadow: var(--card-shadow-hover);
    color: white;
}

/* Statistiques KPI */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.kpi-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
}

.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow-hover);
}

.kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.kpi-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    background: var(--primary-gradient);
}

.kpi-trend {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.kpi-trend.positive {
    color: #10b981;
}

.kpi-trend.negative {
    color: #ef4444;
}

.kpi-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.kpi-label {
    color: #64748b;
    font-size: 1rem;
    font-weight: 500;
}

/* Filtres avancés */
.filters-panel {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    margin-bottom: 2rem;
    overflow: hidden;
}

.filters-header {
    background: #f8fafc;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.filters-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.filters-toggle {
    background: none;
    border: none;
    color: #6366f1;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
}

.filters-content {
    padding: 1.5rem;
    max-height: 400px;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.filters-content.show {
    max-height: 500px;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: var(--transition);
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

.filters-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.btn-outline {
    background: white;
    border: 2px solid #e5e7eb;
    color: #374151;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-outline:hover {
    border-color: #6366f1;
    color: #6366f1;
    background: #f8fafc;
}

/* Table moderne */
.communes-table-container {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    overflow: hidden;
}

.table-header {
    background: #f8fafc;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.table-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.view-toggle {
    display: flex;
    background: #e5e7eb;
    border-radius: 6px;
    padding: 2px;
}

.view-toggle button {
    background: none;
    border: none;
    padding: 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    transition: var(--transition);
    color: #6b7280;
}

.view-toggle button.active {
    background: white;
    color: #6366f1;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.communes-table {
    width: 100%;
    border-collapse: collapse;
}

.communes-table th {
    background: #f8fafc;
    padding: 1rem 1.5rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e2e8f0;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.communes-table td {
    padding: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.communes-table tbody tr {
    transition: var(--transition);
}

.communes-table tbody tr:hover {
    background: #f8fafc;
}

.commune-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.commune-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
}

.commune-details h5 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
}

.commune-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.75rem;
    color: #64748b;
}

.commune-meta span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    line-height: 1;
}

.badge-primary {
    background: #ddd6fe;
    color: #6366f1;
}

.badge-success {
    background: #dcfce7;
    color: #16a34a;
}

.badge-warning {
    background: #fef3c7;
    color: #d97706;
}

.badge-danger {
    background: #fee2e2;
    color: #dc2626;
}

.performance-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.performance-bar {
    width: 60px;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.performance-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s ease;
}

.performance-fill.excellent {
    background: #10b981;
}

.performance-fill.good {
    background: #f59e0b;
}

.performance-fill.average {
    background: #6366f1;
}

.performance-fill.poor {
    background: #ef4444;
}

.actions-dropdown {
    position: relative;
}

.actions-btn {
    background: none;
    border: none;
    padding: 0.5rem;
    border-radius: 50%;
    cursor: pointer;
    color: #6b7280;
    transition: var(--transition);
}

.actions-btn:hover {
    background: #f3f4f6;
    color: #374151;
}

.actions-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: var(--card-shadow);
    min-width: 160px;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: var(--transition);
}

.actions-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.actions-menu a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    color: #374151;
    text-decoration: none;
    font-size: 0.875rem;
    transition: var(--transition);
}

.actions-menu a:hover {
    background: #f3f4f6;
}

.actions-menu a.danger:hover {
    background: #fee2e2;
    color: #dc2626;
}

/* Vue grille */
.communes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
}

.commune-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    overflow: hidden;
    border: 1px solid #f1f5f9;
}

.commune-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow-hover);
}

.commune-card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.commune-card-body {
    padding: 1.5rem;
}

.commune-card-footer {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Pagination moderne */
.pagination-container {
    background: white;
    padding: 1.5rem;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pagination-info {
    color: #64748b;
    font-size: 0.875rem;
}

.pagination {
    display: flex;
    gap: 0.25rem;
}

.pagination a,
.pagination span {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e5e7eb;
    color: #374151;
    text-decoration: none;
    border-radius: 6px;
    transition: var(--transition);
    font-size: 0.875rem;
}

.pagination a:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.pagination .active span {
    background: #6366f1;
    color: white;
    border-color: #6366f1;
}

/* État vide */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #64748b;
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: #94a3b8;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.empty-state p {
    font-size: 1rem;
    margin-bottom: 2rem;
}

/* Responsive */
@media (max-width: 1024px) {
    .kpi-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .communes-dashboard {
        padding: 1rem 0;
    }
    
    .dashboard-header {
        padding: 1.5rem;
    }
    
    .header-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .header-title {
        font-size: 2rem;
    }
    
    .kpi-grid {
        grid-template-columns: 1fr;
    }
    
    .communes-table-container {
        overflow-x: auto;
    }
    
    .communes-table {
        min-width: 800px;
    }
    
    .communes-grid {
        grid-template-columns: 1fr;
        padding: 1rem;
    }
}

/* Animations */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-up {
    animation: slideInUp 0.5s ease-out;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.loading {
    animation: pulse 1.5s ease-in-out infinite;
}
</style>
@endpush

@section('content')
<div class="communes-dashboard">
    <div class="container-fluid">
        <!-- En-tête du dashboard -->
        <div class="dashboard-header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Communes du Cameroun</h1>
                    <p class="header-subtitle">Gestion et suivi des 384 collectivités territoriales décentralisées</p>
                </div>
                <div class="quick-actions">
                    <a href="{{ route('communes.create') }}" class="btn-primary-gradient">
                        <i class="fas fa-plus"></i>
                        Nouvelle Commune
                    </a>
                    <a href="#" class="btn-glass" onclick="exportData()">
                        <i class="fas fa-download"></i>
                        Exporter
                    </a>
                    <button class="btn-glass" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Indicateurs KPI -->
        <div class="kpi-grid">
            <div class="kpi-card animate-slide-up">
                <div class="kpi-header">
                    <div class="kpi-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-city"></i>
                    </div>
                    <div class="kpi-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        +2.3%
                    </div>
                </div>
                <div class="kpi-value">{{ number_format($stats['total']) }}</div>
                <div class="kpi-label">Communes actives</div>
            </div>

            <div class="kpi-card animate-slide-up" style="animation-delay: 0.1s">
                <div class="kpi-header">
                    <div class="kpi-icon" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="kpi-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        +5.7%
                    </div>
                </div>
                <div class="kpi-value">{{ number_format($stats['budget_total'] / 1000000000, 1) }}Mds</div>
                <div class="kpi-label">Budget total (FCFA)</div>
            </div>

            <div class="kpi-card animate-slide-up" style="animation-delay: 0.2s">
                <div class="kpi-header">
                    <div class="kpi-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="kpi-trend {{ $stats['performance_moyenne'] >= 70 ? 'positive' : 'negative' }}">
                        <i class="fas fa-arrow-{{ $stats['performance_moyenne'] >= 70 ? 'up' : 'down' }}"></i>
                        {{ $stats['performance_moyenne'] >= 70 ? '+' : '' }}{{ number_format($stats['performance_moyenne'] - 65, 1) }}%
                    </div>
                </div>
                <div class="kpi-value">{{ number_format($stats['performance_moyenne'], 1) }}%</div>
                <div class="kpi-label">Performance moyenne</div>
            </div>

            <div class="kpi-card animate-slide-up" style="animation-delay: 0.3s">
                <div class="kpi-header">
                    <div class="kpi-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="kpi-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        +12
                    </div>
                </div>
                <div class="kpi-value">{{ $stats['avec_receveur'] + $stats['avec_ordonnateur'] }}</div>
                <div class="kpi-label">Responsables assignés</div>
            </div>
        </div>

        <!-- Messages de feedback -->
        @if(session('success'))
            <div class="alert alert-success animate-slide-up">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger animate-slide-up">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Panel de filtres -->
        <div class="filters-panel animate-slide-up">
            <div class="filters-header">
                <h3 class="filters-title">Filtres de recherche</h3>
                <button class="filters-toggle" onclick="toggleFilters()">
                    <span id="filters-toggle-text">Afficher les filtres</span>
                    <i class="fas fa-chevron-down" id="filters-toggle-icon"></i>
                </button>
            </div>
            <div class="filters-content" id="filters-content">
                <form method="GET" action="{{ route('communes.index') }}" id="filters-form">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label class="form-label" for="search">Recherche générale</label>
                            <input 
                                type="text" 
                                id="search" 
                                name="search" 
                                class="form-control" 
                                placeholder="Nom, code, département..."
                                value="{{ request('search') }}"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="region_id">Région</label>
                            <select id="region_id" name="region_id" class="form-control form-select">
                                <option value="">Toutes les régions</option>
                                @foreach($departements->groupBy('region.nom') as $regionNom => $depts)
                                    <option value="{{ $depts->first()->region->id }}" 
                                            {{ request('region_id') == $depts->first()->region->id ? 'selected' : '' }}>
                                        {{ $regionNom }} ({{ $depts->sum(function($d) { return $d->communes->count(); }) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="departement_id">Département</label>
                            <select id="departement_id" name="departement_id" class="form-control form-select">
                                <option value="">Tous les départements</option>
                                @foreach($departements as $departement)
                                    <option value="{{ $departement->id }}" 
                                            {{ request('departement_id') == $departement->id ? 'selected' : '' }}>
                                        {{ $departement->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="population_min">Population min.</label>
                            <input 
                                type="number" 
                                id="population_min" 
                                name="population_min" 
                                class="form-control" 
                                placeholder="Ex: 10000"
                                value="{{ request('population_min') }}"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="performance">Performance</label>
                            <select id="performance" name="performance" class="form-control form-select">
                                <option value="">Toutes performances</option>
                                <option value="excellente" {{ request('performance') == 'excellente' ? 'selected' : '' }}>
                                    Excellente (≥ 90%)
                                </option>
                                <option value="bonne" {{ request('performance') == 'bonne' ? 'selected' : '' }}>
                                    Bonne (75-89%)
                                </option>
                                <option value="moyenne" {{ request('performance') == 'moyenne' ? 'selected' : '' }}>
                                    Moyenne (50-74%)
                                </option>
                                <option value="faible" {{ request('performance') == 'faible' ? 'selected' : '' }}>
                                    Faible (< 50%)
                                </option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="avec_receveur">Receveur assigné</label>
                            <select id="avec_receveur" name="avec_receveur" class="form-control form-select">
                                <option value="">Peu importe</option>
                                <option value="1" {{ request('avec_receveur') == '1' ? 'selected' : '' }}>
                                    Avec receveur
                                </option>
                                <option value="0" {{ request('avec_receveur') == '0' ? 'selected' : '' }}>
                                    Sans receveur
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filters-actions">
                        <a href="{{ route('communes.index') }}" class="btn-outline">
                            <i class="fas fa-times"></i>
                            Réinitialiser
                        </a>
                        <button type="submit" class="btn-primary-gradient">
                            <i class="fas fa-search"></i>
                            Appliquer les filtres
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau/Grille des communes -->
        <div class="communes-table-container animate-slide-up">
            <div class="table-header">
                <h3 class="table-title">
                    Liste des communes
                    @if($communes->total() > 0)
                        <small class="text-muted">({{ number_format($communes->total()) }} résultats)</small>
                    @endif
                </h3>
                <div class="table-actions">
                    <div class="view-toggle">
                        <button type="button" class="active" data-view="table">
                            <i class="fas fa-table"></i>
                        </button>
                        <button type="button" data-view="grid">
                            <i class="fas fa-th-large"></i>
                        </button>
                    </div>
                    <select class="form-control form-select" style="width: auto;" onchange="changePerPage(this.value)">
                        <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 par page</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 par page</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 par page</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 par page</option>
                    </select>
                </div>
            </div>
            
            <div id="table-view">
                @if($communes->count() > 0)
                    <table class="communes-table">
                        <thead>
                            <tr>
                                <th>
                                    <a href="#" onclick="sortTable('nom')" class="text-decoration-none">
                                        Commune
                                        <i class="fas fa-sort ms-1 {{ request('sort_by') == 'nom' ? 'text-primary' : '' }}"></i>
                                    </a>
                                </th>
                                <th>Localisation</th>
                                <th>Population</th>
                                <th>Performance</th>
                                <th>Responsables</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($communes as $commune)
                                <tr>
                                    <td>
                                        <div class="commune-info">
                                            <div class="commune-avatar">
                                                {{ strtoupper(substr($commune->nom, 0, 2)) }}
                                            </div>
                                            <div class="commune-details">
                                                <h5>{{ $commune->nom }}</h5>
                                                <div class="commune-meta">
                                                    <span>
                                                        <i class="fas fa-code"></i>
                                                        {{ $commune->code }}
                                                    </span>
                                                    @if($commune->superficie)
                                                        <span>
                                                            <i class="fas fa-expand-arrows-alt"></i>
                                                            {{ number_format($commune->superficie) }} km²
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $commune->departement->nom }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $commune->departement->region->nom }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($commune->population)
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-users me-2 text-muted"></i>
                                                {{ number_format($commune->population) }}
                                            </div>
                                        @else
                                            <span class="text-muted">Non renseigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $performance = $commune->tauxRealisations->where('annee_exercice', date('Y'))->first();
                                            $taux = $performance ? $performance->pourcentage : 0;
                                            $class = $taux >= 90 ? 'excellent' : ($taux >= 75 ? 'good' : ($taux >= 50 ? 'average' : 'poor'));
                                            $badge = $taux >= 90 ? 'success' : ($taux >= 75 ? 'warning' : ($taux >= 50 ? 'primary' : 'danger'));
                                        @endphp
                                        <div class="performance-indicator">
                                            <span class="badge badge-{{ $badge }}">{{ number_format($taux, 1) }}%</span>
                                            <div class="performance-bar">
                                                <div class="performance-fill {{ $class }}" style="width: {{ $taux }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @if($commune->receveurs->count() > 0)
                                                <small class="d-flex align-items-center">
                                                    <i class="fas fa-user-tie me-1 text-primary"></i>
                                                    {{ $commune->receveurs->first()->nom }}
                                                </small>
                                            @endif
                                            @if($commune->ordonnateurs->count() > 0)
                                                <small class="d-flex align-items-center">
                                                    <i class="fas fa-user-cog me-1 text-success"></i>
                                                    {{ $commune->ordonnateurs->first()->nom }}
                                                </small>
                                            @endif
                                            @if($commune->receveurs->count() == 0 && $commune->ordonnateurs->count() == 0)
                                                <small class="text-muted">Aucun responsable</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="actions-dropdown">
                                            <button class="actions-btn" onclick="toggleActionsMenu({{ $commune->id }})">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="actions-menu" id="actions-menu-{{ $commune->id }}">
                                                <a href="{{ route('communes.show', $commune) }}">
                                                    <i class="fas fa-eye"></i>
                                                    Voir détails
                                                </a>
                                                <a href="{{ route('communes.edit', $commune) }}">
                                                    <i class="fas fa-edit"></i>
                                                    Modifier
                                                </a>
                                                <a href="#" onclick="exportCommune({{ $commune->id }})">
                                                    <i class="fas fa-download"></i>
                                                    Exporter
                                                </a>
                                                <a href="#" onclick="duplicateCommune({{ $commune->id }})">
                                                    <i class="fas fa-copy"></i>
                                                    Dupliquer
                                                </a>
                                                <div style="border-top: 1px solid #f1f5f9; margin: 0.5rem 0;"></div>
                                                <a href="#" class="danger" onclick="deleteCommune({{ $commune->id }}, '{{ $commune->nom }}')">
                                                    <i class="fas fa-trash"></i>
                                                    Supprimer
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>Aucune commune trouvée</h3>
                        <p>Aucune commune ne correspond à vos critères de recherche.</p>
                        @if(request()->hasAny(['search', 'departement_id', 'region_id', 'performance']))
                            <a href="{{ route('communes.index') }}" class="btn-outline">
                                <i class="fas fa-times"></i>
                                Réinitialiser les filtres
                            </a>
                        @else
                            <a href="{{ route('communes.create') }}" class="btn-primary-gradient">
                                <i class="fas fa-plus"></i>
                                Créer la première commune
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            
            <!-- Vue grille -->
            <div id="grid-view" style="display: none;">
                @if($communes->count() > 0)
                    <div class="communes-grid">
                        @foreach($communes as $commune)
                            @php
                                $performance = $commune->tauxRealisations->where('annee_exercice', date('Y'))->first();
                                $taux = $performance ? $performance->pourcentage : 0;
                                $class = $taux >= 90 ? 'excellent' : ($taux >= 75 ? 'good' : ($taux >= 50 ? 'average' : 'poor'));
                                $badge = $taux >= 90 ? 'success' : ($taux >= 75 ? 'warning' : ($taux >= 50 ? 'primary' : 'danger'));
                            @endphp
                            <div class="commune-card">
                                <div class="commune-card-header">
                                    <div class="commune-info">
                                        <div class="commune-avatar">
                                            {{ strtoupper(substr($commune->nom, 0, 2)) }}
                                        </div>
                                        <div class="commune-details">
                                            <h5>{{ $commune->nom }}</h5>
                                            <div class="commune-meta">
                                                <span>{{ $commune->departement->nom }}</span>
                                                <span>{{ $commune->departement->region->nom }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="commune-card-body">
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Code</small>
                                            <div class="fw-semibold">{{ $commune->code }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Population</small>
                                            <div class="fw-semibold">
                                                {{ $commune->population ? number_format($commune->population) : 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">Performance {{ date('Y') }}</small>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge badge-{{ $badge }}">{{ number_format($taux, 1) }}%</span>
                                            <div class="performance-bar flex-grow-1">
                                                <div class="performance-fill {{ $class }}" style="width: {{ $taux }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">Responsables</small>
                                        <div class="mt-1">
                                            @if($commune->receveurs->count() > 0)
                                                <div class="d-flex align-items-center mb-1">
                                                    <i class="fas fa-user-tie me-2 text-primary"></i>
                                                    <small>{{ $commune->receveurs->first()->nom }}</small>
                                                </div>
                                            @endif
                                            @if($commune->ordonnateurs->count() > 0)
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-cog me-2 text-success"></i>
                                                    <small>{{ $commune->ordonnateurs->first()->nom }}</small>
                                                </div>
                                            @endif
                                            @if($commune->receveurs->count() == 0 && $commune->ordonnateurs->count() == 0)
                                                <small class="text-muted">Aucun responsable assigné</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="commune-card-footer">
                                    <a href="{{ route('communes.show', $commune) }}" class="btn-outline btn-sm">
                                        <i class="fas fa-eye"></i>
                                        Voir
                                    </a>
                                    <div class="actions-dropdown">
                                        <button class="actions-btn" onclick="toggleActionsMenu({{ $commune->id }})">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="actions-menu" id="actions-menu-card-{{ $commune->id }}">
                                            <a href="{{ route('communes.edit', $commune) }}">
                                                <i class="fas fa-edit"></i>
                                                Modifier
                                            </a>
                                            <a href="#" onclick="exportCommune({{ $commune->id }})">
                                                <i class="fas fa-download"></i>
                                                Exporter
                                            </a>
                                            <a href="#" class="danger" onclick="deleteCommune({{ $commune->id }}, '{{ $commune->nom }}')">
                                                <i class="fas fa-trash"></i>
                                                Supprimer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Pagination -->
            @if($communes->hasPages())
                <div class="pagination-container">
                    <div class="pagination-info">
                        Affichage {{ $communes->firstItem() }} - {{ $communes->lastItem() }} 
                        sur {{ number_format($communes->total()) }} communes
                    </div>
                    <div class="pagination">
                        {{ $communes->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la commune <strong id="commune-name-to-delete"></strong> ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    Cette action est irréversible et supprimera toutes les données associées.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn">
                    <i class="fas fa-trash me-2"></i>
                    Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'export -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-download me-2"></i>
                    Exporter les données
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="export-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Format d'export</label>
                                <select name="format" class="form-control form-select">
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel (XLSX)</option>
                                    <option value="csv">CSV</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Type de données</label>
                                <select name="type" class="form-control form-select">
                                    <option value="complet">Rapport complet</option>
                                    <option value="resume">Résumé exécutif</option>
                                    <option value="financier">Données financières uniquement</option>
                                    <option value="gouvernance">Données de gouvernance</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Période</label>
                        <select name="annee" class="form-control form-select">
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
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
<script>
// Variables globales
let currentView = 'table';
let communeToDelete = null;

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializeViewToggle();
    initializeActionsMenus();
    
    // Animation des cartes au chargement
    animateCards();
});

// Gestion des filtres
function toggleFilters() {
    const content = document.getElementById('filters-content');
    const icon = document.getElementById('filters-toggle-icon');
    const text = document.getElementById('filters-toggle-text');
    
    if (content.classList.contains('show')) {
        content.classList.remove('show');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
        text.textContent = 'Afficher les filtres';
    } else {
        content.classList.add('show');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
        text.textContent = 'Masquer les filtres';
    }
}

function initializeFilters() {
    // Auto-submit sur changement de sélection
    const selects = document.querySelectorAll('#filters-form select');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            if (this.value !== '') {
                document.getElementById('filters-form').submit();
            }
        });
    });
    
    // Filtre en cascade région -> département
    const regionSelect = document.getElementById('region_id');
    const departementSelect = document.getElementById('departement_id');
    
    if (regionSelect && departementSelect) {
        regionSelect.addEventListener('change', function() {
            const regionId = this.value;
            
            // Réinitialiser le département
            departementSelect.innerHTML = '<option value="">Tous les départements</option>';
            
            if (regionId) {
                fetch(`/api/departements?region_id=${regionId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(dept => {
                            const option = document.createElement('option');
                            option.value = dept.id;
                            option.textContent = dept.nom;
                            departementSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Erreur:', error));
            }
        });
    }
}

// Gestion des vues (table/grille)
function initializeViewToggle() {
    const toggleButtons = document.querySelectorAll('.view-toggle button');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const view = this.dataset.view;
            switchView(view);
            
            // Mettre à jour l'état actif
            toggleButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

function switchView(view) {
    const tableView = document.getElementById('table-view');
    const gridView = document.getElementById('grid-view');
    
    if (view === 'grid') {
        tableView.style.display = 'none';
        gridView.style.display = 'block';
        currentView = 'grid';
    } else {
        tableView.style.display = 'block';
        gridView.style.display = 'none';
        currentView = 'table';
    }
}

// Gestion des menus d'actions
function initializeActionsMenus() {
    // Fermer les menus quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.actions-dropdown')) {
            document.querySelectorAll('.actions-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
}

function toggleActionsMenu(communeId) {
    const menu = document.getElementById(`actions-menu-${communeId}`) || 
                 document.getElementById(`actions-menu-card-${communeId}`);
    
    // Fermer les autres menus
    document.querySelectorAll('.actions-menu').forEach(otherMenu => {
        if (otherMenu !== menu) {
            otherMenu.classList.remove('show');
        }
    });
    
    // Basculer le menu actuel
    menu.classList.toggle('show');
}

// Tri des colonnes
function sortTable(column) {
    const currentSort = new URLSearchParams(window.location.search).get('sort_by');
    const currentDirection = new URLSearchParams(window.location.search).get('sort_direction') || 'asc';
    
    let newDirection = 'asc';
    if (currentSort === column && currentDirection === 'asc') {
        newDirection = 'desc';
    }
    
    const url = new URL(window.location);
    url.searchParams.set('sort_by', column);
    url.searchParams.set('sort_direction', newDirection);
    
    window.location.href = url.toString();
}

// Changement du nombre d'éléments par page
function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Retour à la première page
    
    window.location.href = url.toString();
}

// Actualisation des données
function refreshData() {
    const btn = event.target.closest('.btn-glass');
    const icon = btn.querySelector('i');
    
    icon.classList.add('fa-spin');
    
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Suppression de commune
function deleteCommune(communeId, communeName) {
    communeToDelete = communeId;
    document.getElementById('commune-name-to-delete').textContent = communeName;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Confirmation de suppression
document.getElementById('confirm-delete-btn').addEventListener('click', function() {
    if (communeToDelete) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/communes/${communeToDelete}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        
        // Afficher un loader
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Suppression...';
        this.disabled = true;
        
        form.submit();
    }
});

// Export de données
function exportData() {
    const modal = new bootstrap.Modal(document.getElementById('exportModal'));
    modal.show();
}

function exportCommune(communeId) {
    // Export d'une commune spécifique
    window.location.href = `/communes/${communeId}/export/pdf`;
}

function executeExport() {
    const form = document.getElementById('export-form');
    const formData = new FormData(form);
    
    const params = new URLSearchParams();
    for (const [key, value] of formData.entries()) {
        params.append(key, value);
    }
    
    // Ajouter les filtres actuels
    const currentParams = new URLSearchParams(window.location.search);
    for (const [key, value] of currentParams.entries()) {
        if (['search', 'departement_id', 'region_id', 'performance'].includes(key)) {
            params.append(`filter_${key}`, value);
        }
    }
    
    window.location.href = `/communes/export?${params.toString()}`;
}

// Duplication de commune
function duplicateCommune(communeId) {
    window.location.href = `/communes/${communeId}/duplicate`;
}

// Animations
function animateCards() {
    const cards = document.querySelectorAll('.kpi-card, .commune-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    entry.target.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
            }
        });
    });
    
    cards.forEach(card => observer.observe(card));
}

// Recherche en temps réel
let searchTimeout;
const searchInput = document.getElementById('search');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                document.getElementById('filters-form').submit();
            }
        }, 500);
    });
}

// Gestion des erreurs AJAX
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
@endpush




{{-- @extends('layouts.app')

@section('title', 'Liste des Communes - Observatoire des Collectivités')
@section('page-title', 'Gestion des Communes')

@push('styles')
<style>
.communes-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.communes-actions {
    display: flex;
    gap: 1rem;
}

.search-filters {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.filter-grid {
    display: grid;
    grid-template-columns: 2fr 1fr auto;
    gap: 1rem;
    align-items: end;
}

.communes-table {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table {
    width: 100%;
    margin: 0;
}

.table th {
    background: #f8f9fa;
    font-weight: 600;
    padding: 1rem;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
}

.commune-info h5 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
}

.commune-meta {
    font-size: 0.875rem;
    color: #666;
    display: flex;
    gap: 1rem;
}

.commune-meta span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.commune-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 4px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #1e7e34;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background: #e0a800;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #666;
    font-size: 0.875rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.875rem;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.pagination-wrapper {
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.alert {
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
    border: 1px solid transparent;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-error {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.sortable {
    cursor: pointer;
    user-select: none;
}

.sortable:hover {
    background: #e9ecef;
}

.sort-indicator {
    margin-left: 0.5rem;
    opacity: 0.5;
}

.sort-indicator.active {
    opacity: 1;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #666;
}

.empty-state i {
    font-size: 3rem;
    color: #ccc;
    margin-bottom: 1rem;
}

.badge {
    display: inline-block;
    padding: 0.25em 0.4em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}

.badge-secondary {
    color: #fff;
    background-color: #6c757d;
}

.text-muted {
    color: #6c757d;
}

.responsables-mini .responsable-item {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.responsables-mini .responsable-item i {
    width: 16px;
    margin-right: 0.5rem;
}
</style>
@endpush

@section('content')
<div class="communes-container">
    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- En-tête -->
    <div class="communes-header">
        <h2>Gestion des Communes</h2>
        <div class="communes-actions">
            <a href="{{ route('communes.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i>
                Nouvelle Commune
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $communes->total() }}</div>
            <div class="stat-label">Total Communes</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $communes->where('receveurs', '!=', null)->count() }}</div>
            <div class="stat-label">Avec Receveur</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $communes->where('ordonnateurs', '!=', null)->count() }}</div>
            <div class="stat-label">Avec Ordonnateur</div>
        </div>
    </div>

    <!-- Filtres de recherche -->
    <div class="search-filters">
        <form method="GET" action="{{ route('communes.index') }}">
            <div class="filter-grid">
                <div class="form-group">
                    <label for="search">Rechercher</label>
                    <input 
                        type="text" 
                        id="search" 
                        name="search" 
                        class="form-control" 
                        placeholder="Nom, code ou département..."
                        value="{{ request('search') }}"
                    >
                </div>
                
                <div class="form-group">
                    <label for="departement_id">Département</label>
                    <select id="departement_id" name="departement_id" class="form-control">
                        <option value="">Tous les départements</option>
                        @foreach($departements as $departement)
                            <option value="{{ $departement->id }}" 
                                    {{ request('departement_id') == $departement->id ? 'selected' : '' }}>
                                {{ $departement->nom }} ({{ $departement->region->nom }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Rechercher
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tableau des communes -->
    <div class="communes-table">
        @if($communes->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable('nom')">
                            Commune
                            <i class="fas fa-sort sort-indicator {{ request('sort_by') == 'nom' ? 'active' : '' }}"></i>
                        </th>
                        <th class="sortable" onclick="sortTable('code')">
                            Code
                            <i class="fas fa-sort sort-indicator {{ request('sort_by') == 'code' ? 'active' : '' }}"></i>
                        </th>
                        <th>Département</th>
                        <th>Responsables</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($communes as $commune)
                        <tr>
                            <td>
                                <div class="commune-info">
                                    <h5>{{ $commune->nom }}</h5>
                                    @if($commune->population)
                                        <div class="commune-meta">
                                            <span>
                                                <i class="fas fa-users"></i>
                                                {{ number_format($commune->population) }} hab.
                                            </span>
                                            @if($commune->superficie)
                                                <span>
                                                    <i class="fas fa-expand-arrows-alt"></i>
                                                    {{ $commune->superficie }} km²
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $commune->code }}</span>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $commune->departement->nom }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $commune->departement->region->nom }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="responsables-mini">
                                    @if($commune->receveurs->count() > 0)
                                        <div class="responsable-item">
                                            <i class="fas fa-user-tie"></i>
                                            {{ $commune->receveurs->first()->nom }}
                                        </div>
                                    @endif
                                    @if($commune->ordonnateurs->count() > 0)
                                        <div class="responsable-item">
                                            <i class="fas fa-user-cog"></i>
                                            {{ $commune->ordonnateurs->first()->nom }}
                                        </div>
                                    @endif
                                    @if($commune->receveurs->count() == 0 && $commune->ordonnateurs->count() == 0)
                                        <small class="text-muted">Aucun responsable</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($commune->telephone)
                                    <div>
                                        <i class="fas fa-phone"></i>
                                        {{ $commune->telephone }}
                                    </div>
                                @endif
                                @if($commune->email)
                                    <div>
                                        <i class="fas fa-envelope"></i>
                                        {{ $commune->email }}
                                    </div>
                                @endif
                                @if(!$commune->telephone && !$commune->email)
                                    <small class="text-muted">Non renseigné</small>
                                @endif
                            </td>
                            <td>
                                <div class="commune-actions">
                                    <a href="{{ route('communes.show', $commune) }}" 
                                       class="btn btn-sm btn-primary" 
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="{{ route('communes.edit', $commune) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form method="POST" 
                                          action="{{ route('communes.destroy', $commune) }}" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commune ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Affichage {{ $communes->firstItem() }} - {{ $communes->lastItem() }} 
                    sur {{ $communes->total() }} communes
                </div>
                {{ $communes->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-city"></i>
                <h3>Aucune commune trouvée</h3>
                <p>Aucune commune ne correspond à vos critères de recherche.</p>
                @if(request()->hasAny(['search', 'departement_id']))
                    <a href="{{ route('communes.index') }}" class="btn btn-secondary">
                        Réinitialiser les filtres
                    </a>
                @else
                    <a href="{{ route('communes.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i>
                        Créer la première commune
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function sortTable(column) {
    const currentSort = new URLSearchParams(window.location.search).get('sort_by');
    const currentDirection = new URLSearchParams(window.location.search).get('sort_direction') || 'asc';
    
    let newDirection = 'asc';
    if (currentSort === column && currentDirection === 'asc') {
        newDirection = 'desc';
    }
    
    const url = new URL(window.location);
    url.searchParams.set('sort_by', column);
    url.searchParams.set('sort_direction', newDirection);
    
    window.location.href = url.toString();
}

// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    const departementSelect = document.getElementById('departement_id');
    if (departementSelect) {
        departementSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
</script>
@endpush
@endsection --}}


