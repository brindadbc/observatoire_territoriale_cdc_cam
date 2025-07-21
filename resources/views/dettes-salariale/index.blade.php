@extends('layouts.app')

@section('title', 'Dettes Salariales')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête avec titre et bouton d'ajout -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Gestion des Dettes Salariales</h1>
                    <p class="text-muted">Liste et gestion des dettes salariales des communes</p>
                </div>
                <div>
                    <a href="{{ route('dettes-salariale.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle Dette
                    </a>
                    <a href="{{ route('dettes-salariale.dashboard') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Tableau de Bord
                    </a>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ number_format($stats['total_dettes'], 0, ',', ' ') }} FCFA</h4>
                                    <p class="card-text">Total des Dettes</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $stats['nb_communes_concernees'] }}</h4>
                                    <p class="card-text">Communes Concernées</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-city fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ number_format($stats['dette_moyenne'], 0, ',', ' ') }} FCFA</h4>
                                    <p class="card-text">Dette Moyenne</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calculator fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ number_format($stats['dette_max'], 0, ',', ' ') }} FCFA</h4>
                                    <p class="card-text">Dette Maximale</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter"></i> Filtres de Recherche
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dettes-salariale.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Recherche</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Nom ou code commune...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="region_id">Région</label>
                                    <select class="form-control" id="region_id" name="region_id">
                                        <option value="">Toutes les régions</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                                {{ $region->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="departement_id">Département</label>
                                    <select class="form-control" id="departement_id" name="departement_id">
                                        <option value="">Tous les départements</option>
                                        @foreach($departements as $departement)
                                            <option value="{{ $departement->id }}" {{ request('departement_id') == $departement->id ? 'selected' : '' }}>
                                                {{ $departement->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="commune_id">Commune</label>
                                    <select class="form-control" id="commune_id" name="commune_id">
                                        <option value="">Toutes les communes</option>
                                        @foreach($communes as $commune)
                                            <option value="{{ $commune->id }}" {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                                                {{ $commune->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_debut">Date Début</label>
                                    <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                           value="{{ request('date_debut') }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary ">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filtres supplémentaires (repliables) -->
                        <div class="collapse" id="filtresAvances">
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="date_fin">Date Fin</label>
                                        <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                               value="{{ request('date_fin') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="montant_min">Montant Min</label>
                                        <input type="number" class="form-control" id="montant_min" name="montant_min" 
                                               value="{{ request('montant_min') }}" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="montant_max">Montant Max</label>
                                        <input type="number" class="form-control" id="montant_max" name="montant_max" 
                                               value="{{ request('montant_max') }}" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="sort_by">Trier par</label>
                                        <select class="form-control" id="sort_by" name="sort_by">
                                            <option value="date_evaluation" {{ request('sort_by') == 'date_evaluation' ? 'selected' : '' }}>Date</option>
                                            <option value="montant" {{ request('sort_by') == 'montant' ? 'selected' : '' }}>Montant</option>
                                            <option value="commune_id" {{ request('sort_by') == 'commune_id' ? 'selected' : '' }}>Commune</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="sort_direction">Ordre</label>
                                        <select class="form-control" id="sort_direction" name="sort_direction">
                                            <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>Décroissant</option>
                                            <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Croissant</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <a href="{{ route('dettes-salariale.index') }}" class="btn btn-secondary form-control">
                                            <i class="fas fa-times"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-2">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#filtresAvances">
                                <i class="fas fa-cog"></i> Filtres Avancés
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Actions groupées et export -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">{{ $dettes->total() }} résultat(s) trouvé(s)</span>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-download"></i> Exporter
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('dettes-salariale.export', array_merge(request()->all(), ['format' => 'excel'])) }}">
                                    <i class="fas fa-file-excel"></i> Excel
                                </a>
                                <a class="dropdown-item" href="{{ route('dettes-salariale.export', array_merge(request()->all(), ['format' => 'pdf'])) }}">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                                <a class="dropdown-item" href="{{ route('dettes-salariale.export', array_merge(request()->all(), ['format' => 'csv'])) }}">
                                    <i class="fas fa-file-csv"></i> CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des dettes -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Commune</th>
                                    <th>Département</th>
                                    <th>Région</th>
                                    <th>Montant (FCFA)</th>
                                    <th>Date Évaluation</th>
                                    <th>Niveau</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dettes as $dette)
                                <tr>
                                    <td>{{ $dette->id }}</td>
                                    <td>
                                        <strong>{{ $dette->commune->nom }}</strong>
                                        <br><small class="text-muted">{{ $dette->commune->code }}</small>
                                    </td>
                                    <td>{{ $dette->commune->departement->nom }}</td>
                                    <td>{{ $dette->commune->departement->region->nom }}</td>
                                    <td>
                                        <span class="font-weight-bold">{{ number_format($dette->montant, 0, ',', ' ') }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($dette->date_evaluation)->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $niveau = 'success';
                                            $texte = 'Faible';
                                            if($dette->montant >= 50000000) { $niveau = 'danger'; $texte = 'Critique'; }
                                            elseif($dette->montant >= 10000000) { $niveau = 'warning'; $texte = 'Élevé'; }
                                            elseif($dette->montant >= 1000000) { $niveau = 'info'; $texte = 'Moyen'; }
                                        @endphp
                                        <span class="badge badge-{{ $niveau }}">{{ $texte }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('dettes-salariale.show', $dette) }}" 
                                               class="btn btn-outline-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('dettes-salariale.edit', $dette) }}" 
                                               class="btn btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            {{-- <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmerSuppression({{ $dette->id }})" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button> --}}
                                            <form action="{{ route('dettes-salariale.destroy', $dette) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Supprimer"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette dette ? Cette action est irréversible')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Aucune dette salariale trouvée</p>
                                        <a href="{{ route('dettes-salariale.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Ajouter une dette
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if($dettes->hasPages())
                <div class="card-footer">
                    {{ $dettes->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- <!-- Modal de confirmation de suppression -->
<div class="modal fade" id="confirmationSuppressionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette dette salariale ?</p>
                <p class="text-danger"><strong>Cette action est irréversible.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form id="formSuppression" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}

@endsection

@section('scripts')
<script>
function confirmerSuppression(id) {
    const form = document.getElementById('formSuppression');
    form.action = '{{ url("dettes/salariale") }}/' + id;
    $('#confirmationSuppressionModal').modal('show');
}

// Filtrage en cascade des sélecteurs
document.getElementById('region_id').addEventListener('change', function() {
    const regionId = this.value;
    const departementSelect = document.getElementById('departement_id');
    const communeSelect = document.getElementById('commune_id');
    
    // Reset des sélecteurs dépendants
    departementSelect.innerHTML = '<option value="">Tous les départements</option>';
    communeSelect.innerHTML = '<option value="">Toutes les communes</option>';
    
    if (regionId) {
        // Filtrer les départements par région
        @foreach($departements as $departement)
        if ({{ $departement->region_id }} == regionId) {
            departementSelect.innerHTML += '<option value="{{ $departement->id }}">{{ $departement->nom }}</option>';
        }
        @endforeach
    } else {
        // Afficher tous les départements
        @foreach($departements as $departement)
        departementSelect.innerHTML += '<option value="{{ $departement->id }}">{{ $departement->nom }}</option>';
        @endforeach
    }
});

document.getElementById('departement_id').addEventListener('change', function() {
    const departementId = this.value;
    const communeSelect = document.getElementById('commune_id');
    
    // Reset du sélecteur communes
    communeSelect.innerHTML = '<option value="">Toutes les communes</option>';
    
    if (departementId) {
        // Filtrer les communes par département
        @foreach($communes as $commune)
        if ({{ $commune->departement_id }} == departementId) {
            communeSelect.innerHTML += '<option value="{{ $commune->id }}">{{ $commune->nom }}</option>';
        }
        @endforeach
    } else {
        // Afficher toutes les communes de la région sélectionnée ou toutes
        const regionId = document.getElementById('region_id').value;
        @foreach($communes as $commune)
        if (!regionId || {{ $commune->departement->region_id }} == regionId) {
            communeSelect.innerHTML += '<option value="{{ $commune->id }}">{{ $commune->nom }}</option>';
        }
        @endforeach
    }
});
</script>
@endsection