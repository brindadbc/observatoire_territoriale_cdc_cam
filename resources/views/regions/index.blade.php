@extends('layouts.app')

@section('title', 'Régions - Observatoire des Collectivités Territoriales')
@section('page-title', 'Régions du Cameroun')

@push('styles')
<style>
    .region-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .region-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        transform: translateY(-3px);
        border-color: var(--primary-color);
    }

    .region-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }

    .region-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .region-name {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        line-height: 1.2;
    }

    .region-code {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .chef-lieu {
        color: #6c757d;
        font-size: 13px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 15px;
    }

    .stat-box {
        text-align: center;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .stat-value {
        font-size: 16px;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
    }

    .stat-label {
        font-size: 11px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin: 0;
    }

    .performance-section {
        margin-bottom: 15px;
    }

    .performance-bar {
        height: 6px;
        background: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 8px;
    }

    .performance-fill {
        height: 100%;
        background: linear-gradient(90deg, #28a745, #20c997);
        transition: width 0.8s ease;
    }

    .performance-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #6c757d;
    }

    .region-actions {
        display: flex;
        gap: 8px;
        justify-content: center;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-action {
        flex: 1;
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 6px;
        border: 1px solid;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .btn-view {
        color: #007bff;
        border-color: #007bff;
        background: rgba(0,123,255,0.05);
    }

    .btn-view:hover {
        background: #007bff;
        color: white;
    }

    .btn-edit {
        color: #28a745;
        border-color: #28a745;
        background: rgba(40,167,69,0.05);
    }

    .btn-edit:hover {
        background: #28a745;
        color: white;
    }

    .btn-delete {
        color: #dc3545;
        border-color: #dc3545;
        background: rgba(220,53,69,0.05);
    }

    .btn-delete:hover {
        background: #dc3545;
        color: white;
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .overview-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
    }

    .overview-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--gradient);
    }

    .overview-card.primary::before { background: linear-gradient(90deg, #667eea, #764ba2); }
    .overview-card.success::before { background: linear-gradient(90deg, #11998e, #38ef7d); }
    .overview-card.warning::before { background: linear-gradient(90deg, #f093fb, #f5576c); }
    .overview-card.info::before { background: linear-gradient(90deg, #667eea, #764ba2); }

    .overview-value {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .overview-label {
        color: #6c757d;
        font-size: 14px;
        margin: 0;
        font-weight: 500;
    }

    .overview-icon {
        position: absolute;
        right: 15px;
        top: 15px;
        font-size: 24px;
        opacity: 0.2;
    }

    .filters-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
        margin-bottom: 30px;
    }

    .filters-row {
        display: flex;
        gap: 15px;
        align-items: end;
        flex-wrap: wrap;
    }

    .filter-group {
        min-width: 200px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
        display: block;
    }

    .search-container {
        position: relative;
        flex: 1;
        min-width: 250px;
    }

    .search-input {
        width: 100%;
        padding: 10px 40px 10px 15px;
        border: 1px solid #dee2e6;
        border-radius: 25px;
        font-size: 14px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 0 0 3px rgba(44,82,130,0.1);
    }

    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        border-radius: 25px;
        padding: 8px 20px;
        color: white;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(44,82,130,0.3);
        color: white;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        
        .filters-row {
            flex-direction: column;
            gap: 15px;
        }
        
        .filter-group, .search-container {
            min-width: 100%;
        }
        
        .stats-overview {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques overview -->
    <div class="stats-overview">
        <div class="overview-card primary">
            <div class="overview-icon">
                <i class="fas fa-map"></i>
            </div>
            <h3 class="overview-value">{{ $stats['total_regions'] }}</h3>
            <p class="overview-label">Régions</p>
        </div>
        <div class="overview-card success">
            <div class="overview-icon">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <h3 class="overview-value">{{ $stats['total_departements'] }}</h3>
            <p class="overview-label">Départements</p>
        </div>
        <div class="overview-card warning">
            <div class="overview-icon">
                <i class="fas fa-city"></i>
            </div>
            <h3 class="overview-value">{{ $stats['total_communes'] }}</h3>
            <p class="overview-label">Communes</p>
        </div>
        <div class="overview-card info">
            <div class="overview-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="overview-value">{{ number_format($stats['population_totale']/1000000, 1) }}M</h3>
            <p class="overview-label">Population</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
        <div class="filters-row">
            <div class="search-container">
                <input type="text" placeholder="Rechercher une région..." class="search-input" id="searchRegion">
                <i class="fas fa-search search-icon"></i>
            </div>
            <div class="filter-group">
                <label class="filter-label">Trier par</label>
                <select class="form-select" id="sortBy">
                    <option value="nom">Nom</option>
                    <option value="budget">Budget</option>
                    <option value="communes">Communes</option>
                    <option value="population">Population</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Ordre</label>
                <select class="form-select" id="sortOrder">
                    <option value="asc">Croissant</option>
                    <option value="desc">Décroissant</option>
                </select>
            </div>
            <button class="btn btn-outline-primary" onclick="applyFilters()">
                <i class="fas fa-filter"></i> Filtrer
            </button>
        </div>
    </div>

    <!-- En-tête section -->
    <div class="section-title">
        <span>Toutes les régions ({{ $regions->count() }})</span>
        <a href="{{ route('regions.create') }}" class="btn-primary-custom">
            <i class="fas fa-plus"></i> Nouvelle région
        </a>
    </div>

    <!-- Grille des régions -->
    <div class="row g-3" id="regionsContainer">
        @foreach($regions as $region)
        <div class="col-lg-4 col-md-6 region-item" data-region="{{ strtolower($region->nom) }}">
            <div class="region-card">
                <div class="region-header">
                    <h4 class="region-name">{{ $region->nom }}</h4>
                    <span class="region-code">{{ $region->code }}</span>
                </div>

                <div class="chef-lieu">
                    <i class="fas fa-map-marker-alt"></i>
                    {{ $region->chef_lieu }}
                </div>

                <div class="stats-grid">
                    <div class="stat-box">
                        <p class="stat-value">{{ $region->departements_count ?? 0 }}</p>
                        <p class="stat-label">Départements</p>
                    </div>
                    <div class="stat-box">
                        <p class="stat-value">{{ $region->communes_count ?? 0 }}</p>
                        <p class="stat-label">Communes</p>
                    </div>
                </div>

                @php
                    $performance = collect($performanceRegions)->firstWhere('region', $region->nom);
                @endphp

                @if($performance)
                <div class="performance-section">
                    <div class="performance-bar">
                        <div class="performance-fill" style="width: {{ $performance['score'] * 10 }}%"></div>
                    </div>
                    <div class="performance-label">
                        <span>Performance</span>
                        <strong>{{ $performance['score'] }}/10</strong>
                    </div>
                </div>
                @endif

                @if($region->superficie || $region->population)
                <div class="mb-3">
                    @if($region->superficie)
                        <small class="text-muted d-block">
                            <i class="fas fa-expand-arrows-alt"></i> 
                            {{ number_format($region->superficie) }} km²
                        </small>
                    @endif
                    @if($region->population)
                        <small class="text-muted d-block">
                            <i class="fas fa-users"></i> 
                            {{ number_format($region->population) }} hab.
                        </small>
                    @endif
                </div>
                @endif

                <div class="region-actions">
                    <a href="{{ route('regions.show', $region) }}" class="btn-action btn-view">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                    <a href="{{ route('regions.edit', $region) }}" class="btn-action btn-edit">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <button type="button" class="btn-action btn-delete"
                            onclick="confirmDelete({{ $region->id }}, '{{ $region->nom }}')">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($regions->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $regions->links() }}
        </div>
    @endif
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la région <strong id="regionName"></strong> ?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cette action est irréversible.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Recherche en temps réel
    document.getElementById('searchRegion').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const regions = document.querySelectorAll('.region-item');
        
        regions.forEach(region => {
            const regionName = region.getAttribute('data-region');
            region.style.display = regionName.includes(searchTerm) ? 'block' : 'none';
        });
    });

    // Filtrage
    function applyFilters() {
        const sortBy = document.getElementById('sortBy').value;
        const sortOrder = document.getElementById('sortOrder').value;
        
        // Implémentation du tri côté client ou appel AJAX
        console.log('Tri par:', sortBy, 'Ordre:', sortOrder);
    }

    // Confirmation suppression
    function confirmDelete(regionId, regionName) {
        document.getElementById('regionName').textContent = regionName;
        document.getElementById('deleteForm').action = `/regions/${regionId}`;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // Animation au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.region-card');
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                observer.observe(card);
            }, index * 50);
        });

        // Animation des barres de performance
        setTimeout(() => {
            const performanceBars = document.querySelectorAll('.performance-fill');
            performanceBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });
        }, 1000);
    });
</script>
@endpush