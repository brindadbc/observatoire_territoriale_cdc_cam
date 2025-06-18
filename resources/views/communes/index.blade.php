@extends('layouts.app')

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
@endsection


