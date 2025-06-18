<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <div class="logo">
                <div class="logo-flag">
                    <div class="flag-green"></div>
                    <div class="flag-red"></div>
                    <div class="flag-yellow"></div>
                </div>
            </div>
            <div class="site-title">
                <h1>Observatoire des Collectivités</h1>
                <p>Territoriales décentralisées</p>
            </div>
        </div>
    </div>
    
    <nav class="sidebar-menu">
        <div class="menu-section">
            <div class="menu-title">Principal</div>
            <ul class="menu-items">
                <li class="menu-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}" 
                    data-route="{{ route('dashboard.index') }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Tableau de Bord</span>
                </li>
                <li class="menu-item {{ request()->routeIs('dashboard.depots') ? 'active' : '' }}"
                    data-route="{{ route('dashboard.depots') }}">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Dépôts de comptes</span>
                </li>
                <li class="menu-item {{ request()->routeIs('regions.*') ? 'active' : '' }}"
                    data-route="{{ route('regions.index') }}">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Régions</span>
                </li>
            </ul>
        </div>
        
        <div class="menu-section">
            <div class="menu-title">Gestion</div>
            <ul class="menu-items">
                <li class="menu-item {{ request()->routeIs('receveurs.*') ? 'active' : '' }}"
                    data-route="{{ route('receveurs.index') }}">
                    <i class="fas fa-users"></i>
                    <span>Receveurs</span>
                </li>
                <li class="menu-item {{ request()->routeIs('ordonnateurs.*') ? 'active' : '' }}"
                    data-route="{{ route('ordonnateurs.index') }}">
                    <i class="fas fa-users"></i>
                    <span>Ordonnateurs</span>
                </li>
                <li class="menu-item {{ request()->routeIs('dettes.cnps') ? 'active' : '' }}"
                    data-route="{{ route('dettes.cnps') }}">
                    <i class="fas fa-file-invoice"></i>
                    <span>Dettes CNPS</span>
                </li>
                <li class="menu-item {{ request()->routeIs('dettes.salariales') ? 'active' : '' }}"
                    data-route="{{ route('dettes.salariales') }}">
                    <i class="fas fa-file-invoice"></i>
                    <span>Dettes Salariales</span>
                </li>
                <li class="menu-item {{ request()->routeIs('dettes.fiscale') ? 'active' : '' }}"
                    data-route="{{ route('dettes.fiscale') }}">
                    <i class="fas fa-file-invoice"></i>
                    <span>Dettes Fiscale</span>
                </li>
                <li class="menu-item {{ request()->routeIs('dettes.feicom') ? 'active' : '' }}"
                    data-route="{{ route('dettes.feicom') }}">
                    <i class="fas fa-file-invoice"></i>
                    <span>Dettes Feicom</span>
                </li>
                <li class="menu-item {{ request()->routeIs('rapports.*') ? 'active' : '' }}"
                    data-route="{{ route('rapports.index') }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Rapports</span>
                </li>
            </ul>
        </div>
        
        <div class="menu-section">
            <div class="menu-title">Paramètres</div>
            <ul class="menu-items">
                <li class="menu-item {{ request()->routeIs('configuration.*') ? 'active' : '' }}"
                    data-route="{{ route('configuration.index') }}">
                    <i class="fas fa-cog"></i>
                    <span>Configuration</span>
                </li>
                <li class="menu-item {{ request()->routeIs('utilisateurs.*') ? 'active' : '' }}"
                    data-route="{{ route('utilisateurs.index') }}">
                    <i class="fas fa-user-shield"></i>
                    <span>Utilisateurs</span>
                </li>
                <li class="menu-item {{ request()->routeIs('aide.*') ? 'active' : '' }}"
                    data-route="{{ route('aide.index') }}">
                    <i class="fas fa-question-circle"></i>
                    <span>Aide</span>
                </li>
            </ul>
        </div>
    </nav>
    
    <div class="user-info">
        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'JD', 0, 2)) }}</div>
        <div class="user-details">
            <div class="user-name">{{ auth()->user()->name ?? 'Jean Dupont' }}</div>
            <div class="user-role">{{ auth()->user()->role ?? 'Administrateur' }}</div>
        </div>
        <div class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
        </div>
    </div>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</aside>