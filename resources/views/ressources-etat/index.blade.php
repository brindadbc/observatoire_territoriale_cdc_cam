<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ressources de l'État - Gestion Communale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --info-color: #1abc9c;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .header {
            background-color: var(--secondary-color);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
            padding: 1rem 1.5rem;
        }
        
        .table th {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        
        .badge {
            font-weight: 500;
            padding: 0.5em 0.75em;
        }
        
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 0.25rem;
            }
            
            .btn-action {
                width: 100%;
                margin-bottom: 0.25rem;
            }
        }
        
        .search-box {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 12px;
            color: #6c757d;
        }
        
        .search-box input {
            padding-left: 40px;
            border-radius: 50px;
            border: 1px solid #ddd;
        }
        
        .stats-card {
            border-left: 4px solid;
            padding: 1rem;
            margin-bottom: 1.5rem;
            background: white;
            border-radius: 8px;
        }
        
        .stats-card.primary {
            border-left-color: var(--primary-color);
        }
        
        .stats-card.success {
            border-left-color: var(--success-color);
        }
        
        .stats-card.warning {
            border-left-color: var(--warning-color);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="bi bi-bank me-2"></i>Ressources de l'État</h1>
                </div>
                {{-- <div class="col-md-6 text-end">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light me-2">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a> --}}
                    <a href="{{ route('ressources-etat.create') }}" class="btn btn-light">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

       <div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <h6>Total Ressources</h6>
            <h3>{{ isset($totalRessources) ? number_format($totalRessources, 2, ',', ' ') : '0,00' }} FCFA</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <h6>Moyenne par Commune</h6>
            <h3>{{ isset($averagePerCommune) ? number_format($averagePerCommune, 0, ',', ' ') : '0' }} FCFA</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <h6>Communes Bénéficiaires</h6>
            <h3>{{ $communesCount ?? '0' }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <h6>Dernière Mise à Jour</h6>
            <h3>{{ isset($lastUpdated) ? $lastUpdated->diffForHumans() : 'N/A' }}</h3>
        </div>
    </div>
</div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Ressources</h5>
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="ressourcesTable">
                        <thead>
                            <tr>
                                <th>Commune</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Date Réception</th>
                                <th>Projet</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ressources as $ressource)
                            <tr>
                                <td>
                                    <a href="{{ route('communes.show', $ressource->commune) }}" class="text-primary">
                                        {{ $ressource->commune->nom }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $ressource->commune->region }}</small>
                                </td>
                                <td>{{ $ressource->type_ressource }}</td>
                                <td>{{ number_format($ressource->montant, 0, ',', ' ') }} FCFA</td>
                                <td>{{ $ressource->date_reception->format('d/m/Y') }}</td>
                                <td>{{ $ressource->projet_associe ?? '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('ressources-etat.show', $ressource) }}" class="btn btn-info btn-action" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('ressources-etat.edit', $ressource) }}" class="btn btn-warning btn-action" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('ressources-etat.destroy', $ressource) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-action" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette ressource ?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de <strong>{{ $ressources->firstItem() }}</strong> à <strong>{{ $ressources->lastItem() }}</strong> sur <strong>{{ $ressources->total() }}</strong> ressources
                    </div>
                    <div>
                        {{ $ressources->links() }}
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
                    <p class="mb-0">Version 1.0.0</p>
                    <p class="mb-0">Dernière mise à jour: {{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialisation de DataTable
            const table = $('#ressourcesTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
                },
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                pageLength: 25,
                order: [[3, 'desc']]
            });
            
            // Recherche instantanée
            $('#searchInput').keyup(function() {
                table.search($(this).val()).draw();
            });
            
            // Animation des lignes
            $('#ressourcesTable tbody tr').hover(
                function() {
                    $(this).css('background-color', 'rgba(52, 152, 219, 0.1)');
                },
                function() {
                    $(this).css('background-color', '');
                }
            );
            
            // Confirmation avant suppression
            $('form[data-confirm]').on('submit', function(e) {
                if (!confirm($(this).data('confirm'))) {
                    e.preventDefault();
                }
            });
            
            // Auto-dismiss des alertes après 5 secondes
            setTimeout(function() {
                $('.alert').fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            }, 5000);
        });
    </script>
</body>
</html>