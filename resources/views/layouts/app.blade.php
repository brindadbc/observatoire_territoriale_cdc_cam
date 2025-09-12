<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Observatoire des Collectivités Territoriales')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
    
    <style>
        /* Variables CSS */
        :root {
            --primary-color: #2c5282;
            --secondary-color: #4299e1;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-color: #e9ecef;
            --text-muted: #6c757d;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.15);
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }

        /* Styles globaux */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 300px;
            background: linear-gradient(180deg, #1a202c 0%, #2d3748 100%);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 25px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background-color: rgba(0,0,0,0.2);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            border: 2px solid #fff;
        }

        .logo-text h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 3px;
            color: #fff;
        }

        .logo-text p {
            font-size: 12px;
            color: #a0aec0;
            font-weight: 300;
            line-height: 1.3;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-section {
            margin-bottom: 25px;
        }

        .menu-section h4 {
            font-size: 11px;
            color: #3276edf5;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 15px;
            padding: 0 25px;
        }

        .menu-section ul {
            list-style: none;
        }

        .menu-section li {
            margin-bottom: 2px;
        }

        .menu-section a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: #cbd5e0;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            position: relative;
        }

        .menu-section a:hover {
            background-color: rgba(66, 153, 225, 0.1);
            border-left-color: #4299e1;
            color: #fff;
        }

        .menu-section li.active a {
            background-color: rgba(66, 153, 225, 0.15);
            border-left-color: #4299e1;
            color: #fff;
            font-weight: 600;
        }

        .menu-section a i {
            width: 20px;
            margin-right: 15px;
            font-size: 16px;
            text-align: center;
        }

        /* Sous-menus */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background-color: rgba(0,0,0,0.2);
        }

        .submenu.show {
            max-height: 500px;
        }

        .submenu a {
            padding: 10px 25px 10px 60px;
            font-size: 14px;
            border-left: none;
        }

        .submenu a:hover {
            background-color: rgba(66, 153, 225, 0.15);
        }

        .menu-toggle {
            cursor: pointer;
        }

        .menu-toggle .fa-chevron-right {
            transition: transform 0.3s ease;
            margin-left: auto;
        }

        .menu-toggle.active .fa-chevron-right {
            transform: rotate(90deg);
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            margin-left: 300px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .header {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #2c5282;
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-box {
            position: relative;
        }

        .search-input {
            padding: 10px 40px 10px 15px;
            border: 1px solid #ddd;
            border-radius: 25px;
            width: 280px;
            font-size: 14px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #4299e1;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
        }

        .notifications, .settings {
            width: 40px;
            height: 40px;
            background-color: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .notifications:hover, .settings:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }

        .page-content {
            padding: 30px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                transition: width 0.3s ease;
            }
            
            .sidebar:hover {
                width: 300px;
            }
            
            .sidebar:not(:hover) .logo-text,
            .sidebar:not(:hover) .menu-section h4,
            .sidebar:not(:hover) .menu-section a span {
                opacity: 0;
                visibility: hidden;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .search-input {
                width: 150px;
            }
        }

        /* ANIMATIONS */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="{{ asset('img/image.png') }}" alt="Cameroun">
                    <div class="logo-text">
                        <h3>Observatoire des Collectivités</h3>
                        <p>Territoriales décentralisées<br>République du Cameroun</p>
                    </div>
                </div>
            </div>

            <div class="sidebar-menu">
                <!-- TABLEAU DE BORD -->
                <div class="menu-section">
                    <h4>TABLEAU DE BORD</h4>
                    <ul>
                        <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard.index') }}">
                                <i class="fas fa-chart-line"></i>
                                <span>Vue d'ensemble</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('indicateurs*') ? 'active' : '' }}">
                            {{-- <a href="{{ route('indicateurs.index') }}"> --}}
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Indicateurs clés</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- TERRITOIRE -->
                <div class="menu-section">
                    <h4>TERRITOIRE</h4>
                    <ul>
                        <li class="{{ request()->is('regions*') ? 'active' : '' }}">
                            <a href="{{ route('regions.index') }}">
                                <i class="fas fa-map"></i>
                                <span>Régions (10)</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('departements*') ? 'active' : '' }}">
                            <a href="{{ route('departements.index') }}">
                                <i class="fas fa-map-marked-alt"></i>
                                <span>Départements (58)</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('communes*') ? 'active' : '' }}">
                            <a href="{{ route('communes.index') }}">
                                <i class="fas fa-city"></i>
                                <span>Communes (384)</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- BUDGET ANNUEL -->
                <div class="menu-section">
                    <h4>BUDGET ANNUEL</h4>
                    <ul>
                        <!-- RESSOURCES -->
                        <li>
                            <a href="javascript:void(0)" class="menu-toggle" onclick="toggleSubmenu('ressources')">
                                <i class="fas fa-coins"></i>
                                <span>Ressources</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <ul class="submenu" id="ressources">
                                <li class="{{ request()->is('ressources-transferees*') ? 'active' : '' }}">
                                    {{-- <a href="{{ route('ressources-transferees.index') }}"> --}}
                                        <span>Ressources transférées État</span>
                                    </a>
                                </li>
                                <li class="{{ request()->is('ressources-propres*') ? 'active' : '' }}">
                                    <a href="{{ route('ressources-commune.index') }}">
                                        <span>Ressources propres</span>
                                    </a>
                                </li>
                                <li class="{{ request()->is('donations-exterieures*') ? 'active' : '' }}">
                                    {{-- <a href="{{ route('donations-exterieures.index') }}"> --}}
                                        <span>Donations extérieures</span>
                                    </a>
                                </li>
                                <li class="{{ request()->is('autres-ressources*') ? 'active' : '' }}">
                                    {{-- <a href="{{ route('autres-ressources.index') }}"> --}}
                                        <span>Autres ressources</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- EMPLOIS -->
                        <li>
                            <a href="javascript:void(0)" class="menu-toggle" onclick="toggleSubmenu('emplois')">
                                <i class="fas fa-tasks"></i>
                                <span>Realisations</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <ul class="submenu" id="emplois">
                                <li class="{{ request()->is('infrastructures*') ? 'active' : '' }}">
                                    {{-- <a href="{{ route('infrastructures.index') }}"> --}}
                                        <span>Infrastructures</span>
                                    </a>
                                </li>
                                <li class="{{ request()->is('equipements*') ? 'active' : '' }}">
                                    {{-- <a href="{{ route('equipements.index') }}"> --}}
                                        <span>Équipements</span>
                                    </a>
                                </li>
                                <li class="{{ request()->is('services-sociaux*') ? 'active' : '' }}">
                                    {{-- <a href="{{ route('services-sociaux.index') }}"> --}}
                                        <span>Services sociaux de base</span>
                                    </a>
                                </li>
                                <li class="{{ request()->is('fonctionnement*') ? 'active' : '' }}">
                                    {{-- <a href="{{ route('fonctionnement.index') }}"> --}}
                                        <span>Autres realisation</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- SUIVI BUDGÉTAIRE -->
                        <li class="{{ request()->is('execution-budgetaire*') ? 'active' : '' }}">
                            {{-- <a href="{{ route('execution-budgetaire.index') }}"> --}}
                                <i class="fas fa-chart-pie"></i>
                                <span>Exécution budgétaire</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- GOUVERNANCE -->
                <div class="menu-section">
                    <h4>GOUVERNANCE</h4>
                    <ul>
                        <li class="{{ request()->is('ordonnateurs*') ? 'active' : '' }}">
                            <a href="{{ route('ordonnateurs.index') }}">
                                <i class="fas fa-user-tie"></i>
                                <span>Ordonnateurs</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('receveurs*') ? 'active' : '' }}">
                            <a href="{{ route('receveurs.index') }}">
                                <i class="fas fa-user-check"></i>
                                <span>Receveurs municipaux</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('depot-comptes*') ? 'active' : '' }}">
                            <a href="{{ route('depot-comptes.index') }}">
                                <i class="fas fa-file-invoice"></i>
                                <span>Dépôts de comptes</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- ENDETTEMENT -->
                <div class="menu-section">
                    <h4>ENDETTEMENT</h4>
                    <ul>
                        <li class="{{ request()->is('dettes-cnps*') ? 'active' : '' }}">
                            <a href="{{ route('dettes-cnps.index') }}">
                                <i class="fas fa-shield-alt"></i>
                                <span>Dettes CNPS</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('dettes-salariales*') ? 'active' : '' }}">
                            <a href="{{ route('dettes-salariale.index') }}">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Dettes salariales</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('dettes-fiscales*') ? 'active' : '' }}">
                            <a href="{{ route('dettes-fiscale.index') }}">
                                <i class="fas fa-receipt"></i>
                                <span>Dettes fiscales</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('dettes-feicom*') ? 'active' : '' }}">
                            <a href="{{ route('dettes-feicom.index') }}">
                                <i class="fas fa-building"></i>
                                <span>Dettes FEICOM</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- DÉFAILLANCES -->
                <div class="menu-section">
                    <h4>DÉFAILLANCES</h4>
                    <ul>
                        <li class="{{ request()->is('retards-depot*') ? 'active' : '' }}">
                            {{-- <a href="{{ route('retards-depot.index') }}"> --}}
                                <i class="fas fa-clock"></i>
                                <span>Retards de dépôt</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('defaillances-gestion*') ? 'active' : '' }}">
                            {{-- <a href="{{ route('defaillances-gestion.index') }}"> --}}
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Défaillances de gestion</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('alertes*') ? 'active' : '' }}">
                            {{-- <a href="{{ route('alertes.index') }}"> --}}
                                <i class="fas fa-bell"></i>
                                <span>Système d'alertes</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- RAPPORTS -->
                <div class="menu-section">
                    <h4>RAPPORTS & ANALYSES</h4>
                    <ul>
                        <li class="{{ request()->is('rapports-performance*') ? 'active' : '' }}">
                            {{-- <a href="{{ route('rapports-performance.index') }}"> --}}
                                <i class="fas fa-chart-bar"></i>
                                <span>Performance financière</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('rapports-gouvernance*') ? 'active' : '' }}">
                            {{-- <a href="{{ route('rapports-gouvernance.index') }}"> --}}
                                <i class="fas fa-balance-scale"></i>
                                <span>Gouvernance locale</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('synthese-annuelle*') ? 'active' : '' }}">
                            {{-- <a href="{{ route('synthese-annuelle.index') }}"> --}}
                                <i class="fas fa-file-alt"></i>
                                <span>Synthèse annuelle</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <h1>@yield('page-title', 'Observatoire des Collectivités Territoriales')</h1>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <input type="text" placeholder="Rechercher une commune, un indicateur..." class="search-input">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="notifications">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="settings">
                        <i class="fas fa-cog"></i>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="page-content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Fonction pour gérer les sous-menus
        function toggleSubmenu(submenuId) {
            const submenu = document.getElementById(submenuId);
            const toggle = event.target.closest('.menu-toggle');
            
            if (submenu.classList.contains('show')) {
                submenu.classList.remove('show');
                toggle.classList.remove('active');
            } else {
                // Fermer tous les autres sous-menus
                document.querySelectorAll('.submenu.show').forEach(sm => {
                    sm.classList.remove('show');
                });
                document.querySelectorAll('.menu-toggle.active').forEach(t => {
                    t.classList.remove('active');
                });
                
                // Ouvrir le sous-menu cliqué
                submenu.classList.add('show');
                toggle.classList.add('active');
            }
        }

        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('animate-fade-in');
        });
    </script>
    
    @stack('scripts')
</body>
</html>