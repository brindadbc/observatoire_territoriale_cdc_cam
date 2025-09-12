<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Ressource Communale - Gestion Communale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2ecc71;
            --secondary-color: #2c3e50;
            --dark-color: #34495e;
            --light-color: #ecf0f1;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: var(--dark-color);
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
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
            font-weight: 600;
        }
        
        .form-body {
            padding: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #dfe6e9;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(46, 204, 113, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #27ae60;
            border-color: #27ae60;
            transform: translateY(-2px);
        }
        
        .commune-info {
            background-color: var(--light-color);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .form-body {
                padding: 1.5rem;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
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
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="bi bi-cash-stack me-2"></i>Nouvelle Ressource Communale</h1>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('ressources-commune.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Enregistrer une nouvelle ressource</h5>
            </div>
            
            <div class="form-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('ressources-commune.store') }}" method="POST" id="ressourceForm">
                    @csrf
                    
                    @if(isset($commune))
                        <div class="commune-info mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-building fs-4 me-3 text-primary"></i>
                                <div>
                                    <h6 class="mb-1">Commune: {{ $commune->nom }}</h6>
                                    <p class="mb-0 text-muted">{{ $commune->region }} / {{ $commune->departement }}</p>
                                </div>
                            </div>
                            <input type="hidden" name="commune_id" value="{{ $commune->id }}">
                        </div>
                    @else
                        <div class="mb-4">
                            <label for="commune_id" class="form-label">Commune *</label>
                            <select class="form-select" id="commune_id" name="commune_id" required>
                                <option value="">Sélectionnez une commune</option>
                                @foreach($communes as $commune)
                                    <option value="{{ $commune->id }}" {{ old('commune_id') == $commune->id ? 'selected' : '' }}>
                                        {{ $commune->nom }} ({{ $commune->region }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="source" class="form-label">Source *</label>
                            <select class="form-select" id="source" name="source" required>
                                <option value="">Sélectionnez une source</option>
                                <option value="Impôts" {{ old('source') == 'Impôts' ? 'selected' : '' }}>Impôts</option>
                                <option value="Taxes" {{ old('source') == 'Taxes' ? 'selected' : '' }}>Taxes</option>
                                <option value="Revenus fonciers" {{ old('source') == 'Revenus fonciers' ? 'selected' : '' }}>Revenus fonciers</option>
                                <option value="Services municipaux" {{ old('source') == 'Services municipaux' ? 'selected' : '' }}>Services municipaux</option>
                                <option value="Activités économiques" {{ old('source') == 'Activités économiques' ? 'selected' : '' }}>Activités économiques</option>
                                <option value="Autres" {{ old('source') == 'Autres' ? 'selected' : '' }}>Autres</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="type_ressource" class="form-label">Type de ressource *</label>
                            <input type="text" class="form-control" id="type_ressource" name="type_ressource" 
                                   value="{{ old('type_ressource') }}" required placeholder="Ex: Taxe de marché">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6 montant-input">
                            <label for="montant" class="form-label">Montant *</label>
                            <input type="number" step="0.01" class="form-control" id="montant" 
                                   name="montant" value="{{ old('montant') }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="date_generation" class="form-label">Date de génération *</label>
                            <input type="date" class="form-control" id="date_generation" 
                                   name="date_generation" value="{{ old('date_generation', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description détaillée *</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="4" required>{{ old('description') }}</textarea>
                        <small class="text-muted">Décrivez la nature de cette ressource et son utilisation prévue</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-5">
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="bi bi-eraser"></i> Réinitialiser
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Enregistrer la ressource
                        </button>
                    </div>
                </form>
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
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Validation en temps réel
            $('input, select, textarea').on('blur', function() {
                if ($(this).prop('required') && !$(this).val()) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            // Auto-formatage du montant
            $('#montant').on('blur', function() {
                if ($(this).val()) {
                    $(this).val(parseFloat($(this).val()).toFixed(2));
                }
            });
            
            // Suggestion de type en fonction de la source
            $('#source').change(function() {
                const source = $(this).val();
                let typeSuggestion = '';
                
                switch(source) {
                    case 'Impôts':
                        typeSuggestion = 'Impôt local';
                        break;
                    case 'Taxes':
                        typeSuggestion = 'Taxe municipale';
                        break;
                    case 'Revenus fonciers':
                        typeSuggestion = 'Loyer des biens communaux';
                        break;
                    case 'Services municipaux':
                        typeSuggestion = 'Prestation de service';
                        break;
                    case 'Activités économiques':
                        typeSuggestion = 'Droit de place marché';
                        break;
                    default:
                        typeSuggestion = '';
                }
                
                if (typeSuggestion && !$('#type_ressource').val()) {
                    $('#type_ressource').val(typeSuggestion);
                }
            });
            
            // Empêcher l'envoi du formulaire si invalide
            $('#ressourceForm').on('submit', function(e) {
                let isValid = true;
                $('[required]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                        isValid = false;
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