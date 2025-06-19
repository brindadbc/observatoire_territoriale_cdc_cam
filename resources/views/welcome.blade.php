<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Observatoire des Finances des Collectivités Territoriales du Cameroun</title>
    <style>
        
          
        :root {
            --primary-color: #007a5e;
            --secondary-color: #fcd116;
            --tertiary-color: #ce1126;
            --dark-color: #1a1a1a;
            --light-color: #f8f9fa;
            --gray-color: #6c757d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
        }
        
        .logo {
            width: 160px;
            height: 160px;
            margin-right: 15px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .site-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .site-title p {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 1.5rem;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }
        
        nav ul li a:hover {
            opacity: 0.8;
        }
        
        /* Section Hero corrigée */
        .hero {
            position: relative;
            height: 80vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://www.gasigasy.mg/wp-content/uploads/2024/12/jad20241202-ass-cameroun-finances-publiques.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: 1;
        }
        
        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4));
            z-index: 2;
        }
        
        .hero-content {
            position: relative;
            z-index: 3;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 15px;
            text-align: center;
            color: white;
        }
        
        .hero h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
        
        .btn {
            display: inline-block;
            background-color: var(--secondary-color);
            color: var(--dark-color);
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .btn:hover {
            background-color: #e9bf0f;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
        
        .features {
            padding: 4rem 0;
            background-color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .section-title p {
            font-size: 1.1rem;
            color: var(--gray-color);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            padding: 2rem;
            background-color: var(--light-color);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
            color: white;
            font-size: 1.5rem;
        }
        
        .feature-title {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .stats {
            padding: 4rem 0;
            background-color: var(--primary-color);
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-numbers {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        /* Responsive */
        @media screen and (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }
            
            .logo-container {
                margin-bottom: 1rem;
            }
            
            .logo {
                width: 120px;
                height: 120px;
            }
            
            nav ul {
                margin-top: 1rem;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            nav ul li {
                margin: 0.5rem 0.75rem;
            }
            
            .hero {
                height: 60vh;
            }
            
            .hero h2 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
        
        @media screen and (max-width: 480px) {
            .hero h2 {
                font-size: 1.5rem;
            }
            
            .hero p {
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }
        }
    
        .btn {
            display: inline-block;
            background-color: var(--secondary-color);
            color: var(--dark-color);
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #e9bf0f;
        }
        
        .features {
            padding: 4rem 0;
            background-color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .section-title p {
            font-size: 1.1rem;
            color: var(--gray-color);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            padding: 2rem;
            background-color: var(--light-color);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
            color: white;
            font-size: 1.5rem;
        }
        
        .feature-title {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .stats {
            padding: 4rem 0;
            background-color: var(--primary-color);
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .stat-numbers {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .dashboard-preview {
            padding: 4rem 0;
            background-color: var(--light-color);
        }
        
        .dashboard-content {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }
        
        .dashboard-text {
            flex: 1;
            min-width: 300px;
        }
        
        .dashboard-text h3 {
            font-size: 1.75rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .dashboard-text ul {
            list-style: none;
            margin-bottom: 1.5rem;
        }
        
        .dashboard-text ul li {
            margin-bottom: 0.75rem;
            display: flex;
            align-items: flex-start;
        }
        
        .dashboard-text ul li:before {
            content: "✓";
            color: var(--primary-color);
            font-weight: bold;
            margin-right: 0.5rem;
        }
        
        .dashboard-image {
            flex: 1;
            min-width: 300px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .dashboard-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .cta {
            padding: 4rem 0;
            background-color: var(--secondary-color);
            color: var(--dark-color);
            text-align: center;
        }
        
        .cta h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .cta p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cta-btn {
            background-color: var(--primary-color);
            color: white;
        }
        
        .cta-btn:hover {
            background-color: #006349;
        }
        
        footer {
            padding: 3rem 0;
            background-color: var(--dark-color);
            color: white;
        }
        
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 2rem;
        }
        
        .footer-info {
            flex: 2;
            min-width: 300px;
        }
        
        .footer-info h4 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .footer-links {
            flex: 1;
            min-width: 200px;
        }
        
        .footer-links h4 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links ul li {
            margin-bottom: 0.5rem;
        }
        
        .footer-links ul li a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links ul li a:hover {
            color: white;
        }
        
        .footer-contact {
            flex: 1;
            min-width: 200px;
        }
        
        .footer-contact h4 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        
        .contact-info {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: flex-start;
        }
        
        .contact-info span {
            margin-right: 0.5rem;
        }
        
        .footer-bottom {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            font-size: 0.9rem;
            color: #ccc;
        }
        
        @media screen and (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }
            
            .logo-container {
                margin-bottom: 1rem;
            }
            
            nav ul {
                margin-top: 1rem;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            nav ul li {
                margin: 0.5rem 0.75rem;
            }
            
            .hero h2 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            
            .dashboard-content {
                flex-direction: column;
            }
            
            .auth-buttons {
                margin-top: 1rem;
                justify-content: center;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo-container">
                <div class="logo">
                    <img src="img/image.png" alt="Logo chambre des comptes">
                </div>
                <div class="site-title">
                    <h2>Chambre des comptes</h2>
                    <h3>Observatoire des Collectivités Territoriales décentralisées</h3>
                    <p>République du Cameroun</p>
                </div>
            </div>
            <nav>
                <ul>
                    <li><a href="/" class="active">Accueil</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Statistiques</a></li>
                    
                    <li><a href="/contact">Contact</a></li>
                    <li class="auth-buttons">
                        <!-- <button class="login-btn" id="loginBtn">Connexion</button> -->
                        <!-- <button class="register-btn" id="registerBtn">Inscription</button> -->
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    

    <section class="hero">
        <div class="hero-content">
            <h2>Suivi de Observatoire des Collectivités Territoriales Décentralisées</h2>
            <p>Une plateforme moderne pour la gestion et l'analyse transparente des versements annuels des communes camerounaises depuis 2018</p>
            <a href="{{route('dashboard.index')}}"  class="btn">Accéder au Tableau de Bord</a> 
        </div>
    </section>

    <section class="features">
        <div class="container">
            <div class="section-title">
                <h2>Nos Services</h2>
                <p>Découvrez les outils de gestion et d'analyse des collectivités territoriales décentralisées</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">Situation des depots</h3>
                    <p>Monitoring détaillé des depots de comptes annuels par commune, département et région depuis 2018.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📈</div>
                    <h3 class="feature-title">Analyses Statistiques</h3>
                    <p>Graphiques et rapports statistiques permettant de visualiser les tendances et performances financières.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h3 class="feature-title">Gestion des Receveurs</h3>
                    <p>Base de données complète des receveurs municipaux avec leurs coordonnées et matricules.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔍</div>
                    <h3 class="feature-title">Suivi des Dettes</h3>
                    <p>Monitoring des dettes envers la Caisse Nationale de Prévoyance (CNPS) de chaque collectivité.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3 class="feature-title">Interface Moderne</h3>
                    <p>Plateforme intuitive accessible sur ordinateur, tablette et smartphone.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3 class="feature-title">Sécurité Avancée</h3>
                    <p>Protection des données confidentielles et gestion avancée des droits d'accès.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">384</div>
                    <div class="stat-label">Communes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">58</div>
                    <div class="stat-label">Départements</div>
                </div>
                <!-- <div class="stat-card">
                    <div class="stat-number">10</div>
                    <div class="stat-label">Régions</div>
                </div> -->
                <div class="stat-card">
                    <div class="stat-numbers">Année de depart</div>
                    <div class="stat-label">A parti de 2018</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">3.6 Mds</div>
                    <div class="stat-label">FCFA de depots de comptes</div>
                </div>
            </div>
             <!-- <img src="{{asset('img/page30-removebg-preview.png')}}" -->
        </div>
    </section>

    <section class="dashboard-preview">
        <div class="container">
            <div class="dashboard-content">
                <div class="dashboard-text">
                    <h3>Tableau de Bord des Collectivités Territoriales Décentralisées</h3>
                    <p>Notre plateforme offre une vision complète et détaillée des finances des collectivités territoriales camerounaises :</p>
                    <ul>
                        <li>Visualisation des versements annuels par commune</li>
                        <li>Suivi des performances financières par région</li>
                        <li>Gestion des receveurs et ordonnateurs</li>
                        <li>Monitoring des dettes envers la CNPS, salariale,fiscale et feicom</li>
                        <li>Génération de rapports personnalisés</li>
                        <li>Analyse comparative entre régions et périodes</li>
                    </ul>
                    <a href="{{route('dashboard.index')}}" class="btn">Explorer le Tableau de Bord</a>
                </div>
                <div class="dashboard-image">
                    <img src="/api/placeholder/600/400" alt="Tableau de bord des finances communales">
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h3>Rejoignez notre plateforme dès aujourd'hui</h3>
            <p>Accédez à des données financières complètes et à jour pour une meilleure gestion des collectivités territoriales décentralisées du Cameroun.</p>
            <a href="#" class="btn cta-btn" id="ctaRegisterBtn">Decouvrez les maintenant</a>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <h4>Observatoire des Collectivités Territoriales décentralisées</h4>
                    <p>Plateforme officielle de suivi et d'analyse des collectivités territoriales décentralisées du Cameroun. Un outil au service de la transparence et de la bonne gouvernance financière.</p>
                </div>
                <div class="footer-links">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="#">Accueil</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Statistiques</a></li>
                        <li><a href="#">Contact</a></li>
                       
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Contact</h4>
                    <div class="contact-info">
                        <span>📍</span>
                        <p>Avenue Winston Churchill Hippodrone, Yaoundé, Cameroun</p>
                     
                    </div>
                    <div class="contact-info"> 
                        <p>BP 1770 Yaoundé</p></div>
                    <div class="contact-info">
                        <span>📞</span>
                        <p>+237 222 22 15 66</p>
                    </div>
                    <div class="contact-info">
                        <span>✉️</span>
                        <p>observatoirectd@chambredescomptes.cm</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Observatoire  des Collectivités Territoriales décentralisées du Cameroun. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>

    // JavaScript pour la gestion des modals de connexion et d'inscription
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    const ctaRegisterBtn = document.getElementById('ctaRegisterBtn');
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    const closeLoginModal = document.getElementById('closeLoginModal');
    const closeRegisterModal = document.getElementById('closeRegisterModal');
    const switchToRegister = document.getElementById('switchToRegister');
    const switchToLogin = document.getElementById('switchToLogin');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    // Fonctions pour afficher/cacher les modals
    function showLoginModal() {
        loginModal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Empêche le défilement de la page
    }

    function showRegisterModal() {
        registerModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function hideLoginModal() {
        loginModal.style.display = 'none';
        document.body.style.overflow = '';
        loginForm.reset(); // Réinitialise le formulaire
    }

    function hideRegisterModal() {
        registerModal.style.display = 'none';
        document.body.style.overflow = '';
        registerForm.reset(); // Réinitialise le formulaire
    }

    // Événements pour afficher les modals
    loginBtn.addEventListener('click', showLoginModal);
    registerBtn.addEventListener('click', showRegisterModal);
    ctaRegisterBtn.addEventListener('click', showRegisterModal);

    // Événements pour fermer les modals
    closeLoginModal.addEventListener('click', hideLoginModal);
    closeRegisterModal.addEventListener('click', hideRegisterModal);

    // Fermeture des modals en cliquant à l'extérieur
    window.addEventListener('click', function(event) {
        if (event.target === loginModal) {
            hideLoginModal();
        }
        if (event.target === registerModal) {
            hideRegisterModal();
        }
    });

    // Basculer entre les modals de connexion et d'inscription
    switchToRegister.addEventListener('click', function(e) {
        e.preventDefault();
        hideLoginModal();
        showRegisterModal();
    });

    switchToLogin.addEventListener('click', function(e) {
        e.preventDefault();
        hideRegisterModal();
        showLoginModal();
    });

    // Validation et soumission du formulaire de connexion
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;
        
        // Vérification basique des champs
        if (!validateEmail(email)) {
            alert('Veuillez entrer une adresse email valide.');
            return;
        }
        
        if (password.length < 6) {
            alert('Le mot de passe doit contenir au moins 6 caractères.');
            return;
        }
        
        // Simulation d'une connexion réussie
        alert('Connexion réussie!');
        hideLoginModal();
        
        // Pour une vraie implémentation, vous enverriez ces données à votre backend
        // fetch('/api/login', {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //     },
        //     body: JSON.stringify({ email, password }),
        // })
        // .then(response => response.json())
        // .then(data => {
        //     if (data.success) {
        //         window.location.href = '/dashboard';
        //     } else {
        //         alert(data.message);
        //     }
        // })
        // .catch(error => {
        //     console.error('Erreur:', error);
        //     alert('Une erreur est survenue lors de la connexion.');
        // });
    });

    // Validation et soumission du formulaire d'inscription
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const firstName = document.getElementById('registerFirstName').value;
        const lastName = document.getElementById('registerLastName').value;
        const email = document.getElementById('registerEmail').value;
        const phone = document.getElementById('registerPhone').value;
        const organization = document.getElementById('registerOrganization').value;
        const position = document.getElementById('registerPosition').value;
        const password = document.getElementById('registerPassword').value;
        const confirmPassword = document.getElementById('registerConfirmPassword').value;
        
        // Vérification des champs
        if (!firstName || !lastName) {
            alert('Veuillez remplir tous les champs obligatoires.');
            return;
        }
        
        if (!validateEmail(email)) {
            alert('Veuillez entrer une adresse email valide.');
            return;
        }
        
        if (!validatePhone(phone)) {
            alert('Veuillez entrer un numéro de téléphone valide.');
            return;
        }
        
        if (password.length < 6) {
            alert('Le mot de passe doit contenir au moins 6 caractères.');
            return;
        }
        
        if (password !== confirmPassword) {
            alert('Les mots de passe ne correspondent pas.');
            return;
        }
        
        // Simulation d'une inscription réussie
        alert('Inscription réussie! Vous pouvez maintenant vous connecter.');
        hideRegisterModal();
        showLoginModal();
        
        // Pour une vraie implémentation, vous enverriez ces données à votre backend
        // fetch('/api/register', {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //     },
        //     body: JSON.stringify({
        //         firstName,
        //         lastName,
        //         email,
        //         phone,
        //         organization,
        //         position,
        //         password
        //     }),
        // })
        // .then(response => response.json())
        // .then(data => {
        //     if (data.success) {
        //         alert('Inscription réussie! Vous pouvez maintenant vous connecter.');
        //         hideRegisterModal();
        //         showLoginModal();
        //     } else {
        //         alert(data.message);
        //     }
        // })
        // .catch(error => {
        //     console.error('Erreur:', error);
        //     alert('Une erreur est survenue lors de l\'inscription.');
        // });
    });

    // Fonctions de validation
    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    
    function validatePhone(phone) {
        // Validation basique pour les numéros de téléphone camerounais
        // Format: +237 XXX XXX XXX ou 6XX XXX XXX
        const re = /^(\+237|237)?\s?[6-9][0-9]{8}$/;
        return re.test(String(phone).replace(/\s/g, ''));
    }

    // Gestionnaire pour la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (loginModal.style.display === 'flex') {
                hideLoginModal();
            }
            if (registerModal.style.display === 'flex') {
                hideRegisterModal();
            }
        }
    });

    // Animation pour les formulaires (optionnel)
    document.querySelectorAll('.form-group input').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (this.value === '') {
                this.parentElement.classList.remove('focused');
            }
        });
    });
});
</script>