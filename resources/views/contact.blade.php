<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Observatoire des Finances des Collectivités Territoriales du Cameroun</title>
    <style>
        :root {
            --primary-color: #007a5e;
            --secondary-color: #fcd116;
            --tertiary-color: #ce1126;
            --dark-color: #1a1a1a;
            --light-color: #f8f9fa;
            --gray-color: #6c757d;
            --success-color: #28a745;
            --error-color: #dc3545;
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
            width: 80px;
            height: 80px;
            margin-right: 15px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        .logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
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
        
        nav ul li a:hover, nav ul li a.active {
            opacity: 0.8;
            text-decoration: underline;
        }
        
        .page-header {
            background: linear-gradient(rgba(0, 122, 94, 0.9), rgba(0, 122, 94, 0.9)), 
                        linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 4rem 0 2rem;
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .page-header p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .contact-section {
            padding: 4rem 0;
            background-color: white;
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: start;
        }
        
        .contact-form {
            background-color: var(--light-color);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .contact-form h2 {
            color: var(--primary-color);
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            background-color: white;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 122, 94, 0.1);
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-group select {
            cursor: pointer;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, var(--primary-color), #006349);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            width: 100%;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 122, 94, 0.3);
        }
        
        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .contact-info {
            padding-left: 1rem;
        }
        
        .contact-info h2 {
            color: var(--primary-color);
            font-size: 1.8rem;
            margin-bottom: 2rem;
        }
        
        .info-card {
            background-color: var(--light-color);
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
        }
        
        .info-card:hover {
            transform: translateY(-3px);
        }
        
        .info-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .info-icon {
            width: 50px;
            height: 50px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 1rem;
            font-size: 1.5rem;
            color: white;
        }
        
        .info-card h3 {
            color: var(--primary-color);
            font-size: 1.3rem;
        }
        
        .info-card p {
            color: var(--gray-color);
            line-height: 1.6;
        }
        
        .info-card a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .info-card a:hover {
            text-decoration: underline;
        }
        
        .hours-section {
            padding: 3rem 0;
            background-color: var(--primary-color);
            color: white;
        }
        
        .hours-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .hours-card {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
        }
        
        .hours-card h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--secondary-color);
        }
        
        .hours-list {
            list-style: none;
        }
        
        .hours-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: space-between;
        }
        
        .hours-list li:last-child {
            border-bottom: none;
        }
        
        .map-section {
            padding: 4rem 0;
            background-color: white;
        }
        
        .map-section h2 {
            text-align: center;
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 2rem;
        }
        
        .map-container {
            background-color: var(--light-color);
            height: 400px;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2rem;
            color: var(--gray-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: none;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .loading {
            display: none;
            text-align: center;
            margin: 1rem 0;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
        
        .contact-info-footer {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: flex-start;
        }
        
        .contact-info-footer span {
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
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .contact-info {
                padding-left: 0;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            
            .hours-grid {
                grid-template-columns: 1fr;
            }
            
            .contact-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo-container">
                <div class="logo">
                    <!-- Logo placeholder -->
                    <span style="font-size: 2rem; color: var(--primary-color);">🏛</span>
                </div>
                <div class="site-title">
                    <h1>Observatoire des Finances des Collectivités Territoriales</h1>
                    <p>République du Cameroun</p>
                </div>
            </div>
            <nav>
                <ul>
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="#">À propos</a></li>
                    <li><a href="#">Statistiques</a></li>
                    <li><a href="contact.html" class="active">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="page-header">
        <div class="container">
            <h1>Contactez-nous</h1>
            <p>Nous sommes à votre disposition pour répondre à vos questions concernant les finances des collectivités territoriales du Cameroun</p>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-form">
                    <h2>Envoyez-nous un message</h2>
                    <div class="alert alert-success" id="successAlert">
                        Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.
                    </div>
                    <div class="alert alert-error" id="errorAlert">
                        Une erreur est survenue lors de l'envoi de votre message. Veuillez réessayer.
                    </div>
                    <div class="loading" id="loadingSpinner">
                        <div class="spinner"></div>
                        <p>Envoi en cours...</p>
                    </div>
                    <form id="contactForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName">Prénom *</label>
                                <input type="text" id="firstName" name="firstName" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName">Nom *</label>
                                <input type="text" id="lastName" name="lastName" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Téléphone</label>
                                <input type="tel" id="phone" name="phone" placeholder="+237 6XX XXX XXX">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="organization">Organisation / Collectivité</label>
                            <input type="text" id="organization" name="organization" placeholder="Nom de votre organisation">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Sujet *</label>
                            <select id="subject" name="subject" required>
                                <option value="">Sélectionnez un sujet</option>
                                <option value="information">Demande d'information</option>
                                <option value="support">Support technique</option>
                                <option value="data">Questions sur les données</option>
                                <option value="access">Demande d'accès</option>
                                <option value="partnership">Partenariat</option>
                                <option value="other">Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" required placeholder="Décrivez votre demande en détail..."></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn" id="submitBtn">
                            Envoyer le message
                        </button>
                    </form>
                </div>
                
                <div class="contact-info">
                    <h2>Nos coordonnées</h2>
                    
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="info-icon">📍</div>
                            <h3>Adresse</h3>
                        </div>
                        <p>Ministère de la Décentralisation et du Développement Local<br>
                        Boulevard du 20 Mai<br>
                        BP 12345, Yaoundé<br>
                        Cameroun</p>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="info-icon">📞</div>
                            <h3>Téléphone</h3>
                        </div>
                        <p>Standard: <a href="tel:+237222221566">+237 222 22 15 66</a><br>
                        Support: <a href="tel:+237699123456">+237 699 12 34 56</a><br>
                        Urgences: <a href="tel:+237677987654">+237 677 98 76 54</a></p>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="info-icon">✉</div>
                            <h3>Email</h3>
                        </div>
                        <p>Contact général: <a href="mailto:contact@finances-collectivites.cm">contact@finances-collectivites.cm</a><br>
                        Support: <a href="mailto:support@finances-collectivites.cm">support@finances-collectivites.cm</a><br>
                        Partenariats: <a href="mailto:partenaires@finances-collectivites.cm">partenaires@finances-collectivites.cm</a></p>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="info-icon">🌐</div>
                            <h3>Réseaux sociaux</h3>
                        </div>
                        <p>Suivez-nous pour les dernières actualités:<br>
                        <a href="#" target="_blank">Facebook</a> | 
                        <a href="#" target="_blank">Twitter</a> | 
                        <a href="#" target="_blank">LinkedIn</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="hours-section">
        <div class="container">
            <div class="hours-grid">
                <div class="hours-card">
                    <h3>Heures d'ouverture</h3>
                    <ul class="hours-list">
                        <li><span>Lundi - Vendredi</span><span>08h00 - 17h00</span></li>
                        <li><span>Samedi</span><span>08h00 - 12h00</span></li>
                        <li><span>Dimanche</span><span>Fermé</span></li>
                    </ul>
                </div>
                
                <div class="hours-card">
                    <h3>Support technique</h3>
                    <ul class="hours-list">
                        <li><span>Lundi - Vendredi</span><span>08h00 - 18h00</span></li>
                        <li><span>Samedi</span><span>09h00 - 13h00</span></li>
                        <li><span>Urgences</span><span>24h/24</span></li>
                    </ul>
                </div>
                
                <div class="hours-card">
                    <h3>Temps de réponse</h3>
                    <ul class="hours-list">
                        <li><span>Email</span><span>< 24h</span></li>
                        <li><span>Téléphone</span><span>Immédiat</span></li>
                        <li><span>Demandes complexes</span><span>2-3 jours</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="map-section">
        <div class="container">
            <h2>Notre localisation</h2>
            <div class="map-container">
                <p>📍 Carte interactive disponible prochainement<br>
                Yaoundé, Quartier Administratif</p>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <h4>Observatoire des Finances des Collectivités Territoriales</h4>
                    <p>Plateforme officielle de suivi et d'analyse des finances des collectivités territoriales décentralisées du Cameroun. Un outil au service de la transparence et de la bonne gouvernance financière.</p>
                </div>
                <div class="footer-links">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="#">Accueil</a></li>
                        <li><a href="#">Tableau de Bord</a></li>
                        <li><a href="#">Statistiques</a></li>
                        <li><a href="#">Rapports</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Assistance</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Contact</h4>
                    <div class="contact-info-footer">
                        <span>📍</span>
                        <p>Ministère de la Décentralisation et du Développement Local, Yaoundé, Cameroun</p>
                    </div>
                    <div class="contact-info-footer">
                        <span>📞</span>
                        <p>+237 222 22 15 66</p>
                    </div>
                    <div class="contact-info-footer">
                        <span>✉</span>
                        <p>contact@finances-collectivites.cm</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Observatoire des Finances des Collectivités Territoriales du Cameroun. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contactForm = document.getElementById('contactForm');
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');
            
            // Animation des champs de formulaire
            const formInputs = document.querySelectorAll('.form-group input, .form-group select, .form-group textarea');
            formInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                    this.parentElement.style.transition = 'transform 0.2s ease';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
            
            // Validation en temps réel
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            
            emailInput.addEventListener('blur', function() {
                if (this.value && !validateEmail(this.value)) {
                    this.style.borderColor = 'var(--error-color)';
                    showFieldError(this, 'Veuillez entrer une adresse email valide');
                } else {
                    this.style.borderColor = 'var(--primary-color)';
                    hideFieldError(this);
                }
            });
            
            phoneInput.addEventListener('blur', function() {
                if (this.value && !validatePhone(this.value)) {
                    this.style.borderColor = 'var(--error-color)';
                    showFieldError(this, 'Format de téléphone invalide (ex: +237 6XX XXX XXX)');
                } else {
                    this.style.borderColor = '#e9ecef';
                    hideFieldError(this);
                }
            });
            
            // Soumission du formulaire
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Cacher les alertes précédentes
                hideAlerts();
                
                // Valider le formulaire
                if (!validateForm()) {
                    return;
                }
                
                // Simuler l'envoi
                submitMessage();
            });
            
            function validateForm() {
                const firstName = document.getElementById('firstName').value.trim();
                const lastName = document.getElementById('lastName').value.trim();
                const email = document.getElementById('email').value.trim();
                const subject = document.getElementById('subject').value;
                const message = document.getElementById('message').value.trim();
                
                if (!firstName || !lastName) {
                    showError('Veuillez remplir tous les champs obligatoires.');
                    return false;
                }
                
                if (!validateEmail(email)) {
                    showError('Veuillez entrer une adresse email valide.');
                    return false;
                }
                
                if (!subject) {
                    showError('Veuillez sélectionner un sujet.');
                    return false;
                }
                
                if (message.length < 10) {
                    showError('Le message doit contenir au moins 10 caractères.');
                    return false;
                }
                
                const phone = document.getElementById('phone').value.trim();
                if (phone && !validatePhone(phone)) {
                    showError('Format de téléphone invalide.');
                    return false;
                }
                
                return true;
            }
            
            function submitMessage() {
                // Afficher le loading
                loadingSpinner.style.display = 'block';
                submitBtn.disabled = true;
                submitBtn.textContent = 'Envoi en cours...';
                
                // Simuler une requête API
                setTimeout(() => {
                    // Cacher le loading
                    loadingSpinner.style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Envoyer le message';
                    
                    // Simuler une réponse réussie (90% de chance de succès)
                    if (Math.random() > 0.1) {
                        showSuccess();
                    } else {
                        showError('Erreur de connexion. Veuillez réessayer.');
                    }
                }, 2000); }
                
                // Dans un vrai projet, vous feriez quelque chose comme :
                /*
                const formData = new FormData(contactForm);
                fetch('/api/contact', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    loadingSpinner.style.display = 'none';
                    submitBtn.disabled 
