<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Édition de Don Extérieur</title>
    <style>
        :root {
            --primary-color: #3490dc;
            --secondary-color: #6c757d;
            --success-color: #38c172;
            --danger-color: #e3342f;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: var(--light-bg);
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: var(--primary-color);
            margin-bottom: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 144, 220, 0.2);
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2779bd;
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .invalid-feedback {
            color: var(--danger-color);
            font-size: 14px;
            margin-top: 5px;
        }
        
        .is-invalid {
            border-color: var(--danger-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Éditer un Don Extérieur</h1>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <form action="{{ route('dons-exterieurs.update', $donExterieur->id) }}" method="POST" id="donForm">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="donateur">Donateur</label>
                <input type="text" name="donateur" id="donateur" 
                       class="form-control @error('donateur') is-invalid @enderror" 
                       value="{{ old('donateur', $donExterieur->donateur) }}">
                @error('donateur')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="montant">Montant (€)</label>
                <input type="number" step="0.01" name="montant" id="montant" 
                       class="form-control @error('montant') is-invalid @enderror" 
                       value="{{ old('montant', $donExterieur->montant) }}">
                @error('montant')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="date_reception">Date de réception</label>
                <input type="date" name="date_reception" id="date_reception" 
                       class="form-control @error('date_reception') is-invalid @enderror" 
                       value="{{ old('date_reception', $donExterieur->date_reception->format('Y-m-d')) }}">
                @error('date_reception')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="4" 
                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $donExterieur->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="statut">Statut</label>
                <select name="statut" id="statut" class="form-control @error('statut') is-invalid @enderror">
                    <option value="en_attente" {{ old('statut', $donExterieur->statut) == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="confirme" {{ old('statut', $donExterieur->statut) == 'confirme' ? 'selected' : '' }}>Confirmé</option>
                    <option value="rejete" {{ old('statut', $donExterieur->statut) == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                </select>
                @error('statut')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="{{ route('dons-exterieurs.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validation côté client
            const form = document.getElementById('donForm');
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Validation du donateur
                const donateur = document.getElementById('donateur');
                if (!donateur.value.trim()) {
                    donateur.classList.add('is-invalid');
                    isValid = false;
                }
                
                // Validation du montant
                const montant = document.getElementById('montant');
                if (!montant.value || parseFloat(montant.value) <= 0) {
                    montant.classList.add('is-invalid');
                    isValid = false;
                }
                
                // Validation de la date
                const dateReception = document.getElementById('date_reception');
                if (!dateReception.value) {
                    dateReception.classList.add('is-invalid');
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
            const inputs = form.querySelectorAll('input, textarea, select');
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