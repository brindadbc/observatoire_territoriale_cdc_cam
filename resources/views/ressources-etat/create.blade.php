<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($ressource) ? 'Modifier' : 'Créer' }} Ressource - Gestion Communale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
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
        
        .form-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .form-card .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #ced4da;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .is-invalid {
            border-color: var(--danger-color);
        }
        
        .is-invalid:focus {
            box-shadow: 0 0 0 0.25rem rgba(231, 76, 60, 0.25);
        }
        
        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        @media (max-width: 768px) {
            .form-control, .form-select {
                padding: 0.5rem 0.75rem;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 1rem;
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
            color: #6c757d;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="bi bi-bank me-2"></i>{{ isset($ressource) ? 'Modifier' : 'Créer une' }} Ressource de l'État</h1>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ isset($ressource) ? route('ressources-etat.show', $ressource) : route('ressources-etat.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left"></i> Annuler
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card card">
                    <div class="card-header">
                        <h5 class="mb-0">Informations de la Ressource</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($ressource) ? route('ressources-etat.update', $ressource) : route('ressources-etat.store') }}" method="POST">
                            @csrf
                            @if(isset($ressource))
                                @method('PUT')
                            @endif

                            @if(!isset($commune))
                            <div class="mb-4">
                                <label for="commune_id" class="form-label">Commune Bénéficiaire</label>
                                <select class="form-select @error('commune_id') is-invalid @enderror" id="commune_id" name="commune_id" required>
                                    <option value="">Sélectionnez une commune</option>
                                    @foreach($communes as $c)
                                        <option value="{{ $c->id }}" 
                                            {{ old('commune_id', $ressource->commune_id ?? '') == $c->id ? 'selected' : '' }}>
                                            {{ $c->nom }} ({{ $c->region }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('commune_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @else
                                <input type="hidden" name="commune_id" value="{{ $commune->id }}">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Cette ressource sera attribuée à la commune: <strong>{{ $commune->nom }}</strong> ({{ $commune->region }})
                                </div>
                            @endif

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="type_ressource" class="form-label">Type de Ressource</label>
                                    <select class="form-select @error('type_ressource') is-invalid @enderror" id="type_ressource" name="type_ressource" required>
                                        <option value="">Sélectionnez un type</option>
                                        <option value="Subvention" {{ old('type_ressource', $ressource->type_ressource ?? '') == 'Subvention' ? 'selected' : '' }}>Subvention</option>
                                        <option value="Dotation" {{ old('type_ressource', $ressource->type_ressource ?? '') == 'Dotation' ? 'selected' : '' }}>Dotation</option>
                                        <option value="Fonds Spécial" {{ old('type_ressource', $ressource->type_ressource ?? '') == 'Fonds Spécial' ? 'selected' : '' }}>Fonds Spécial</option>
                                        <option value="Projet Gouvernemental" {{ old('type_ressource', $ressource->type_ressource ?? '') == 'Projet Gouvernemental' ? 'selected' : '' }}>Projet Gouvernemental</option>
                                        <option value="Autre" {{ old('type_ressource', $ressource->type_ressource ?? '') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    @error('type_ressource')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="reference" class="form-label">Référence</label>
                                    <input type="text" class="form-control @error('reference') is-invalid @enderror" id="reference" 
                                           name="reference" value="{{ old('reference', $ressource->reference ?? '') }}" required>
                                    @error('reference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6 montant-input">
                                    <label for="montant" class="form-label">Montant</label>
                                    <input type="number" step="0.01" class="form-control @error('montant') is-invalid @enderror" id="montant" 
                                           name="montant" value="{{ old('montant', $ressource->montant ?? '') }}" required>
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="date_reception" class="form-label">Date de Réception</label>
                                    <input type="date" class="form-control @error('date_reception') is-invalid @enderror" id="date_reception" 
                                           name="date_reception" value="{{ old('date_reception', isset($ressource) ? $ressource->date_reception->format('Y-m-d') : '') }}" required>
                                    @error('date_reception')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="projet_associe" class="form-label">Projet Associé (si applicable)</label>
                                <input type="text" class="form-control @error('projet_associe') is-invalid @enderror" id="projet_associe" 
                                       name="projet_associe" value="{{ old('projet_associe', $ressource->projet_associe ?? '') }}">
                                @error('projet_associe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" 
                                          name="description" rows="4" required>{{ old('description', $ressource->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="reset" class="btn btn-outline-secondary me-md-2">
                                    <i class="bi bi-eraser"></i> Réinitialiser
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> {{ isset($ressource) ? 'Mettre à jour' : 'Enregistrer' }}
                                </button>
                            </div>
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
                    <p class="mb-0">{{ isset($ressource) ? 'Modification' : 'Création' }} le: {{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Formatage automatique de la référence
            $('#reference').on('input', function() {
                $(this).val($(this).val().toUpperCase().replace(/\s+/g, '-'));
            });
            
            // Formatage du montant
            $('#montant').on('blur', function() {
                if ($(this).val()) {
                    $(this).val(parseFloat($(this).val()).toFixed(2));
                }
            });
            
            // Date par défaut = aujourd'hui si création
            @if(!isset($ressource))
                if (!$('#date_reception').val()) {
                    $('#date_reception').val(new Date().toISOString().split('T')[0]);
                }
            @endif
            
            // Validation en temps réel
            $('form').on('blur', 'input, select, textarea', function() {
                if ($(this).val() === '' && $(this).prop('required')) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        });
    </script>
</body>
</html>