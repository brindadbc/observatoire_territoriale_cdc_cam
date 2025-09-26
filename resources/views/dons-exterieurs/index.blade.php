<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dons Extérieurs - Gestion Communale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #f39c12;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --info-color: #3498db;
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
        
        .badge-donateur {
            background-color: var(--info-color);
        }
        
        .badge-type {
            background-color: var(--success-color);
        }
        
        .filter-card {
            margin-bottom: 2rem;
        }
        
        @media (max-width: 768px) {
            .filter-options {
                flex-direction: column;
            }
            
            .filter-options .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="bi bi-globe me-2"></i>Dons Extérieurs aux Communes</h1>
                </div>
                <!-- <div class="col-md-6 text-end">
                    {{-- <a href="{{ route('dashboard') }}" class="btn btn-outline-light me-2"> --}}
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a> -->
                    <a href="{{ route('dons-exterieurs.create') }}" class="btn btn-light">
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

        <div class="card filter-card">
            <div class="card-body">
                <h5 class="card-title">Filtres</h5>
                <form method="GET" action="{{ route('dons-exterieurs.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="type_aide" class="form-label">Type d'aide</label>
                            <select class="form-select" id="type_aide" name="type_aide">
                                <option value="">Tous les types</option>
                                <option value="Financière" {{ request('type_aide') == 'Financière' ? 'selected' : '' }}>Financière</option>
                                <option value="Matérielle" {{ request('type_aide') == 'Matérielle' ? 'selected' : '' }}>Matérielle</option>
                                <option value="Technique" {{ request('type_aide') == 'Technique' ? 'selected' : '' }}>Technique</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="donateur" class="form-label">Donateur</label>
                            <input type="text" class="form-control" id="donateur" name="donateur" value="{{ request('donateur') }}" placeholder="Rechercher par donateur">
                        </div>
                        <div class="col-md-4">
                            <label for="commune_id" class="form-label">Commune</label>
                            <select class="form-select" id="commune_id" name="commune_id">
                                <option value="">Toutes les communes</option>
                                @foreach($communes as $commune)
                                    <option value="{{ $commune->id }}" {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                                        {{ $commune->nom }} ({{ $commune->region }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-end filter-options">
                                <a href="{{ route('dons-exterieurs.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel"></i> Appliquer
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Liste des Dons</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="donsTable">
                        <thead>
                            <tr>
                                <th>Commune</th>
                                <th>Donateur</th>
                                <th>Type d'Aide</th>
                                <th>Montant/Valeur</th>
                                <th>Date Réception</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dons as $don)
                            <tr>
                                <td>
                                    <a href="{{ route('communes.show', $don->commune) }}" class="text-primary">
                                        {{ $don->commune->nom }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $don->commune->region }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-donateur">{{ $don->donateur }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-type">{{ $don->type_aide }}</span>
                                </td>
                                <td>{{ number_format($don->montant, 0, ',', ' ') }} FCFA</td>
                                <td>{{ $don->date_reception->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dons-exterieurs.show', $don) }}" class="btn btn-info btn-sm" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('dons-exterieurs.edit', $don) }}" class="btn btn-warning btn-sm" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('dons-exterieurs.destroy', $don) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?')">
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
                        Affichage de <strong>{{ $dons->firstItem() }}</strong> à <strong>{{ $dons->lastItem() }}</strong> sur <strong>{{ $dons->total() }}</strong> dons
                    </div>
                    <div>
                        {{ $dons->links() }}
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
            $('#donsTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
                },
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                pageLength: 25,
                order: [[4, 'desc']]
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