{{-- @extends('layouts.app')

@section('title', 'Départements - Observatoire des Collectivités')
@section('page-title', 'Gestion des Départements')

@section('content')
<div class="departements-index">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('dashboard.index') }}">Tableau de bord</a>
        <span>/</span>
        <span>Départements</span>
    </div>

    <!-- Actions -->
    <div class="page-actions">
        <a href="{{ route('departements.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nouveau Département
        </a>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <div class="stat-content">
                <h4>Total Départements</h4>
                <div class="stat-number">{{ $departements->total() }}</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-city"></i>
            </div>
            <div class="stat-content">
                <h4>Total Communes</h4>
                <div class="stat-number">{{ $departements->sum('communes_count') }}</div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="filters-section">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Rechercher un département...">
        </div>
        
        <div class="filters">
            <select id="regionFilter" class="filter-select">
                <option value="">Toutes les régions</option>
                @foreach($departements->pluck('region')->unique() as $region)
                    <option value="{{ $region->nom }}">{{ $region->nom }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Liste des départements -->
    <div class="departements-grid">
        @foreach($departements as $departement)
        <div class="departement-card" data-region="{{ $departement->region->nom }}">
            <div class="card-header">
                <h3>{{ $departement->nom }}</h3>
                <div class="card-code">{{ $departement->code }}</div>
            </div>
            
            <div class="card-body">
                <div class="info-row">
                    <span class="label">Région:</span>
                    <span class="value">{{ $departement->region->nom }}</span>
                </div>
                
                @if($departement->chef_lieu)
                <div class="info-row">
                    <span class="label">Chef-lieu:</span>
                    <span class="value">{{ $departement->chef_lieu }}</span>
                </div>
                @endif
                
                <div class="info-row">
                    <span class="label">Communes:</span>
                    <span class="value communes-count">{{ $departement->communes_count }}</span>
                </div>
                
                @if($departement->superficie)
                <div class="info-row">
                    <span class="label">Superficie:</span>
                    <span class="value">{{ number_format($departement->superficie, 0) }} km²</span>
                </div>
                @endif
                
                @if($departement->population)
                <div class="info-row">
                    <span class="label">Population:</span>
                    <span class="value">{{ number_format($departement->population, 0) }} hab.</span>
                </div>
                @endif
            </div>
            
            <div class="card-actions">
                <a href="{{ route('departements.show', $departement) }}" class="btn btn-outline" title="Voir détails">
                    <i class="fas fa-eye"></i>
                    Détails
                </a>
                
                <a href="{{ route('departements.edit', $departement) }}" class="btn btn-outline" title="Modifier">
                    <i class="fas fa-edit"></i>
                    Modifier
                </a>
                
                <form action="{{ route('departements.destroy', $departement) }}" method="POST" class="delete-form" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce département ?')">
                        <i class="fas fa-trash"></i>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $departements->links() }}
    </div>
</div>

@push('styles')
<style>
.departements-index {
    padding: 20px;
}

.page-actions {
    margin-bottom: 30px;
    text-align: right;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-icon.blue { background: #3498db; }
.stat-icon.green { background: #2ecc71; }

.stat-content h4 {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #666;
}

.stat-number {
    font-size: 28px;
    font-weight: bold;
    color: #2c3e50;
}

.filters-section {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    align-items: center;
}

.search-box {
    position: relative;
    flex: 1;
    max-width: 400px;
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.search-box input {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border: 1px solid #ddd;
    border-radius: 25px;
    font-size: 14px;
}

.filter-select {
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 25px;
    background: white;
    min-width: 200px;
}

.departements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

.departement-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.departement-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
}

.card-code {
    background: rgba(255,255,255,0.2);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.card-body {
    padding: 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #f0f0f0;
}

.info-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.label {
    font-weight: 600;
    color: #666;
    font-size: 14px;
}

.value {
    color: #2c3e50;
    font-weight: 500;
}

.communes-count {
    background: #e74c3c;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.card-actions {
    padding: 15px 20px;
    background: #f8f9fa;
    display: flex;
    gap: 10px;
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-outline {
    background: transparent;
    color: #666;
    border: 1px solid #ddd;
}

.btn-outline:hover {
    background: #f8f9fa;
}

.btn-danger {
    color: #e74c3c;
    border-color: #e74c3c;
}

.btn-danger:hover {
    background: #e74c3c;
    color: white;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.pagination-wrapper {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche
    const searchInput = document.getElementById('searchInput');
    const regionFilter = document.getElementById('regionFilter');
    const cards = document.querySelectorAll('.departement-card');
    
    function filterCards() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedRegion = regionFilter.value;
        
        cards.forEach(card => {
            const cardText = card.textContent.toLowerCase();
            const cardRegion = card.dataset.region;
            
            const matchesSearch = cardText.includes(searchTerm);
            const matchesRegion = !selectedRegion || cardRegion === selectedRegion;
            
            if (matchesSearch && matchesRegion) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterCards);
    regionFilter.addEventListener('change', filterCards);
});
</script>
@endpush
@endsection --}}



@extends('layouts.app')

@section('title', 'Départements - Observatoire des Collectivités')
@section('page-title', 'Gestion des Départements')

@section('content')
<div class="departements-index">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('dashboard.index') }}">Tableau de bord</a>
        <span>/</span>
        <span>Départements</span>
    </div>

    <!-- Actions -->
    <div class="page-actions">
        <a href="{{ route('departements.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nouveau Département
        </a>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <div class="stat-content">
                <h4>Total Départements</h4>
                <div class="stat-number">{{ $departements->total() }}</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-city"></i>
            </div>
            <div class="stat-content">
                <h4>Total Communes</h4>
                <div class="stat-number">{{ $departements->sum('communes_count') }}</div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="filters-section">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Rechercher un département..." autocomplete="off">
            <div id="searchResults" class="search-results"></div>
        </div>
        
        <div class="filters">
            <select id="regionFilter" class="filter-select">
                <option value="">Toutes les régions</option>
                @foreach($departements->pluck('region')->unique() as $region)
                    <option value="{{ $region->nom }}">{{ $region->nom }}</option>
                @endforeach
            </select>
            
            <button id="resetFilters" class="btn btn-outline">
                <i class="fas fa-undo"></i>
                Réinitialiser
            </button>
        </div>
    </div>

    <!-- Tableau des départements -->
    <div class="table-container">
        <table class="departements-table" id="departementsTable">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Région</th>
                    <th>Chef-lieu</th>
                    <th>Communes</th>
                    <th>Superficie</th>
                    <th>Population</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departements as $departement)
                <tr class="departement-row" 
                    data-region="{{ $departement->region->nom }}" 
                    data-search="{{ strtolower($departement->nom . ' ' . $departement->code . ' ' . $departement->region->nom . ' ' . ($departement->chef_lieu ?? '')) }}">
                    <td class="code-cell">
                        <span class="department-code">{{ $departement->code }}</span>
                    </td>
                    <td class="nom-cell">
                        <strong>{{ $departement->nom }}</strong>
                    </td>
                    <td class="region-cell">
                        <span class="region-badge">{{ $departement->region->nom }}</span>
                    </td>
                    <td class="chef-lieu-cell">
                        {{ $departement->chef_lieu ?? '-' }}
                    </td>
                    <td class="communes-cell">
                        <span class="communes-count">{{ $departement->communes_count }}</span>
                    </td>
                    <td class="superficie-cell">
                        @if($departement->superficie)
                            {{ number_format($departement->superficie, 0) }} km²
                        @else
                            -
                        @endif
                    </td>
                    <td class="population-cell">
                        @if($departement->population)
                            {{ number_format($departement->population, 0) }} hab.
                        @else
                            -
                        @endif
                    </td>
                    <td class="actions-cell">
                        <div class="action-buttons">
                            <a href="{{ route('departements.show', $departement) }}" class="btn btn-sm btn-outline" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <a href="{{ route('departements.edit', $departement) }}" class="btn btn-sm btn-outline" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <form action="{{ route('departements.destroy', $departement) }}" method="POST" class="delete-form" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce département ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Message si aucun résultat -->
        <div id="noResults" class="no-results" style="display: none;">
            <div class="no-results-content">
                <i class="fas fa-search"></i>
                <h3>Aucun département trouvé</h3>
                <p>Aucun département ne correspond à vos critères de recherche.</p>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $departements->links() }}
    </div>
</div>

@push('styles')
<style>
.departements-index {
    padding: 20px;
}

.page-actions {
    margin-bottom: 30px;
    text-align: right;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-icon.blue { background: #3498db; }
.stat-icon.green { background: #2ecc71; }

.stat-content h4 {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #666;
}

.stat-number {
    font-size: 28px;
    font-weight: bold;
    color: #2c3e50;
}

.filters-section {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    flex: 1;
    max-width: 400px;
    min-width: 300px;
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    z-index: 2;
}

.search-box input {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border: 1px solid #ddd;
    border-radius: 25px;
    font-size: 14px;
    background: white;
    transition: all 0.3s ease;
}

.search-box input:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.search-result-item {
    padding: 12px 15px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.search-result-item:hover {
    background-color: #f8f9fa;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
}

.search-result-info {
    font-size: 12px;
    color: #666;
}

.filters {
    display: flex;
    gap: 15px;
    align-items: center;
}

.filter-select {
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 25px;
    background: white;
    min-width: 200px;
    font-size: 14px;
}

.table-container {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.departements-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.departements-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.departements-table th {
    padding: 15px 12px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.departements-table tbody tr {
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.departements-table tbody tr:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.departements-table td {
    padding: 15px 12px;
    vertical-align: middle;
}

.department-code {
    background: #e74c3c;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
}

.region-badge {
    background: #3498db;
    color: white;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 500;
}

.communes-count {
    background: #2ecc71;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    min-width: 30px;
    text-align: center;
    display: inline-block;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-sm {
    padding: 6px 10px;
    font-size: 12px;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-outline {
    background: transparent;
    color: #666;
    border: 1px solid #ddd;
}

.btn-outline:hover {
    background: #f8f9fa;
}

.btn-danger {
    color: #e74c3c;
    border-color: #e74c3c;
}

.btn-danger:hover {
    background: #e74c3c;
    color: white;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.pagination-wrapper {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

.no-results {
    padding: 60px 20px;
    text-align: center;
    background: white;
}

.no-results-content i {
    font-size: 48px;
    color: #bdc3c7;
    margin-bottom: 20px;
}

.no-results-content h3 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 24px;
}

.no-results-content p {
    margin: 0;
    color: #7f8c8d;
    font-size: 16px;
}

/* Responsive */
@media (max-width: 1024px) {
    .departements-table {
        font-size: 12px;
    }
    
    .departements-table th,
    .departements-table td {
        padding: 10px 8px;
    }
}

@media (max-width: 768px) {
    .filters-section {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        max-width: none;
        min-width: auto;
    }
    
    .filters {
        flex-wrap: wrap;
    }
    
    .departements-table {
        font-size: 11px;
    }
    
    .departements-table th,
    .departements-table td {
        padding: 8px 6px;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 2px;
    }


    /* CSS supplémentaire pour une meilleure expérience mobile */

@media (max-width: 768px) {
    /* Transformation du tableau en cartes sur mobile */
    .departements-table {
        display: block;
        width: 100%;
    }
    
    .departements-table thead {
        display: none;
    }
    
    .departements-table tbody {
        display: block;
        width: 100%;
    }
    
    .departements-table tbody tr {
        display: block;
        background: white;
        border-radius: 12px;
        margin-bottom: 15px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: none;
        position: relative;
    }
    
    .departements-table tbody tr:hover {
        transform: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    
    .departements-table tbody td {
        display: block;
        width: 100%;
        text-align: left;
        padding: 8px 0;
        border: none;
        position: relative;
    }
    
    .departements-table tbody td:before {
        content: attr(data-label) ": ";
        font-weight: 600;
        color: #666;
        display: inline-block;
        min-width: 120px;
        margin-right: 10px;
    }
    
    /* Styles spécifiques pour chaque colonne */
    .code-cell:before { content: "Code"; }
    .nom-cell:before { content: "Nom"; }
    .region-cell:before { content: "Région"; }
    .chef-lieu-cell:before { content: "Chef-lieu"; }
    .communes-cell:before { content: "Communes"; }
    .superficie-cell:before { content: "Superficie"; }
    .population-cell:before { content: "Population"; }
    .actions-cell:before { content: "Actions"; }
    
    .nom-cell {
        border-bottom: 1px solid #e0e0e0;
        margin-bottom: 10px;
        padding-bottom: 10px;
    }
    
    .nom-cell strong {
        font-size: 18px;
        color: #2c3e50;
    }
    
    .actions-cell {
        border-top: 1px solid #e0e0e0;
        margin-top: 10px;
        padding-top: 15px;
    }
    
    .action-buttons {
        flex-direction: row;
        justify-content: flex-start;
        gap: 8px;
        margin-top: 5px;
    }
    
    .btn-sm {
        padding: 8px 12px;
        font-size: 12px;
    }
    
    /* Amélioration de la barre de recherche sur mobile */
    .search-box {
        max-width: none;
        min-width: auto;
        width: 100%;
        margin-bottom: 15px;
    }
    
    .filters {
        width: 100%;
        flex-direction: column;
        gap: 10px;
    }
    
    .filter-select {
        width: 100%;
        min-width: auto;
    }
    
    /* Statistiques sur mobile */
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .stat-card {
        padding: 15px;
        gap: 12px;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .stat-number {
        font-size: 24px;
    }
}

@media (max-width: 480px) {
    .departements-index {
        padding: 15px;
    }
    
    .departements-table tbody tr {
        padding: 15px;
        margin-bottom: 12px;
    }
    
    .nom-cell strong {
        font-size: 16px;
    }
    
    .stat-card {
        padding: 12px;
        gap: 10px;
    }
    
    .stat-icon {
        width: 45px;
        height: 45px;
        font-size: 18px;
    }
    
    .stat-number {
        font-size: 20px;
    }
    
    .search-box input {
        padding: 10px 12px 10px 40px;
        font-size: 14px;
    }
    
    .btn {
        padding: 8px 12px;
        font-size: 13px;
    }
    
    .btn-sm {
        padding: 6px 10px;
        font-size: 11px;
    }
}

/* Animation pour le chargement */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Style pour les résultats surlignés */
mark {
    background-color: #fff3cd;
    color: #856404;
    padding: 1px 2px;
    border-radius: 2px;
}

/* Indicateur de pagination active */
.pagination .page-item.active .page-link {
    background-color: #3498db;
    border-color: #3498db;
}

/* Style pour les badges améliorés */
.department-code,
.region-badge,
.communes-count {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const regionFilter = document.getElementById('regionFilter');
    const resetButton = document.getElementById('resetFilters');
    const searchResults = document.getElementById('searchResults');
    const rows = document.querySelectorAll('.departement-row');
    const table = document.getElementById('departementsTable');
    const noResults = document.getElementById('noResults');
    
    let searchTimeout;
    let allDepartements = []; // Pour stocker tous les départements de toutes les pages
    
    // Charger tous les départements au démarrage
    loadAllDepartements();
    
    // Fonction pour charger tous les départements via AJAX
    function loadAllDepartements() {
        fetch('{{ route("departements.index") }}?all=true', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            allDepartements = data.departements || [];
        })
        .catch(error => {
            console.error('Erreur lors du chargement des départements:', error);
        });
    }
    
    // Fonction de recherche globale
    function globalSearch(term) {
        if (!term.trim()) {
            hideSearchResults();
            filterLocalRows();
            return;
        }
        
        const searchTerm = term.toLowerCase();
        const results = allDepartements.filter(dept => 
            dept.nom.toLowerCase().includes(searchTerm) ||
            dept.code.toLowerCase().includes(searchTerm) ||
            (dept.region && dept.region.nom.toLowerCase().includes(searchTerm)) ||
            (dept.chef_lieu && dept.chef_lieu.toLowerCase().includes(searchTerm))
        );
        
        displaySearchResults(results, searchTerm);
    }
    
    // Afficher les résultats de recherche
    function displaySearchResults(results, searchTerm) {
        if (results.length === 0) {
            hideSearchResults();
            showNoResults();
            return;
        }
        
        const resultsHtml = results.map(dept => `
            <div class="search-result-item" data-dept-id="${dept.id}">
                <div class="search-result-title">${highlightText(dept.nom, searchTerm)}</div>
                <div class="search-result-info">
                    Code: ${dept.code} | Région: ${dept.region ? dept.region.nom : 'N/A'}
                    ${dept.chef_lieu ? ` | Chef-lieu: ${dept.chef_lieu}` : ''}
                </div>
            </div>
        `).join('');
        
        searchResults.innerHTML = resultsHtml;
        searchResults.style.display = 'block';
        
        // Ajouter les événements de clic
        searchResults.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', function() {
                const deptId = this.dataset.deptId;
                window.location.href = `{{ route('departements.show', ['departement' => ':id']) }}`.replace(':id', deptId);
            });
        });
        
        // Filtrer aussi les lignes locales
        filterLocalRows();
    }
    
    // Masquer les résultats de recherche
    function hideSearchResults() {
        searchResults.style.display = 'none';
        hideNoResults();
    }
    
    // Filtrer les lignes du tableau actuel
    function filterLocalRows() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedRegion = regionFilter.value;
        let visibleCount = 0;
        
        rows.forEach(row => {
            const searchData = row.dataset.search;
            const rowRegion = row.dataset.region;
            
            const matchesSearch = !searchTerm || searchData.includes(searchTerm);
            const matchesRegion = !selectedRegion || rowRegion === selectedRegion;
            
            if (matchesSearch && matchesRegion) {
                row.style.display = 'table-row';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Afficher le message "aucun résultat" si nécessaire pour le tableau local
        if (visibleCount === 0 && !searchInput.value.trim()) {
            // Ne pas montrer "aucun résultat" si c'est juste un filtre vide
        } else if (visibleCount === 0) {
            showNoResults();
        } else {
            hideNoResults();
        }
    }
    
    // Afficher le message "aucun résultat"
    function showNoResults() {
        table.style.display = 'none';
        noResults.style.display = 'block';
    }
    
    // Masquer le message "aucun résultat"
    function hideNoResults() {
        table.style.display = 'table';
        noResults.style.display = 'none';
    }
    
    // Surligner le texte recherché
    function highlightText(text, searchTerm) {
        if (!searchTerm) return text;
        const regex = new RegExp(`(${searchTerm})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }
    
    // Événements de recherche
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            globalSearch(this.value);
        }, 300);
    });
    
    // Événement de filtre par région
    regionFilter.addEventListener('change', function() {
        hideSearchResults();
        filterLocalRows();
    });
    
    // Bouton de réinitialisation
    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        regionFilter.value = '';
        hideSearchResults();
        filterLocalRows();
        searchInput.focus();
    });
    
    // Masquer les résultats quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            hideSearchResults();
        }
    });
    
    // Afficher les résultats quand on clique dans l'input avec du contenu
    searchInput.addEventListener('focus', function() {
        if (this.value.trim() && allDepartements.length > 0) {
            globalSearch(this.value);
        }
    });
    
    // Gestion des touches du clavier
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            hideSearchResults();
            filterLocalRows();
        }
    });
});
</script>
@endpush
@endsection