 <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Observatoire des Collectivités Territoriales')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
    <style>

        /* =================================
   STYLE CSS POUR VUE COMMUNE
   ================================= */

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

/* Container principal */
.commune-dashboard {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: var(--dark-color);
}

/* =================================
   BREADCRUMB
   ================================= */
.breadcrumb {
    background: white;
    padding: 15px 20px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    margin-bottom: 20px;
    font-size: 14px;
}

.breadcrumb a {
    color: var(--primary-color);
    text-decoration: none;
    transition: var(--transition);
}

.breadcrumb a:hover {
    color: var(--secondary-color);
    text-decoration: underline;
}

.breadcrumb span {
    margin: 0 8px;
    color: var(--text-muted);
}

/* =================================
   HEADER COMMUNE
   ================================= */
.commune-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 30px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.commune-info h2 {
    margin: 0 0 15px 0;
    font-size: 2.2rem;
    font-weight: 600;
}

.commune-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    opacity: 0.9;
}

.meta-item i {
    width: 16px;
    text-align: center;
}

.commune-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition);
}

.btn-export {
    background: var(--success-color);
    color: white;
}

.btn-export:hover {
    background: #218838;
    transform: translateY(-2px);
}

.btn-edit {
    background: var(--warning-color);
    color: var(--dark-color);
}

.btn-edit:hover {
    background: #e0a800;
    transform: translateY(-2px);
}

/* =================================
   SECTIONS GÉNÉRALES
   ================================= */
.responsables-section,
.finances-section,
.chart-section,
.dettes-section,
.problemes-section {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    margin-bottom: 30px;
    overflow: hidden;
}

.responsables-section h3,
.finances-section h3,
.chart-section h3,
.dettes-section h3 {
    background: var(--light-color);
    margin: 0;
    padding: 20px 30px;
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--primary-color);
    border-bottom: 1px solid var(--border-color);
}

/* =================================
   RESPONSABLES
   ================================= */
.responsables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    padding: 30px;
}

.responsable-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.responsable-card:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-2px);
}

.responsable-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 20px;
}

.responsable-info h4 {
    margin: 0 0 5px 0;
    color: var(--primary-color);
    font-size: 1.1rem;
}

.responsable-info p {
    margin: 0;
    color: var(--text-muted);
    font-weight: 500;
}

/* =================================
   FINANCES
   ================================= */
.finances-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 30px;
}

.finance-card {
    padding: 25px;
    border-radius: var(--border-radius);
    text-align: center;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.finance-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-color);
}

.finance-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.finance-card.budget {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
}

.finance-card.budget::before {
    background: var(--info-color);
}

.finance-card.realisation {
    background: linear-gradient(135deg, #f3e5f5, #e1bee7);
}

.finance-card.realisation::before {
    background: #9c27b0;
}

.finance-card.taux {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
}

.finance-card.evaluation {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
}

.finance-card.evaluation::before {
    background: var(--warning-color);
}

.finance-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 15px;
}

.finance-header h4 {
    margin: 0;
    font-size: 1rem;
    color: var(--dark-color);
}

.finance-header i {
    font-size: 1.2rem;
    opacity: 0.7;
}

.finance-amount {
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.taux-value.good {
    color: var(--success-color);
}

.taux-value.medium {
    color: var(--warning-color);
}

.taux-value.bad {
    color: var(--danger-color);
}

.finance-evaluation {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-color);
}

/* =================================
   GRAPHIQUE
   ================================= */
.chart-container {
    background: white;
}

.chart-header {
    background: var(--light-color);
    padding: 20px 30px;
    border-bottom: 1px solid var(--border-color);
}

.chart-header h3 {
    margin: 0;
    font-size: 1.3rem;
    color: var(--primary-color);
}

.chart-content {
    padding: 30px;
    height: 400px;
    position: relative;
}

/* =================================
   DETTES
   ================================= */
.dettes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 30px;
}

.dette-card {
    padding: 25px;
    border-radius: var(--border-radius);
    text-align: center;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.dette-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.dette-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

.dette-card.cnps {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
}

.dette-card.cnps::before {
    background: var(--success-color);
}

.dette-card.fiscale {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
}

.dette-card.fiscale::before {
    background: var(--warning-color);
}

.dette-card.feicom {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
}

.dette-card.feicom::before {
    background: var(--info-color);
}

.dette-card.salariale {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
}

.dette-card.salariale::before {
    background: var(--danger-color);
}

.dette-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 15px;
}

.dette-header h4 {
    margin: 0;
    font-size: 1rem;
    color: var(--dark-color);
}

.dette-amount {
    font-size: 1.6rem;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 8px;
}

.dette-count {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-style: italic;
}

/* =================================
   PROBLÈMES ET TABS
   ================================= */
.section-tabs {
    display: flex;
    background: var(--light-color);
    border-bottom: 1px solid var(--border-color);
}

.tab-btn {
    flex: 1;
    padding: 15px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-weight: 500;
    color: var(--text-muted);
    transition: var(--transition);
    position: relative;
}

.tab-btn.active {
    color: var(--primary-color);
    background: white;
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-color);
}

.tab-btn:hover:not(.active) {
    background: rgba(44, 82, 130, 0.1);
}

.tab-content {
    display: none;
    padding: 30px;
}

.tab-content.active {
    display: block;
}

.problemes-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.probleme-item {
    padding: 20px;
    border-radius: var(--border-radius);
    border-left: 4px solid var(--info-color);
    background: var(--light-color);
    transition: var(--transition);
}

.probleme-item:hover {
    background: white;
    box-shadow: var(--shadow);
}

.probleme-item.grave {
    border-left-color: var(--danger-color);
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
}

.probleme-item.resolved {
    opacity: 0.7;
    border-left-color: var(--success-color);
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
}

.probleme-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.probleme-type {
    font-weight: 600;
    color: var(--primary-color);
    background: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
}

.probleme-date {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.probleme-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.probleme-status.resolved {
    background: var(--success-color);
    color: white;
}

.probleme-status.pending {
    background: var(--warning-color);
    color: var(--dark-color);
}

.retard-duree {
    background: var(--danger-color);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.probleme-description {
    color: var(--dark-color);
    line-height: 1.5;
    margin-bottom: 10px;
}

.probleme-gravite {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    font-weight: 500;
}

.probleme-gravite.grave {
    color: var(--danger-color);
}

.probleme-gravite i {
    font-size: 1rem;
}

.no-problemes {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-muted);
    font-style: italic;
}

.no-problemes i {
    font-size: 2rem;
    margin-bottom: 10px;
    color: var(--success-color);
    display: block;
}

/* =================================
   RESPONSIVE DESIGN
   ================================= */
@media (max-width: 768px) {
    .commune-dashboard {
        padding: 15px;
    }
    
    .commune-header {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    
    .commune-meta {
        justify-content: center;
    }
    
    .finances-grid,
    .dettes-grid,
    .responsables-grid {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 20px;
    }
    
    .finance-amount,
    .dette-amount {
        font-size: 1.4rem;
    }
    
    .commune-info h2 {
        font-size: 1.8rem;
    }
    
    .section-tabs {
        flex-direction: column;
    }
    
    .tab-btn {
        text-align: left;
    }
    
    .probleme-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .chart-content {
        padding: 15px;
        height: 300px;
    }
}

@media (max-width: 480px) {
    .commune-dashboard {
        padding: 10px;
    }
    
    .commune-header {
        padding: 20px;
    }
    
    .finances-grid,
    .dettes-grid,
    .responsables-grid {
        padding: 15px;
    }
    
    .finance-card,
    .dette-card,
    .responsable-card {
        padding: 15px;
    }
    
    .tab-content {
        padding: 20px 15px;
    }
    
    .probleme-item {
        padding: 15px;
    }
}
        /* =============================================
   STYLES GLOBAUX - OBSERVATOIRE DES COLLECTIVITÉS
   ============================================= */

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

/* =============================================
   SIDEBAR
   ============================================= */

.sidebar {
    width: 280px;
    background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
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
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    background-color: rgba(0,0,0,0.1);
}

.logo {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.logo-text h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 2px;
    color: #fff;
}

.logo-text p {
    font-size: 12px;
    color: #bdc3c7;
    font-weight: 300;
}

.sidebar-menu {
    padding: 20px 0;
}

.menu-section {
    margin-bottom: 30px;
}

.menu-section h4 {
    font-size: 11px;
    color: #95a5a6;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
    padding: 0 20px;
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
    padding: 12px 20px;
    color: #ecf0f1;
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.menu-section a:hover {
    background-color: rgba(255,255,255,0.1);
    border-left-color: #3498db;
    color: #fff;
}

.menu-section li.active a {
    background-color: rgba(52, 152, 219, 0.2);
    border-left-color: #3498db;
    color: #fff;
    font-weight: 500;
}

.menu-section a i {
    width: 20px;
    margin-right: 12px;
    font-size: 16px;
    text-align: center;
}

/* =============================================
   MAIN CONTENT
   ============================================= */

.main-content {
    flex: 1;
    margin-left: 280px;
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
    color: #2c3e50;
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
    width: 250px;
    font-size: 14px;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #3498db;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
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

.notifications i, .settings i {
    color: #6c757d;
    font-size: 16px;
}

.page-content {
    padding: 30px;
}

/* =============================================
   CARDS ET STATISTIQUES
   ============================================= */

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #3498db, #2ecc71);
}

.stat-card.danger::before {
    background: linear-gradient(90deg, #e74c3c, #c0392b);
}

.stat-card.warning::before {
    background: linear-gradient(90deg, #f39c12, #e67e22);
}

.stat-card.success::before {
    background: linear-gradient(90deg, #2ecc71, #27ae60);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.stat-title {
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-icon.blue {
    background: linear-gradient(135deg, #3498db, #2980b9);
}

.stat-icon.green {
    background: linear-gradient(135deg, #2ecc71, #27ae60);
}

.stat-icon.red {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
}

.stat-icon.orange {
    background: linear-gradient(135deg, #f39c12, #e67e22);
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 8px;
    line-height: 1;
}

.stat-change {
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
}

.stat-change.positive {
    color: #27ae60;
}

.stat-change.negative {
    color: #e74c3c;
}

.stat-change i {
    font-size: 12px;
}

/* =============================================
   CHARTS ET GRAPHIQUES
   ============================================= */

.chart-container {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 30px;
    border: 1px solid #e9ecef;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
}

.chart-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
}

.chart-filters {
    display: flex;
    gap: 10px;
}

.filter-btn {
    padding: 8px 16px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-btn.active {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.filter-btn:hover {
    border-color: #3498db;
}

.chart-content {
    height: 400px;
    position: relative;
}

/* =============================================
   TABLEAUX
   ============================================= */

.table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
}

.table-header {
    padding: 20px 25px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
}

.table-actions {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

.btn-success {
    background: #27ae60;
    color: white;
}

.btn-success:hover {
    background: #2ecc71;
}

.btn-danger {
    background: #e74c3c;
    color: white;
}

.btn-danger:hover {
    background: #c0392b;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.data-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table tr:hover {
    background-color: #f8f9fa;
}

.data-table td {
    font-size: 14px;
    color: #495057;
}

/* =============================================
   BADGES ET STATUS
   ============================================= */

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
}

.badge-success {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
    border: 1px solid rgba(39, 174, 96, 0.2);
}

.badge-warning {
    background: rgba(243, 156, 18, 0.1);
    color: #f39c12;
    border: 1px solid rgba(243, 156, 18, 0.2);
}

.badge-danger {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
    border: 1px solid rgba(231, 76, 60, 0.2);
}

.badge-info {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
    border: 1px solid rgba(52, 152, 219, 0.2);
}

.badge-secondary {
    background: rgba(149, 165, 166, 0.1);
    color: #95a5a6;
    border: 1px solid rgba(149, 165, 166, 0.2);
}

/* =============================================
   FORMULAIRES
   ============================================= */

.form-container {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #2c3e50;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background-color: #fff;
}

.form-control:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 12px center;
    background-repeat: no-repeat;
    background-size: 16px;
    padding-right: 40px;
}

/* =============================================
   PAGINATION
   ============================================= */

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    padding: 20px 0;
}

.pagination a,
.pagination span {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    text-decoration: none;
    color: #495057;
    font-size: 14px;
    transition: all 0.3s ease;
}

.pagination a:hover {
    background-color: #3498db;
    color: white;
    border-color: #3498db;
}

.pagination .active span {
    background-color: #3498db;
    color: white;
    border-color: #3498db;
}

/* =============================================
   MODALS
   ============================================= */

.modal {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-dialog {
    background: white;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.modal-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6c757d;
}

.modal-body {
    padding: 25px;
}

.modal-footer {
    padding: 20px 25px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* =============================================
   ALERTS
   ============================================= */

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    font-size: 14px;
}

.alert-success {
    background-color: rgba(39, 174, 96, 0.1);
    border-color: rgba(39, 174, 96, 0.2);
    color: #27ae60;
}

.alert-danger {
    background-color: rgba(231, 76, 60, 0.1);
    border-color: rgba(231, 76, 60, 0.2);
    color: #e74c3c;
}

.alert-warning {
    background-color: rgba(243, 156, 18, 0.1);
    border-color: rgba(243, 156, 18, 0.2);
    color: #f39c12;
}

.alert-info {
    background-color: rgba(52, 152, 219, 0.1);
    border-color: rgba(52, 152, 219, 0.2);
    color: #3498db;
}

/* =============================================
   GRID SYSTEM
   ============================================= */

.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -15px;
}

.col {
    flex: 1;
    padding: 0 15px;
}

.col-1 { flex: 0 0 8.333333%; max-width: 8.333333%; }
.col-2 { flex: 0 0 16.666667%; max-width: 16.666667%; }
.col-3 { flex: 0 0 25%; max-width: 25%; }
.col-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
.col-5 { flex: 0 0 41.666667%; max-width: 41.666667%; }
.col-6 { flex: 0 0 50%; max-width: 50%; }
.col-7 { flex: 0 0 58.333333%; max-width: 58.333333%; }
.col-8 { flex: 0 0 66.666667%; max-width: 66.666667%; }
.col-9 { flex: 0 0 75%; max-width: 75%; }
.col-10 { flex: 0 0 83.333333%; max-width: 83.333333%; }
.col-11 { flex: 0 0 91.666667%; max-width: 91.666667%; }
.col-12 { flex: 0 0 100%; max-width: 100%; }

/* =============================================
   UTILITIES
   ============================================= */

.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }

.text-primary { color: #3498db; }
.text-success { color: #27ae60; }
.text-danger { color: #e74c3c; }
.text-warning { color: #f39c12; }
.text-info { color: #17a2b8; }
.text-muted { color: #6c757d; }

.bg-primary { background-color: #3498db; }
.bg-success { background-color: #27ae60; }
.bg-danger { background-color: #e74c3c; }
.bg-warning { background-color: #f39c12; }
.bg-info { background-color: #17a2b8; }

.m-0 { margin: 0; }
.m-1 { margin: 0.25rem; }
.m-2 { margin: 0.5rem; }
.m-3 { margin: 1rem; }
.m-4 { margin: 1.5rem; }
.m-5 { margin: 3rem; }

.p-0 { padding: 0; }
.p-1 { padding: 0.25rem; }
.p-2 { padding: 0.5rem; }
.p-3 { padding: 1rem; }
.p-4 { padding: 1.5rem; }
.p-5 { padding: 3rem; }

.mb-0 { margin-bottom: 0; }
.mb-1 { margin-bottom: 0.25rem; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-3 { margin-bottom: 1rem; }
.mb-4 { margin-bottom: 1.5rem; }
.mb-5 { margin-bottom: 3rem; }

.mt-0 { margin-top: 0; }
.mt-1 { margin-top: 0.25rem; }
.mt-2 { margin-top: 0.5rem; }
.mt-3 { margin-top: 1rem; }
.mt-4 { margin-top: 1.5rem; }
.mt-5 { margin-top: 3rem; }

.d-none { display: none; }
.d-block { display: block; }
.d-inline { display: inline; }
.d-inline-block { display: inline-block; }
.d-flex { display: flex; }

.justify-content-start { justify-content: flex-start; }
.justify-content-end { justify-content: flex-end; }
.justify-content-center { justify-content: center; }
.justify-content-between { justify-content: space-between; }
.justify-content-around { justify-content: space-around; }

.align-items-start { align-items: flex-start; }
.align-items-end { align-items: flex-end; }
.align-items-center { align-items: center; }
.align-items-baseline { align-items: baseline; }
.align-items-stretch { align-items: stretch; }

/* =============================================
   RESPONSIVE DESIGN
   ============================================= */

@media (max-width: 768px) {
    .sidebar {
        width: 70px;
        transition: width 0.3s ease;
    }
    
    .sidebar:hover {
        width: 280px;
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
    
    .header {
        padding: 15px 20px;
    }
    
    .header h1 {
        font-size: 20px;
    }
    
    .search-input {
        width: 150px;
    }
    
    .page-content {
        padding: 20px;
    }
    
    .stats-container {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .stat-card {
        padding: 20px;
    }
    
    .stat-value {
        font-size: 24px;
    }
    
    .chart-container {
        padding: 20px;
    }
    
    .chart-content {
        height: 300px;
    }
    
    .data-table {
        font-size: 12px;
    }
    
    .data-table th,
    .data-table td {
        padding: 10px;
    }
}

@media (max-width: 576px) {
    .header-right {
        gap: 10px;
    }
    
    .search-box {
        display: none;
    }
    
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .chart-filters {
        flex-direction: column;
        gap: 5px;
    }
    
    .filter-btn {
        width: 100%;
        text-align: center;
    }
    
    .table-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .table-actions {
        width: 100%;
        justify-content: space-between;
    }
    
    .btn {
        padding: 8px 16px;
        font-size: 12px;
    }
    
    .modal-dialog {
        width: 95%;
        margin: 10px;
    }
    
    .modal-header,
    .modal-body,
    .modal-footer {
        padding: 15px 20px;
    }
}

/* =============================================
   ANIMATIONS
   ============================================= */

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

.animate-slide-in-left {
    animation: slideInLeft 0.5s ease-out;
}

.animate-slide-in-right {
    animation: slideInRight 0.5s ease-out;
}

/* =============================================
   LOADING STATES
   ============================================= */

.loading {
    position: relative;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* =============================================
   PRINT STYLES
   ============================================= */

@media print {
    .sidebar,
    .header,
    .btn,
    .modal {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0;
    }
    
    .page-content {
        padding: 0;
    }
    
    .chart-container,
    .table-container,
    .stat-card {
        box-shadow: none;
        border: 1px solid #ddd;
        page-break-inside: avoid;
    }
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
                        <p>Territoriales décentralisées</p>
                    </div>
                </div>
            </div>

            <div class="sidebar-menu">
                <div class="menu-section">
                    <h4>PRINCIPAL</h4>
                    <ul>
                        <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard.index') }}">
                                <i class="fas fa-chart-line"></i>
                                Tableau de Bord
                            </a>
                        </li>
                        <li class="{{ request()->is('depot-comptes*') ? 'active' : '' }}">
                            <a href="{{ route('depot-comptes.index') }}">
                                <i class="fas fa-file-alt"></i>
                                Dépots de comptes
                            </a>
                        </li>
                        <li class="{{ request()->is('regions*') ? 'active' : '' }}">
                            <a href="{{ route('regions.index') }}">
                                <i class="fas fa-map-marked-alt"></i>
                                Régions
                            </a>
                        </li>
                        <li class="{{ request()->is('departements*') ? 'active' : '' }}">
                            <a href="{{ route('departements.index') }}">
                                <i class="fas fa-map-marked-alt"></i>
                                Departements
                            </a>
                        </li>
                        <li class="{{ request()->is('communes*') ? 'active' : '' }}">
                            <a href="{{ route('communes.index') }}">
                                <i class="fas fa-map-marked-alt"></i>
                                Communes
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h4>GESTION</h4>
                    <ul>
                        <li class="{{ request()->is('receveurs*') ? 'active' : '' }}">
                            <a href="{{ route('receveurs.index') }}">
                                <i class="fas fa-users"></i>
                                Receveurs
                            </a>
                        </li>
                        <li class="{{ request()->is('ordonnateurs*') ? 'active' : '' }}">
                            <a href="{{ route('ordonnateurs.index') }}">
                                <i class="fas fa-user-tie"></i>
                                ordonnateurs
                            </a>
                        </li>
                        <li class="{{ request()->is('dettes-cnps*') ? 'active' : '' }}">
                            <a href="{{ route('dettes-cnps.index') }}">
                                <i class="fas fa-exclamation-triangle"></i>
                                Dettes CNPS
                            </a>
                        </li>
                        <li class="{{ request()->is('dettes-salariales*') ? 'active' : '' }}">
                            <a href="{{ route('dettes-salariales.index') }}">
                                <i class="fas fa-money-bill-wave"></i>
                                Dettes Salariales
                            </a>
                        </li>
                        <li class="{{ request()->is('dettes-fiscale*') ? 'active' : '' }}">
                            <a href="{{ route('dettes-fiscale.index') }}">
                                <i class="fas fa-receipt"></i>
                                Dettes Fiscale
                            </a>
                        </li>
                        <li class="{{ request()->is('dettes-feicom*') ? 'active' : '' }}">
                            <a href="{{ route('dettes-feicom.index') }}">
                                <i class="fas fa-building"></i>
                                Dettes Feicom
                            </a>
                        </li>
                        <li class="{{ request()->is('rapports*') ? 'active' : '' }}">
                            <a href="{{ route('rapports.index') }}">
                                <i class="fas fa-chart-bar"></i>
                                Rapports
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
                    <h1>@yield('page-title', 'Tableau de Bord')</h1>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <input type="text" placeholder="Rechercher..." class="search-input">
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
    @stack('scripts')
</body>
</html> 




