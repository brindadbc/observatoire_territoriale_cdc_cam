@extends('layouts.app')

@section('title', 'Gestion des Régions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Gestion des Régions</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Régions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Messages de succès/erreur --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">Liste des Régions</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('regions.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"> Nouvelle Région</i>
                            </a>
                           
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filtres --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('regions.index') }}">
                                <div class="input-group">
                                    <select class="form-select" name="annee" onchange="this.form.submit()">
                                        @for($year = date('Y'); $year >= 2020; $year--)
                                            <option value="{{ $year }}" {{ $annee == $year ? 'selected' : '' }}>
                                                Année {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Tableau des régions --}}
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap w-100" id="regionsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Région</th>
                                    <th>Départements</th>
                                    <th>Communes</th>
                                    <th>Taux Moyen (%)</th>
                                    <th>Dettes CNPS</th>
                                    <th>Conformité (%)</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($regions as $region)
                                    <tr>
                                        <td>
                                            <strong>{{ $region['nom'] }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $region['nb_departements'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $region['nb_communes'] }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    @php
                                                        $taux = $region['taux_moyen_realisation'];
                                                        $color = $taux >= 85 ? 'success' : ($taux >= 70 ? 'warning' : 'danger');
                                                    @endphp
                                                    <div class="progress" style="width: 60px; height: 8px;">
                                                        <div class="progress-bar bg-{{ $color }}" 
                                                             style="width: {{ min($taux, 100) }}%"></div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <strong>{{ number_format($taux, 1) }}%</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($region['total_dettes_cnps'] > 0)
                                                <span class="text-danger">
                                                    {{ number_format($region['total_dettes_cnps'], 0, ',', ' ') }} FCFA
                                                </span>
                                            @else
                                                <span class="text-success">Aucune dette</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $conformite = $region['conformite_depots'];
                                                $badgeClass = $conformite >= 80 ? 'success' : ($conformite >= 60 ? 'warning' : 'danger');
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">
                                                {{ number_format($conformite, 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $status = $region['status'];
                                                $statusClass = match($status) {
                                                    'Excellent' => 'success',
                                                    'Bon' => 'primary',
                                                    'Moyen' => 'warning',
                                                    default => 'danger'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ $status }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('regions.show', $region['id']) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Voir les détails">
                                                    <i class="bx bx-show"></i>Voir les détails
                                                </a>
                                                <a href="{{ route('regions.edit', $region['id']) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Modifier">
                                                    <i class="bx bx-edit"></i>Modifier
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDelete({{ $region['id'] }}, '{{ $region['nom'] }}')"
                                                        title="Supprimer">
                                                    <i class="bx bx-trash"></i>Supprimer
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="bx bx-map-alt font-size-48 text-muted mb-3"></i>
                                                <h5 class="text-muted">Aucune région trouvée</h5>
                                                <p class="text-muted">Commencez par créer votre première région.</p>
                                                <a href="{{ route('regions.create') }}" class="btn btn-primary">
                                                    <i class="bx bx-plus me-1"></i> Créer une région
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques globales --}}
    @if($regions->count() > 0)
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Total Régions</p>
                                <h4 class="mb-2">{{ $regions->count() }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="bx bx-map-alt font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Total Départements</p>
                                <h4 class="mb-2">{{ $regions->sum('nb_departements') }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-info mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-info">
                                        <i class="bx bx-buildings font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Total Communes</p>
                                <h4 class="mb-2">{{ $regions->sum('nb_communes') }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-warning mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-warning">
                                        <i class="bx bx-home font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Taux Moyen National</p>
                                <h4 class="mb-2">{{ number_format($regions->avg('taux_moyen_realisation'), 1) }}%</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-success mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-success">
                                        <i class="bx bx-trending-up font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Modal de confirmation de suppression --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la région <strong id="regionName"></strong> ?</p>
                <p class="text-warning">
                    <i class="bx bx-warning me-1"></i>
                    Cette action est irréversible et ne peut être effectuée que si la région ne contient aucun département.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialisation DataTable
    $(document).ready(function() {
        $('#regionsTable').DataTable({
            "pageLength": 10,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            },
            "order": [[0, "asc"]]
        });
    });

    // Fonction de confirmation de suppression
    function confirmDelete(regionId, regionName) {
        document.getElementById('regionName').textContent = regionName;
        document.getElementById('deleteForm').action = `/regions/${regionId}`;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endpush