@extends('layouts.app')

@section('title', 'Détails de la dette CNPS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Détails de la dette CNPS</h1>
                    <p class="text-muted">{{ $detteCnps->commune->nom }} - {{ $detteCnps->commune->departement->nom }}</p>
                </div>
                <div>
                    <a href="{{ route('dettes-cnps.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <a href="{{ route('dettes-cnps.edit', $detteCnps) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <form method="POST" action="{{ route('dettes-cnps.destroy', $detteCnps) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette dette ?')">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>

            <!-- Informations principales -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle"></i> Informations de la dette
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Montant :</strong></td>
                                            <td>
                                                <span class="badge bg-danger fs-6">
                                                    {{ number_format($detteCnps->montant, 0, ',', ' ') }} FCFA
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date d'évaluation :</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($detteCnps->date_evaluation)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Commune :</strong></td>
                                            <td>{{ $detteCnps->commune->nom }} ({{ $detteCnps->commune->code }})</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Département :</strong></td>
                                            <td>{{ $detteCnps->commune->departement->nom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Région :</strong></td>
                                            <td>{{ $detteCnps->commune->departement->region->nom }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    @if($detteCnps->description)
                                    <div class="mb-3">
                                        <strong>Description :</strong>
                                        <p class="mt-2 p-3 bg-light rounded">{{ $detteCnps->description }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques rapides -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-chart-line"></i> Statistiques
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="mb-2">
                                    <small class="text-muted">Total des dettes de cette commune</small>
                                </div>
                                <h4 class="text-danger">
                                    {{ number_format($evolution->sum('total'), 0, ',', ' ') }} FCFA
                                </h4>
                            </div>
                            <div class="text-center">
                                <div class="mb-2">
                                    <small class="text-muted">Nombre d'évaluations</small>
                                </div>
                                <h5 class="text-info">{{ $historique->count() + 1 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Évolution des dettes -->
            @if($evolution->count() > 1)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-area"></i> Évolution des dettes CNPS
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="evolutionChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Historique -->
            @if($historique->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history"></i> Historique des dettes CNPS
                                <small class="text-muted">({{ $historique->count() }} dernières évaluations)</small>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date d'évaluation</th>
                                            <th>Montant</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($historique as $dette)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($dette->date_evaluation)->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    {{ number_format($dette->montant, 0, ',', ' ') }} FCFA
                                                </span>
                                            </td>
                                            <td>{{ Str::limit($dette->description, 50) }}</td>
                                            <td>
                                                <a href="{{ route('dettes-cnps.show', $dette) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
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
    </div>
</div>

@if($evolution->count() > 1)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    
    const evolutionData = @json($evolution);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: evolutionData.map(item => item.annee),
            datasets: [{
                label: 'Dette CNPS (FCFA)',
                data: evolutionData.map(item => item.total),
                borderColor: 'rgb(220, 53, 69)',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                        }
                    }
                }
            },
            elements: {
                point: {
                    radius: 5,
                    hoverRadius: 8
                }
            }
        }
    });
});
</script>
@endpush
@endif
@endsection