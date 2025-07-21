@extends('layouts.app')

@section('title', 'Détails Dette Salariale #' . $detteSalariale->id)

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Dette Salariale #{{ $detteSalariale->id }}</h1>
            <p class="text-muted">{{ $detteSalariale->commune->nom }} - {{ \Carbon\Carbon::parse($detteSalariale->date_evaluation)->format('d/m/Y') }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('dettes-salariale.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('dettes-salariale.edit', $detteSalariale) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            {{-- <button type="button" class="btn btn-danger" onclick="confirmerSuppression()">
                <i class="fas fa-trash"></i> Supprimer
            </button> --}}
         <form method="POST" action="{{ route('dettes-salariale.destroy', $detteSalariale) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Vous êtes sur le point de supprimer définitivement cette dette salariale ;Êtes-vous sûr de vouloir supprimer cette dette ?')">
                            <i class="fas fa-trash"></i> Supprimer definitivement
                        </button>
                    </form>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <!-- Carte principale -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Informations de la Dette
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold">Commune :</td>
                                    <td>{{ $detteSalariale->commune->nom }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Code Commune :</td>
                                    <td>{{ $detteSalariale->commune->code }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Département :</td>
                                    <td>{{ $detteSalariale->commune->departement->nom }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Région :</td>
                                    <td>{{ $detteSalariale->commune->departement->region->nom }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold">Date d'évaluation :</td>
                                    <td>{{ \Carbon\Carbon::parse($detteSalariale->date_evaluation)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Ancienneté :</td>
                                    <td>{{ \Carbon\Carbon::parse($detteSalariale->date_evaluation)->diffForHumans() }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Créé le :</td>
                                    <td>{{ $detteSalariale->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Modifié le :</td>
                                    <td>{{ $detteSalariale->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Montant et niveau de criticité -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave"></i> Montant et Évaluation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="display-4 text-primary mb-0">
                                {{ number_format($detteSalariale->montant, 0, ',', ' ') }}
                            </h2>
                            <p class="text-muted">francs CFA</p>
                        </div>
                        <div class="col-md-6 text-right">
                            @php
                                $niveau = 'success';
                                $texte = 'Faible';
                                $icone = 'fa-check-circle';
                                if($detteSalariale->montant >= 50000000) { 
                                    $niveau = 'danger'; 
                                    $texte = 'Critique'; 
                                    $icone = 'fa-exclamation-triangle';
                                }
                                elseif($detteSalariale->montant >= 10000000) { 
                                    $niveau = 'warning'; 
                                    $texte = 'Élevé'; 
                                    $icone = 'fa-exclamation-circle';
                                }
                                elseif($detteSalariale->montant >= 1000000) { 
                                    $niveau = 'info'; 
                                    $texte = 'Moyen'; 
                                    $icone = 'fa-info-circle';
                                }
                            @endphp
                            <h4>
                                <span class="badge badge-{{ $niveau }} badge-lg">
                                    <i class="fas {{ $icone }}"></i> {{ $texte }}
                                </span>
                            </h4>
                            <p class="text-muted">Niveau de criticité</p>
                        </div>
                    </div>
                    
                    <!-- Analyse salariale -->
                    @if(isset($analyseSalariale))
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-info">{{ $analyseSalariale['effectif_estime'] }}</h5>
                                <small class="text-muted">Effectif estimé</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-warning">{{ number_format($analyseSalariale['dette_par_agent'], 0, ',', ' ') }}</h5>
                                <small class="text-muted">FCFA par agent</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                @php
                                    $tendanceClass = $analyseSalariale['tendance'] == 'hausse' ? 'text-danger' : 
                                                   ($analyseSalariale['tendance'] == 'baisse' ? 'text-success' : 'text-info');
                                    $tendanceIcon = $analyseSalariale['tendance'] == 'hausse' ? 'fa-arrow-up' : 
                                                  ($analyseSalariale['tendance'] == 'baisse' ? 'fa-arrow-down' : 'fa-minus');
                                @endphp
                                <h5 class="{{ $tendanceClass }}">
                                    <i class="fas {{ $tendanceIcon }}"></i> {{ ucfirst($analyseSalariale['tendance']) }}
                                </h5>
                                <small class="text-muted">Tendance</small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recommandations -->
            @if(isset($analyseSalariale['recommandations']) && count($analyseSalariale['recommandations']) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb"></i> Recommandations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($analyseSalariale['recommandations'] as $recommandation)
                        <div class="list-group-item border-0 px-0">
                            <i class="fas fa-arrow-right text-primary mr-2"></i>
                            {{ $recommandation }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Historique de la commune -->
            @if($historique->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Historique des Dettes Salariales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Évolution</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($historique as $dette)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($dette->date_evaluation)->format('d/m/Y') }}</td>
                                    <td>{{ number_format($dette->montant, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        @php
                                            $evolution = (($dette->montant - $detteSalariale->montant) / $detteSalariale->montant) * 100;
                                        @endphp
                                        @if($evolution > 0)
                                            <span class="text-danger">
                                                <i class="fas fa-arrow-up"></i> +{{ number_format($evolution, 1) }}%
                                            </span>
                                        @elseif($evolution < 0)
                                            <span class="text-success">
                                                <i class="fas fa-arrow-down"></i> {{ number_format($evolution, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-minus"></i> Stable
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('dettes-salariale.show', $dette) }}" 
                                           class="btn btn-sm btn-outline-info">
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

            <!-- Comparaison avec communes similaires -->
            @if(isset($analyseSalariale['communes_similaires']) && $analyseSalariale['communes_similaires']->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-balance-scale"></i> Comparaison avec Communes Similaires
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Commune</th>
                                    <th>Montant</th>
                                    <th>Comparaison</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analyseSalariale['communes_similaires'] as $commune)
                                <tr>
                                    <td>{{ $commune->commune->nom }}</td>
                                    <td>{{ number_format($commune->montant, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        @php
                                            $ratio = ($commune->montant / $detteSalariale->montant) * 100;
                                        @endphp
                                        @if($ratio > 100)
                                            <span class="text-danger">{{ number_format($ratio, 0) }}% plus élevé</span>
                                        @elseif($ratio < 100)
                                            <span class="text-success">{{ number_format(100-$ratio, 0) }}% plus faible</span>
                                        @else
                                            <span class="text-muted">Équivalent</span>
                                        @endif
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
        <div class="col-md-4">
            <!-- Autres types de dettes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-pie"></i> Autres Dettes de la Commune
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-2">
                                <h6 class="text-primary">{{ number_format($autresDettes['cnps'], 0, ',', ' ') }}</h6>
                                <small class="text-muted">CNPS</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-2">
                                <h6 class="text-success">{{ number_format($autresDettes['feicom'], 0, ',', ' ') }}</h6>
                                <small class="text-muted">FEICOM</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h6 class="text-warning">{{ number_format($autresDettes['fiscale'], 0, ',', ' ') }}</h6>
                                <small class="text-muted">Fiscale</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h6 class="text-danger">{{ number_format($detteSalariale->montant, 0, ',', ' ') }}</h6>
                                <small class="text-muted">Salariale</small>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $totalDettes = $autresDettes['cnps'] + $autresDettes['feicom'] + $autresDettes['fiscale'] + $detteSalariale->montant;
                    @endphp
                    
                    @if($totalDettes > 0)
                    <hr>
                    <div class="text-center">
                        <h5 class="text-info">{{ number_format($totalDettes, 0, ',', ' ') }} FCFA</h5>
                        <small class="text-muted">Total des dettes</small>
                    </div>
                    <div class="progress mt-2" style="height: 20px;">
                        @php
                            $pctSalariale = ($detteSalariale->montant / $totalDettes) * 100;
                            $pctCnps = ($autresDettes['cnps'] / $totalDettes) * 100;
                            $pctFeicom = ($autresDettes['feicom'] / $totalDettes) * 100;
                            $pctFiscale = ($autresDettes['fiscale'] / $totalDettes) * 100;
                        @endphp
                        <div class="progress-bar bg-danger" style="width: {{ $pctSalariale }}%" title="Salariale: {{ number_format($pctSalariale, 1) }}%"></div>
                        <div class="progress-bar bg-primary" style="width: {{ $pctCnps }}%" title="CNPS: {{ number_format($pctCnps, 1) }}%"></div>
                        <div class="progress-bar bg-success" style="width: {{ $pctFeicom }}%" title="FEICOM: {{ number_format($pctFeicom, 1) }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ $pctFiscale }}%" title="Fiscale: {{ number_format($pctFiscale, 1) }}%"></div>
                    </div>
                    <small class="text-muted d-block mt-1">
                        Dette salariale représente {{ number_format($pctSalariale, 1) }}% du total
                    </small>
                    @endif
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt"></i> Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('dettes-salariale.create') }}?commune_id={{ $detteSalariale->commune_id }}" 
                           class="btn btn-outline-primary btn-block">
                            <i class="fas fa-plus"></i> Nouvelle Dette pour cette Commune
                        </a>
                        
                        <a href="{{ route('dettes-salariale.index') }}?commune_id={{ $detteSalariale->commune_id }}" 
                           class="btn btn-outline-info btn-block">
                            <i class="fas fa-list"></i> Toutes les Dettes de la Commune
                        </a>
                        
                        <button class="btn btn-outline-success btn-block" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimer cette Fiche
                        </button>
                        
                        <button class="btn btn-outline-warning btn-block" onclick="exporterPDF()">
                            <i class="fas fa-file-pdf"></i> Exporter en PDF
                        </button>
                    </div>
                </div>
            </div>

            <!-- Évolution graphique -->
            @if(isset($analyseSalariale['evolution_trois_ans']) && $analyseSalariale['evolution_trois_ans']->count() > 1)
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> Évolution sur 3 ans
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" height="200"></canvas>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- <!-- Modal de confirmation de suppression -->
<div class="modal fade" id="confirmationSuppressionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Attention !</strong> Vous êtes sur le point de supprimer définitivement cette dette salariale.
                </div>
                <p><strong>Commune :</strong> {{ $detteSalariale->commune->nom }}</p>
                <p><strong>Montant :</strong> {{ number_format($detteSalariale->montant, 0, ',', ' ') }} FCFA</p>
                <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($detteSalariale->date_evaluation)->format('d/m/Y') }}</p>
                <p class="text-danger"><strong>Cette action est irréversible.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form method="POST" action="{{ route('dettes-salariale.destroy', $detteSalariale) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer Définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div> --}}

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function confirmerSuppression() {
    $('#confirmationSuppressionModal').modal('show');
}

function exporterPDF() {
    // Implémenter l'export PDF
    alert('Fonctionnalité d\'export PDF à implémenter');
}

// Graphique d'évolution
@if(isset($analyseSalariale['evolution_trois_ans']) && $analyseSalariale['evolution_trois_ans']->count() > 1)
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    const donnees = @json($analyseSalariale['evolution_trois_ans']);
    
    const labels = donnees.map(d => new Date(d.date_evaluation).toLocaleDateString('fr-FR'));
    const montants = donnees.map(d => d.montant);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Montant (FCFA)',
                data: montants,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Montant: ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                        }
                    }
                }
            }
        }
    });
});
@endif

// Style d'impression
const style = document.createElement('style');
style.textContent = `
    @media print {
        .btn, .card-header, .modal { display: none !important; }
        .card { border: 1px solid #000 !important; box-shadow: none !important; }
        body { font-size: 12px; }
        .display-4 { font-size: 2rem !important; }
    }
`;
document.head.appendChild(style);
</script>
@endsection