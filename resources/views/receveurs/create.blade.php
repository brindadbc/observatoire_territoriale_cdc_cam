@extends('layouts.app')

@section('title', 'Nouveau Receveur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Nouveau Receveur</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('receveurs.index') }}">Receveurs</a></li>
                        <li class="breadcrumb-item active">Nouveau</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Messages d'erreur --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations du Receveur</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('receveurs.store') }}" method="POST" id="receveurForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" 
                                           name="nom" 
                                           value="{{ old('nom') }}" 
                                           placeholder="Nom et prénom du receveur"
                                           required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="matricule" class="form-label">Matricule <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('matricule') is-invalid @enderror" 
                                           id="matricule" 
                                           name="matricule" 
                                           value="{{ old('matricule') }}" 
                                           placeholder="Matricule unique"
                                           required>
                                    @error('matricule')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                    <select class="form-select @error('statut') is-invalid @enderror" 
                                            id="statut" 
                                            name="statut" 
                                            required>
                                        <option value="">Sélectionner un statut</option>
                                        @foreach($statuts as $statut)
                                            <option value="{{ $statut }}" {{ old('statut') == $statut ? 'selected' : '' }}>
                                                {{ $statut }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_prise_fonction" class="form-label">Date de prise de fonction <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('date_prise_fonction') is-invalid @enderror" 
                                           id="date_prise_fonction" 
                                           name="date_prise_fonction" 
                                           value="{{ old('date_prise_fonction') }}" 
                                           required>
                                    @error('date_prise_fonction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" 
                                           class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" 
                                           name="telephone" 
                                           value="{{ old('telephone') }}" 
                                           placeholder="Ex: +237 6XX XXX XXX">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="commune_id" class="form-label">Commune d'assignation</label>
                                    <select class="form-select @error('commune_id') is-invalid @enderror" 
                                            id="commune_id" 
                                            name="commune_id">
                                        <option value="">Sélectionner une commune (optionnel)</option>
                                        @foreach($communes as $commune)
                                            <option value="{{ $commune->id }}" {{ old('commune_id') == $commune->id ? 'selected' : '' }}>
                                                {{ $commune->nom }} - {{ $commune->departement->nom }} ({{ $commune->departement->region->nom }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('commune_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Seules les communes sans receveur actif sont disponibles.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('receveurs.index') }}" class="btn btn-secondary me-2">
                                <i class="ri-arrow-left-line me-1"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aide</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Instructions</h6>
                        <ul class="mb-0">
                            <li>Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.</li>
                            <li>Le matricule doit être unique dans le système.</li>
                            <li>La commune d'assignation est optionnelle lors de la création.</li>
                            <li>Un receveur actif ne peut être assigné qu'à une seule commune.</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6 class="alert-heading">Statuts disponibles</h6>
                        <ul class="mb-0">
                            <li><strong>Actif :</strong> Receveur en activité</li>
                            <li><strong>Inactif :</strong> Receveur temporairement inactif</li>
                            <li><strong>En congé :</strong> Receveur en congé</li>
                            <li><strong>Retraité :</strong> Receveur à la retraite</li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h6 class="alert-heading">Bonnes pratiques</h6>
                        <ul class="mb-0">
                            <li>Vérifiez l'unicité du matricule avant validation</li>
                            <li>Utilisez un format de téléphone standard</li>
                            <li>La date de prise de fonction doit être cohérente</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation côté client
    const form = document.getElementById('receveurForm');
    const matriculeInput = document.getElementById('matricule');
    
    // Transformation automatique du matricule en majuscules
    matriculeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Validation du formulaire
    form.addEventListener('submit', function(e) {
        const nom = document.getElementById('nom').value.trim();
        const matricule = document.getElementById('matricule').value.trim();
        const statut = document.getElementById('statut').value;
        const dateFonction = document.getElementById('date_prise_fonction').value;
        
        if (!nom || !matricule || !statut || !dateFonction) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }

        // Validation de la date (ne doit pas être future)
        const today = new Date();
        const selectedDate = new Date(dateFonction);
        
        if (selectedDate > today) {
            e.preventDefault();
            alert('La date de prise de fonction ne peut pas être dans le futur.');
            return false;
        }
    });

    // Aide contextuelle pour les communes
    const communeSelect = document.getElementById('commune_id');
    communeSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            console.log('Commune sélectionnée:', selectedOption.text);
        }
    });
});
</script>
@endpush