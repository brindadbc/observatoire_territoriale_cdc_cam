<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Édition de Ressource</title>
    <style>
        :root {
            --primary-color: #6f42c1;
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
            box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.2);
        }
        
        .form-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            background-color: white;
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
            background-color: #5a32a3;
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
        
        .file-upload {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .file-preview {
            margin-top: 10px;
            max-width: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Éditer une Ressource</h1>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <form action="{{ route('autres-ressources.update', $ressource->id) }}" method="POST" enctype="multipart/form-data" id="ressourceForm">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="titre">Titre *</label>
                <input type="text" name="titre" id="titre" 
                       class="form-control @error('titre') is-invalid @enderror" 
                       value="{{ old('titre', $ressource->titre) }}" required>
                @error('titre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="type">Type de ressource *</label>
                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="">Sélectionnez un type</option>
                    <option value="document" {{ old('type', $ressource->type) == 'document' ? 'selected' : '' }}>Document</option>
                    <option value="lien" {{ old('type', $ressource->type) == 'lien' ? 'selected' : '' }}>Lien externe</option>
                    <option value="fichier" {{ old('type', $ressource->type) == 'fichier' ? 'selected' : '' }}>Fichier</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group" id="contenuField">
                @if($ressource->type == 'lien')
                    <label for="contenu">URL *</label>
                    <input type="url" name="contenu" id="contenu" 
                           class="form-control @error('contenu') is-invalid @enderror" 
                           value="{{ old('contenu', $ressource->contenu) }}" required>
                @else
                    <label for="contenu">Contenu</label>
                    <textarea name="contenu" id="contenu" rows="5" 
                              class="form-control @error('contenu') is-invalid @enderror">{{ old('contenu', $ressource->contenu) }}</textarea>
                @endif
                @error('contenu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group" id="fichierField" style="{{ $ressource->type != 'fichier' ? 'display: none;' : '' }}">
                <label for="fichier">Fichier</label>
                <div class="file-upload">
                    <input type="file" name="fichier" id="fichier" 
                           class="form-control @error('fichier') is-invalid @enderror">
                    @if($ressource->chemin_fichier)
                        <div>Fichier actuel : {{ basename($ressource->chemin_fichier) }}</div>
                        <img src="{{ asset($ressource->chemin_fichier) }}" alt="Prévisualisation" class="file-preview">
                    @endif
                </div>
                @error('fichier')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="3" 
                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $ressource->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="est_public">Visibilité</label>
                <select name="est_public" id="est_public" class="form-select @error('est_public') is-invalid @enderror">
                    <option value="1" {{ old('est_public', $ressource->est_public) ? 'selected' : '' }}>Public</option>
                    <option value="0" {{ !old('est_public', $ressource->est_public) ? 'selected' : '' }}>Privé</option>
                </select>
                @error('est_public')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="{{ route('autres-ressources.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion dynamique du type de ressource
            const typeSelect = document.getElementById('type');
            const contenuField = document.getElementById('contenuField');
            const fichierField = document.getElementById('fichierField');
            
            typeSelect.addEventListener('change', function() {
                const type = this.value;
                const contenuLabel = contenuField.querySelector('label');
                const contenuInput = contenuField.querySelector('#contenu');
                
                if (type === 'lien') {
                    contenuLabel.textContent = 'URL *';
                    if (contenuInput.tagName === 'TEXTAREA') {
                        const input = document.createElement('input');
                        input.type = 'url';
                        input.name = 'contenu';
                        input.id = 'contenu';
                        input.className = 'form-control';
                        input.required = true;
                        input.value = contenuInput.value;
                        contenuInput.replaceWith(input);
                    }
                } else {
                    contenuLabel.textContent = type === 'document' ? 'Contenu *' : 'Contenu';
                    if (contenuInput.tagName === 'INPUT') {
                        const textarea = document.createElement('textarea');
                        textarea.name = 'contenu';
                        textarea.id = 'contenu';
                        textarea.className = 'form-control';
                        textarea.rows = type === 'document' ? 5 : 3;
                        textarea.textContent = contenuInput.value;
                        if (type === 'document') textarea.required = true;
                        contenuInput.replaceWith(textarea);
                    }
                }
                
                // Gestion du champ fichier
                fichierField.style.display = type === 'fichier' ? 'block' : 'none';
            });
            
            // Validation du formulaire
            const form = document.getElementById('ressourceForm');
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Validation du titre
                const titre = document.getElementById('titre');
                if (!titre.value.trim()) {
                    titre.classList.add('is-invalid');
                    isValid = false;
                }
                
                // Validation du type
                const type = document.getElementById('type');
                if (!type.value) {
                    type.classList.add('is-invalid');
                    isValid = false;
                }
                
                // Validation du contenu/URL selon le type
                const contenu = document.getElementById('contenu');
                if ((type.value === 'document' && !contenu.value.trim()) || 
                    (type.value === 'lien' && !contenu.value.trim())) {
                    contenu.classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    // Scroll vers le premier champ invalide
                    const firstInvalid = form.querySelector('.is-invalid');
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
            
            // Prévisualisation du fichier
            const fileInput = document.getElementById('fichier');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const preview = document.querySelector('.file-preview');
                        if (preview) {
                            if (file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    preview.src = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            } else {
                                preview.src = '';
                                preview.alt = 'Aperçu non disponible pour ce type de fichier';
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>