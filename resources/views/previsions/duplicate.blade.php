@extends('layouts.app')

@section('title', 'Dupliquer une prévision')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-copy"></i>
                        Dupliquer la prévision
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('previsions.show', $prevision) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informations de la prévision originale -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-calendar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Prévision originale</span>
                                    <span class="info-box-number">{{ $prevision->annee_exercice }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-euro-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Montant</span>
                                    <span class="info-box-number">{{ number_format($prevision->montant, 0, ',', ' ') }} €</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Commune concernée</h5>
                                <strong>{{ $prevision->commune->nom }}</strong>
                                ({{ $prevision->commune->departement->nom }} - {{ $prevision->commune->departement->region->nom }})
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de duplication -->
                    <form method="POST" action="{{ route('previsions.duplicate', $prevision) }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nouvelle_annee">Nouvelle année d'exercice <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('nouvelle_annee') is-invalid @enderror" 
                                           id="nouvelle_annee" 
                                           name="nouvelle_annee" 
                                           value="{{ old('nouvelle_annee', $prevision->annee_exercice + 1) }}"
                                           min="2000" 
                                           max="{{ date('Y') + 10 }}"
                                           required>
                                    @error('nouvelle_annee')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Année pour laquelle créer la nouvelle prévision
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ajustement_pourcentage">Ajustement du montant (%)</label>
                                    <input type="number" 
                                           class="form-control @error('ajustement_pourcentage') is-invalid @enderror" 
                                           id="ajustement_pourcentage" 
                                           name="ajustement_pourcentage" 
                                           value="{{ old('ajustement_pourcentage', 0) }}"
                                           min="-100" 
                                           max="1000"
                                           step="0.1"
                                           onchange="calculerNouveauMontant()">
                                    @error('ajustement_pourcentage')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Pourcentage d'ajustement du montant (positif = augmentation, négatif = diminution)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Montant original</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ number_format($prevision->montant, 2, ',', ' ') }} €" 
                                           readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nouveau montant estimé</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nouveau_montant_affichage"
                                           value="{{ number_format($prevision->montant, 2, ',', ' ') }} €" 
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirmer_duplication" required>
                                <label class="custom-control-label" for="confirmer_duplication">
                                    Je confirme vouloir dupliquer cette prévision
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-copy"></i> Dupliquer la prévision
                            </button>
                            <a href="{{ route('previsions.show', $prevision) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculerNouveauMontant() {
    const montantOriginal = {{ $prevision->montant }};
    const ajustement = document.getElementById('ajustement_pourcentage').value;
    const nouveauMontant = montantOriginal * (1 + (ajustement / 100));
    
    document.getElementById('nouveau_montant_affichage').value = 
        nouveauMontant.toLocaleString('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' €';
}
</script>
@endsection