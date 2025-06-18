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
@endsection