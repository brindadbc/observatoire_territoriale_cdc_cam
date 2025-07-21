 @extends('layouts.app')

@section('title', 'Nouveau Département - Observatoire des Collectivités')
@section('page-title', 'Créer un Département')

@section('content')
<div class="departement-create">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('dashboard.index') }}">Tableau de bord</a>
        <span>/</span>
        <a href="{{ route('departements.index') }}">Départements</a>
        <span>/</span>
        <span>Nouveau</span>
    </div>

    <!-- Messages d'erreur -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Erreurs de validation :</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Formulaire -->
    <div class="form-container">
        <div class="form-header">
            <h2>Informations du département</h2>
            <p>Renseignez les informations du nouveau département</p>
        </div>

        <form action="{{ route('departements.store') }}" method="POST" class="departement-form">
            @csrf
            
            <div class="form-grid">
                <!-- Informations principales -->
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Informations principales</h3>
                    
                    <div class="form-group">
                        <label for="nom" class="required">Nom du département</label>
                        <input type="text" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom') }}" 
                               required
                               class="form-control @error('nom') is-invalid @enderror"
                               placeholder="Ex: Fako">
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                  

                    <div class="form-group">
                        <label for="region_id" class="required">Région</label>
                        <select id="region_id" 
                                name="region_id" 
                                required
                                class="form-control @error('region_id') is-invalid @enderror">
                            <option value="">Sélectionnez une région</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                    {{ $region->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('region_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

    

            <!-- Actions -->
             <div class="form-actions">
                <a href="{{ route('departements.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.departement-create {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.form-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
}

.form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    text-align: center;
}

.form-header h2 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 600;
}

.form-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 16px;
}

.departement-form {
    padding: 40px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
}

.form-section {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 10px;
    border: 2px solid #e9ecef;
}

.form-section h3 {
    margin: 0 0 25px 0;
    color: #495057;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 15px;
    border-bottom: 2px solid #dee2e6;
}

.form-section h3 i {
    color: #667eea;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.form-group label.required::after {
    content: " *";
    color: #e74c3c;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control.is-invalid {
    border-color: #e74c3c;
}

.invalid-feedback {
    display: block;
    color: #e74c3c;
    font-size: 12px;
    margin-top: 5px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px solid #e9ecef;
}

.btn {
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.alert {
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 2px solid #f5c6cb;
}

.alert ul {
    margin: 0;
    padding-left: 20px;
}

.alert li {
    margin-bottom: 5px;
}

.breadcrumb {
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}

.breadcrumb a {
    color: #667eea;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb span {
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-génération du code basé sur le nom
    const nomInput = document.getElementById('nom');
    const codeInput = document.getElementById('code');
    
    nomInput.addEventListener('input', function() {
        if (!codeInput.value) {
            const nom = this.value.trim();
            if (nom) {
                // Prendre les 2-3 premières lettres et les mettre en majuscules
                const code = nom.substring(0, 3).toUpperCase();
                codeInput.value = code;
            }
        }
    });
    
    // Validation en temps réel
    const form = document.querySelector('.departement-form');
    const inputs = form.querySelectorAll('.form-control');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required');
        
        // Supprimer les classes d'erreur existantes
        field.classList.remove('is-invalid');
        
        if (isRequired && !value) {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Validation spécifique pour le code
        if (field.name === 'code' && value && value.length > 10) {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Validation pour les nombres
        if (field.type === 'number' && value && parseFloat(value) < 0) {
            field.classList.add('is-invalid');
            return false;
        }
        
        return true;
    }
    
    // Validation avant soumission
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Veuillez corriger les erreurs dans le formulaire.');
        }
    });
});
</script>
@endpush
@endsection 


{{-- @extends('layouts.app')

@section('title', 'Importer Départements - Observatoire des Collectivités')
@section('page-title', 'Importer tous les Départements')

@section('content')
<div class="import-departements">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('dashboard.index') }}">Tableau de bord</a>
        <span>/</span>
        <a href="{{ route('departements.index') }}">Départements</a>
        <span>/</span>
        <span>Importation</span>
    </div>

    <!-- Messages -->
    <div id="message-container" style="display: none;"></div>

    <!-- Formulaire d'importation -->
    <div class="import-container">
        <div class="import-header">
            <h2><i class="fas fa-upload"></i> Importation des Départements</h2>
            <p>Importer tous les 58 départements du Cameroun en une seule fois</p>
        </div>

        <div class="import-content">
            <div class="import-info">
                <h3><i class="fas fa-info-circle"></i> Information</h3>
                <p>Cette action va créer automatiquement tous les départements du Cameroun dans votre base de données.</p>
                <ul>
                    <li>✓ 58 départements seront créés</li>
                    <li>✓ Répartis dans les 10 régions</li>
                    <li>✓ Les doublons seront automatiquement ignorés</li>
                    <li>✓ Opération sécurisée avec transaction</li>
                </ul>
            </div>

            <div class="import-actions">
                <button type="button" id="import-btn" class="btn btn-primary">
                    <i class="fas fa-download"></i>
                    Importer tous les départements
                </button>
                
                <a href="{{ route('departements.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
            </div>

            <!-- Barre de progression -->
            <div id="progress-container" style="display: none;" class="progress-container">
                <div class="progress-bar">
                    <div id="progress-fill" class="progress-fill"></div>
                </div>
                <div id="progress-text" class="progress-text">Importation en cours...</div>
            </div>

            <!-- Résultats -->
            <div id="results-container" style="display: none;" class="results-container">
                <h3><i class="fas fa-check-circle"></i> Résultats de l'importation</h3>
                <div id="results-content"></div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.import-departements {
    padding: 20px;
    max-width: 800px;
    margin: 0 auto;
}

.import-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
}

.import-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 30px;
    text-align: center;
}

.import-header h2 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 600;
}

.import-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 16px;
}

.import-content {
    padding: 40px;
}

.import-info {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 30px;
    border-left: 4px solid #28a745;
}

.import-info h3 {
    margin: 0 0 15px 0;
    color: #28a745;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.import-info ul {
    margin: 15px 0 0 0;
    padding-left: 20px;
}

.import-info li {
    margin-bottom: 8px;
    color: #495057;
}

.import-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 30px;
}

.btn {
    padding: 15px 30px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.progress-container {
    margin: 30px 0;
}

.progress-bar {
    width: 100%;
    height: 20px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #28a745, #20c997);
    width: 0%;
    transition: width 0.3s ease;
}

.progress-text {
    text-align: center;
    font-weight: 600;
    color: #495057;
}

.results-container {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 10px;
    padding: 20px;
    margin-top: 30px;
}

.results-container h3 {
    margin: 0 0 15px 0;
    color: #155724;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border: 1px solid #dee2e6;
}

.stat-number {
    font-size: 24px;
    font-weight: bold;
    color: #28a745;
    margin-bottom: 5px;
}

.stat-label {
    color: #6c757d;
    font-size: 14px;
}

.breadcrumb {
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}

.breadcrumb a {
    color: #28a745;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb span {
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const importBtn = document.getElementById('import-btn');
    const progressContainer = document.getElementById('progress-container');
    const progressFill = document.getElementById('progress-fill');
    const progressText = document.getElementById('progress-text');
    const resultsContainer = document.getElementById('results-container');
    const resultsContent = document.getElementById('results-content');
    const messageContainer = document.getElementById('message-container');

    importBtn.addEventListener('click', function() {
        // Confirmer l'action
        if (!confirm('Êtes-vous sûr de vouloir importer tous les départements ? Cette action ne peut pas être annulée.')) {
            return;
        }

        // Désactiver le bouton et afficher la progression
        importBtn.disabled = true;
        progressContainer.style.display = 'block';
        resultsContainer.style.display = 'none';
        messageContainer.style.display = 'none';

        // Simuler la progression
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += 10;
            progressFill.style.width = progress + '%';
            progressText.textContent = `Importation en cours... ${progress}%`;
            
            if (progress >= 90) {
                clearInterval(progressInterval);
            }
        }, 200);

        // Effectuer la requête AJAX
        fetch('{{ route("departements.import") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(progressInterval);
            progressFill.style.width = '100%';
            progressText.textContent = 'Importation terminée !';

            setTimeout(() => {
                progressContainer.style.display = 'none';
                
                if (data.success) {
                    showResults(data);
                } else {
                    showMessage('danger', data.message || 'Erreur lors de l\'importation');
                }
                
                importBtn.disabled = false;
            }, 1000);
        })
        .catch(error => {
            clearInterval(progressInterval);
            progressContainer.style.display = 'none';
            showMessage('danger', 'Erreur de connexion lors de l\'importation');
            importBtn.disabled = false;
        });
    });

    function showResults(data) {
        const stats = data.statistics;
        
        let html = `
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">${stats.created}</div>
                    <div class="stat-label">Départements créés</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${stats.skipped}</div>
                    <div class="stat-label">Départements ignorés</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${stats.errors}</div>
                    <div class="stat-label">Erreurs</div>
                </div>
            </div>
        `;

        if (stats.errors > 0 && data.errors.length > 0) {
            html += `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Erreurs rencontrées :</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            ${data.errors.map(error => `<li>${error}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            `;
        }

        resultsContent.innerHTML = html;
        resultsContainer.style.display = 'block';
    }

    function showMessage(type, message) {
        messageContainer.innerHTML = `
            <div class="alert alert-${type}">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'danger' ? 'times' : 'exclamation-triangle'}"></i>
                <div>${message}</div>
            </div>
        `;
        messageContainer.style.display = 'block';
    }
});
</script>
@endpush
@endsection --}}