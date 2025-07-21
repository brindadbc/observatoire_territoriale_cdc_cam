@extends('layouts.app')

@section('title', 'Détails Dette FEICOM')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Détails de la Dette FEICOM</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dettes-feicom.index') }}">Dettes FEICOM</a></li>
                        <li class="breadcrumb-item active">Détails</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-information-outline me-2"></i>
                        Informations de la Dette
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Commune</label>
                                <div class="fw-bold fs-5">
                                    {{ $detteFeicom->commune->nom }}
                                    <span class="badge bg-primary ms-2">{{ $detteFeicom->commune->code }}</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Département</label>
                                <div class="fw-semibold">{{ $detteFeicom->commune->departement->nom }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Région</label>
                                <div class="fw-semibold">{{ $detteFeicom->commune->departement->region->nom }}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Montant de la Dette</label>
                                <div class="fw-bold fs-3 text-danger">
                                    {{ number_format($detteFeicom->montant, 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Date d'Évaluation</label>
                                <div class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($detteFeicom->date_evaluation)->format('d/m/Y') }}
                                    <small class="text-muted">
                                        ({{ \Carbon\Carbon::parse($detteFeicom->date_evaluation)->diffForHumans() }})
                                    </small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Date d'Enregistrement</label>
                                <div class="fw-semibold">
                                    {{ $detteFeicom->created_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparaison avec autres dettes -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-chart-pie me-2"></i>
                        Comparaison avec Autres Dettes ({{ \Carbon\Carbon::parse($detteFeicom->date_evaluation)->year }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">FEICOM</h6>
                                <div class="fw-bold text-primary fs-5">
                                    {{ number_format($detteFeicom->montant, 0, ',', ' ') }}
                                </div>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">CNPS</h6>
                                <div class="fw-bold text-warning fs-5">
                                    {{ number_format($autresDettes['cnps'], 0, ',', ' ') }}
                                </div>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">fiscale</h6>
                                <div class="fw-bold text-info fs-5">
                                    {{ number_format($autresDettes['fiscale'], 0, ',', ' ') }}
                                </div>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">Salariale</h6>
                                <div class="fw-bold text-success fs-5">
                                    {{ number_format($autresDettes['salariale'], 0, ',', ' ') }}
                                </div>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $totalDettes = $detteFeicom->montant + $autresDettes['cnps'] + $autresDettes['fiscale'] + $autresDettes['salariale'];
                    @endphp
                    
                    @if($totalDettes > 0)
                    <div class="mt-4">
                        <h6>Répartition des Dettes</h6>
                        <div class="progress mb-2" style="height: 25px;">
                            @php
                                $percentFeicom = ($detteFeicom->montant / $totalDettes) * 100;
                                $percentCnps = ($autresDettes['cnps'] / $totalDettes) * 100;
                                $percentFiscale = ($autresDettes['fiscale'] / $totalDettes) * 100;
                                $percentSalariale = ($autresDettes['salariale'] / $totalDettes) * 100;
                            @endphp
                            
                            @if($percentFeicom > 0)
                            <div class="progress-bar bg-primary" style="width: {{ $percentFeicom }}%">
                                {{ round($percentFeicom, 1) }}%
                            </div>
                            @endif
                            
                            @if($percentCnps > 0)
                            <div class="progress-bar bg-warning" style="width: {{ $percentCnps }}%">
                                {{ round($percentCnps, 1) }}%
                            </div>
                            @endif
                            
                            @if($percentFiscale > 0)
                            <div class="progress-bar bg-info" style="width: {{ $percentFiscale }}%">
                                {{ round($percentFiscale, 1) }}%
                            </div>
                            @endif
                            
                            @if($percentSalariale > 0)
                            <div class="progress-bar bg-success" style="width: {{ $percentSalariale }}%">
                                {{ round($percentSalariale, 1) }}%
                            </div>
                            @endif
                        </div>
                        <div class="text-center">
                            <strong>Total des Dettes: {{ number_format($totalDettes, 0, ',', ' ') }} FCFA</strong>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-cog me-2"></i>
                        Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('dettes-feicom.edit', $detteFeicom) }}" class="btn btn-primary">
                            <i class="mdi mdi-pencil"></i> Modifier
                        </a>
                        
                        {{-- <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                            <i class="mdi mdi-delete"></i> Supprimer
                        </button> --}}
                           <form method="POST" action="{{ route('dettes-feicom.destroy', $detteFeicom) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette dette ?')">
                            <i class="mdi mdi-delete"></i> Supprimer
                        </button>
                    </form>
                        
                        <hr>
                        
                        <a href="{{ route('dettes-feicom.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Retour à la liste
                        </a>
                        
                        <a href="{{ route('dettes-feicom.create') }}" class="btn btn-outline-primary">
                            <i class="mdi mdi-plus"></i> Nouvelle dette
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-chart-line me-2"></i>
                        Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted">Rang parmi les dettes FEICOM</label>
                        <div class="fw-bold">À calculer</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted">Pourcentage du total régional</label>
                        <div class="fw-bold">À calculer</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted">Évolution depuis dernière évaluation</label>
                        <div class="fw-bold text-success">À calculer</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des dettes FEICOM de cette commune -->
    @if($historique->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-history me-2"></i>
                        Historique des Dettes FEICOM - {{ $detteFeicom->commune->nom }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date d'Évaluation</th>
                                    <th>Montant</th>
                                    <th>Évolution</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($historique as $dette)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($dette->date_evaluation)->format('d/m/Y') }}</td>
                                    <td class="fw-bold">{{ number_format($dette->montant, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        @if($loop->index > 0)
                                            @php
                                                $precedent = $historique[$loop->index - 1];
                                                $evolution = $dette->montant - $precedent->montant;
                                                $pourcentage = $precedent->montant > 0 ? ($evolution / $precedent->montant) * 100 : 0;
                                            @endphp
                                            
                                            @if($evolution > 0)
                                                <span class="badge bg-danger">
                                                    +{{ number_format($evolution, 0, ',', ' ') }} 
                                                    ({{ number_format($pourcentage, 1) }}%)
                                                </span>
                                            @elseif($evolution < 0)
                                                <span class="badge bg-success">
                                                    {{ number_format($evolution, 0, ',', ' ') }} 
                                                    ({{ number_format($pourcentage, 1) }}%)
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Stable</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('dettes-feicom.show', $dette) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-eye"></i> Voir
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- <!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette dette FEICOM ?</p>
                <div class="alert alert-warning">
                    <strong>Cette action est irréversible !</strong><br>
                    Commune: <strong>{{ $detteFeicom->commune->nom }}</strong><br>
                    Montant: <strong>{{ number_format($detteFeicom->montant, 0, ',', ' ') }} FCFA</strong><br>
                    Date: <strong>{{ \Carbon\Carbon::parse($detteFeicom->date_evaluation)->format('d/m/Y') }}</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('dettes-feicom.destroy', $detteFeicom) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div> --}}
{{-- 
<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette dette feicom ?</p>
                <div class="alert alert-warning">
                    <strong>Attention :</strong> Cette action est irréversible.
                </div>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('dettes-feicom.destroy', $detteFeicom) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form> --}}
             
            </div>
        </div>
    </div>
</div> --}}

<script>
function confirmDelete() {
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>

{{-- <!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette dette FEICOM ?</p>
                <div class="alert alert-warning">
                    <strong>Cette action est irréversible !</strong><br>
                    Commune: <strong>{{ $detteFeicom->commune->nom }}</strong><br>
                    Montant: <strong>{{ number_format($detteFeicom->montant, 0, ',', ' ') }} FCFA</strong><br>
                    Date: <strong>{{ \Carbon\Carbon::parse($detteFeicom->date_evaluation)->format('d/m/Y') }}</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteFormShow" action="{{ route('dettes-feicom.destroy', $detteFeicom) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtnShow">
                        <i class="mdi mdi-delete"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    const form = document.getElementById('deleteFormShow');
    const confirmBtn = document.getElementById('confirmDeleteBtnShow');
    
    // Gérer la soumission du formulaire
    form.onsubmit = function(e) {
        e.preventDefault();
        
        // Désactiver le bouton
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Suppression...';
        
        // Soumettre le formulaire normalement
        fetch(form.action, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                // Rediriger vers l'index
                window.location.href = "{{ route('dettes-feicom.index') }}";
            } else {
                throw new Error('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression. Veuillez réessayer.');
            
            // Réactiver le bouton
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="mdi mdi-delete"></i> Supprimer';
        });
    };
    
    // Afficher le modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Nettoyer le formulaire quand le modal se ferme
document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('deleteFormShow');
    const confirmBtn = document.getElementById('confirmDeleteBtnShow');
    
    form.onsubmit = null;
    confirmBtn.disabled = false;
    confirmBtn.innerHTML = '<i class="mdi mdi-delete"></i> Supprimer';
});
</script> --}}
@endsection