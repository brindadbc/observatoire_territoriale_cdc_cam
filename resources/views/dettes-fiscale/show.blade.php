@extends('layouts.app')

@section('title', 'Détails de la dette fiscale')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Détails de la dette fiscale</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dettes-fiscale.index') }}">Dettes fiscales</a></li>
                            <li class="breadcrumb-item active">Détails</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('dettes-fiscale.edit', $detteFiscale) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('dettes-fiscale.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary"></i>
                        Informations de la dette
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Commune :</td>
                                    <td>{{ $detteFiscale->commune->nom }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Code commune :</td>
                                    <td>{{ $detteFiscale->commune->code }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Département :</td>
                                    <td>{{ $detteFiscale->commune->departement->nom }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Région :</td>
                                    <td>{{ $detteFiscale->commune->departement->region->nom }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Montant :</td>
                                    <td class="text-danger fw-bold">
                                        {{ number_format($detteFiscale->montant, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Date d'évaluation :</td>
                                    <td>{{ \Carbon\Carbon::parse($detteFiscale->date_evaluation)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Créé le :</td>
                                    <td>{{ $detteFiscale->created_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Modifié le :</td>
                                    <td>{{ $detteFiscale->updated_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie text-success"></i>
                        Autres dettes de la commune
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Dette CNPS :</span>
                            <span class="fw-bold">{{ number_format($autresDettes['cnps'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Dette FEICOM :</span>
                            <span class="fw-bold">{{ number_format($autresDettes['feicom'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Dette salariale :</span>
                            <span class="fw-bold">{{ number_format($autresDettes['salariale'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total autres dettes :</span>
                        <span class="fw-bold text-danger">
                            {{ number_format(array_sum($autresDettes), 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des dettes fiscales -->
    @if($historique->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history text-info"></i>
                        Historique des dettes fiscales de {{ $detteFiscale->commune->nom }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date d'évaluation</th>
                                    <th>Montant</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($historique as $dette)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($dette->date_evaluation)->format('d/m/Y') }}</td>
                                    <td class="text-danger fw-bold">
                                        {{ number_format($dette->montant, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td>
                                        <a href="{{ route('dettes-fiscale.show', $dette) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Voir
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

    <!-- Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Actions disponibles</h6>
                    <div class="btn-group" role="group">
                        <a href="{{ route('dettes-fiscale.edit', $detteFiscale) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier cette dette
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                        <a href="{{ route('dettes-fiscale.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nouvelle dette
                        </a>
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
                <p>Êtes-vous sûr de vouloir supprimer cette dette fiscale ?</p>
                <div class="alert alert-warning">
                    <strong>Attention :</strong> Cette action est irréversible.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('dettes-fiscale.destroy', $detteFiscale) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection