<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autres Ressources - Gestion Communale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #6c757d;
            --secondary-color: #2c3e50;
            --info-color: #3498db;
            --success-color: #2ecc71;
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        
        .badge-source {
            background-color: var(--info-color);
        }
        
        .badge-type {
            background-color: var(--success-color);
        }
        
        .stats-card {
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            margin-bottom: 1.5rem;
            background: white;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="bi bi-collection me-2"></i>Autres Ressources des Communes</h1>
                </div>
                {{-- <div class="col-md-6 text-end">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light me-2">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a> --}}
                    <a href="{{ route('autres-ressources.create') }}" class="btn btn-light">
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
            <div class="col-md-4">
                <div class="stats-card">
                    <h6>Total Ressources</h6>
                    <h3>{{ number_format($totalRessources, 0, ',', ' ') }} FCFA</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <h6>Sources Différentes</h6>
                    <h3>{{ $sourcesCount }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <h6>Dernier Ajout</h6>
                    <h3>{{ $lastAdded->diffForHumans() }}</h3>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Ressources</h5>
                <div>
                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="ressourcesTable">
                        <thead>
                            <tr>
                                <th>Commune</th>
                                <th>Source</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Date Réception</th>
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
                                <td>
                                    <span class="badge badge-source">{{ $ressource->source }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-type">{{ $ressource->type_ressource }}</span>
                                </td>
                                <td>{{ number_format($ressource->montant, 0, ',', ' ') }} FCFA</td>
                                <td>{{ $ressource->date_reception->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('autres-ressources.show', $ressource) }}" class="btn btn-info btn-sm" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('autres-ressources.edit', $ressource) }}" class="btn btn-warning btn-sm" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('autres-ressources.destroy', $ressource) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette ressource ?')">
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
                order: [[4, 'desc']]
            });
            
            // Recherche instantanée
            $('#searchInput').keyup(function() {
                table.search($(this).val()).draw();
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