<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Ressource - Gestion Communale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #6c757d;
            --secondary-color: #2c3e50;
            --info-color: #3498db;
            --light-color: #ecf0f1;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .header {
            background-color: var(--secondary-color);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .detail-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .detail-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }
        
        .info-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
            display: flex;
            justify-content: space-between;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .info-value {
            text-align: right;
        }
        
        .montant {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .badge {
            font-weight: 500;
            padding: 0.5em 0.75em;
        }
        
        .timeline {
            position: relative;
            padding-left: 2rem;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #eee;
        }
        
        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }
        
        .timeline-icon {
            position: absolute;
            left: -2rem;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--primary-color);
        }
        
        .danger-zone {
            border-left: 4px solid #e74c3c;
        }
        
        @media (max-width: 768px) {
            .info-item {
                flex-direction: column;
            }
            
            .info-value {
                text-align: left;
                margin-top: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="bi bi-collection me-2"></i>Détails de la Ressource</h1>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('autres-ressources.index') }}" class="btn btn-outline-light me-2">
                        <i class="bi bi-arrow-left"></i> Retour à la liste
                    </a>
                    <a href="{{ route('autres-ressources.edit', $ressource) }}" class="btn btn-light">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="detail-card card">
                    <div class="card-header">
                        <h5 class="mb-0">Informations sur la ressource</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <span class="info-label">Commune:</span>
                            <span class="info-value">
                                <a href="{{ route('communes.show', $ressource->commune) }}" class="text-primary">
                                    {{ $ressource->commune->nom }}
                                </a>
                                <div class="text-muted small">{{ $ressource->commune->region }}</div>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Source:</span>
                            <span class="info-value">
                                <span class="badge bg-info">{{ $ressource->source }}</span>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Type de ressource:</span>
                            <span class="info-value">
                                <span class="badge bg-primary">{{ $ressource->type_ressource }}</span>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Montant/Valeur:</span>
                            <span class="info-value montant">
                                {{ number_format($ressource->montant, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Date de réception:</span>
                            <span class="info-value">
                                {{ $ressource->date_reception->format('d/m/Y') }}
                                <div class="text-muted small">{{ $ressource->date_reception->diffForHumans() }}</div>
                            </span>
                        </div>
                        
                        <div class="info-item" style="border-bottom: none;">
                            <span class="info-label">Dernière modification:</span>
                            <span class="info-value">
                                {{ $ressource->updated_at->format('d/m/Y H:i') }}
                                <div class="text-muted small">{{ $ressource->updated_at->diffForHumans() }}</div>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-card card">
                    <div class="card-header">
                        <h5 class="mb-0">Description</h5>
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-light rounded">
                            {{ $ressource->description }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="detail-card card">
                    <div class="card-header">
                        <h5 class="mb-0">Historique</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-plus-circle text-primary"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Création</h6>
                                    <p class="mb-1 text-muted small">{{ $ressource->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="mb-0">Enregistrement initial dans le système</p>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-cash-coin text-success"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Réception</h6>
                                    <p class="mb-1 text-muted small">{{ $ressource->date_reception->format('d/m/Y') }}</p>
                                    <p class="mb-0">Date effective de réception</p>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-pencil text-warning"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Dernière modification</h6>
                                    <p class="mb-1 text-muted small">{{ $ressource->updated_at->format('d/m/Y H:i') }}</p>
                                    <p class="mb-0">Mise à jour des informations</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-card card danger-zone">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Zone dangereuse</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Cette action est irréversible. Soyez certain avant de continuer.</p>
                        <form action="{{ route('autres-ressources.destroy', $ressource) }}" method="POST" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-outline-danger w-100" onclick="confirmDelete()">
                                <i class="bi bi-trash"></i> Supprimer cette ressource
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Système de Gestion des Ressources Communales</h5>
                    <p class="mb-0">© {{ date('Y') }} Ministère des Collectivités Territoriales</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">Consulté le: {{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete() {
            if (confirm('Êtes-vous sûr de vouloir supprimer définitivement cette ressource ? Cette action est irréversible.')) {
                document.getElementById('deleteForm').submit();
            }
        }
        
        $(document).ready(function() {
            // Animation des cartes au survol
            $('.detail-card').hover(
                function() {
                    $(this).css('transform', 'translateY(-5px)');
                    $(this).css('box-shadow', '0 8px 20px rgba(0,0,0,0.15)');
                },
                function() {
                    $(this).css('transform', '');
                    $(this).css('box-shadow', '0 4px 12px rgba(0,0,0,0.1)');
                }
            );
        });
    </script>
</body>
</html>