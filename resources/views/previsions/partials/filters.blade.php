<div class="card mb-4">
    <div class="card-header">
        <h6 class="card-title mb-0">
            <i class="fas fa-filter"></i> Filtres et recherche
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('previsions.index') }}" class="row g-3">
            <!-- Recherche textuelle -->
            <div class="col-md-3">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" name="search" id="search" class="form-control" 
                       placeholder="Nom ou code commune..." 
                       value="{{ request('search') }}">
            </div>

            <!-- Année -->
            <div class="col-md-2">
                <label for="annee" class="form-label">Année</label>
                <select name="annee" id="annee" class="form-select">
                    @foreach($anneesDisponibles as $anneeDisp)
                        <option value="{{ $anneeDisp }}" {{ $annee == $anneeDisp ? 'selected' : '' }}>
                            {{ $anneeDisp }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Département -->
            <div class="col-md-3">
                <label for="departement_id" class="form-label">Département</label>
                <select name="departement_id" id="departement_id" class="form-select">
                    <option value="">Tous les départements</option>
                    @foreach($departements as $departement)
                        <option value="{{ $departement->id }}" 
                                {{ $departementId == $departement->id ? 'selected' : '' }}>
                            {{ $departement->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Commune -->
            <div class="col-md-3">
                <label for="commune_id" class="form-label">Commune</label>
                <select name="commune_id" id="commune_id" class="form-select">
                    <option value="">Toutes les communes</option>
                    @foreach($communes as $commune)
                        <option value="{{ $commune->id }}" 
                                {{ $communeId == $commune->id ? 'selected' : '' }}>
                            {{ $commune->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Boutons -->
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('previsions.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>






