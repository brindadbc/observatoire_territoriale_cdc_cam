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
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .header {
            background-color: var(--secondary-color);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .detail-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .detail-card .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        
        .info-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .montant {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .badge {
            font-weight: 500;
            padding: 0.5em 0.75em;
        }
        
        @media (max-width: 768px) {
            .info-item {
                flex-direction: column;
            }
            
            .info-label {
                margin-bottom: 0.5rem;
            }
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
        
        .timeline-content {
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="bi bi-bank me-2"></i>Détails de la Ressource</h1>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('ressources-etat.index') }}" class="btn btn-outline-light me-2">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                    <a href="{{ route('ressources-etat.edit', $ressource) }}" class="btn btn-light">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="detail-card card">
                    <div class="card-header">
                        <h5 class="mb-0">Informations de la Ressource</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center info-item">
                            <span class="info-label">Référence:</span>
                            <span class="badge bg-secondary">{{ $ressource->reference }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center info-item">
                            <span class="info-label">Commune Bénéficiaire:</span>
                            <span>
                                <a href="{{ route('communes.show', $ressource->commune) }}" class="text-primary">
                                    {{ $ressource->commune->nom }} ({{ $ressource->commune->region }})
                                </a>
                            </span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center info-item">
                            <span class="info-label">Type de Ressource:</span>
                            <span class="badge bg-info">{{ $ressource->type_ressource }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center info-item">
                            <span class="info-label">Montant:</span>
                            <span class="montant">{{ number_format($ressource->montant, 0, ',', ' ') }} FCFA</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center info-item">
                            <span class="info-label">Date de Réception:</span>
                            <span>{{ $ressource->date_reception->format('d/m/Y') }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center info-item">
                            <span class="info-label">Projet Associé:</span>
                            <span>{{ $ressource->projet_associe ?? 'Non spécifié' }}</span>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label mb-2">Description:</div>
                            <div class="border p-3 rounded bg-light">
                                {{ $ressource->description }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
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
                                    <p class="mb-1 text-muted small">{{ $ressource->created_at->diffForHumans() }}</p>
                                    <p class="mb-0">Ressource enregistrée dans le système</p>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-pencil text-warning"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Dernière Modification</h6>
                                    <p class="mb-1 text-muted small">{{ $ressource->updated_at->diffForHumans() }}</p>
                                    <p class="mb-0">Mise à jour des informations</p>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="bi bi-cash-coin text-success"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Montant Alloué</h6>
                                    <p class="mb-1 text-muted small">{{ $ressource->date_reception->format('d/m/Y') }}</p>
                                    <p class="mb-0">Transfert effectué vers la commune</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-card card mt-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Zone Dangereuse</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Cette action est irréversible. Soyez certain avant de continuer.</p>
                        <form action="{{ route('ressources-etat.destroy', $ressource) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette ressource ?')">
                                <i class="bi bi-trash"></i> Supprimer cette Ressource
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
        $(document).ready(function() {
            // Animation des cartes
            $('.detail-card').hover(
                function() {
                    $(this).css('transform', 'translateY(-5px)');
                    $(this).css('box-shadow', '0 10px 20px rgba(0,0,0,0.1)');
                },
                function() {
                    $(this).css('transform', '');
                    $(this).css('box-shadow', '0 4px 6px rgba(0,0,0,0.1)');
                }
            );
            
            // Confirmation avant suppression
            $('form[data-confirm]').on('submit', function(e) {
                if (!confirm($(this).data('confirm'))) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>