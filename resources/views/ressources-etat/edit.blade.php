<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Ressource de l'État</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: #333;
        }
        
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .form-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
        }
        
        .form-body {
            padding: 2rem;
        }
        
        .form-title {
            margin: 0;
            font-weight: 600;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--secondary-color);
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
        }
        
        .btn-outline-secondary {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        
        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.875rem;
        }
        
        .is-invalid {
            border-color: var(--danger-color) !important;
        }
        
        .datepicker-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .form-container {
                margin: 1rem;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h2 class="form-title">
                <i class="bi bi-pencil-square me-2"></i>Modifier Ressource de l'État
            </h2>
        </div>
        
        <div class="form-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <form action="{{ route('ressources-etat.update', $ressource->id) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="commune_id" class="form-label">Commune *</label>
                        <select name="commune_id" id="commune_id" class="form-select @error('commune_id') is-invalid @enderror" required>
                            <option value="">Sélectionner une commune</option>
                            @foreach($communes as $commune)
                                <option value="{{ $commune->id }}" {{ old('commune_id', $ressource->commune_id) == $commune->id ? 'selected' : '' }}>
                                    {{ $commune->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('commune_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="type_ressource" class="form-label">Type de ressource *</label>
                        <select name="type_ressource" id="type_ressource" class="form-select @error('type_ressource') is-invalid @enderror" required>
                            <option value="">Sélectionner un type</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type_ressource', $ressource->type_ressource) == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_ressource')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $ressource->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label for="montant" class="form-label">Montant (FCFA) *</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="montant" id="montant" 
                                   class="form-control @error('montant') is-invalid @enderror" 
                                   value="{{ old('montant', $ressource->montant) }}" required>
                            <span class="input-group-text">FCFA</span>
                        </div>
                        @error('montant')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="date_reception" class="form-label">Date de réception *</label>
                        <div class="position-relative">
                            <input type="date" name="date_reception" id="date_reception" 
                                   class="form-control @error('date_reception') is-invalid @enderror" 
                                   value="{{ old('date_reception', $ressource->date_reception->format('Y-m-d')) }}" required>
                            <i class="bi bi-calendar-date datepicker-icon"></i>
                        </div>
                        @error('date_reception')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="reference" class="form-label">Référence *</label>
                        <input type="text" name="reference" id="reference" 
                               class="form-control @error('reference') is-invalid @enderror" 
                               value="{{ old('reference', $ressource->reference) }}" required>
                        @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="projet_associe" class="form-label">Projet associé</label>
                    <input type="text" name="projet_associe" id="projet_associe" 
                           class="form-control @error('projet_associe') is-invalid @enderror" 
                           value="{{ old('projet_associe', $ressource->projet_associe) }}">
                    @error('projet_associe')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('ressources-etat.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Retour
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validation côté client
            const form = document.getElementById('editForm');
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const requiredFields = [
                    'commune_id', 'type_ressource', 'description',
                    'montant', 'date_reception', 'reference'
                ];
                
                requiredFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    }
                });
                
                // Validation spécifique pour le montant
                const montant = document.getElementById('montant');
                if (montant.value && parseFloat(montant.value) <= 0) {
                    montant.classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    // Scroll vers le premier champ invalide
                    const firstInvalid = form.querySelector('.is-invalid');
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
            
            // Suppression des messages d'erreur lors de la saisie
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });
            
            // Confirmation avant abandon des modifications
            const originalFormData = new FormData(form);
            let formChanged = false;
            
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    const currentFormData = new FormData(form);
                    formChanged = !arraysEqual([...originalFormData], [...currentFormData]);
                });
            });
            
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = 'Vous avez des modifications non enregistrées. Voulez-vous vraiment quitter ?';
                }
            });
            
            function arraysEqual(arr1, arr2) {
                if (arr1.length !== arr2.length) return false;
                for (let i = 0; i < arr1.length; i++) {
                    if (arr1[i][0] !== arr2[i][0] || arr1[i][1] !== arr2[i][1]) {
                        return false;
                    }
                }
                return true;
            }
        });
    </script>
</body>
</html>