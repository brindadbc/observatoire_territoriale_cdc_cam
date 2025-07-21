{{-- @extends('layouts.app')

@section('title', 'Détails de la Réalisation')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Réalisation - {{ $realisation->commune->nom }}</h1>
            <p class="text-muted">{{ $realisation->date_realisation->format('d/m/Y') }} • {{ $realisation->annee_exercice }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('realisations.edit', $realisation) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <form method="POST" action="{{ route('realisations.destroy', $realisation) }}" class="d-inline"
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réalisation ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </form>
            <a href="{{ route('realisations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <!-- Détails de la réalisation -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Informations détaillées
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Localisation</h6>
                            <div class="mb-3">
                                <strong>Commune:</strong> {{ $realisation->commune->nom }}<br>
                                <small class="text-muted">Code: {{ $realisation->commune->code }}</small>
                            </div>
                            <div class="mb-3">
                                <strong>Département:</strong> {{ $realisation->commune->departement->nom }}
                            </div>
                            <div class="mb-3">
                                <strong>Région:</strong> {{ $realisation->commune->departement->region->nom }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Détails financiers</h6>
                            <div class="mb-3">
                                <strong>Montant réalisé:</strong><br>
                                <span class="h4 text-success">{{ number_format($stats['montant_realisation'], 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="mb-3">
                                <strong>Date de réalisation:</strong><br>
                                {{ $stats['date_realisation']->format('d/m/Y') }}
                            </div>
                            <div class="mb-3">
                                <strong>Année d'exercice:</strong><br>
                                {{ $stats['annee_exercice'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparaison avec la prévision -->
            @if($realisation->prevision)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Comparaison avec la prévision
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Montant prévu</h6>
                                <h4 class="text-primary">{{ number_format($realisation->prevision->montant, 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Écart</h6>
                                <h4 class="text-{{ ($stats['ecart_prevision'] ?? 0) >= 0 ? 'success' : 'danger' }}">
                                    {{ ($stats['ecart_prevision'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($stats['ecart_prevision'] ?? 0, 0, ',', ' ') }} FCFA
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Taux de réalisation</h6>
                                <h4 class="text-{{ $stats['pourcentage_prevision'] >= 100 ? 'success' : ($stats['pourcentage_prevision'] >= 75 ? 'warning' : 'danger') }}">
                                    {{ number_format($stats['pourcentage_prevision'], 1) }}%
                                </h4>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Barre de progression -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Progression</span>
                            <span class="badge bg-{{ $stats['status'] === 'Objectif atteint' ? 'success' : ($stats['status'] === 'Bon' ? 'info' : ($stats['status'] === 'Moyen' ? 'warning' : 'secondary')) }}">
                                {{ $stats['status'] }}
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $stats['pourcentage_prevision'] >= 100 ? 'success' : ($stats['pourcentage_prevision'] >= 75 ? 'warning' : 'danger') }}" 
                                 style="width: {{ min($stats['pourcentage_prevision'], 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Aucune prévision associée</strong><br>
                Cette réalisation n'est pas liée à une prévision budgétaire.
            </div>
            @endif

            <!-- Autres réalisations de la commune -->
            @if($autresRealisations->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Autres réalisations de {{ $realisation->commune->nom }} ({{ $realisation->annee_exercice }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Montant</th>
                                    <th class="text-end">Prévision</th>
                                    <th class="text-center">Taux</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($autresRealisations as $autre)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($autre['date_realisation'])->format('d/m/Y') }}</td>
                                    <td class="text-end">{{ number_format($autre['montant'], 0, ',', ' ') }} FCFA</td>
                                    <td class="text-end">
                                        {{ $autre['prevision_montant'] ? number_format($autre['prevision_montant'], 0, ',', ' ') . ' FCFA' : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if($autre['pourcentage_prevision'])
                                            <span class="badge bg-{{ $autre['pourcentage_prevision'] >= 100 ? 'success' : ($autre['pourcentage_prevision'] >= 75 ? 'warning' : 'danger') }}">
                                                {{ number_format($autre['pourcentage_prevision'], 1) }}%
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $autre['status'] === 'Objectif atteint' ? 'success' : ($autre['status'] === 'Bon' ? 'info' : ($autre['status'] === 'Moyen' ? 'warning' : 'secondary')) }}">
                                            {{ $autre['status'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('realisations.show', $autre['id']) }}" class="btn btn-sm btn-outline-info">
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
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statut et métriques -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-tachometer-alt"></i> Métriques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-flag fa-2x text-{{ $stats['status'] === 'Objectif atteint' ? 'success' : ($stats['status'] === 'Bon' ? 'info' : ($stats['status'] === 'Moyen' ? 'warning' : 'secondary')) }} mb-2"></i>
                                <h6>Statut</h6>
                                <span class="badge bg-{{ $stats['status'] === 'Objectif atteint' ? 'success' : ($stats['status'] === 'Bon' ? 'info' : ($stats['status'] === 'Moyen' ? 'warning' : 'secondary')) }}">
                                    {{ $stats['status'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparaison départementale -->
            @if($comparaison->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Comparaison départementale
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Commune</th>
                                    <th class="text-center">Taux</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comparaison->take(10) as $comp)
                                <tr class="{{ $comp['est_actuelle'] ? 'table-warning' : '' }}">
                                    <td>
                                        {{ $comp['commune'] }}
                                        @if($comp['est_actuelle'])
                                            <i class="fas fa-arrow-right text-primary"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $comp['taux_realisation'] >= 100 ? 'success' : ($comp['taux_realisation'] >= 75 ? 'warning' : 'danger') }}">
                                            {{ number_format($comp['taux_realisation'], 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Évolution annuelle -->
            @if($evolutionAnnuelle->count() > 1)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line"></i> Évolution annuelle
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" width="400" height="200"></canvas>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
@if($evolutionAnnuelle->count() > 1)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionData = @json($evolutionAnnuelle->values());
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: evolutionData.map(item => item.annee),
            datasets: [{
                label: 'Montant réalisé',
                data: evolutionData.map(item => item.montant_realise),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1
            }, {
                label: 'Prévision',
                data: evolutionData.map(item => item.prevision_totale),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + 
                                   new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif
@endpush

@endsection --}}



{{-- @extends('layouts.app')

@section('title', 'Détails de la Réalisation')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Réalisation - {{ $realisation->commune->nom }}</h1>
            <p class="text-muted">
                {{ $realisation->date_realisation instanceof \Carbon\Carbon ? $realisation->date_realisation->format('d/m/Y') : \Carbon\Carbon::parse($realisation->date_realisation)->format('d/m/Y') }} 
                • {{ $realisation->annee_exercice }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('realisations.edit', $realisation) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <form method="POST" action="{{ route('realisations.destroy', $realisation) }}" class="d-inline"
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réalisation ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </form>
            <a href="{{ route('realisations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <!-- Détails de la réalisation -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Informations détaillées
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Localisation</h6>
                            <div class="mb-3">
                                <strong>Commune:</strong> {{ $realisation->commune->nom }}<br>
                                <small class="text-muted">Code: {{ $realisation->commune->code }}</small>
                            </div>
                            <div class="mb-3">
                                <strong>Département:</strong> {{ $realisation->commune->departement->nom }}
                            </div>
                            <div class="mb-3">
                                <strong>Région:</strong> {{ $realisation->commune->departement->region->nom }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Détails financiers</h6>
                            <div class="mb-3">
                                <strong>Montant réalisé:</strong><br>
                                <span class="h4 text-success">{{ number_format($stats['montant_realisation'], 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="mb-3">
                                <strong>Date de réalisation:</strong><br>
                                {{ isset($stats['date_realisation']) && $stats['date_realisation'] instanceof \Carbon\Carbon ? $stats['date_realisation']->format('d/m/Y') : \Carbon\Carbon::parse($stats['date_realisation'])->format('d/m/Y') }}
                            </div>
                            <div class="mb-3">
                                <strong>Année d'exercice:</strong><br>
                                {{ $stats['annee_exercice'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparaison avec la prévision -->
            @if($realisation->prevision)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Comparaison avec la prévision
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Montant prévu</h6>
                                <h4 class="text-primary">{{ number_format($realisation->prevision->montant, 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Écart</h6>
                                <h4 class="text-{{ ($stats['ecart_prevision'] ?? 0) >= 0 ? 'success' : 'danger' }}">
                                    {{ ($stats['ecart_prevision'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($stats['ecart_prevision'] ?? 0, 0, ',', ' ') }} FCFA
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Taux de réalisation</h6>
                                <h4 class="text-{{ $stats['pourcentage_prevision'] >= 100 ? 'success' : ($stats['pourcentage_prevision'] >= 75 ? 'warning' : 'danger') }}">
                                    {{ number_format($stats['pourcentage_prevision'], 1) }}%
                                </h4>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Barre de progression -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Progression</span>
                            <span class="badge bg-{{ $stats['status'] === 'Objectif atteint' ? 'success' : ($stats['status'] === 'Bon' ? 'info' : ($stats['status'] === 'Moyen' ? 'warning' : 'secondary')) }}">
                                {{ $stats['status'] }}
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $stats['pourcentage_prevision'] >= 100 ? 'success' : ($stats['pourcentage_prevision'] >= 75 ? 'warning' : 'danger') }}" 
                                 style="width: {{ min($stats['pourcentage_prevision'], 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Aucune prévision associée</strong><br>
                Cette réalisation n'est pas liée à une prévision budgétaire.
            </div>
            @endif

            <!-- Autres réalisations de la commune -->
            @if($autresRealisations->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Autres réalisations de {{ $realisation->commune->nom }} ({{ $realisation->annee_exercice }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Montant</th>
                                    <th class="text-end">Prévision</th>
                                    <th class="text-center">Taux</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($autresRealisations as $autre)
                                <tr>
                                    <td>
                                        {{ is_string($autre['date_realisation']) ? \Carbon\Carbon::parse($autre['date_realisation'])->format('d/m/Y') : $autre['date_realisation']->format('d/m/Y') }}
                                    </td>
                                    <td class="text-end">{{ number_format($autre['montant'], 0, ',', ' ') }} FCFA</td>
                                    <td class="text-end">
                                        {{ $autre['prevision_montant'] ? number_format($autre['prevision_montant'], 0, ',', ' ') . ' FCFA' : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if($autre['pourcentage_prevision'])
                                            <span class="badge bg-{{ $autre['pourcentage_prevision'] >= 100 ? 'success' : ($autre['pourcentage_prevision'] >= 75 ? 'warning' : 'danger') }}">
                                                {{ number_format($autre['pourcentage_prevision'], 1) }}%
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $autre['status'] === 'Objectif atteint' ? 'success' : ($autre['status'] === 'Bon' ? 'info' : ($autre['status'] === 'Moyen' ? 'warning' : 'secondary')) }}">
                                            {{ $autre['status'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('realisations.show', $autre['id']) }}" class="btn btn-sm btn-outline-info">
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
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statut et métriques -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-tachometer-alt"></i> Métriques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-flag fa-2x text-{{ $stats['status'] === 'Objectif atteint' ? 'success' : ($stats['status'] === 'Bon' ? 'info' : ($stats['status'] === 'Moyen' ? 'warning' : 'secondary')) }} mb-2"></i>
                                <h6>Statut</h6>
                                <span class="badge bg-{{ $stats['status'] === 'Objectif atteint' ? 'success' : ($stats['status'] === 'Bon' ? 'info' : ($stats['status'] === 'Moyen' ? 'warning' : 'secondary')) }}">
                                    {{ $stats['status'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparaison départementale -->
            @if($comparaison->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Comparaison départementale
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Commune</th>
                                    <th class="text-center">Taux</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comparaison->take(10) as $comp)
                                <tr class="{{ $comp['est_actuelle'] ? 'table-warning' : '' }}">
                                    <td>
                                        {{ $comp['commune'] }}
                                        @if($comp['est_actuelle'])
                                            <i class="fas fa-arrow-right text-primary"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $comp['taux_realisation'] >= 100 ? 'success' : ($comp['taux_realisation'] >= 75 ? 'warning' : 'danger') }}">
                                            {{ number_format($comp['taux_realisation'], 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Évolution annuelle -->
            @if($evolutionAnnuelle->count() > 1)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line"></i> Évolution annuelle
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" width="400" height="200"></canvas>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
@if($evolutionAnnuelle->count() > 1)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionData = @json($evolutionAnnuelle->values());
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: evolutionData.map(item => item.annee),
            datasets: [{
                label: 'Montant réalisé',
                data: evolutionData.map(item => item.montant_realise),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1
            }, {
                label: 'Prévision',
                data: evolutionData.map(item => item.prevision_totale),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.1
            }]
                </div>
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + 
                                   new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif
@endpush

@endsection --}}



{{-- resources/views/realisations/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails de la Réalisation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Détails de la Réalisation</h3>
                    <div>
                        <a href="{{ route('realisations.edit', $realisation) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('realisations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        {{-- Informations principales --}}
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Informations Générales</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Année d'exercice:</strong></td>
                                            <td>{{ $realisation->annee_exercice }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date de réalisation:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($realisation->date_realisation)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Montant:</strong></td>
                                            <td class="text-primary">
                                                <strong>{{ number_format($realisation->montant, 0, ',', ' ') }} FCFA</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Commune:</strong></td>
                                            <td>{{ $realisation->commune->nom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Département:</strong></td>
                                            <td>{{ $realisation->commune->departement->nom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Région:</strong></td>
                                            <td>{{ $realisation->commune->departement->region->nom }}</td>
                                        </tr>
                                        @if($realisation->prevision)
                                        <tr>
                                            <td><strong>Prévision associée:</strong></td>
                                            <td>{{ number_format($realisation->prevision->montant, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Statistiques de la commune --}}
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Statistiques de la Commune ({{ $realisation->annee_exercice }})</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6>Prévision</h6>
                                                    <h4 class="text-info">{{ number_format($statsCommune['prevision'], 0, ',', ' ') }} FCFA</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6>Total Réalisations</h6>
                                                    <h4 class="text-success">{{ number_format($statsCommune['total_realisations'], 0, ',', ' ') }} FCFA</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6>Taux de Réalisation</h6>
                                                    <h4 class="text-primary">{{ number_format($statsCommune['taux_realisation'], 1) }}%</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6>Évaluation</h6>
                                                    <h4 class="
                                                        @if($statsCommune['evaluation'] == 'Excellent') text-success
                                                        @elseif($statsCommune['evaluation'] == 'Bon') text-info
                                                        @elseif($statsCommune['evaluation'] == 'Moyen') text-warning
                                                        @else text-danger
                                                        @endif
                                                    ">{{ $statsCommune['evaluation'] }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6>Écart</h6>
                                                    <h4 class="{{ $statsCommune['ecart'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $statsCommune['ecart'] >= 0 ? '+' : '' }}{{ number_format($statsCommune['ecart'], 0, ',', ' ') }} FCFA
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6>Nb Réalisations</h6>
                                                    <h4 class="text-dark">{{ $statsCommune['nb_realisations'] }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($realisation->description)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Description</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $realisation->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Évolution de la commune --}}
                    @if($evolutionCommune->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Évolution de la Commune</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Année</th>
                                                    <th>Taux de Réalisation</th>
                                                    <th>Évaluation</th>
                                                    <th>Tendance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($evolutionCommune as $index => $evolution)
                                                <tr>
                                                    <td>{{ $evolution['annee'] }}</td>
                                                    <td>{{ number_format($evolution['pourcentage'], 1) }}%</td>
                                                    <td>
                                                        <span class="badge 
                                                            @if($evolution['evaluation'] == 'Excellent') bg-success
                                                            @elseif($evolution['evaluation'] == 'Bon') bg-info
                                                            @elseif($evolution['evaluation'] == 'Moyen') bg-warning
                                                            @else bg-danger
                                                            @endif
                                                        ">{{ $evolution['evaluation'] }}</span>
                                                    </td>
                                                    <td>
                                                        @if($index > 0)
                                                            @php
                                                                $precedent = $evolutionCommune[$index - 1];
                                                                $tendance = $evolution['pourcentage'] - $precedent['pourcentage'];
                                                            @endphp
                                                            @if($tendance > 0)
                                                                <i class="fas fa-arrow-up text-success"></i>
                                                                <span class="text-success">+{{ number_format($tendance, 1) }}%</span>
                                                            @elseif($tendance < 0)
                                                                <i class="fas fa-arrow-down text-danger"></i>
                                                                <span class="text-danger">{{ number_format($tendance, 1) }}%</span>
                                                            @else
                                                                <i class="fas fa-minus text-muted"></i>
                                                                <span class="text-muted">Stable</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
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

                    {{-- Autres réalisations de la commune --}}
                    @if($autresRealisations->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Autres Réalisations de la Commune ({{ $realisation->annee_exercice }})</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Montant</th>
                                                    <th>Prévision</th>
                                                    <th>Description</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($autresRealisations as $autreRealisation)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($autreRealisation->date_realisation)->format('d/m/Y') }}</td>
                                                    <td>{{ number_format($autreRealisation->montant, 0, ',', ' ') }} FCFA</td>
                                                    <td>
                                                        @if($autreRealisation->prevision)
                                                            {{ number_format($autreRealisation->prevision->montant, 0, ',', ' ') }} FCFA
                                                        @else
                                                            <span class="text-muted">Non liée</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ Str::limit($autreRealisation->description, 50) }}</td>
                                                    <td>
                                                        <a href="{{ route('realisations.show', $autreRealisation) }}" class="btn btn-sm btn-info">
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

                    {{-- Actions --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <a href="{{ route('realisations.edit', $realisation) }}" class="btn btn-warning">
                                                <i class="fas fa-edit"></i> Modifier cette réalisation
                                            </a>
                                            <a href="{{ route('realisations.create') }}" class="btn btn-success">
                                                <i class="fas fa-plus"></i> Nouvelle réalisation
                                            </a>
                                        </div>
                                        <div>
                                            <form method="POST" action="{{ route('realisations.destroy', $realisation) }}" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réalisation ? Cette action est irréversible.')">
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
                        </div>
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
    // Graphique d'évolution (optionnel avec Chart.js)
    const evolutionData = @json($evolutionCommune);
    
    if (evolutionData.length > 1) {
        // Créer un graphique simple d'évolution
        const ctx = document.getElementById('evolutionChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: evolutionData.map(item => item.annee),
                    datasets: [{
                        label: 'Taux de Réalisation (%)',
                        data: evolutionData.map(item => item.pourcentage),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush