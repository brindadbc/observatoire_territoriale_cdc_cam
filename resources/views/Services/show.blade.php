@extends('layouts.app')

@section('title', $service->name)

@section('content')
<div class="service-detail-container">
    <!-- Header with back button and actions -->
    <div class="profile-header">
        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary back-btn">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        
        <div class="header-actions">
            <a href="{{ route('services.edit', $service->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                <i class="fas fa-trash"></i> Supprimer
            </button>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="actionsDropdown" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i> Actions
                </button>
                <ul class="dropdown-menu" aria-labelledby="actionsDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-alt"></i> Générer rapport</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-plus"></i> Planifier maintenance</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-power-off"></i> {{ $service->status == 'active' ? 'Désactiver' : 'Activer' }}</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main service info -->
    <div class="service-profile">
        <div class="service-header">
            <div class="service-icon" style="background-color: {{ $service->category->color }};">
                <i class="{{ $service->category->icon }}"></i>
            </div>
            <div class="service-title">
                <h1>{{ $service->name }}</h1>
                <div class="service-meta">
                    <span class="badge badge-primary">{{ $service->category->name }}</span>
                    <span class="badge badge-secondary">{{ $service->district->name }}</span>
                    <span class="status-badge status-{{ $service->status }}">{{ ucfirst($service->status) }}</span>
                </div>
            </div>
        </div>
        
        <div class="service-content">
            <!-- Left column - Details -->
            <div class="service-details">
                <div class="detail-section">
                    <h3><i class="fas fa-info-circle"></i> Informations de Base</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Type</label>
                            <p>{{ $service->type }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Responsable</label>
                            <p>{{ $service->manager ?? 'Non spécifié' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Téléphone</label>
                            <p>{{ $service->phone ?? 'Non spécifié' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Email</label>
                            <p>{{ $service->email ?? 'Non spécifié' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Horaires</label>
                            <p>{{ $service->schedule ?? 'Non spécifié' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Capacité</label>
                            <p>{{ $service->capacity ?? 'Non spécifié' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Localisation</h3>
                    <div class="map-container" id="serviceMap"></div>
                    <div class="address-info">
                        <p><i class="fas fa-location-arrow"></i> {{ $service->address }}</p>
                        <p><i class="fas fa-city"></i> {{ $service->district->name }}, {{ $service->commune->name }}</p>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h3><i class="fas fa-file-alt"></i> Description</h3>
                    <div class="description-content">
                        {!! $service->description ?? '<p class="text-muted">Aucune description disponible</p>' !!}
                    </div>
                </div>
            </div>
            
            <!-- Right column - Stats and requests -->
            <div class="service-stats">
                <div class="stats-card">
                    <h3><i class="fas fa-chart-line"></i> Statistiques</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value">{{ $service->total_requests }}</div>
                            <div class="stat-label">Demandes totales</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $service->completed_requests }}</div>
                            <div class="stat-label">Résolues</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $service->pending_requests }}</div>
                            <div class="stat-label">En attente</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $service->rejected_requests }}</div>
                            <div class="stat-label">Rejetées</div>
                        </div>
                    </div>
                    <div class="completion-chart">
                        <canvas id="requestsChart"></canvas>
                    </div>
                </div>
                
                <div class="requests-section">
                    <h3><i class="fas fa-clipboard-list"></i> Dernières Demandes</h3>
                    <div class="requests-list">
                        @forelse($recentRequests as $request)
                        <div class="request-item">
                            <div class="request-header">
                                <span class="request-id">#{{ $request->id }}</span>
                                <span class="request-date">{{ $request->created_at->format('d/m/Y') }}</span>
                                <span class="request-status status-{{ $request->status }}">{{ $request->status }}</span>
                            </div>
                            <div class="request-body">
                                <h4>{{ $request->subject }}</h4>
                                <p class="request-meta">
                                    <span><i class="fas fa-user"></i> {{ $request->citizen_name }}</span>
                                    <span><i class="fas fa-phone"></i> {{ $request->citizen_phone }}</span>
                                </p>
                            </div>
                            <div class="request-actions">
                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#viewRequestModal" data-id="{{ $request->id }}">
                                    <i class="fas fa-eye"></i> Voir
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-info-circle"></i>
                            <p>Aucune demande récente</p>
                        </div>
                        @endforelse
                    </div>
                    <a href="{{ route('requests.index', ['service_id' => $service->id]) }}" class="view-all-link">
                        Voir toutes les demandes <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
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
                <form id="deleteForm" method="POST" action="{{ route('services.destroy', $service->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Request Modal -->
<div class="modal fade" id="viewRequestModal" tabindex="-1" role="dialog" aria-labelledby="viewRequestModalLabel" aria-hidden="true">
    <!-- Modal content would be loaded via AJAX -->
</div>
@endsection

@push('styles')
<style>
    .service-detail-container {
        padding: 20px;
        background-color: #f5f7fa;
        min-height: 100vh;
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .back-btn {
        display: flex;
        align-items: center;
    }

    .back-btn i {
        margin-right: 5px;
    }

    .header-actions {
        display: flex;
        gap: 10px;
    }

    /* Service Profile */
    .service-profile {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .service-header {
        display: flex;
        align-items: center;
        padding: 25px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-bottom: 1px solid #e0e6ed;
    }

    .service-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        margin-right: 25px;
        flex-shrink: 0;
    }

    .service-title h1 {
        margin: 0 0 10px 0;
        font-size: 1.8rem;
        color: #2c3e50;
    }

    .service-meta {
        display: flex;
        gap: 10px;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-primary {
        background-color: #e8f4fc;
        color: #3498db;
    }

    .badge-secondary {
        background-color: #f0f0f0;
        color: #6c757d;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
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

    /* Service Content */
    .service-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        padding: 25px;
    }

    @media (max-width: 992px) {
        .service-content {
            grid-template-columns: 1fr;
        }
    }

    /* Detail Sections */
    .detail-section {
        margin-bottom: 30px;
    }

    .detail-section h3 {
        font-size: 1.2rem;
        margin: 0 0 20px 0;
        color: #2c3e50;
        display: flex;
        align-items: center;
    }

    .detail-section h3 i {
        margin-right: 10px;
        color: #3498db;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }

    .detail-item {
        margin-bottom: 15px;
    }

    .detail-item label {
        display: block;
        font-size: 0.8rem;
        color: #7f8c8d;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .detail-item p {
        margin: 0;
        font-size: 0.95rem;
    }

    /* Map Container */
    .map-container {
        height: 250px;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 15px;
        border: 1px solid #e0e6ed;
    }

    .address-info {
        font-size: 0.9rem;
    }

    .address-info p {
        margin: 0 0 5px 0;
        display: flex;
        align-items: center;
    }

    .address-info i {
        margin-right: 8px;
        color: #3498db;
        width: 16px;
        text-align: center;
    }

    /* Description */
    .description-content {
        line-height: 1.6;
    }

    /* Stats Card */
    .stats-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .stats-card h3 {
        font-size: 1.2rem;
        margin: 0 0 20px 0;
        color: #2c3e50;
        display: flex;
        align-items: center;
    }

    .stats-card h3 i {
        margin-right: 10px;
        color: #3498db;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-item {
        text-align: center;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #7f8c8d;
    }

    .completion-chart {
        height: 200px;
    }

    /* Requests Section */
    .requests-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .requests-section h3 {
        font-size: 1.2rem;
        margin: 0 0 20px 0;
        color: #2c3e50;
        display: flex;
        align-items: center;
    }

    .requests-section h3 i {
        margin-right: 10px;
        color: #3498db;
    }

    .requests-list {
        max-height: 400px;
        overflow-y: auto;
        margin-bottom: 15px;
    }

    .request-item {
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .request-item:hover {
        border-color: #3498db;
    }

    .request-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-size: 0.8rem;
    }

    .request-id {
        font-weight: 600;
        color: #3498db;
    }

    .request-date {
        color: #7f8c8d;
    }

    .request-status {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-completed {
        background-color: #d4edda;
        color: #155724;
    }

    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
    }

    .request-body h4 {
        font-size: 1rem;
        margin: 0 0 5px 0;
    }

    .request-meta {
        display: flex;
        gap: 15px;
        font-size: 0.8rem;
        color: #7f8c8d;
    }

    .request-meta i {
        margin-right: 3px;
    }

    .request-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 30px;
        color: #7f8c8d;
    }

    .empty-state i {
        font-size: 2rem;
        margin-bottom: 10px;
        color: #bdc3c7;
    }

    .empty-state p {
        margin: 0;
    }

    /* View All Link */
    .view-all-link {
        display: inline-block;
        color: #3498db;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .view-all-link i {
        margin-left: 5px;
        transition: transform 0.3s ease;
    }

    .view-all-link:hover i {
        transform: translateX(3px);
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize service map
        const map = L.map('serviceMap').setView([{{ $service->latitude }}, {{ $service->longitude }}], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([{{ $service->latitude }}, {{ $service->longitude }}])
            .addTo(map)
            .bindPopup(`
                <b>{{ $service->name }}</b><br>
                {{ $service->type }}<br>
                {{ $service->address }}
            `);

        // Initialize requests chart
        const ctx = document.getElementById('requestsChart').getContext('2d');
        const requestsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($requestsChart['labels']),
                datasets: [{
                    label: 'Demandes',
                    data: @json($requestsChart['data']),
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // View request modal handler
        $('#viewRequestModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const requestId = button.data('id');
            
            $.get(`/api/requests/${requestId}`, function(data) {
                $('#viewRequestModal .modal-content').html(`
                    <div class="modal-header">
                        <h5 class="modal-title">Demande #${data.id}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="request-details">
                            <div class="detail-row">
                                <label>Date:</label>
                                <span>${new Date(data.created_at).toLocaleDateString()}</span>
                            </div>
                            <div class="detail-row">
                                <label>Statut:</label>
                                <span class="status-${data.status}">${data.status}</span>
                            </div>
                            <div class="detail-row">
                                <label>Citoyen:</label>
                                <span>${data.citizen_name}</span>
                            </div>
                            <div class="detail-row">
                                <label>Téléphone:</label>
                                <span>${data.citizen_phone}</span>
                            </div>
                            <div class="detail-row">
                                <label>Objet:</label>
                                <span>${data.subject}</span>
                            </div>
                            <div class="detail-row full-width">
                                <label>Description:</label>
                                <p>${data.description}</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    </div>
                `);
            });
        });
    });
</script>
@endpush