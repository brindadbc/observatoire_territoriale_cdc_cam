<!-- Contrôles AJAX pour changement rapide de statut et assignation -->
<div class="d-flex gap-2 align-items-center">
    <!-- Changement de statut -->
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-{{ $receveur->statut === 'Actif' ? 'success' : ($receveur->statut === 'Inactif' ? 'danger' : 'warning') }} dropdown-toggle" 
                type="button" 
                id="statutDropdown{{ $receveur->id }}" 
                data-bs-toggle="dropdown">
            {{ $receveur->statut }}
        </button>
        <ul class="dropdown-menu">
            @foreach(['Actif', 'Inactif', 'En congé', 'Retraité'] as $statut)
                @if($statut !== $receveur->statut)
                    <li>
                        <a class="dropdown-item" 
                           href="#" 
                           onclick="changerStatut({{ $receveur->id }}, '{{ $statut }}')">
                            <span class="badge bg-{{ $statut === 'Actif' ? 'success' : ($statut === 'Inactif' ? 'danger' : 'warning') }} me-2">
                                {{ $statut }}
                            </span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>

    <!-- Assignation de commune -->
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-info dropdown-toggle" 
                type="button" 
                id="communeDropdown{{ $receveur->id }}" 
                data-bs-toggle="dropdown">
            @if($receveur->commune)
                {{ Str::limit($receveur->commune->nom, 15) }}
            @else
                Non assigné
            @endif
        </button>
        <ul class="dropdown-menu" style="max-height: 300px; overflow-y: auto;">
            @if($receveur->commune)
                <li>
                    <a class="dropdown-item text-danger" 
                       href="#" 
                       onclick="assignerCommune({{ $receveur->id }}, null)">
                        <i class="fas fa-times me-2"></i> Désassigner
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
            @endif
            @foreach($communes ?? [] as $commune)
                @if(!$receveur->commune || $commune->id !== $receveur->commune->id)
                    <li>
                        <a class="dropdown-item" 
                           href="#" 
                           onclick="assignerCommune({{ $receveur->id }}, {{ $commune->id }})">
                            <small>
                                {{ $commune->nom }}<br>
                                <span class="text-muted">{{ $commune->departement->nom }}</span>
                            </small>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>

@push('scripts')
<script>
// Fonction pour changer le statut via AJAX
function changerStatut(receveurId, nouveauStatut) {
    if (!confirm(`Confirmer le changement de statut vers "${nouveauStatut}" ?`)) {
        return;
    }

    // Afficher un loader
    const button = document.getElementById(`statutDropdown${receveurId}`);
    const originalText = button.textContent;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mise à jour...';
    button.disabled = true;

    fetch(`/receveurs/${receveurId}/statut`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            statut: nouveauStatut
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour l'interface
            button.textContent = nouveauStatut;
            button.className = `btn btn-sm btn-outline-${getStatutClass(nouveauStatut)} dropdown-toggle`;
            
            // Afficher un message de succès
            showToast('success', data.message);
            
            // Optionnel: recharger la page après 1 seconde
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('error', 'Erreur lors du changement de statut');
        
        // Restaurer l'état original
        button.textContent = originalText;
    })
    .finally(() => {
        button.disabled = false;
    });
}

// Fonction pour assigner une commune via AJAX
function assignerCommune(receveurId, communeId) {
    const action = communeId ? 'assigner à cette commune' : 'désassigner de sa commune actuelle';
    
    if (!confirm(`Confirmer l'action : ${action} ?`)) {
        return;
    }

    // Afficher un loader
    const button = document.getElementById(`communeDropdown${receveurId}`);
    const originalText = button.textContent;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mise à jour...';
    button.disabled = true;

    fetch(`/receveurs/${receveurId}/commune`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            commune_id: communeId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour l'interface
            if (data.commune) {
                button.textContent = data.commune.nom.length > 15 ? 
                                   data.commune.nom.substring(0, 15) + '...' : 
                                   data.commune.nom;
            } else {
                button.textContent = 'Non assigné';
            }
            
            // Afficher un message de succès
            showToast('success', data.message);
            
            // Optionnel: recharger la page après 1 seconde
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('error', 'Erreur lors de l\'assignation');
        
        // Restaurer l'état original
        button.textContent = originalText;
    })
    .finally(() => {
        button.disabled = false;
    });
}

// Fonction utilitaire pour obtenir la classe CSS du statut
function getStatutClass(statut) {
    switch(statut) {
        case 'Actif': return 'success';
        case 'Inactif': return 'danger';
        default: return 'warning';
    }
}

// Fonction pour afficher des notifications toast
function showToast(type, message) {
    // Créer un toast Bootstrap si disponible
    if (typeof bootstrap !== 'undefined') {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        const toastId = 'toast-' + Date.now();
        
        const toastHtml = `
            <div id="${toastId}" class="toast" role="alert">
                <div class="toast-header">
                    <i class="fas fa-${type === 'success' ? 'check-circle text-success' : 'exclamation-circle text-danger'} me-2"></i>
                    <strong class="me-auto">${type === 'success' ? 'Succès' : 'Erreur'}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
        
        // Supprimer le toast après qu'il soit caché
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    } else {
        // Fallback avec alert si Bootstrap n'est pas disponible
        alert(message);
    }
}

// Créer le conteneur de toast s'il n'existe pas
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1055';
    document.body.appendChild(container);
    return container;
}
</script>
@endpush