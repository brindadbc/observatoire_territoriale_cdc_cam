@extends('layouts.app')

@section('title', 'Modifier la réalisation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Modifier la réalisation</h1>
                <div class="page-actions">
                    <a href="{{ route('realisations.show', $realisation) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> Voir la réalisation
                    </a>
                    <a href="{{ route('realisations.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informations de la réalisation</h3>
                    <div class="card-actions">
                        <small class="text-muted">
                            Créée le {{ $realisation->created_at->format('d/m/Y à H:i') }}
                        </small>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('realisations.update', $realisation) }}" method="POST" id="realisationForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="annee_exercice" class="form-label required">Année d'exercice</label>
                                    <select class="form-select @error('annee_exercice') is-invalid @enderror" 
                                            id="annee_exercice" name="annee_exercice" required>
                                        <option value="">Sélectionnez une année</option>
                                        @for($i = 2020; $i <= date('Y') + 1; $i++)
                                            <option value="{{ $i }}" {{ old('annee_exercice', $realisation->annee_exercice) == $i ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('annee_exercice')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_realisation" class="form-label required">Date de réalisation</label>
                                    <input type="date" class="form-control @error('date_realisation') is-invalid @enderror" 
                                           id="date_realisation" name="date_realisation" 
                                           value="{{ old('date_realisation', $realisation->date_realisation) }}" required>
                                    @error('date_realisation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="commune_id" class="form-label required">Commune</label>
                                    <select class="form-select @error('commune_id') is-invalid @enderror" 
                                            id="commune_id" name="commune_id" required>
                                        <option value="">Sélectionnez une commune</option>
                                        @foreach($communes as $commune)
                                            <option value="{{ $commune->id }}" 
                                                    data-departement="{{ $commune->departement->nom ?? '' }}"
                                                    {{ old('commune_id', $realisation->commune_id) == $commune->id ? 'selected' : '' }}>
                                                {{ $commune->nom }} ({{ $commune->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('commune_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Département: <span id="departement-info">{{ $realisation->commune->departement->nom ?? '-' }}</span>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="montant" class="form-label required">Montant (FCFA)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('montant') is-invalid @enderror" 
                                               id="montant" name="montant" 
                                               value="{{ old('montant', $realisation->montant) }}" required>
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="prevision_id" class="form-label">Prévision associée</label>
                            <select class="form-select @error('prevision_id') is-invalid @enderror" 
                                    id="prevision_id" name="prevision_id">
                                <option value="">Aucune prévision associée</option>
                                @foreach($previsions as $prevision)
                                    <option value="{{ $prevision->id }}" 
                                            data-montant="{{ $prevision->montant }}"
                                            data-commune="{{ $prevision->commune->nom }}"
                                            {{ old('prevision_id', $realisation->prevision_id) == $prevision->id ? 'selected' : '' }}>
                                        {{ number_format($prevision->montant, 0, ',', ' ') }} FCFA - {{ $prevision->commune->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('prevision_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Prévision: <span id="prevision-info">
                                    @if($realisation->prevision)
                                        {{ number_format($realisation->prevision->montant, 0, ',', ' ') }} FCFA
                                    @else
                                        -
                                    @endif
                                </span>
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Description optionnelle de la réalisation...">{{ old('description', $realisation->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maximum 500 caractères</small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à jour la réalisation
                            </button>
                            <a href="{{ route('realisations.show', $realisation) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informations actuelles</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">Commune:</span>
                        <span class="info-value">{{ $realisation->commune->nom }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Département:</span>
                        <span class="info-value">{{ $realisation->commune->departement->nom ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Région:</span>
                        <span class="info-value">{{ $realisation->commune->departement->region->nom ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Année:</span>
                        <span class="info-value">{{ $realisation->annee_exercice }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Montant:</span>
                        <span class="info-value">{{ number_format($realisation->montant, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($realisation->date_realisation)->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            @if($realisation->prevision)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Prévision associée</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">Montant prévu:</span>
                        <span class="info-value">{{ number_format($realisation->prevision->montant, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Écart:</span>
                        <span class="info-value {{ $realisation->montant > $realisation->prevision->montant ? 'text-success' : 'text-danger' }}">
                            {{ number_format($realisation->montant - $realisation->prevision->montant, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Pourcentage:</span>
                        <span class="info-value">
                            {{ $realisation->prevision->montant > 0 ? round(($realisation->montant / $realisation->prevision->montant) * 100, 2) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <div class="card" id="prevision-details" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">Nouvelle prévision</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">Montant prévu:</span>
                        <span class="info-value" id="prevision-montant">-</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Année:</span>
                        <span class="info-value" id="prevision-annee">-</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Commune:</span>
                        <span class="info-value" id="prevision-commune">-</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aide</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle"></i> Attention</h5>
                        <ul class="mb-0">
                            <li>Modifier la commune ou l'année recalculera automatiquement les taux</li>
                            <li>Les modifications impactent les statistiques globales</li>
                            <li>Assurez-vous de la cohérence des données</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette réalisation ?</p>
                <div class="alert alert-danger">
                    <strong>Cette action est irréversible !</strong><br>
                    Les taux de réalisation seront automatiquement recalculés.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('realisations.destroy', $realisation) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.card-actions {
    display: flex;
    align-items: center;
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
}

.form-actions {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

.info-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 500;
    color: #6c757d;
}

.info-value {
    font-weight: 600;
    color: #495057;
}

.input-group-text {
    background-color: #f8f9fa;
    border-left: 1px solid #ced4da;
}

.card-header h3 {
    margin-bottom: 0;
    font-size: 1.1rem;
}

.alert h5 {
    margin-bottom: 0.75rem;
    font-size: 1rem;
}

.alert ul {
    padding-left: 1.25rem;
}

.alert li {
    margin-bottom: 0.25rem;
}

.text-success {
    color: #28a745 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.btn-danger {
    margin-left: auto;
}

.modal-content {
    border-radius: 0.5rem;
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Mettre à jour les informations du département quand la commune change
    $('#commune_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const departement = selectedOption.data('departement');
        $('#departement-info').text(departement || '-');
        
        // Charger les prévisions pour cette commune
        loadPrevisions();
    });

    // Charger les prévisions quand l'année change
    $('#annee_exercice').on('change', function() {
        loadPrevisions();
    });

    // Mettre à jour les détails de la prévision quand elle change
    $('#prevision_id').on('change', function() {
        updatePrevisionDetails();
    });

    // Fonction pour charger les prévisions
    function loadPrevisions() {
        const communeId = $('#commune_id').val();
        const annee = $('#annee_exercice').val();
        
        if (!communeId || !annee) {
            // Garder les prévisions existantes si pas de changement majeur
            if ($('#prevision_id option').length <= 1) {
                $('#prevision_id').html('<option value="">Aucune prévision associée</option>');
            }
            return;
        }

        // Ne recharger que si la commune ou l'année a changé
        const currentCommune = '{{ $realisation->commune_id }}';
        const currentAnnee = '{{ $realisation->annee_exercice }}';
        
        if (communeId == currentCommune && annee == currentAnnee) {
            // Pas de changement, garder les prévisions existantes
            return;
        }

        // Afficher un loader
        $('#prevision_id').html('<option value="">Chargement...</option>');
        
        // Appel AJAX pour récupérer les prévisions
        $.ajax({
            url: '{{ route("realisations.previsions-by-commune") }}',
            method: 'GET',
            data: {
                commune_id: communeId,
                annee: annee
            },
            success: function(response) {
                let options = '<option value="">Aucune prévision associée</option>';
                
                if (response.length > 0) {
                    response.forEach(function(prevision) {
                        const montantFormate = new Intl.NumberFormat('fr-FR').format(prevision.montant);
                        options += `<option value="${prevision.id}" data-montant="${prevision.montant}" data-commune="${prevision.commune.nom}">
                            ${montantFormate} FCFA - ${prevision.commune.nom}
                        </option>`;
                    });
                }
                
                $('#prevision_id').html(options);
                updatePrevisionDetails();
            },
            error: function() {
                $('#prevision_id').html('<option value="">Erreur de chargement</option>');
                $('#prevision-info').text('Erreur');
            }
        });
    }

    // Fonction pour mettre à jour les détails de la prévision
    function updatePrevisionDetails() {
        const selectedOption = $('#prevision_id').find('option:selected');
        const previsionId = selectedOption.val();
        
        if (!previsionId) {
            $('#prevision-info').text('-');
            $('#prevision-details').hide();
            return;
        }

        const montant = selectedOption.data('montant');
        const commune = selectedOption.data('commune');
        const annee = $('#annee_exercice').val();
        
        if (montant) {
            const montantFormate = new Intl.NumberFormat('fr-FR').format(montant);
            $('#prevision-info').text(`${montantFormate} FCFA`);
            $('#prevision-montant').text(`${montantFormate} FCFA`);
            $('#prevision-annee').text(annee);
            $('#prevision-commune').text(commune);
            $('#prevision-details').show();
        }
    }

    // Formater le montant en temps réel
    $('#montant').on('input', function() {
        const value = $(this).val();
        if (value) {
            // Validation basique
            if (value < 0) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        }
    });

    // Validation du formulaire
    $('#realisationForm').on('submit', function(e) {
        let isValid = true;
        
        // Vérifier les champs obligatoires
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Vérifier le montant
        const montant = parseFloat($('#montant').val());
        if (isNaN(montant) || montant < 0) {
            $('#montant').addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Veuillez corriger les erreurs dans le formulaire.');
        }
    });

    // Initialiser les détails de la prévision si une est déjà sélectionnée
    if ($('#prevision_id').val()) {
        updatePrevisionDetails();
    }
});

// Fonction pour confirmer la suppression
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
@endsection