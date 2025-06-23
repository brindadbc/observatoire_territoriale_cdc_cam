@extends('layouts.app')

@section('title', 'Détails du receveur')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Détails du receveur</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('receveurs.index') }}">Receveurs</a></li>
                            <li class="breadcrumb-item active">{{ $receveur->nom }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <a href="{{ route('receveurs.edit', $receveur) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('receveurs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informations générales -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user"></i> Informations générales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Nom :</strong></div>
                        <div class="col-sm-8">{{ $receveur->nom }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Matricule :</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-primary">{{ $receveur->matricule }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Statut :</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-{{ $receveur->statut === 'Actif' ? 'success' : ($receveur->statut === 'Inactif' ? 'danger' : 'warning') }}">
                                {{ $receveur->statut }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Téléphone :</strong></div>
                        <div class="col-sm-8">{{ $receveur->telephone ?? 'Non renseigné' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Prise de fonction :</strong></div>
                        <div class="col-sm-8">{{ \Carbon\Carbon::parse($receveur->date_prise_fonction)->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Assignation -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt"></i> Assignation
                    </h5>
                </div>
                <div class="card-body">
                    @if($receveur->commune)
                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Commune :</strong></div>
                            <div class="col-sm-8">{{ $receveur->commune->nom }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Département :</strong></div>
                            <div class="col-sm-8">{{ $receveur->commune->departement->nom }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Région :</strong></div>
                            <div class="col-sm-8">{{ $receveur->commune->departement->region->nom }}</div>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p>Receveur non assigné à une commune</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistiques et performance -->
        <div class="col-md-8">
            <!-- Statistiques des dépôts -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="card-title">Total dépôts</div>
                                    <div class="h4">{{ $statsDepots['total_depots'] }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-file-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="card-title">Dépôts {{ $annee }}</div>
                                    <div class="h4">{{ $statsDepots['depots_annee'] }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="card-title">Validés</div>
                                    <div class="h4">{{ $statsDepots['depots_valides'] }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="card-title">Taux validation</div>
                                    <div class="h4">{{ $statsDepots['taux_validation'] }}%</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des dépôts -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Historique des dépôts ({{ $annee }})
                    </h5>
                    <div class="btn-group btn-group-sm">
                        @foreach(range(date('Y'), date('Y')-5) as $year)
                            <a href="{{ route('receveurs.show', ['receveur' => $receveur, 'annee' => $year]) }}" 
                               class="btn btn-outline-primary {{ $year == $annee ? 'active' : '' }}">
                                {{ $year }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="card-body">
                    @if($historiqueDepots->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Commune</th>
                                        <th>Date dépôt</th>
                                        <th>Année exercice</th>
                                        <th>Validation</th>
                                        <th>Observations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historiqueDepots as $depot)
                                        <tr>
                                            <td>{{ $depot['commune'] }}</td>
                                            <td>{{ \Carbon\Carbon::parse($depot['date_depot'])->format('d/m/Y') }}</td>
                                            <td>{{ $depot['annee_exercice'] }}</td>
                                            <td>
                                                <span class="badge bg-{{ $depot['validation'] ? 'success' : 'warning' }}">
                                                    {{ $depot['validation'] ? 'Validé' : 'En attente' }}
                                                </span>
                                            </td>
                                            <td>{{ $depot['observations'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Aucun dépôt pour l'année {{ $annee }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Performance annuelle -->
            @if($performanceAnnuelle->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar"></i> Performance annuelle
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Année</th>
                                        <th>Total dépôts</th>
                                        <th>Dépôts validés</th>
                                        <th>Taux de validation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($performanceAnnuelle as $perf)
                                        <tr>
                                            <td><strong>{{ $perf->annee }}</strong></td>
                                            <td>{{ $perf->total_depots }}</td>
                                            <td>{{ $perf->depots_valides }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $perf->taux_validation >= 80 ? 'success' : ($perf->taux_validation >= 60 ? 'warning' : 'danger') }}" 
                                                         style="width: {{ $perf->taux_validation }}%">
                                                        {{ $perf->taux_validation }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Confirmation de suppression
    function confirmerSuppression() {
        return confirm('Êtes-vous sûr de vouloir supprimer ce receveur ?');
    }
</script>
@endpush