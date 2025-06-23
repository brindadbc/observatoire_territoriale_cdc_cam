@extends('layouts.app')

@section('title', 'Modifier le receveur')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Modifier le receveur</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('receveurs.index') }}">Receveurs</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('receveurs.show', $receveur) }}">{{ $receveur->nom }}</a></li>
                            <li class="breadcrumb-item active">Modifier</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <a href="{{ route('receveurs.show', $receveur) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages d'erreur -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6><i class="fas fa-exclamation-triangle"></i> Erreurs de validation :</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulaire de modification -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit"></i> Informations du receveur
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('receveurs.update', $receveur) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Nom -->
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">
                                    Nom complet <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nom') is-invalid @enderror" 
                                       id="nom" 
                                       name="nom" 
                                       value="{{ old('nom', $receveur->nom) }}" 
                                       required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Matricule -->
                            <div class="col-md-6 mb-3">
                                <label for="matricule" class="form-label">
                                    Matricule <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('matricule') is-invalid @enderror" 
                                       id="matricule" 
                                       name="matricule" 
                                       value="{{ old('matricule', $receveur->matricule) }}" 
                                       required>
                                @error('matricule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Statut -->
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">
                                    Statut <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('statut') is-invalid @enderror" 
                                        id="statut" 
                                        name="statut" 
                                        required>
                                    <option value="">Sélectionnez un statut</option>
                                    @foreach($statuts as $statut)
                                        <option value="{{ $statut }}" 
                                                {{ old('statut', $receveur->statut) == $statut ? 'selected' : '' }}>
                                            {{ $statut }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date de prise de fonction -->
                            <div class="col-md-6 mb-3">
                                <label for="date_prise_fonction" class="form-label">
                                    Date de prise de fonction <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('date_prise_fonction') is-invalid @enderror" 
                                       id="date_prise_fonction" 
                                       name="date_prise_fonction" 
                                       value="{{ old('date_prise_fonction', $receveur->date_prise_fonction) }}" 
                                       required>
                                @error('date_prise_fonction')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Téléphone -->
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" 
                                       class="form-control @error('telephone') is-invalid @enderror" 
                                       id="telephone" 
                                       name="telephone" 
                                       value="{{ old('telephone', $receveur->telephone) }}" 
                                       placeholder="Ex: +237 6XX XXX XXX">
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Commune -->
                            <div class="col-md-6 mb-3">
                                <label for="commune_id" class="form-label">Commune assignée</label>
                                <select class="form-select @error('commune_id') is-invalid @enderror" 
                                        id="commune_id" 
                                        name="commune_id">
                                    <option value="">Aucune assignation</option>
                                    @foreach($communes as $commune)
                                        <option value="{{ $commune->id }}" 
                                                {{ old('commune_id', $receveur->commune_id) == $commune->id ? 'selected' : '' }}>
                                            {{ $commune->nom }} 
                                            ({{ $commune->departement->nom }}, {{ $commune->departement->region->nom }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('commune_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> 
                                    Seules les communes sans receveur actif sont affichées
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                                <a href="{{ route('receveurs.show', $receveur) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            </div>
                            <div>
                                <button type="button" 
                                        class="btn btn-danger" 
                                        onclick="confirmerSuppression()"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalSuppression">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations actuelles -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Informations actuelles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-5"><strong>Nom :</strong></div>
                        <div class="col-7">{{ $receveur->nom }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Matricule :</strong></div>
                        <div class="col-7">
                            <span class="badge bg-primary">{{ $receveur->matricule }}</span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Statut :</strong></div>
                        <div class="col-7">
                            <span class="badge bg-{{ $receveur->statut === 'Actif' ? 'success' : ($receveur->statut === 'Inactif' ? 'danger' : 'warning') }}">
                                {{ $receveur->statut }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Commune :</strong></div>
                        <div class="col-7">
                            @if($receveur->commune)
                                {{ $receveur->commune->nom }}<br>
                                <small class="text-muted">{{ $receveur->commune->departement->nom }}</small>
                            @else
                                <span class="text-muted">Non assigné</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Créé le :</strong></div>
                        <div class="col-7">{{ $receveur->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($receveur->updated_at != $receveur->created_at)
                        <div class="row mb-2">
                            <div class="col-5"><strong>Modifié le :</strong></div>
                            <div class="col-7">{{ $receveur->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie"></i> Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-primary">{{ $receveur->depotsComptes()->count() }}</div>
                            <small class="text-muted">Dépôts total</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success">{{ $receveur->depotsComptes()->where('validation', true)->count() }}</div>
                            <small class="text-muted">Validés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="modalSuppression" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger"></i> 
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le receveur <strong>{{ $receveur->nom }}</strong> ?</p>
                <p class="text-danger"><i class="fas fa-warning"></i> Cette action est irréversible.</p>
                @if($receveur->depotsComptes()->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i>
                        Ce receveur a {{ $receveur->depotsComptes()->count() }} dépôt(s) de compte(s) enregistré(s). 
                        La suppression ne sera pas possible.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                @if($receveur->depotsComptes()->count() == 0)
                    <form method="POST" action="{{ route('receveurs.destroy', $receveur) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Supprimer définitivement
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Confirmation avant suppression
    function confirmerSuppression() {
        return confirm('Êtes-vous sûr de vouloir supprimer ce receveur ?');
    }

    // Auto-formatage du matricule en majuscules
    document.getElementById('matricule').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Validation du téléphone
    document.getElementById('telephone').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, ''); // Supprimer tous les caractères non numériques
        if (value.length > 0) {
            if (value.startsWith('237')) {
                // Format: +237 6XX XXX XXX
                value = '+' + value.substring(0, 3) + ' ' + 
                       value.substring(3, 4) + 
                       value.substring(4, 6) + ' ' +
                       value.substring(6, 9) + ' ' +
                       value.substring(9, 12);
            } else if (value.startsWith('6')) {
                // Format: 6XX XXX XXX
                value = value.substring(0, 1) + 
                       value.substring(1, 3) + ' ' +
                       value.substring(3, 6) + ' ' +
                       value.substring(6, 9);
            }
        }
        this.value = value;
    });

    // Alerte pour changement de commune
    document.getElementById('commune_id').addEventListener('change', function() {
        const currentCommune = "{{ $receveur->commune_id }}";
        const newCommune = this.value;
        
        if (currentCommune && newCommune && currentCommune != newCommune) {
            if (!confirm('Vous allez changer l\'assignation de ce receveur. Êtes-vous sûr ?')) {
                this.value = currentCommune;
            }
        }
    });
</script>
@endpush