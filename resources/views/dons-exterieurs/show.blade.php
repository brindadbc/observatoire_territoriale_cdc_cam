<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Don Extérieur</title>
    <style>
        :root {
            --primary-color: #3490dc;
            --secondary-color: #6c757d;
            --success-color: #38c172;
            --danger-color: #e3342f;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: var(--light-bg);
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: var(--primary-color);
            margin-bottom: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .detail-card {
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            margin-bottom: 30px;
            background-color: #f8fafc;
            border-radius: 0 4px 4px 0;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }
        
        .detail-label {
            font-weight: 600;
            min-width: 200px;
            color: var(--secondary-color);
        }
        
        .detail-value {
            flex: 1;
        }
        
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .action-buttons {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2779bd;
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('dons-exterieurs.index') }}" class="back-link">&larr; Retour à la liste</a>
        
        <h1>Détails du Don Extérieur</h1>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="detail-card">
            <div class="detail-row">
                <div class="detail-label">Donateur:</div>
                <div class="detail-value">{{ $donExterieur->donateur }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Montant:</div>
                <div class="detail-value">{{ number_format($donExterieur->montant, 2, ',', ' ') }} €</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Date de réception:</div>
                <div class="detail-value">{{ $donExterieur->date_reception->format('d/m/Y') }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Description:</div>
                <div class="detail-value">{{ $donExterieur->description ?? 'Non spécifié' }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Statut:</div>
                <div class="detail-value">
                    @php
                        $statusClasses = [
                            'en_attente' => 'status-pending',
                            'confirme' => 'status-confirmed',
                            'rejete' => 'status-rejected'
                        ];
                    @endphp
                    <span class="status {{ $statusClasses[$donExterieur->statut] }}">
                        {{ ucfirst(str_replace('_', ' ', $donExterieur->statut)) }}
                    </span>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Date de création:</div>
                <div class="detail-value">{{ $donExterieur->created_at->format('d/m/Y H:i') }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Dernière modification:</div>
                <div class="detail-value">{{ $donExterieur->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="{{ route('dons-exterieurs.edit', $donExterieur->id) }}" class="btn btn-primary">Modifier</a>
            
            <form action="{{ route('dons-exterieurs.destroy', $donExterieur->id) }}" method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Supprimer</button>
            </form>
            
            <a href="{{ route('dons-exterieurs.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>
    </div>

    <script>
        function confirmDelete() {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce don ? Cette action est irréversible.')) {
                document.getElementById('deleteForm').submit();
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Animation pour l'affichage des détails
            const detailCard = document.querySelector('.detail-card');
            detailCard.style.opacity = '0';
            detailCard.style.transform = 'translateY(20px)';
            detailCard.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            
            setTimeout(() => {
                detailCard.style.opacity = '1';
                detailCard.style.transform = 'translateY(0)';
            }, 100);
            
            // Gestion des messages flash
            const alert = document.querySelector('.alert');
            if (alert) {
                setTimeout(() => {
                    alert.style.transition = 'opacity 1s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 1000);
                }, 5000);
            }
        });
    </script>
</body>
</html>