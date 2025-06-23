@extends('layouts.app')

@section('title', 'Détails - ' . $ordonnateur->nom)

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $ordonnateur->nom }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('ordonnateurs.index') }}">Ordonnateurs</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $ordonnateur->nom }}</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('ordonnateurs.edit', $ordonnateur) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            <button type="button" class="btn btn-outline-danger" onclick="confirmerSuppression()">
                <i class="fas fa-trash me-2"></i>Supprimer
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Informations personnelles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Nom :</td>
                                    <td>{{ $ordonnateur->nom }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Fonction :</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $ordonnateur->fonction }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Date de prise de fonction :</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($ordonnateur->date_prise_fonction)->format('d/m/Y') }}
                                        <small class="text-muted">
                                            ({{ \Carbon\Carbon::parse($ordonnateur->date_prise_fonction)->diffForHumans() }})
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Téléphone :</td>
                                    <td>
                                        @if($ordonnateur->telephone)
                                            <a href="tel:{{ $ordonnateur->telephone }}" class="text-decoration-none">
                                                <i class="fas fa-phone me-1"></i>{{ $ordonnateur->telephone }}
                                            </a>
                                        @else
                                            <span class="text-muted">Non renseigné</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <div class="avatar-placeholder bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                     style="width: 120px; height: 120px; font-size: 48px;">
                                    {{ strtoupper(substr($ordonnateur->nom, 0, 2)) }}
                                </div>
                                <div class="mt-3">
                                    <h5 class="mb-1">{{ $ordonnateur->nom }}</h5>
                                    <p class="text-muted mb-0">{{ $ordonnateur->fonction }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignation à une commune -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Assignation
                    </h5>
                    @if($ordonnateur->commune)
                        <button class="btn btn-sm btn-outline-warning" onclick="libererDeCommune()">
                            <i class="fas fa-times me-1"></i>Libérer
                        </button>
                    @else
                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#assignModal">
                            <i class="fas fa-plus me-1"></i>Assigner
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($ordonnateur->commune)
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">
                                    <i class="fas fa-check-circle me-2"></i>Assigné à une commune
                                </h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-bold text-muted">Commune :</td>
                                        <td>{{ $ordonnateur->commune->nom }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Département :</td>
                                        <td>{{ $ordonnateur->commune->departement->nom }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Région :</td>
                                        <td>{{ $ordonnateur->commune->departement->region->nom }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                @if(!empty($stats))
                                    <h6>Statistiques de la commune</h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <h6 class="text-primary mb-1">{{ number_format($stats['previsions_annee']) }} FCFA</h6>
                                                <small class="text-muted">Prévisions {{ date('Y') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <h6 class="text-success mb-1">{{ $stats['taux_realisation'] }}%</h6>
                                                <small class="text-muted">Taux réalisation</small>
                                            </div>
                                        </div>
                                    </div>
                                    @if($stats['nb_defaillances'] > 0)
                                        <div class="alert alert-warning mt-3 mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            {{ $stats['nb_defaillances'] }} défaillance(s) non résolue(s)
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Non assigné à une commune</h5>
                            <p class="text-muted">Cet ordonnateur n'est actuellement assigné à aucune commune.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal">
                                <i class="fas fa-plus me-2"></i>Assigner à une commune
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Historique -->
            @if($historique->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Historique des assignations
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($historique as $entry)
                                <div class="timeline-item {{ $entry['est_actuel'] ? 'active' : '' }}">
                                    <div class="timeline-marker {{ $entry['est_actuel'] ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">{{ $entry['commune'] }}</h6>
                                        <p class="text-muted mb-1">{{ $entry['departement'] }} - {{ $entry['region'] }}</p>
                                        <small class="text-muted">
                                            Du {{ \Carbon\Carbon::parse($entry['date_debut'])->format('d/m/Y') }}
                                            @if($entry['date_fin'])
                                                au {{ \Carbon\Carbon::parse($entry['date_fin'])->format('d/m/Y') }}
                                            @else
                                                <span class="badge bg-success ms-1">En cours</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Panneau latéral -->
        <div class="col-md-4">
            <!-- Actions rapides -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('ordonnateurs.edit', $ordonnateur) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Modifier les informations
                        </a>
                        @if($ordonnateur->commune)
                            <button class="btn btn-outline-warning" onclick="libererDeCommune()">
                                <i class="fas fa-times me-2"></i>Libérer de la commune
                            </button>
                        @else
                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#assignModal">
                                <i class="fas fa-plus me-2"></i>Assigner à une commune
                            </button>
                        @endif
                        @if($ordonnateur->telephone)
                            <a href="tel:{{ $ordonnateur->telephone }}" class="btn btn-outline-info">
                                <i class="fas fa-phone me-2"></i>Appeler
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations système -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations système
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">Créé le :</td>
                            <td>{{ $ordonnateur->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Modifié le :</td>
                            <td>{{ $ordonnateur->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Statut :</td>
                            <td>
                                @if($ordonnateur->commune_id)
                                    <span class="badge bg-success">Assigné</span>
                                @else
                                    <span class="badge bg-warning">Libre</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'assignation -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assigner à une commune</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('ordonnateurs.assign-commune', $ordonnateur) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="commune_id" class="form-label">Sélectionner une commune</label>
                        <select class="form-select" id="commune_id" name="commune_id" required>
                            <option value="">Choisir une commune...</option>
                            @php
                                $communes = \App\Models\Commune::with('departement.region')->orderBy('nom')->get();
                            @endphp
                            @foreach($communes->groupBy('departement.region.nom') as $region => $communesRegion)
                                <optgroup label="Région {{ $region }}">
                                    @foreach($communesRegion->groupBy('departement.nom') as $departement => $communesDepartement)
                                        <optgroup label="── {{ $departement }}">
                                            @foreach($communesDepartement as $commune)
                                                <option value="{{ $commune->id }}">{{ $commune->nom }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Assigner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5>Êtes-vous sûr ?</h5>
                    <p class="text-muted">
                        Cette action supprimera définitivement l'ordonnateur <strong>{{ $ordonnateur->nom }}</strong>.
                        Cette action est irréversible.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" action="{{ route('ordonnateurs.destroy', $ordonnateur) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #dee2e6;
}

.timeline-item.active .timeline-content {
    border-left-color: #28a745;
}
</style>
@endpush

@push('scripts')
<script>
function confirmerSuppression() {
    new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
}

function libererDeCommune() {
    if (confirm('Êtes-vous sûr de vouloir libérer cet ordonnateur de sa commune ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("ordonnateurs.liberer-commune", $ordonnateur) }}';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';

        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
