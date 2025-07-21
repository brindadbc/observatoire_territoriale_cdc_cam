@extends('layouts.app')

@section('title', 'Importer des prévisions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-import"></i>
                        Importer des prévisions
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('previsions.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Instructions -->
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Instructions d'importation</h5>
                        <ul class="mb-0">
                            <li>Le fichier doit être au format Excel (.xlsx) ou CSV (.csv)</li>
                            <li>Les colonnes requises sont : <strong>commune_id, annee_exercice, montant</strong></li>
                            <li>Les communes doivent exister dans la base de données</li>
                            <li>Les années doivent être comprises entre 2000 et {{ date('Y') + 10 }}</li>
                            <li>Les montants doivent être des nombres positifs</li>
                        </ul>
                    </div>

                    <!-- Télécharger le modèle -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Télécharger le modèle</h3>
                                </div>
                                <div class="card-body">
                                    <p>Téléchargez le modèle Excel pour vous assurer du bon format.</p>
                                    <a href="{{ route('previsions.export.excel', ['template' => 1]) }}" class="btn btn-primary">
                                        <i class="fas fa-download"></i> Télécharger le modèle Excel
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Format attendu</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>commune_id</th>
                                                    <th>annee_exercice</th>
                                                    <th>montant</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>2024</td>
                                                    <td>50000</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>2024</td>
                                                    <td>75000</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire d'importation -->
                    <form method="POST" action="{{ route('previsions.import.process') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="fichier">Fichier à importer <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" 
                                                   class="custom-file-input @error('fichier') is-invalid @enderror" 
                                                   id="fichier" 
                                                   name="fichier" 
                                                   accept=".xlsx,.xls,.csv"
                                                   required>
                                            <label class="custom-file-label" for="fichier">Choisir un fichier</label>
                                        </div>
                                    </div>
                                    @error('fichier')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Formats acceptés : .xlsx, .xls, .csv (max 10MB)
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="separateur">Séparateur CSV</label>
                                    <select name="separateur" id="separateur" class="form-control">
                                        <option value=";" selected>Point-virgule (;)</option>
                                        <option value=",">Virgule (,)</option>
                                        <option value="|">Pipe (|)</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Seulement pour les fichiers CSV
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="encodage">Encodage du fichier</label>
                                    <select name="encodage" id="encodage" class="form-control">
                                        <option value="UTF-8" selected>UTF-8</option>
                                        <option value="ISO-8859-1">ISO-8859-1</option>
                                        <option value="Windows-1252">Windows-1252</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ligne_entete">Ligne d'en-tête</label>
                                    <select name="ligne_entete" id="ligne_entete" class="form-control">
                                        <option value="1" selected>Ligne 1</option>
                                        <option value="2">Ligne 2</option>
                                        <option value="0">Pas d'en-tête</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="ignorer_doublons" name="ignorer_doublons" value="1">
                                <label class="custom-control-label" for="ignorer_doublons">
                                    Ignorer les doublons (même commune + même année)
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="mode_test" name="mode_test" value="1">
                                <label class="custom-control-label" for="mode_test">
                                    Mode test (vérifier sans importer)
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-file-import"></i> Importer les prévisions
                            </button>
                            <a href="{{ route('previsions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Aide sur les communes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Liste des communes disponibles</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped" id="communesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Code</th>
                                    <th>Département</th>
                                    <th>Région</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($communes ?? [] as $commune)
                                    <tr>
                                        <td>{{ $commune->id }}</td>
                                        <td>{{ $commune->nom }}</td>
                                        <td>{{ $commune->code }}</td>
                                        <td>{{ $commune->departement->nom }}</td>
                                        <td>{{ $commune->departement->region->nom }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Afficher le nom du fichier sélectionné
document.getElementById('fichier').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'Choisir un fichier';
    document.querySelector('.custom-file-label').textContent = fileName;
});

// Afficher/masquer les options CSV selon le type de fichier
document.getElementById('fichier').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || '';
    const isCsv = fileName.toLowerCase().endsWith('.csv');
    
    document.getElementById('separateur').closest('.form-group').style.display = isCsv ? 'block' : 'none';
});

// Recherche dans le tableau des communes
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control mb-3';
    searchInput.placeholder = 'Rechercher une commune...';
    
    const table = document.getElementById('communesTable');
    if (table) {
        table.parentNode.insertBefore(searchInput, table);
        
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        });
    }
});
</script>
@endsection