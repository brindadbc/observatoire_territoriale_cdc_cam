@extends('layouts.app')

@section('title', 'Services Sociaux de Base')

@section('content')
<div class="services-container">
    <!-- Header with actions -->
    <div class="page-header">
        <h1><i class="fas fa-hands-helping"></i> Services Sociaux de Base</h1>
        <div class="actions">
            <a href="{{ route('services.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Service
            </a>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download"></i> Exporter
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel"></i> Excel</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf"></i> PDF</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-csv"></i> CSV</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <form id="serviceFilters">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search">Recherche</label>
                    <input type="text" id="search" name="search" placeholder="Nom, type, quartier..." value="{{ request('search') }}">
                </div>
                
                <div class="filter-group">
                    <label for="category">Catégorie</label>
                    <select id="category" name="category">
                        <option value="">Toutes</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="status">Statut</label>
                    <select id="status" name="status">
                        <option value="">Tous</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="district">Quartier</label>
                    <select id="district" name="district">
                        <option value="">Tous</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ request('district') == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Services Table -->
    <div class="table-responsive">
        <table class="services-table">
            <thead>
                <tr>
                    <th class="sortable" data-sort="name">
                        Nom <i class="fas fa-sort"></i>
                    </th>
                    <th class="sortable" data-sort="category_id">
                        Catégorie <i class="fas fa-sort"></i>
                    </th>
                    <th>Quartier</th>
                    <th class="sortable" data-sort="status">
                        Statut <i class="fas fa-sort"></i>
                    </th>
                    <th>Demandes (30j)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td>
                        <div class="service-info">
                            <div class="service-icon" style="background-color: {{ $service->category->color }};">
                                <i class="{{ $service->category->icon }}"></i>
                            </div>
                            <div>
                                <h4>{{ $service->name }}</h4>
                                <p class="service-type">{{ $service->type }}</p>
                            </div>
                        </div>
                    </td>
                    <td>{{ $service->category->name }}</td>
                    <td>{{ $service->district->name }}</td>
                    <td>
                        <span class="status-badge status-{{ $service->status }}">
                            {{ ucfirst($service->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="requests-indicator">
                            <span class="requests-count">{{ $service->recent_requests_count }}</span>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ $service->request_completion_rate }}%;" 
                                     aria-valuenow="{{ $service->request_completion_rate }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('services.show', $service->id) }}" class="btn btn-sm btn-info" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('services.edit', $service->id) }}" class="btn btn-sm btn-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $service->id }}" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        {{ $services->links() }}
        
        <div class="pagination-info">
            Affichage de {{ $services->firstItem() }} à {{ $services->lastItem() }} sur {{ $services->total() }} services
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce service ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .services-container {
        padding: 20px;
        background-color: #f5f7fa;
        min-height: 100vh;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e0e6ed;
    }

    .page-header h1 {
        color: #2c3e50;
        font-weight: 600;
        font-size: 1.8rem;
        margin: 0;
    }

    .page-header h1 i {
        margin-right: 10px;
        color: #3498db;
    }

    .actions {
        display: flex;
        gap: 10px;
    }

    /* Filters */
    .filters-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    #serviceFilters {
        width: 100%;
    }

    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 0.9rem;
        color: #7f8c8d;
        font-weight: 500;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e0e6ed;
        border-radius: 4px;
        font-size: 0.9rem;
        transition: border-color 0.3s ease;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #3498db;
    }

    .filter-actions {
        display: flex;
        align-items: flex-end;
        gap: 10px;
    }

    /* Table Styles */
    .table-responsive {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .services-table {
        width: 100%;
        border-collapse: collapse;
    }

    .services-table thead {
        background-color: #f8f9fa;
    }

    .services-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 2px solid #e0e6ed;
    }

    .services-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e0e6ed;
        vertical-align: middle;
    }

    .services-table tbody tr:last-child td {
        border-bottom: none;
    }

    .services-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .sortable {
        cursor: pointer;
        user-select: none;
    }

    .sortable:hover {
        color: #3498db;
    }

    .sortable i {
        margin-left: 5px;
    }

    /* Service Info */
    .service-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .service-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .service-type {
        font-size: 0.8rem;
        color: #7f8c8d;
        margin: 3px 0 0 0;
    }

    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-maintenance {
        background-color: #cce5ff;
        color: #004085;
    }

    /* Requests Indicator */
    .requests-indicator {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .requests-count {
        font-weight: 600;
        min-width: 20px;
        text-align: center;
    }

    .progress {
        flex: 1;
        height: 6px;
        background-color: #ecf0f1;
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-bar {
        background-color: #2ecc71;
        height: 100%;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 5px;
    }

    .action-buttons .btn-sm {
        padding: 5px 8px;
        font-size: 0.8rem;
    }

    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
    }

    .pagination-info {
        font-size: 0.9rem;
        color: #7f8c8d;
    }

    /* Responsive Table */
    @media (max-width: 768px) {
        .filter-row {
            flex-direction: column;
        }
        
        .filter-group {
            min-width: 100%;
        }
        
        .filter-actions {
            width: 100%;
            justify-content: flex-end;
        }
        
        .services-table {
            display: block;
            overflow-x: auto;
        }
        
        .pagination-container {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle sortable columns
        $('.sortable').click(function() {
            const sortField = $(this).data('sort');
            const currentUrl = new URL(window.location.href);
            const currentSort = currentUrl.searchParams.get('sort');
            const currentDirection = currentUrl.searchParams.get('direction');
            
            let newDirection = 'asc';
            if (currentSort === sortField && currentDirection === 'asc') {
                newDirection = 'desc';
            }
            
            currentUrl.searchParams.set('sort', sortField);
            currentUrl.searchParams.set('direction', newDirection);
            window.location.href = currentUrl.toString();
        });

        // Highlight current sort column
        const urlParams = new URLSearchParams(window.location.search);
        const sortParam = urlParams.get('sort');
        const directionParam = urlParams.get('direction');
        
        if (sortParam) {
            $(`.sortable[data-sort="${sortParam}"]`).addClass('sorted').append(
                $(`<i class="fas fa-sort-${directionParam === 'asc' ? 'up' : 'down'}"></i>`)
            );
            $(`.sortable[data-sort="${sortParam}"] i.fa-sort`).remove();
        }

        // Delete service confirmation
        $('.delete-btn').click(function() {
            const serviceId = $(this).data('id');
            const deleteUrl = `/services/${serviceId}`;
            
            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').modal('show');
        });

        // Responsive adjustments
        function handleResponsive() {
            if ($(window).width() < 576) {
                $('.action-buttons').addClass('btn-group');
                $('.action-buttons .btn').addClass('btn-sm');
            } else {
                $('.action-buttons').removeClass('btn-group');
            }
        }

        $(window).resize(handleResponsive);
        handleResponsive();
    });
</script>
@endpush