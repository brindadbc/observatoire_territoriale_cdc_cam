@extends('layouts.app')

@section('title', $region->nom . ' - Observatoire des Collectivités Territoriales')
@section('page-title', 'Région ' . $region->nom)

@push('styles')
<style>
    .region-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .region-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .region-header-content {
        position: relative;
        z-index: 1;
    }

    .region-title {
        font-size: 32px;
        font-weight: 700;
        margin: 0;
        margin-bottom: 10px;
    }

    .region-subtitle {
        font-size: 16px;
        opacity: 0.9;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .region-code-badge {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 6px 15px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid rgba(255,255,255,0.3);
        display: inline-block;
        margin-top: 15px;
    }

    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .info-item {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .info-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
        margin-bottom: 5px;
    }

    .info-label {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-description {
        font-size: 12px;
        color: #868e96;
        margin-top: 5px;
    }

    .progress-section {
        margin-bottom: 20px;
    }

    .progress-item {
        margin-bottom: 15px;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .progress-label {
        font-size: 14px;
        font-weight: 600;
        color: #495057;
    }

    .progress-value {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary-color);
    }

    .progress-bar-container {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #28a745, #20c997);
        border-radius: 4px;
        transition: width 1s ease;
        position: relative;
    }

    .progress-bar-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-custom {
        padding: 12px 25px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: 140px;
        justify-content: center;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(44,82,130,0.3);
        color: white;
    }

    .btn-success-custom {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40,167,69,0.3);
        color: white;
    }

    .btn-warning-custom {
        background: linear-gradient(135deg, #f39c12, #f1c40f);
        color: white;
    }

    .btn-warning-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(243,156,18,0.3);
        color: white;
    }

    .btn-secondary-custom {
        background: #6c757d;
        color: white;
    }

    .btn-secondary-custom:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(108,117,125,0.3);
        color: white;
    }

    .departments-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .department-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .department-item:hover {
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }

    .department-name {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        font-size: 14px;
    }

    .department-info {
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.3;
    }

    .timeline-section {
        position: relative;
    }

    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 20px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        width: 2px;
        height: 100%;
        background: #e9ecef;
    }

    .timeline-item::after {
        content: '';
        position: absolute;
        left: 6px;
        top: 8px;
        width: 10px;
        height: 10px;
        background: var(--primary-color);
        border-radius: 50%;
    }

    .timeline-content {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        border: 1px solid #e9ecef;
    }

    .timeline-date {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .timeline-title {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .region-title {
            font-size: 24px;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn-custom {
            min-width: 100%;
        }
        
        .region-header {
            padding: 20px;
        }
        
        .info-card {
            padding: 20px;
        }
    }

    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.6s ease;
    }

    .fade-in.active {
        opacity: 1;
        transform: translateY(0);
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

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('regions.index') }}">Régions</a></li>
            <li class="breadcrumb-item active">{{ $region->nom }}</li>
        </ol>
    </nav>

    <!-- En-tête région -->
    <div class="region-header fade-in">
        <div class="region-header-content">
            <h1 class="region-title">{{ $region->nom }}</h1>
            <p class="region-subtitle">
                <i class="fas fa-map-marker-alt"></i>
                Chef-lieu : {{ $region->chef_lieu }}
            </p>
            <span class="region-code-badge">{{ $region->code }}</span>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <!-- Statistiques générales -->
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    Statistiques générales
                </h3>
                
                <div class="info-grid">
                    <div class="info-item">
                        <h4 class="info-value">{{ $stats['nombre_departements'] ?? 0 }}</h4>
                        <p class="info-label">Départements</p>
                        <p class="info-description">Subdivisions administratives</p>
                    </div>
                    <div class="info-item">
                        <h4 class="info-value">{{ $stats['nombre_communes'] ?? 0 }}</h4>
                        <p class="info-label">Communes</p>
                        <p class="info-description">Total dans la région</p>
                    </div>
                    @if($stats['superficie'] ?? $region->superficie)
                    <div class="info-item">
                        <h4 class="info-value">{{ number_format($stats['superficie'] ?? $region->superficie) }}</h4>
                        <p class="info-label">Superficie (km²)</p>
                        <p class="info-description">Étendue territoriale</p>
                    </div>
                    @endif
                    @if($stats['population'] ?? $region->population)
                    <div class="info-item">
                        <h4 class="info-value">{{ number_format($stats['population'] ?? $region->population) }}</h4>
                        <p class="info-label">Population</p>
                        <p class="info-description">Habitants recensés</p>
                    </div>
                    @endif
                    @if(($stats['superficie'] ?? $region->superficie) && ($stats['population'] ?? $region->population))
                    <div class="info-item">
                        <h4 class="info-value">{{ number_format(($stats['population'] ?? $region->population) / ($stats['superficie'] ?? $region->superficie), 1) }}</h4>
                        <p class="info-label">Densité (hab/km²)</p>
                        <p class="info-description">Population par km²</p>
                    </div>
                    @endif
                    @if(isset($stats['budget_total']))
                    <div class="info-item">
                        <h4 class="info-value">{{ number_format($stats['budget_total'], 1) }}M</h4>
                        <p class="info-label">Budget Total</p>
                        <p class="info-description">En millions FCFA</p>
                    </div>
                    @endif
                    @if(isset($stats['taux_execution']))
                    <div class="info-item">
                        <h4 class="info-value">{{ number_format($stats['taux_execution'], 1) }}%</h4>
                        <p class="info-label">Taux d'exécution</p>
                        <p class="info-description">Exécution budgétaire</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Indicateurs de performance -->
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    Indicateurs de performance
                </h3>
                
                <div class="progress-section">
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Couverture administrative</span>
                            <span class="progress-value">85%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" data-width="85"></div>
                        </div>
                    </div>
                    
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Développement économique</span>
                            <span class="progress-value">72%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" data-width="72"></div>
                        </div>
                    </div>
                    
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Infrastructure</span>
                            <span class="progress-value">68%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" data-width="68"></div>
                        </div>
                    </div>
                    
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Services sociaux</span>
                            <span class="progress-value">79%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" data-width="79"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Départements -->
            @if($region->departements && $region->departements->count() > 0)
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    Départements ({{ $stats['nombre_departements'] }})
                </h3>
                
                <ul class="departments-list">
                    @foreach($region->departements as $departement)
                    <li class="department-item">
                        <div class="department-name">{{ $departement->nom }}</div>
                        <div class="department-info">
                            Chef-lieu : {{ $departement->chef_lieu ?? 'Non renseigné' }} • 
                            {{ $departement->communes->count() ?? 0 }} commune(s)
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @else
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    Départements
                </h3>
                
                <div class="empty-state">
                    <i class="fas fa-map"></i>
                    <p>Aucun département enregistré pour cette région.</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Informations complémentaires -->
        <div class="col-lg-4">
            <!-- Informations détaillées -->
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    Informations détaillées
                </h3>
                
                <div class="mb-3">
                    <strong>Nom complet :</strong><br>
                    <span class="text-muted">{{ $region->nom }}</span>
                </div>
                
                <div class="mb-3">
                    <strong>Code région :</strong><br>
                    <span class="text-muted">{{ $region->code }}</span>
                </div>
                
                <div class="mb-3">
                    <strong>Chef-lieu :</strong><br>
                    <span class="text-muted">{{ $region->chef_lieu }}</span>
                </div>

                <div class="mb-3">
                    <strong>Départements :</strong><br>
                    <span class="text-muted">{{ $stats['nombre_departements'] }} département(s)</span>
                </div>
                
                <div class="mb-3">
                    <strong>Communes :</strong><br>
                    <span class="text-muted">{{ $stats['nombre_communes'] }} commune(s)</span>
                </div>
                
                @if($stats['superficie'] ?? $region->superficie)
                <div class="mb-3">
                    <strong>Superficie :</strong><br>
                    <span class="text-muted">{{ number_format($stats['superficie'] ?? $region->superficie) }} km²</span>
                </div>
                @endif
                
                @if($stats['population'] ?? $region->population)
                <div class="mb-3">
                    <strong>Population :</strong><br>
                    <span class="text-muted">{{ number_format($stats['population'] ?? $region->population) }} habitants</span>
                </div>
                @endif

                @if(isset($stats['budget_total']))
                <div class="mb-3">
                    <strong>Budget total :</strong><br>
                    <span class="text-muted">{{ number_format($stats['budget_total'], 1) }} millions FCFA</span>
                </div>
                @endif

                @if(isset($stats['taux_execution']))
                <div class="mb-3">
                    <strong>Taux d'exécution :</strong><br>
                    <span class="text-muted">{{ number_format($stats['taux_execution'], 1) }}%</span>
                </div>
                @endif
                
                <div class="mb-3">
                    <strong>Créée le :</strong><br>
                    <span class="text-muted">{{ $region->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                
                @if($region->updated_at != $region->created_at)
                <div class="mb-3">
                    <strong>Dernière modification :</strong><br>
                    <span class="text-muted">{{ $region->updated_at->format('d/m/Y à H:i') }}</span>
                </div>
                @endif
            </div>

            <!-- Statistiques communes -->
            @if($stats['nombre_communes'] > 0)
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-city"></i>
                    </div>
                    Répartition des communes
                </h3>
                
                <div class="info-grid">
                    <div class="info-item">
                        <h4 class="info-value">{{ $stats['nombre_communes'] }}</h4>
                        <p class="info-label">Total communes</p>
                        <p class="info-description">Dans la région</p>
                    </div>
                    
                    @if($stats['nombre_departements'] > 0)
                    <div class="info-item">
                        <h4 class="info-value">{{ number_format($stats['nombre_communes'] / $stats['nombre_departements'], 1) }}</h4>
                        <p class="info-label">Moy. par dépt.</p>
                        <p class="info-description">Communes/département</p>
                    </div>
                    @endif
                </div>

                <!-- Répartition par département -->
                @if($region->departements && $region->departements->count() > 0)
                <div class="mt-3">
                    <h6 class="mb-3">Répartition par département :</h6>
                    @foreach($region->departements as $departement)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                        <span class="fw-bold">{{ $departement->nom }}</span>
                        <span class="badge bg-primary">{{ $departement->communes->count() ?? 0 }} communes</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            <!-- Historique récent -->
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    Historique récent
                </h3>
                
                <div class="timeline-section">
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-date">{{ $region->updated_at->format('d/m/Y') }}</div>
                            <div class="timeline-title">Dernière mise à jour des informations</div>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-date">{{ $region->created_at->format('d/m/Y') }}</div>
                            <div class="timeline-title">Création de la région dans le système</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    Actions rapides
                </h3>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('departements.create', ['region' => $region->id]) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus"></i> Ajouter un département
                    </a>
                    <a href="{{ route('communes.index', ['region' => $region->id]) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-city"></i> Voir les {{ $stats['nombre_communes'] }} communes
                    </a>
                    <button class="btn btn-outline-success btn-sm" onclick="exportData()">
                        <i class="fas fa-file-export"></i> Exporter les données
                    </button>
                    <button class="btn btn-outline-warning btn-sm" onclick="generateReport()">
                        <i class="fas fa-chart-pie"></i> Générer un rapport
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="action-buttons fade-in">
        <a href="{{ route('regions.index') }}" class="btn-custom btn-secondary-custom">
            <i class="fas fa-arrow-left"></i>
            Retour à la liste
        </a>
        <a href="{{ route('regions.edit', $region) }}" class="btn-custom btn-warning-custom">
            <i class="fas fa-edit"></i>
            Modifier
        </a>
        <a href="{{ route('departements.index', ['region' => $region->id]) }}" class="btn-custom btn-primary-custom">
            <i class="fas fa-map-marked-alt"></i>
            Voir départements ({{ $stats['nombre_departements'] }})
        </a>
        <a href="{{ route('communes.index', ['region' => $region->id]) }}" class="btn-custom btn-primary-custom">
            <i class="fas fa-city"></i>
            Voir communes ({{ $stats['nombre_communes'] }})
        </a>
        <button type="button" class="btn-custom btn-success-custom" onclick="printReport()">
            <i class="fas fa-print"></i>
            Imprimer rapport
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation d'apparition progressive
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('active');
                    }, index * 150);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        const fadeElements = document.querySelectorAll('.fade-in');
        fadeElements.forEach(element => {
            observer.observe(element);
        });

        // Animation des barres de progression
        setTimeout(() => {
            const progressBars = document.querySelectorAll('.progress-bar-fill');
            progressBars.forEach(bar => {
                const width = bar.getAttribute('data-width');
                bar.style.width = '0%';
                
                setTimeout(() => {
                    bar.style.width = width + '%';
                }, 300);
            });
        }, 1000);

        // Animation des éléments de timeline
        const timelineItems = document.querySelectorAll('.timeline-item');
        timelineItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            item.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }, 800 + (index * 200));
        });

        // Hover effects pour les info-items
        const infoItems = document.querySelectorAll('.info-item');
        infoItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.borderLeft = '4px solid var(--primary-color)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.borderLeft = '1px solid #e9ecef';
            });
        });
    });

    // Fonction d'impression
    function printReport() {
        window.print();
    }

    // Fonction d'export (à implémenter selon vos besoins)
    function exportData() {
        // Implémentation de l'export
        console.log('Export des données de la région {{ $region->nom }}');
        console.log('Départements: {{ $stats["nombre_departements"] }}');
        console.log('Communes: {{ $stats["nombre_communes"] }}');
        
        // Exemple d'export simple vers CSV
        const data = [
            ['Région', '{{ $region->nom }}'],
            ['Code', '{{ $region->code }}'],
            ['Chef-lieu', '{{ $region->chef_lieu }}'],
            ['Départements', '{{ $stats["nombre_departements"] }}'],
            ['Communes', '{{ $stats["nombre_communes"] }}'],
            @if(isset($stats['superficie']))
            ['Superficie', '{{ $stats["superficie"] ?? $region->superficie }}'],
            @endif
            @if(isset($stats['population']))
            ['Population', '{{ $stats["population"] ?? $region->population }}'],
            @endif
        ];
        
        let csvContent = "data:text/csv;charset=utf-8,";
        data.forEach(row => {
            csvContent += row.join(",") + "\n";
        });
        
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "region_{{ $region->code }}_data.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Fonction de génération de rapport
    function generateReport() {
        console.log('Génération du rapport pour la région {{ $region->nom }}');
        alert('Génération du rapport en cours...\n\nRégion: {{ $region->nom }}\nDépartements: {{ $stats["nombre_departements"] }}\nCommunes: {{ $stats["nombre_communes"] }}');
    }
</script>

<style>
@media print {
    .action-buttons,
    .breadcrumb,
    nav,
    .btn {
        display: none !important;
    }
    
    .info-card {
        break-inside: avoid;
        page-break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .region-header {
        background: #f8f9fa !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
    }
}
</style>
@endpush







{{-- @extends('layouts.app')

@section('title', $region->nom . ' - Observatoire des Collectivités Territoriales')
@section('page-title', 'Région ' . $region->nom)

@push('styles')
<style>
    .region-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .region-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .region-header-content {
        position: relative;
        z-index: 1;
    }

    .region-title {
        font-size: 32px;
        font-weight: 700;
        margin: 0;
        margin-bottom: 10px;
    }

    .region-subtitle {
        font-size: 16px;
        opacity: 0.9;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .region-code-badge {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 6px 15px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid rgba(255,255,255,0.3);
        display: inline-block;
        margin-top: 15px;
    }

    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .info-item {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .info-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
        margin-bottom: 5px;
    }

    .info-label {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-description {
        font-size: 12px;
        color: #868e96;
        margin-top: 5px;
    }

    .progress-section {
        margin-bottom: 20px;
    }

    .progress-item {
        margin-bottom: 15px;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .progress-label {
        font-size: 14px;
        font-weight: 600;
        color: #495057;
    }

    .progress-value {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary-color);
    }

    .progress-bar-container {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #28a745, #20c997);
        border-radius: 4px;
        transition: width 1s ease;
        position: relative;
    }

    .progress-bar-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-custom {
        padding: 12px 25px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: 140px;
        justify-content: center;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(44,82,130,0.3);
        color: white;
    }

    .btn-success-custom {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40,167,69,0.3);
        color: white;
    }

    .btn-warning-custom {
        background: linear-gradient(135deg, #f39c12, #f1c40f);
        color: white;
    }

    .btn-warning-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(243,156,18,0.3);
        color: white;
    }

    .btn-secondary-custom {
        background: #6c757d;
        color: white;
    }

    .btn-secondary-custom:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(108,117,125,0.3);
        color: white;
    }

    .departments-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .department-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .department-item:hover {
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }

    .department-name {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        font-size: 14px;
    }

    .department-info {
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.3;
    }

    .timeline-section {
        position: relative;
    }

    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 20px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        width: 2px;
        height: 100%;
        background: #e9ecef;
    }

    .timeline-item::after {
        content: '';
        position: absolute;
        left: 6px;
        top: 8px;
        width: 10px;
        height: 10px;
        background: var(--primary-color);
        border-radius: 50%;
    }

    .timeline-content {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        border: 1px solid #e9ecef;
    }

    .timeline-date {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .timeline-title {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .region-title {
            font-size: 24px;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn-custom {
            min-width: 100%;
        }
        
        .region-header {
            padding: 20px;
        }
        
        .info-card {
            padding: 20px;
        }
    }

    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.6s ease;
    }

    .fade-in.active {
        opacity: 1;
        transform: translateY(0);
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

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('regions.index') }}">Régions</a></li>
            <li class="breadcrumb-item active">{{ $region->nom }}</li>
        </ol>
    </nav>

    <!-- En-tête région -->
    <div class="region-header fade-in">
        <div class="region-header-content">
            <h1 class="region-title">{{ $region->nom }}</h1>
            <p class="region-subtitle">
                <i class="fas fa-map-marker-alt"></i>
                Chef-lieu : {{ $region->chef_lieu }}
            </p>
            <span class="region-code-badge">{{ $region->code }}</span>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <!-- Statistiques générales -->
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    Statistiques générales
                </h3>
                
                <div class="info-grid">
                    <div class="info-item">
                        <h4 class="info-value">{{ $region->nombre_departements ?? 0 }}</h4>
                        <p class="info-label">Départements</p>
                        <p class="info-description">Subdivisions administratives</p>
                    </div>
                    <div class="info-item">
                        <h4 class="info-value">{{ $region->nombre_communes ?? 0 }}</h4>
                        <p class="info-label">Communes</p>
                        <p class="info-description">Total dans la région</p>
                    </div>
                    @if($region->superficie)
                    <div class="info-item">
                        <h4 class="info-value">{{ number_format($region->superficie) }}</h4>
                        <p class="info-label">Superficie (km²)</p>
                        <p class="info-description">Étendue territoriale</p>
                    </div>
                    @endif
                    @if($region->population)
                    <div class="info-item">
                        <h4 class="info-value">{{ number_format($region->population) }}</h4>
                        <p class="info-label">Population</p>
                        <p class="info-description">Habitants recensés</p>
                    </div>
                    @endif
                    @if($region->superficie && $region->population)
                    <div class="info-item">
                        <h4 class="info-value">{{ number_format($region->population / $region->superficie, 1) }}</h4>
                        <p class="info-label">Densité (hab/km²)</p>
                        <p class="info-description">Population par km²</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Indicateurs de performance -->
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    Indicateurs de performance
                </h3>
                
                <div class="progress-section">
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Couverture administrative</span>
                            <span class="progress-value">85%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" data-width="85"></div>
                        </div>
                    </div>
                    
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Développement économique</span>
                            <span class="progress-value">72%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" data-width="72"></div>
                        </div>
                    </div>
                    
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Infrastructure</span>
                            <span class="progress-value">68%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" data-width="68"></div>
                        </div>
                    </div>
                    
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Services sociaux</span>
                            <span class="progress-value">79%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" data-width="79"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Départements -->
            @if($region->departements && $region->departements->count() > 0)
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    Départements ({{ $region->departements->count() }})
                </h3>
                
                <ul class="departments-list">
                    @foreach($region->departements as $departement)
                    <li class="department-item">
                        <div class="department-name">{{ $departement->nom }}</div>
                        <div class="department-info">
                            Chef-lieu : {{ $departement->chef_lieu ?? 'Non renseigné' }} • 
                            {{ $departement->communes_count ?? 0 }} commune(s)
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @else
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    Départements
                </h3>
                
                <div class="empty-state">
                    <i class="fas fa-map"></i>
                    <p>Aucun département enregistré pour cette région.</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Informations complémentaires -->
        <div class="col-lg-4">
            <!-- Informations détaillées -->
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    Informations détaillées
                </h3>
                
                <div class="mb-3">
                    <strong>Nom complet :</strong><br>
                    <span class="text-muted">{{ $region->nom }}</span>
                </div>
                
                <div class="mb-3">
                    <strong>Code région :</strong><br>
                    <span class="text-muted">{{ $region->code }}</span>
                </div>
                
                <div class="mb-3">
                    <strong>Chef-lieu :</strong><br>
                    <span class="text-muted">{{ $region->chef_lieu }}</span>
                </div>
                
                @if($region->superficie)
                <div class="mb-3">
                    <strong>Superficie :</strong><br>
                    <span class="text-muted">{{ number_format($region->superficie) }} km²</span>
                </div>
                @endif
                
                @if($region->population)
                <div class="mb-3">
                    <strong>Population :</strong><br>
                    <span class="text-muted">{{ number_format($region->population) }} habitants</span>
                </div>
                @endif
                
                <div class="mb-3">
                    <strong>Créée le :</strong><br>
                    <span class="text-muted">{{ $region->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                
                @if($region->updated_at != $region->created_at)
                <div class="mb-3">
                    <strong>Dernière modification :</strong><br>
                    <span class="text-muted">{{ $region->updated_at->format('d/m/Y à H:i') }}</span>
                </div>
                @endif
            </div>

            <!-- Historique récent -->
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    Historique récent
                </h3>
                
                <div class="timeline-section">
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-date">{{ $region->updated_at->format('d/m/Y') }}</div>
                            <div class="timeline-title">Dernière mise à jour des informations</div>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-date">{{ $region->created_at->format('d/m/Y') }}</div>
                            <div class="timeline-title">Création de la région dans le système</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="info-card fade-in">
                <h3 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    Actions rapides
                </h3>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('departements.create', ['region' => $region->id]) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus"></i> Ajouter un département
                    </a>
                    <a href="{{ route('communes.index', ['region' => $region->id]) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-city"></i> Voir les communes
                    </a>
                    <button class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-export"></i> Exporter les données
                    </button>
                    <button class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-chart-pie"></i> Générer un rapport
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="action-buttons fade-in">
        <a href="{{ route('regions.index') }}" class="btn-custom btn-secondary-custom">
            <i class="fas fa-arrow-left"></i>
            Retour à la liste
        </a>
        <a href="{{ route('regions.edit', $region) }}" class="btn-custom btn-warning-custom">
            <i class="fas fa-edit"></i>
            Modifier
        </a>
        <a href="{{ route('departements.index', ['region' => $region->id]) }}" class="btn-custom btn-primary-custom">
            <i class="fas fa-map-marked-alt"></i>
            Voir départements
        </a>
        <button type="button" class="btn-custom btn-success-custom" onclick="printReport()">
            <i class="fas fa-print"></i>
            Imprimer rapport
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation d'apparition progressive
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('active');
                    }, index * 150);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        const fadeElements = document.querySelectorAll('.fade-in');
        fadeElements.forEach(element => {
            observer.observe(element);
        });

        // Animation des barres de progression
        setTimeout(() => {
            const progressBars = document.querySelectorAll('.progress-bar-fill');
            progressBars.forEach(bar => {
                const width = bar.getAttribute('data-width');
                bar.style.width = '0%';
                
                setTimeout(() => {
                    bar.style.width = width + '%';
                }, 300);
            });
        }, 1000);

        // Animation des éléments de timeline
        const timelineItems = document.querySelectorAll('.timeline-item');
        timelineItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            item.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }, 800 + (index * 200));
        });

        // Hover effects pour les info-items
        const infoItems = document.querySelectorAll('.info-item');
        infoItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.borderLeft = '4px solid var(--primary-color)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.borderLeft = '1px solid #e9ecef';
            });
        });
    });

    // Fonction d'impression
    function printReport() {
        window.print();
    }

    // Fonction d'export (à implémenter selon vos besoins)
    function exportData() {
        // Implémentation de l'export
        console.log('Export des données de la région');
    }
</script>

<style>
@media print {
    .action-buttons,
    .breadcrumb,
    nav,
    .btn {
        display: none !important;
    }
    
    .info-card {
        break-inside: avoid;
        page-break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .region-header {
        background: #f8f9fa !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
    }
}
</style>
@endpush --}}