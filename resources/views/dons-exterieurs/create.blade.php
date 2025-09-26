<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Don Extérieur - Gestion Communale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #f39c12;
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
        
        .form-control, .form-select, .form-textarea {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #dfe6e9;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(243, 156, 18, 0.25);
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
            background-color: #e67e22;
            border-color: #e67e22;
            transform: translateY(-2px);
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
        
        .aide-type-badge {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .aide-type-badge:hover {
            transform: scale(1.05);
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
    </style>
</head>
<body>
<select class="form-select" id="commune_id" name="commune_id">
    <option value="">Toutes les communes</option>
    @foreach($communes as $commune)
        <option value="{{ $commune->id }}" 
                {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
            {{ $commune->nom }} ({{ $commune->region }})
        </option>
    @endforeach
</select>
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="bi bi-globe me-2"></i>Nouveau Don Extérieur</h1>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('dons-exterieurs.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Enregistrer un nouveau don</h5>
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

                <form action="{{ route('dons-exterieurs.store') }}" method="POST" id="donForm">
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
                            <label for="commune_id" class="form-label">Commune Bénéficiaire *</label>
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

                    <div class="mb-4">
                        <label for="donateur" class="form-label">Donateur *</label>
                        <input type="text" class="form-control" id="donateur" name="donateur" 
                               value="{{ old('donateur') }}" required placeholder="Ex: UNICEF, Ambassade de France...">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Type d'aide *</label>
                        <div class="d-flex flex-wrap gap-2 mb-3" id="aideTypeSelector">
                            <span class="badge bg-primary aide-type-badge" data-value="Financière">Financière</span>
                            <span class="badge bg-success aide-type-badge" data-value="Matérielle">Matérielle</span>
                            <span class="badge bg-info aide-type-badge" data-value="Technique">Technique</span>
                            <span class="badge bg-warning text-dark aide-type-badge" data-value="Alimentaire">Alimentaire</span>
                            <span class="badge bg-secondary aide-type-badge" data-value="Médicale">Médicale</span>
                        </div>
                        <input type="text" class="form-control" id="type_aide" name="type_aide" 
                               value="{{ old('type_aide') }}" required readonly>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6 montant-input">
                            <label for="montant" class="form-label">Montant/Valeur *</label>
                            <input type="number" step="0.01" class="form-control" id="montant" 
                                   name="montant" value="{{ old('montant') }}" required>
                            <small class="text-muted">Pour les dons non financiers, estimer la valeur en FCFA</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="date_reception" class="form-label">Date de réception *</label>
                            <input type="date" class="form-control" id="date_reception" 
                                   name="date_reception" value="{{ old('date_reception', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control form-textarea" id="description" name="description" 
                                  rows="4" required>{{ old('description') }}</textarea>
                        <small class="text-muted">Décrivez la nature du don et son utilisation prévue</small>
                    </div>

                    <div class="mb-4">
                        <label for="projet_associe" class="form-label">Projet associé (si applicable)</label>
                        <input type="text" class="form-control" id="projet_associe" name="projet_associe" 
                               value="{{ old('projet_associe') }}" placeholder="Ex: Construction école primaire">
                    </div>

                    <div class="mb-4">
                        <label for="conditions" class="form-label">Conditions particulières</label>
                        <textarea class="form-control form-textarea" id="conditions" name="conditions" 
                                  rows="2">{{ old('conditions') }}</textarea>
                        <small class="text-muted">Conditions d'utilisation imposées par le donateur</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-5">
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="bi bi-eraser"></i> Réinitialiser
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Enregistrer le don
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
            // Sélection du type d'aide
            $('.aide-type-badge').click(function() {
                $('.aide-type-badge').removeClass('border border-2 border-dark');
                $(this).addClass('border border-2 border-dark');
                $('#type_aide').val($(this).data('value'));
            });
            
            // Formatage du montant
            $('#montant').on('blur', function() {
                if ($(this).val()) {
                    $(this).val(parseFloat($(this).val()).toFixed(2));
                }
            });
            
            // Validation avant soumission
            $('#donForm').on('submit', function(e) {
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