<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Ressource - Gestion Communale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2ecc71;
            --secondary-color: #2c3e50;
            --warning-color: #f39c12;
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
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .form-header {
            background-color: var(--warning-color);
            color: white;
            padding: 1.5rem;
            font-weight: 600;
        }
        
        .form-body {
            padding: 2rem;
        }
        
        .commune-info {
            background-color: var(--light-color);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .montant-input {
            position: relative;
        }
        
        .montant-input::after {
            content: 'FCFA';
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-weight: 600;
        }
        
        .history-item {
            border-left: 3px solid var(--primary-color);
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<!-- Ajoutez ceci en haut du form-body -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4">
    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Corrigez la route du formulaire -->
<form action="{{ route('ressources-commune.update', $ressource->id) }}" method="POST">
    @csrf
    @method('PUT')
    <!-- ... reste du formulaire inchangé ... -->
</form>
<body>
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="bi bi-cash-stack me-2"></i>Modifier Ressource Communale</h1>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('ressources-commune.show', $ressource) }}" class="btn btn-outline-light me-2">
                        <i class="bi bi-arrow-left"></i> Retour aux détails
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Modification de la ressource</h5>
            </div>
            
            <div class="form-body">
                <!-- Messages de session -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form action="{{ route('ressources-commune.update', $ressource) }}" method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="commune-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-building fs-4 me-3 text-primary"></i>
                            <div>
                                <h6 class="mb-1">Commune: {{ $ressource->commune->nom }}</h6>
                                <p class="mb-0 text-muted">{{ $ressource->commune->region }} / {{ $ressource->commune->departement }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="source" class="form-label">Source *</label>
                            <select class="form-select @error('source') is-invalid @enderror" id="source" name="source" required>
                                <option value="Impôts" {{ old('source', $ressource->source) == 'Impôts' ? 'selected' : '' }}>Impôts</option>
                                <option value="Taxes" {{ old('source', $ressource->source) == 'Taxes' ? 'selected' : '' }}>Taxes</option>
                                <option value="Revenus fonciers" {{ old('source', $ressource->source) == 'Revenus fonciers' ? 'selected' : '' }}>Revenus fonciers</option>
                                <option value="Services municipaux" {{ old('source', $ressource->source) == 'Services municipaux' ? 'selected' : '' }}>Services municipaux</option>
                                <option value="Activités économiques" {{ old('source', $ressource->source) == 'Activités économiques' ? 'selected' : '' }}>Activités économiques</option>
                                <option value="Autres" {{ old('source', $ressource->source) == 'Autres' ? 'selected' : '' }}>Autres</option>
                            </select>
                            @error('source')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="type_ressource" class="form-label">Type de ressource *</label>
                            <input type="text" class="form-control @error('type_ressource') is-invalid @enderror" id="type_ressource" name="type_ressource" 
                                   value="{{ old('type_ressource', $ressource->type_ressource) }}" required>
                            @error('type_ressource')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6 montant-input">
                            <label for="montant" class="form-label">Montant *</label>
                            <input type="number" step="0.01" class="form-control @error('montant') is-invalid @enderror" id="montant" 
                                   name="montant" value="{{ old('montant', $ressource->montant) }}" required>
                            @error('montant')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="date_generation" class="form-label">Date de génération *</label>
                            <input type="date" class="form-control @error('date_generation') is-invalid @enderror" id="date_generation" 
                                   name="date_generation" value="{{ old('date_generation', $ressource->date_generation ? $ressource->date_generation->format('Y-m-d') : '') }}" required>
                            @error('date_generation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description détaillée *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" 
                                  rows="4" required>{{ old('description', $ressource->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Historique des modifications</label>
                        <div class="history-item">
                            <strong>Création:</strong> {{ $ressource->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="history-item">
                            <strong>Dernière modification:</strong> {{ $ressource->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-5">
                        <a href="{{ route('ressources-commune.show', $ressource) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Formatage du montant
            $('#montant').on('blur', function() {
                if ($(this).val()) {
                    $(this).val(parseFloat($(this).val()).toFixed(2));
                }
            });
            
            // Validation avant soumission
            $('#editForm').on('submit', function(e) {
                let isValid = true;
                $('[required]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').remove();
                        $(this).after('<div class="invalid-feedback">Ce champ est obligatoire</div>');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $('.is-invalid').first().offset().top - 100
                    }, 500);
                }
            });
        });
    </script>
</body>
</html>