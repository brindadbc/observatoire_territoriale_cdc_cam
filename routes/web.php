<?php

use App\Http\Controllers\CommunesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DefaillanceController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\Depot_compteController;
use App\Http\Controllers\dette_cnpsController;
use App\Http\Controllers\Dette_feicomController;
use App\Http\Controllers\Dette_fiscaleController;
use App\Http\Controllers\Dette_salarialeController;
use App\Http\Controllers\OrdonnateurController;
use App\Http\Controllers\PrevisionController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\RealisationController;
use App\Http\Controllers\ReceveurController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RetardController;
use App\Http\Controllers\StatistiquesController;
use App\Http\Controllers\Taux_RealisationController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::view('/', 'welcome');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::view('/contact', 'contact');

// Redirection de la page d'accueil vers le tableau de bord
Route::get('/dashboard', function () {
    return redirect()->route('dashboard.index');
});

/*
|--------------------------------------------------------------------------
| TABLEAU DE BORD PRINCIPAL
|--------------------------------------------------------------------------
*/



// Tableau de bord principal avec statistiques générales
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard.index');

// Tableau de bord avec filtrage par année
Route::get('/dashboard/{annee}', [DashboardController::class, 'index'])
    ->name('dashboard.annee')
    ->where('annee', '[0-9]{4}');

/*
|--------------------------------------------------------------------------
| GESTION DES RÉGIONS
|--------------------------------------------------------------------------
*/

// // Affichage de toutes les régions
// Route::get('/regions', [RegionController::class, 'index'])
//     ->name('regions.index');

// // Détails d'une région spécifique
// Route::get('/regions/{region}', [RegionController::class, 'show'])
//     ->name('regions.show');

// // Détails d'une région avec filtrage par année
// Route::get('/regions/{region}/{annee}', [RegionController::class, 'show'])
//     ->name('regions.show.annee')
//     ->where('annee', '[0-9]{4}');

// // Statistiques détaillées d'une région (AJAX)
// Route::get('/api/regions/{region}/stats', [RegionController::class, 'getStats'])
//     ->name('api.regions.stats');

// // Données pour les graphiques d'une région (AJAX)
// Route::get('/api/regions/{region}/graphiques/{annee}', [RegionController::class, 'getGraphiquesData'])
//     ->name('api.regions.graphiques')
//     ->where('annee', '[0-9]{4}');



Route::prefix('api')->name('api.')->group(function () {
    Route::get('regions/{region}/stats', [RegionController::class, 'getStats'])
        ->name('regions.stats');
    Route::get('regions/{region}/graphiques/{annee}', [RegionController::class, 'getGraphiquesData'])
        ->name('regions.graphiques')
        ->where('annee', '[0-9]{4}');
    Route::get('regions/{region}/data', [RegionController::class, 'getRegionData'])
        ->name('regions.data');
});

// Routes personnalisées pour les régions (AVANT resource)
Route::get('regions/{region}/{annee}', [RegionController::class, 'show'])
    ->name('regions.show.annee')
    ->where('annee', '[0-9]{4}');

// Routes CRUD standard pour les régions
Route::resource('regions', RegionController::class);


    
    /*
|--------------------------------------------------------------------------
| Routes pour la gestion des Régions
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth'])->group(function () {
    
    // Routes CRUD standard pour les régions
    // Route::resource('regions', RegionController::class);

//     Route::prefix('regions')->name('regions.')->group(function () {
//     Route::get('/', [RegionController::class, 'index'])->name('index');
//     // Route::get('/create', [RegionController::class, 'create'])->name('create');
//     Route::post('/', [RegionController::class, 'store'])->name('store');
//     Route::get('/{region}', [RegionController::class, 'show'])->name('show');
//     Route::get('/{region}/edit', [RegionController::class, 'edit'])->name('edit');
//     Route::put('/{region}', [RegionController::class, 'update'])->name('update');
//     Route::delete('/{region}', [RegionController::class, 'destroy'])->name('destroy');
// });

//     Route::get('/regions/create', [RegionController::class, 'create'])->name('regions.create');
// Route::post('/regions', [RegionController::class, 'store'])->name('regions.store');
    
//     // Route API pour récupérer les données d'une région (utilisée par AJAX)
//     Route::get('regions/{region}/data', [RegionController::class, 'getRegionData'])
//         ->name('regions.data');
        
   
/*
|--------------------------------------------------------------------------
| GESTION DES DÉPARTEMENTS
|--------------------------------------------------------------------------
*/
Route::resource('departements', DepartementController::class);
// Affichage de tous les départements
Route::get('/departements', [DepartementController::class, 'index'])
    ->name('departements.index');

// Départements d'une région spécifique
Route::get('/regions/{region}/departements', [DepartementController::class, 'parRegion'])
    ->name('departements.region');

// Détails d'un département spécifique
Route::get('/departements/{departement}', [DepartementController::class, 'show'])
    ->name('departements.show');

// Détails d'un département avec filtrage par année
Route::get('/departements/{departement}/{annee}', [DepartementController::class, 'show'])
    ->name('departements.show.annee')
    ->where('annee', '[0-9]{4}');

// Statistiques détaillées d'un département (AJAX)
Route::get('/api/departements/{departement}/stats', [DepartementController::class, 'getStats'])
    ->name('api.departements.stats');

// Évolution des performances d'un département (AJAX)
Route::get('/api/departements/{departement}/evolution', [DepartementController::class, 'getEvolution'])
    ->name('api.departements.evolution');

/*
|--------------------------------------------------------------------------
| GESTION DES COMMUNES
|--------------------------------------------------------------------------
*/

// Affichage de toutes les communes
Route::get('/communes', [CommunesController::class, 'index'])
    ->name('communes.index');

Route::resource('communes', CommunesController::class);

// Communes d'un département spécifique
Route::get('/departements/{departement}/communes', [CommunesController::class, 'parDepartement'])
    ->name('communes.departement');

// Communes d'une région spécifique
Route::get('/regions/{region}/communes', [CommunesController::class, 'parRegion'])
    ->name('communes.region');

// Détails d'une commune spécifique
Route::get('/communes/{commune}', [CommunesController::class, 'show'])
    ->name('communes.show');

// Détails d'une commune avec filtrage par année
Route::get('/communes/{commune}/{annee}', [CommunesController::class, 'show'])
    ->name('communes.show.annee')
    ->where('annee', '[0-9]{4}');

// Formulaire de création d'une commune
Route::get('/communes/create', [CommunesController::class, 'create'])
    ->name('communes.create');

// Sauvegarde d'une nouvelle commune
Route::post('/communes', [CommunesController::class, 'store'])
    ->name('communes.store');

// Formulaire d'édition d'une commune
Route::get('/communes/{commune}/edit', [CommunesController::class, 'edit'])
    ->name('communes.edit');

// Mise à jour d'une commune
Route::put('/communes/{commune}', [CommunesController::class, 'update'])
    ->name('communes.update');

// Historique des performances d'une commune (AJAX)
Route::get('/api/communes/{commune}/historique', [CommunesController::class, 'getHistorique'])
    ->name('api.communes.historique');

/*
|--------------------------------------------------------------------------
| GESTION DES DÉPÔTS DE COMPTES
|--------------------------------------------------------------------------
*/

Route::resource('depots-comptes', Depot_compteController::class);

// Affichage de tous les dépôts de comptes
Route::get('/depots-comptes', [Depot_compteController::class, 'index'])
    ->name('depots-comptes.index');

// Dépôts de comptes par commune
Route::get('/communes/{commune}/depots-comptes', [Depot_compteController::class, 'parCommune'])
    ->name('depots.commune');

// Détails d'un dépôt de compte
Route::get('/depots-comptes/{depot}', [Depot_compteController::class, 'show'])
    ->name('depots.show');

// Validation d'un dépôt de compte
Route::patch('/depots-comptes/{depot}/valider', [Depot_compteController::class, 'valider'])
    ->name('depots.valider');

// Rejet d'un dépôt de compte
Route::patch('/depots-comptes/{depot}/rejeter', [Depot_compteController::class, 'rejeter'])
    ->name('depots.rejeter');

/*
|--------------------------------------------------------------------------
| GESTION DES DETTES
|--------------------------------------------------------------------------
*/

// Routes pour les dettes CNPS
Route::prefix('dettes-cnps')->name('dettes-cnps.')->group(function () {
    Route::get('/', [dette_cnpsController::class, 'index'])->name('index');
    Route::get('/create', [dette_cnpsController::class, 'create'])->name('create');
    Route::post('/', [dette_cnpsController::class, 'store'])->name('store');
    Route::get('/{dette}', [dette_cnpsController::class, 'show'])->name('show');
    Route::get('/{dette}/edit', [dette_cnpsController::class, 'edit'])->name('edit');
    Route::put('/{dette}', [dette_cnpsController::class, 'update'])->name('update');
    Route::delete('/{dette}', [dette_cnpsController::class, 'destroy'])->name('destroy');
});

// Routes pour les dettes fiscales
Route::prefix('dettes-fiscales')->name('dettes-fiscale.')->group(function () {
    Route::get('/', [Dette_fiscaleController::class, 'index'])->name('index');
    Route::get('/create', [Dette_fiscaleController::class, 'create'])->name('create');
    Route::post('/', [Dette_fiscaleController::class, 'store'])->name('store');
    Route::get('/{dette}', [Dette_fiscaleController::class, 'show'])->name('show');
    Route::get('/{dette}/edit', [Dette_fiscaleController::class, 'edit'])->name('edit');
    Route::put('/{dette}', [Dette_fiscaleController::class, 'update'])->name('update');
    Route::delete('/{dette}', [Dette_fiscaleController::class, 'destroy'])->name('destroy');
});

// Routes pour les dettes FEICOM
Route::prefix('dettes-feicom')->name('dettes-feicom.')->group(function () {
    Route::get('/', [Dette_feicomController::class, 'index'])->name('index');
    Route::get('/create', [Dette_feicomController::class, 'create'])->name('create');
    Route::post('/', [Dette_feicomController::class, 'store'])->name('store');
    Route::get('/{dette}', [Dette_feicomController::class, 'show'])->name('show');
    Route::get('/{dette}/edit', [Dette_FeicomController::class, 'edit'])->name('edit');
    Route::put('/{dette}', [Dette_FeicomController::class, 'update'])->name('update');
    Route::delete('/{dette}', [Dette_FeicomController::class, 'destroy'])->name('destroy');
});

// Routes pour les dettes salariales
Route::prefix('dettes-salariales')->name('dettes-salariales.')->group(function () {
    Route::get('/', [Dette_salarialeController::class, 'index'])->name('index');
    Route::get('/create', [Dette_salarialeController::class, 'create'])->name('create');
    Route::post('/', [Dette_salarialeController::class, 'store'])->name('store');
    Route::get('/{dette}', [Dette_salarialeController::class, 'show'])->name('show');
    Route::get('/{dette}/edit', [Dette_salarialeController::class, 'edit'])->name('edit');
    Route::put('/{dette}', [Dette_salarialeController::class, 'update'])->name('update');
    Route::delete('/{dette}', [Dette_salarialeController::class, 'destroy'])->name('destroy');
});

/*
|--------------------------------------------------------------------------
| GESTION DES RECEVEURS ET ORDONNATEURS
|--------------------------------------------------------------------------
*/

// Routes pour les receveurs
Route::prefix('receveurs')->name('receveurs.')->group(function () {
    Route::get('/', [ReceveurController::class, 'index'])->name('index');
    Route::get('/create', [ReceveurController::class, 'create'])->name('create');
    Route::post('/', [ReceveurController::class, 'store'])->name('store');
    Route::get('/{receveur}', [ReceveurController::class, 'show'])->name('show');
    Route::get('/{receveur}/edit', [ReceveurController::class, 'edit'])->name('edit');
    Route::put('/{receveur}', [ReceveurController::class, 'update'])->name('update');
    Route::delete('/{receveur}', [ReceveurController::class, 'destroy'])->name('destroy');
});

// Routes pour les ordonnateurs
Route::prefix('ordonnateurs')->name('ordonnateurs.')->group(function () {
    Route::get('/', [OrdonnateurController::class, 'index'])->name('index');
    Route::get('/create', [OrdonnateurController::class, 'create'])->name('create');
    Route::post('/', [OrdonnateurController::class, 'store'])->name('store');
    Route::get('/{ordonnateur}', [OrdonnateurController::class, 'show'])->name('show');
    Route::get('/{ordonnateur}/edit', [OrdonnateurController::class, 'edit'])->name('edit');
    Route::put('/{ordonnateur}', [OrdonnateurController::class, 'update'])->name('update');
    Route::delete('/{ordonnateur}', [OrdonnateurController::class, 'destroy'])->name('destroy');
});

/*
|--------------------------------------------------------------------------
| GESTION DES RAPPORTS ET EXPORTS
|--------------------------------------------------------------------------
*/

// Génération de rapports
Route::prefix('rapports')->name('rapports.')->group(function () {
    // Page principale des rapports
    Route::get('/', [RapportController::class, 'index'])->name('index');
    
    // Rapport général du pays
    Route::get('/general/{annee}', [RapportController::class, 'rapportGeneral'])
        ->name('general')
        ->where('annee', '[0-9]{4}');
    
    // Rapport par région
    Route::get('/region/{region}/{annee}', [RapportController::class, 'genererRapportRegion'])
        ->name('region')
        ->where('annee', '[0-9]{4}');
    
    // Rapport par département
    Route::get('/departement/{departement}/{annee}', [RapportController::class, 'rapportDepartement'])
        ->name('departement')
        ->where('annee', '[0-9]{4}');
    
    // Rapport par commune
    Route::get('/commune/{commune}/{annee}', [RapportController::class, 'rapportCommune'])
        ->name('commune')
        ->where('annee', '[0-9]{4}');
    
    // Rapport de performance
    Route::get('/performance/{annee}', [RapportController::class, 'rapportPerformance'])
        ->name('performance')
        ->where('annee', '[0-9]{4}');
    
    // Rapport des dettes
    Route::get('/dettes/{annee}', [RapportController::class, 'rapportDettes'])
        ->name('dettes')
        ->where('annee', '[0-9]{4}');
    
    // Rapport des défaillances
    Route::get('/defaillances/{annee}', [RapportController::class, 'rapportDefaillances'])
        ->name('defaillances')
        ->where('annee', '[0-9]{4}');
});

// Routes d'export de données
Route::prefix('exports')->name('exports.')->group(function () {
    // Export général
    Route::post('/donnees', [RapportController::class, 'exporterDonnees'])
        ->name('donnees');
    
    // Export par région
    Route::get('/region/{region}/{annee}/{format}', [RapportController::class, 'exportRegion'])
        ->name('region')
        ->where(['annee' => '[0-9]{4}', 'format' => 'excel|csv|pdf']);
    
    // Export par département
    Route::get('/departement/{departement}/{annee}/{format}', [RapportController::class, 'exportDepartement'])
        ->name('departement')
        ->where(['annee' => '[0-9]{4}', 'format' => 'excel|csv|pdf']);
    
    // Export des communes
    Route::get('/communes/{annee}/{format}', [RapportController::class, 'exportCommunes'])
        ->name('communes')
        ->where(['annee' => '[0-9]{4}', 'format' => 'excel|csv|pdf']);
    
    // Export des dépôts de comptes
    Route::get('/depots-comptes/{annee}/{format}', [RapportController::class, 'exportDepots'])
        ->name('depots')
        ->where(['annee' => '[0-9]{4}', 'format' => 'excel|csv|pdf']);
    
    // Export des dettes
    Route::get('/dettes/{type}/{annee}/{format}', [RapportController::class, 'exportDettes'])
        ->name('dettes')
        ->where(['annee' => '[0-9]{4}', 'format' => 'excel|csv|pdf', 'type' => 'cnps|fiscale|feicom|salariale|toutes']);
});

/*
|--------------------------------------------------------------------------
| API ROUTES POUR AJAX ET DONNÉES DYNAMIQUES
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->group(function () {
    // Statistiques générales
    Route::get('/stats/{annee}', [DashboardController::class, 'getStatsApi'])
        ->name('stats')
        ->where('annee', '[0-9]{4}');
    
    // Données pour graphiques du dashboard
    Route::get('/dashboard/graphiques/{annee}', [DashboardController::class, 'getGraphiquesData'])
        ->name('dashboard.graphiques')
        ->where('annee', '[0-9]{4}');
    
    // Données pour la carte des régions
    Route::get('/carte-regions/{annee}', [DashboardController::class, 'getCarteRegions'])
        ->name('carte.regions')
        ->where('annee', '[0-9]{4}');
    
    // Recherche de communes
    Route::get('/communes/search', [CommunesController::class, 'search'])
        ->name('communes.search');
    
    // Recherche de receveurs
    Route::get('/receveurs/search', [ReceveurController::class, 'search'])
        ->name('receveurs.search');
    
    // Recherche d'ordonnateurs
    Route::get('/ordonnateurs/search', [OrdonnateurController::class, 'search'])
        ->name('ordonnateurs.search');
    
    // Validation en temps réel
    Route::post('/validate/commune', [CommunesController::class, 'validateAjax'])
        ->name('validate.commune');
    
//     // Notifications
//     Route::get('/notifications', [NotificationController::class, 'getNotifications'])
//         ->name('notifications');
    
//     Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])
//         ->name('notifications.read');
 });

// /*
// |--------------------------------------------------------------------------
// | ROUTES DE RECHERCHE ET FILTRAGE
// |--------------------------------------------------------------------------
// */

// // Recherche globale
// Route::get('/search', [SearchController::class, 'index'])
//     ->name('search.index');

// Route::post('/search', [SearchController::class, 'search'])
//     ->name('search.results');

// // Filtrage avancé
// Route::post('/filter/communes', [FilterController::class, 'communes'])
//     ->name('filter.communes');

// Route::post('/filter/regions', [FilterController::class, 'regions'])
//     ->name('filter.regions');

// Route::post('/filter/departements', [FilterController::class, 'departements'])
//     ->name('filter.departements');

// /*
// |--------------------------------------------------------------------------
// | ROUTES D'ADMINISTRATION
// |--------------------------------------------------------------------------
// */

// // Groupe de routes pour l'administration (avec middleware admin)
// Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
//     // Dashboard admin
//     Route::get('/dashboard', [AdminController::class, 'dashboard'])
//         ->name('dashboard');
    
//     // // Gestion des utilisateurs
//     // Route::resource('users', UserController::class);
    
//     // Gestion des permissions
//     Route::resource('permissions', PermissionController::class);
    
//     // Paramètres système
//     Route::get('/settings', [AdminController::class, 'settings'])
//         ->name('settings');
    
//     Route::post('/settings', [AdminController::class, 'updateSettings'])
//         ->name('settings.update');
    
//     // Logs et audit
//     Route::get('/logs', [AdminController::class, 'logs'])
//         ->name('logs');
    
//     // Sauvegarde et restauration
//     Route::post('/backup', [AdminController::class, 'backup'])
//         ->name('backup');
    
//     Route::post('/restore', [AdminController::class, 'restore'])
//         ->name('restore');
// });

// /*
// |--------------------------------------------------------------------------
// | ROUTES DE CONFIGURATION ET MAINTENANCE
// |--------------------------------------------------------------------------
// */

// // Mise à jour des taux de réalisation (tâche cron)
// Route::get('/cron/update-taux-realisation', [CronController::class, 'updateTauxRealisation'])
//     ->name('cron.taux.realisation');

// // Calcul automatique des défaillances
// Route::get('/cron/calculate-defaillances', [CronController::class, 'calculateDefaillances'])
//     ->name('cron.defaillances');

// // Nettoyage des données temporaires
// Route::get('/cron/cleanup', [CronController::class, 'cleanup'])
//     ->name('cron.cleanup');

// /*
// |--------------------------------------------------------------------------
// | ROUTES DE TÉLÉCHARGEMENT DE DOCUMENTS
// |--------------------------------------------------------------------------
// */

// // Téléchargement de rapports PDF
// Route::get('/download/rapport/{type}/{id}/{annee}', [DownloadController::class, 'rapport'])
//     ->name('download.rapport')
//     ->where(['annee' => '[0-9]{4}', 'type' => 'region|departement|commune']);

// // Téléchargement de fichiers Excel
// Route::get('/download/excel/{type}/{annee}', [DownloadController::class, 'excel'])
//     ->name('download.excel')
//     ->where(['annee' => '[0-9]{4}']);

// // Téléchargement de modèles
// Route::get('/download/template/{type}', [DownloadController::class, 'template'])
//     ->name('download.template');

// /*
// |--------------------------------------------------------------------------
// | ROUTES POUR GESTION DES ERREURS ET HELP
// |--------------------------------------------------------------------------
// */

// // Page d'aide
// Route::get('/help', function () {
//     return view('help.index');
// })->name('help');

// // FAQ
// Route::get('/faq', function () {
//     return view('help.faq');
// })->name('faq');

// // Contact support
// Route::get('/contact', function () {
//     return view('contact.index');
// })->name('contact');

// Route::post('/contact', [ContactController::class, 'send'])
//     ->name('contact.send');

// // Route fallback pour les erreurs 404
// Route::fallback(function () {
//     return response()->view('errors.404', [], 404);
// });