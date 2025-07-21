<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">
            <i class="fas fa-table"></i> Liste des prévisions ({{ $previsions->total() }})
        </h6>
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                    data-bs-toggle="dropdown">
                <i class="fas fa-download"></i> Exporter
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('previsions.export', array_merge(request()->query(), ['format' => 'excel'])) }}">
                        <i class="fas fa-file-excel text-success"></i> Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('previsions.export', array_merge(request()->query(), ['format' => 'pdf'])) }}">
                        <i class="fas fa-file-pdf text-danger"></i> PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('previsions.export', array_merge(request()->query(), ['format' => 'csv'])) }}">
                        <i class="fas fa-file-csv text-info"></i> CSV
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body p-0">
        @if($previsions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'commune.nom', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none">
                                    Commune
                                    @if(request('sort_by') === 'commune.nom')
                                        <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Département</th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'annee_exercice', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none">
                                    Année
                                    @if(request('sort_by') === 'annee_exercice')
                                        <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-end">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'montant', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none">
                                    Montant Prévu
                                    @if(request('sort_by') === 'montant')
                                        <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-end">Montant Réalisé</th>
                            <th class="text-center">Taux</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previsions as $prevision)
                            <tr>
                                <td>
                                    <strong>{{ $prevision->commune->nom }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $prevision->commune->code }}</small>
                                </td>
                                <td>
                                    {{ $prevision->commune->departement->nom }}
                                    <br>
                                    <small class="text-muted">{{ $prevision->commune->departement->region->nom }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $prevision->annee_exercice }}</span>
                                </td>
                                <td class="text-end">
                                    <strong>{{ number_format($prevision->montant, 0, ',', ' ') }}</strong>
                                    <small class="text-muted d-block">FCFA</small>
                                </td>
                                <td class="text-end">
                                    <strong>{{ number_format($prevision->montant_realise, 0, ',', ' ') }}</strong>
                                    <small class="text-muted d-block">FCFA</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $prevision->taux_realisation >= 80 ? 'bg-success' : ($prevision->taux_realisation >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ number_format($prevision->taux_realisation, 1) }}%
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('previsions.show', $prevision) }}" 
                                           class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('previsions.edit', $prevision) }}" 
                                           class="btn btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmDelete({{ $prevision->id }})" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucune prévision trouvée</h5>
                <p class="text-muted">Essayez de modifier vos critères de recherche ou créez une nouvelle prévision.</p>
                <a href="{{ route('previsions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Créer une prévision
                </a>
            </div>
        @endif
    </div>
    
    @if($previsions->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Affichage de {{ $previsions->firstItem() }} à {{ $previsions->lastItem() }} 
                    sur {{ $previsions->total() }} résultats
                </div>
                <div>
                    {{ $previsions->links() }}
                </div>
            </div>
        </div>
    @endif
</div>