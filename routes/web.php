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
use App\Http\Controllers\DetteController;
use App\Http\Controllers\OrdonnateurController;
use App\Http\Controllers\PrevisionController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\RealisationController;
use App\Http\Controllers\ReceveurController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RessourcesPropresController;
use App\Http\Controllers\RetardController;
use App\Http\Controllers\StatistiquesController;
use App\Http\Controllers\Taux_RealisationController;
use App\Http\Controllers\TauxRealisationController;
use App\Http\Controllers\RessourceEtatController;
use App\Http\Controllers\DonationsExterieuresController;
use App\Http\Controllers\RessourcesTransfereesEtatController;
use App\Http\Controllers\RessourcesPropresController;
use App\Http\Controllers\AutresRessourcesController;
use App\Models\Defaillance;
use App\Models\Infrastructure;
use App\Models\Commune;
use App\Models\ServiceSocial;
use App\Http\Controllers\InfrastructureController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\ServiceSocialController;
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


    Route::group(['prefix' => 'departements', 'as' => 'departements.'], function () {
    // Routes existantes
    Route::get('/', [DepartementController::class, 'index'])->name('index');
    Route::get('/create', [DepartementController::class, 'create'])->name('create');
    Route::post('/', [DepartementController::class, 'store'])->name('store');
    
    // Nouvelles routes pour l'importation
    Route::get('/import', [DepartementController::class, 'showImportForm'])->name('import.form');
    Route::post('/import', [DepartementController::class, 'importAll'])->name('import');
});
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

// Routes pour la gestion des dépôts de comptes
Route::prefix('depot-comptes')->name('depot-comptes.')->group(function () {
    // Routes CRUD classiques
    Route::get('/', [Depot_compteController::class, 'index'])->name('index');
    Route::get('/create', [Depot_compteController::class, 'create'])->name('create');
    Route::post('/', [Depot_compteController::class, 'store'])->name('store');
    Route::get('/{depotCompte}', [Depot_compteController::class, 'show'])->name('show');
    Route::get('/{depotCompte}/edit', [Depot_compteController::class, 'edit'])->name('edit');
    Route::put('/{depotCompte}', [Depot_compteController::class, 'update'])->name('update');
    Route::delete('/{depotCompte}', [Depot_compteController::class, 'destroy'])->name('destroy');
    
    // Routes spécifiques
    Route::get('/rapport/annuel', [Depot_compteController::class, 'rapport'])->name('rapport');
    Route::post('/validation/bulk', [Depot_compteController::class, 'bulkValidation'])->name('bulk-validation');
});




/*
|--------------------------------------------------------------------------
| GESTION DES DETTES
|--------------------------------------------------------------------------
*/



// Routes pour les dettes CNPS
Route::prefix('dettes-cnps')->name('dettes-cnps.')->group(function () {
    Route::get('/', [Dette_CnpsController::class, 'index'])->name('index');
    Route::get('/create', [Dette_CnpsController::class, 'create'])->name('create');
    Route::post('/', [Dette_CnpsController::class, 'store'])->name('store');
    Route::get('/{detteCnps}', [Dette_CnpsController::class, 'show'])->name('show');
    Route::get('/{detteCnps}/edit', [Dette_CnpsController::class, 'edit'])->name('edit');
    Route::put('/{detteCnps}', [Dette_CnpsController::class, 'update'])->name('update');
    Route::delete('/{detteCnps}', [Dette_CnpsController::class, 'destroy'])->name('destroy');
    Route::get('/export/data', [Dette_CnpsController::class, 'export'])->name('export');
    Route::get('/rapport/statistiques', [Dette_CnpsController::class, 'rapport'])->name('rapport');
});

// Routes AJAX pour les filtres dynamiques (optionnel)
Route::get('/api/departements/{region}', function($regionId) {
    return \App\Models\Departement::where('region_id', $regionId)->orderBy('nom')->get();
});

Route::get('/api/communes/{departement}', function($departementId) {
    return \App\Models\Commune::where('departement_id', $departementId)->orderBy('nom')->get();
});


// Routes pour les dettes fiscales
Route::prefix('dettes-fiscale')->name('dettes-fiscale.')->group(function () {
    // Routes CRUD classiques
    Route::get('/', [Dette_FiscaleController::class, 'index'])->name('index');
    Route::get('/create', [Dette_FiscaleController::class, 'create'])->name('create');
    Route::post('/', [Dette_FiscaleController::class, 'store'])->name('store');
    Route::get('/{detteFiscale}', [Dette_FiscaleController::class, 'show'])->name('show');
    Route::get('/{detteFiscale}/edit', [Dette_FiscaleController::class, 'edit'])->name('edit');
    Route::put('/{detteFiscale}', [Dette_FiscaleController::class, 'update'])->name('update');
    Route::delete('/{detteFiscale}', [Dette_FiscaleController::class, 'destroy'])->name('destroy');
    
    // Routes supplémentaires
    Route::get('/dashboard/statistiques', [Dette_FiscaleController::class, 'dashboard'])->name('dashboard');
    Route::get('/rapport/comparatif', [Dette_FiscaleController::class, 'rapportComparatif'])->name('rapport-comparatif');
    Route::get('/export/donnees', [Dette_FiscaleController::class, 'export'])->name('export');
    Route::get('/export/comparatif', [Dette_FiscaleController::class, 'exportComparatif'])->name('export-comparatif');
});




// Routes pour les dettes FEICOM
Route::prefix('dettes-feicom')->name('dettes-feicom.')->group(function () {
    // Routes spécifiques DOIVENT être placées AVANT les routes génériques
    
    // Routes pour les rapports (AVANT les routes génériques)
    Route::get('/rapports/regions', [Dette_FeicomController::class, 'rapportParRegion'])->name('rapport-regions');
    Route::get('/rapports/departements', [Dette_FeicomController::class, 'rapportParDepartement'])->name('rapport-departements');
    
    // Routes pour l'export (AVANT les routes génériques)
    Route::get('/export/data', [Dette_FeicomController::class, 'export'])->name('export');
    Route::get('/export', [Dette_FeicomController::class, 'exportForm'])->name('export.form'); // Si vous avez un formulaire d'export
    
    // Routes API/AJAX (AVANT les routes génériques)
    Route::get('/api/commune-dettes', [Dette_FeicomController::class, 'getDettesByCommune'])->name('getDettesByCommune');
    
    // Routes CRUD de base (APRÈS les routes spécifiques)
    Route::get('/', [Dette_FeicomController::class, 'index'])->name('index');
    Route::get('/create', [Dette_FeicomController::class, 'create'])->name('create');
    Route::post('/', [Dette_FeicomController::class, 'store'])->name('store');
    
    // Routes avec paramètres (TOUJOURS EN DERNIER)
    Route::get('/{detteFeicom}', [Dette_FeicomController::class, 'show'])->name('show');
    Route::get('/{detteFeicom}/edit', [Dette_FeicomController::class, 'edit'])->name('edit');
    Route::put('/{detteFeicom}', [Dette_FeicomController::class, 'update'])->name('update');
    Route::delete('/{detteFeicom}', [Dette_FeicomController::class, 'destroy'])->name('destroy');
});


Route::prefix('dettes-salariales')->name('dettes-salariale.')->group(function () {
    // Routes CRUD principales
    Route::get('/', [Dette_SalarialeController::class, 'index'])->name('index');
    Route::get('/create', [Dette_SalarialeController::class, 'create'])->name('create');
    Route::post('/', [Dette_SalarialeController::class, 'store'])->name('store');
    Route::get('/{detteSalariale}', [Dette_SalarialeController::class, 'show'])->name('show');
    Route::get('/{detteSalariale}/edit', [Dette_SalarialeController::class, 'edit'])->name('edit');
    Route::put('/{detteSalariale}', [Dette_SalarialeController::class, 'update'])->name('update');
    Route::delete('/{detteSalariale}', [Dette_SalarialeController::class, 'destroy'])->name('destroy');
    
    // Routes supplémentaires
    Route::get('/dashboard/overview', [Dette_SalarialeController::class, 'dashboard'])->name('dashboard');
    Route::get('/export/data', [Dette_SalarialeController::class, 'export'])->name('export');
    Route::get('/api/chart-data', [Dette_SalarialeController::class, 'chartData'])->name('chart-data');
});

// Routes API pour AJAX
Route::prefix('api/dettes-salariales')->name('api.dettes-salariale.')->group(function () {
    Route::get('/chart-data', [Dette_SalarialeController::class, 'chartData'])->name('chart-data');
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
    
    // Routes AJAX
    Route::patch('/{receveur}/statut', [ReceveurController::class, 'changerStatut'])->name('changer-statut');
    Route::patch('/{receveur}/commune', [ReceveurController::class, 'assignerCommune'])->name('assigner-commune');
});




Route::prefix('ordonnateurs')->name('ordonnateurs.')->group(function () {
    // Routes CRUD standard
    Route::get('/', [OrdonnateurController::class, 'index'])->name('index');
    Route::get('/create', [OrdonnateurController::class, 'create'])->name('create');
    Route::post('/', [OrdonnateurController::class, 'store'])->name('store');
    Route::get('/{ordonnateur}', [OrdonnateurController::class, 'show'])->name('show');
    Route::get('/{ordonnateur}/edit', [OrdonnateurController::class, 'edit'])->name('edit');
    Route::put('/{ordonnateur}', [OrdonnateurController::class, 'update'])->name('update');
    Route::delete('/{ordonnateur}', [OrdonnateurController::class, 'destroy'])->name('destroy');
    Route::resource('ordonnateurs', OrdonnateurController::class);
    // Routes spécifiques pour l'assignation
    Route::put('/{ordonnateur}/assign-commune', [OrdonnateurController::class, 'assignToCommune'])->name('assign-commune');
    Route::put('/{ordonnateur}/liberer-commune', [OrdonnateurController::class, 'libererDeCommune'])->name('liberer-commune');
});

// Routes API pour AJAX
Route::prefix('api/ordonnateurs')->name('api.ordonnateurs.')->group(function () {
    Route::get('/commune/{commune}', [OrdonnateurController::class, 'getByCommune'])->name('by-commune');
    Route::get('/libres', [OrdonnateurController::class, 'getLibres'])->name('libres');
});








Route::prefix('previsions')->name('previsions.')->group(function () {
    // Routes CRUD de base
    Route::get('/', [PrevisionController::class, 'index'])->name('index');
    Route::get('/create', [PrevisionController::class, 'create'])->name('create');
    Route::post('/', [PrevisionController::class, 'store'])->name('store');
    Route::get('/{prevision}', [PrevisionController::class, 'show'])->name('show');
    Route::get('/{prevision}/edit', [PrevisionController::class, 'edit'])->name('edit');
    Route::put('/{prevision}', [PrevisionController::class, 'update'])->name('update');
    Route::delete('/{prevision}', [PrevisionController::class, 'destroy'])->name('destroy');
    
    // Routes spéciales
    Route::post('/{prevision}/duplicate', [PrevisionController::class, 'duplicate'])->name('duplicate');
    Route::get('/analyses/tendances', [PrevisionController::class, 'analysesTendances'])->name('analyses.tendances');

    // Routes pour l'export
    Route::get('/export/excel', [PrevisionController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/pdf', [PrevisionController::class, 'exportPdf'])->name('export.pdf');
    
    // Routes pour l'import
    Route::get('/import/form', [PrevisionController::class, 'importForm'])->name('import.form');
    Route::post('/import/process', [PrevisionController::class, 'importProcess'])->name('import.process');
});

// ================== ROUTES POUR LES RÉALISATIONS ==================

// Routes CRUD complètes pour les réalisations
Route::resource('realisations', RealisationController::class);

// Routes spécifiques pour les réalisations
Route::group(['prefix' => 'realisations'], function () {
    // Export des réalisations
    Route::get('/export', [RealisationController::class, 'export'])
        ->name('realisations.export');
        Route::get('/statistiques', [RealisationController::class, 'statistiques'])
        ->name('realisations.statistiques');
    
    // API pour récupérer les réalisations par commune
    Route::get('/by-commune/{commune}', [RealisationController::class, 'getByCommune'])
        ->name('realisations.by-commune');
    
    // API pour récupérer les réalisations par département
    Route::get('/by-departement/{departement}', [RealisationController::class, 'getByDepartement'])
        ->name('realisations.by-departement');
    
    // API pour récupérer les réalisations par prévision
    Route::get('/by-prevision/{prevision}', [RealisationController::class, 'getByPrevision'])
        ->name('realisations.by-prevision');
    
    // Statistiques des réalisations par année
    Route::get('/stats/{annee}', [RealisationController::class, 'getStats'])
        ->name('realisations.stats');
    
    // Comparaison département
    Route::get('/comparaison/{departement}/{annee}', [RealisationController::class, 'comparaisonDepartement'])
        ->name('realisations.comparaison-departement');
        Route::get('/prevision/{prevision}', [RealisationController::class, 'indexByPrevision'])->name('by-prevision');
    Route::get('/prevision/{prevision}/create', [RealisationController::class, 'createForPrevision'])->name('create-for-prevision');
    // Dans routes/web.php, ajoutez cette route

 

});
Route::get('/api/previsions-by-commune', [RealisationController::class, 'getPrevisionsByCommune'])
    ->name('previsions.by-commune');Route::get('/realisations/previsions-by-commune', [RealisationController::class, 'getPrevisionsByCommune'])
    ->name('realisations.previsions-by-commune');
   Route::get('/realisations/commune-stats', [RealisationController::class, 'getCommuneStats'])
    ->name('realisations.commune-stats');


// ================== ROUTES COMBINÉES PRÉVISIONS/RÉALISATIONS ==================

Route::group(['prefix' => 'gestion-financiere'], function () {
    // Dashboard des prévisions et réalisations
    Route::get('/dashboard', [PrevisionController::class, 'dashboard'])
        ->name('gestion-financiere.dashboard');
    
    // Analyse comparative prévisions vs réalisations
    Route::get('/analyse/{annee?}', [PrevisionController::class, 'analyseComparative'])
        ->name('gestion-financiere.analyse');
    
    // Rapport global par département
    Route::get('/rapport-departement/{departement}/{annee?}', [PrevisionController::class, 'rapportDepartement'])
        ->name('gestion-financiere.rapport-departement');
    
    // Rapport global par commune
    Route::get('/rapport-commune/{commune}/{annee?}', [RealisationController::class, 'rapportCommune'])
        ->name('gestion-financiere.rapport-commune');
    
    // Export consolidé
    Route::get('/export-consolide', [PrevisionController::class, 'exportConsolide'])
        ->name('gestion-financiere.export-consolide');
});

// ================== ROUTES API POUR LES DONNÉES DYNAMIQUES ==================

// Route::group(['prefix' => 'api', 'middleware' => ['api'

// routes/web.php
// Route::get('/taux-realisations', [Taux_RealisationController::class, 'index'])->name('taux-realisations.index');
// Route::get('/taux-realisations/dashboard', [Taux_RealisationController::class, 'dashboard'])->name('taux-realisations.dashboard');
// Route::get('/taux-realisations/{prevision}', [Taux_RealisationController::class, 'show'])->name('taux-realisations.show');
// Route::get('/taux-realisations/export', [Taux_RealisationController::class, 'export'])->name('taux-realisations.export');

Route::prefix('taux-realisations')->name('taux-realisations.')->group(function () {
    Route::get('/', [TauxRealisationController::class, 'index'])->name('index');
    Route::get('/dashboard', [TauxRealisationController::class, 'dashboard'])->name('dashboard');
    Route::get('/export', [TauxRealisationController::class, 'export'])->name('export');
    Route::get('/{prevision}', [TauxRealisationController::class, 'show'])->name('show');
});

// Routes pour intégration dans les autres pages
Route::get('/communes/{commune}/taux-realisation', [CommunesController::class, 'tauxRealisation'])->name('communes.taux-realisation');
Route::get('/regions/{region}/taux-realisation', [RegionController::class, 'tauxRealisation'])->name('regions.taux-realisation');
Route::get('/departements/{departement}/taux-realisation', [DepartementController::class, 'tauxRealisation'])->name('departements.taux-realisation');

// route::resources('retards',RetardController::class);
// route::resources('defaillances',DefaillanceController::class);
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
    
    // // Notifications
    // Route::get('/notifications', [NotificationController::class, 'getNotifications'])
    //     ->name('notifications');
    
    // Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])
    //     ->name('notifications.read');
 });


 // Routes pour les Communes
Route::prefix('communes')->name('communes.')->group(function () {
    // Route::get('/', [CommuneController::class, 'index'])->name('index');
    // Route::get('/create', [CommuneController::class, 'create'])->name('create');
    // Route::post('/', [CommuneController::class, 'store'])->name('store');
    // Route::get('/{commune}', [CommuneController::class, 'show'])->name('show');
    // Route::get('/{commune}/edit', [CommuneController::class, 'edit'])->name('edit');
    // Route::put('/{commune}', [CommuneController::class, 'update'])->name('update');
    // Route::delete('/{commune}', [CommuneController::class, 'destroy'])->name('destroy');
    
    // Routes AJAX
    Route::get('/ajax/by-departement/{departement}', [CommunesController::class, 'byDepartement'])->name('by-departement');
    Route::get('/ajax/by-region/{region}', [CommunesController::class, 'byRegion'])->name('by-region');
});

// Routes pour les Départements
Route::prefix('departements')->name('departements.')->group(function () {
    // Route::get('/', [DepartementController::class, 'index'])->name('index');
    // Route::get('/create', [DepartementController::class, 'create'])->name('create');
    // Route::post('/', [DepartementController::class, 'store'])->name('store');
    // Route::get('/{departement}', [DepartementController::class, 'show'])->name('show');
    // Route::get('/{departement}/edit', [DepartementController::class, 'edit'])->name('edit');
    // Route::put('/{departement}', [DepartementController::class, 'update'])->name('update');
    // Route::delete('/{departement}', [DepartementController::class, 'destroy'])->name('destroy');
    
    // Routes AJAX
    Route::get('/ajax/by-region/{region}', [DepartementController::class, 'byRegion'])->name('by-region');
});

// // Routes pour les Régions
// Route::prefix('regions')->name('regions.')->group(function () {
//     Route::get('/', [RegionController::class, 'index'])->name('index');
//     Route::get('/create', [RegionController::class, 'create'])->name('create');
//     Route::post('/', [RegionController::class, 'store'])->name('store');
//     Route::get('/{region}', [RegionController::class, 'show'])->name('show');
//     Route::get('/{region}/edit', [RegionController::class, 'edit'])->name('edit');
//     Route::put('/{region}', [RegionController::class, 'update'])->name('update');
//     Route::delete('/{region}', [RegionController::class, 'destroy'])->name('destroy');
// });

// Routes pour les Taux de Réalisation
Route::prefix('taux-realisation')->name('taux-realisation.')->group(function () {
    Route::get('/', [TauxRealisationController::class, 'index'])->name('index');
    Route::get('/rapports', [TauxRealisationController::class, 'rapports'])->name('rapports');
    Route::get('/comparaisons', [TauxRealisationController::class, 'comparaisons'])->name('comparaisons');
    Route::post('/recalculer', [TauxRealisationController::class, 'recalculer'])->name('recalculer');
});

// Routes pour les rapports et statistiques
Route::prefix('rapports')->name('rapports.')->group(function () {
    Route::get('/synthese', [DashboardController::class, 'syntheseAnnuelle'])->name('synthese');
    Route::get('/comparaisons', [DashboardController::class, 'comparaisons'])->name('comparaisons');
    Route::get('/evolution', [DashboardController::class, 'evolution'])->name('evolution');
});

// Routes API pour les données AJAX
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/communes/search', [CommunesController::class, 'search'])->name('communes.search');
    Route::get('/statistiques/{annee}', [DashboardController::class, 'statistiquesAnnee'])->name('statistiques.annee');
    Route::get('/graphique-evolution/{commune}', [PrevisionController::class, 'graphiqueEvolution'])->name('graphique.evolution');
});



















// Route::middleware(['auth'])->group(function () {
    
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // API pour changement de période (AJAX)
    Route::get('/dashboard/periode/{periode}', [DashboardController::class, 'getPeriodeData'])->name('dashboard.periode');
    
    // Export des données du dashboard
    Route::get('/dashboard/export/{format}', [DashboardController::class, 'export'])->name('dashboard.export');
    
    // Statistiques par région (pour les modales/popups)
    Route::get('/dashboard/region/{id}/stats', [DashboardController::class, 'getRegionStats'])->name('dashboard.region.stats');
    
    // Actualisation des données temps réel
    Route::get('/dashboard/refresh', [DashboardController::class, 'refreshData'])->name('dashboard.refresh');

    // Routes pour les autres sections mentionnées dans la sidebar
    
    // INDICATEURS
    // Route::get('/indicateurs', [IndicateursController::class, 'index'])->name('indicateurs.index');
    
    // TERRITOIRE
    Route::resource('regions', RegionController::class);
    Route::resource('departements', DepartementController::class);
    Route::resource('communes', CommunesController::class);
    
    // RESSOURCES
    // Route::resource('ressources-transferees', RessourcesTransfereesController::class);
    // Route::resource('ressources-propres', RessourcesPropresController::class);
    // Route::resource('donations-exterieures', DonationsExterieuresController::class);
    // Route::resource('autres-ressources', AutresRessourcesController::class);
    
    // // EMPLOIS
    // Route::resource('infrastructures', InfrastructuresController::class);
    // Route::resource('equipements', EquipementsController::class);
    // Route::resource('services-sociaux', ServicesSociauxController::class);
    // Route::resource('fonctionnement', FonctionnementController::class);
    
    // // SUIVI BUDGÉTAIRE
    // Route::resource('execution-budgetaire', ExecutionBudgetaireController::class);
    
    // // GOUVERNANCE
    // Route::resource('ordonnateurs', OrdonnateurstController::class);
    // Route::resource('receveurs', ReceveursController::class);
    // Route::resource('depot-comptes', DepotComptesController::class);
    
    // // ENDETTEMENT
    // Route::resource('dettes-cnps', DettesCnpsController::class);
    // Route::resource('dettes-salariales', DettesSalarialesController::class);
    // Route::resource('dettes-fiscales', DettesFiscaleController::class);
    // Route::resource('dettes-feicom', DettesFeicomController::class);
    
    // // DÉFAILLANCES
    // Route::resource('retards-depot', RetardsDepotController::class);
    // Route::resource('defaillances-gestion', DefaillancesGestionController::class);
    // Route::resource('alertes', AlertesController::class);
    
    // // RAPPORTS
    // Route::resource('rapports-performance', RapportsPerformanceController::class);
    // Route::resource('rapports-gouvernance', RapportsGouvernanceController::class);
    // Route::resource('synthese-annuelle', SyntheseAnnuelleController::class);




Route::resource('ressources-etat', RessourcesTransfereesEtatController::class);
// routes/web.php
Route::resource('ressources-commune', RessourcesPropresController::class);
Route::delete('ressources-commune/{ressource}', [RessourcesPropresController::class, 'destroy'])
    ->name('ressources-commune.destroy');
   Route::put('ressources-commune/{id}', [RessourcesPropresController::class, 'update'])
    ->name('ressources-commune.update');
Route::resource('dons-exterieurs', DonationsExterieuresController::class);
Route::resource('autres-ressources', AutresRessourcesController::class);


Route::resource('infrastructures', InfrastructureController::class);
Route::resource('equipements', EquipementController::class);
Route::resource('services-sociaux', ServiceSocialController::class);
Route::resource('fonctionnements', FonctionnementController::class);

// Dashboard
// Route::get('/dashboard', function () {
//     $communes = Commune::count();
//     $infrastructures = Infrastructure::count();
//     $services = ServiceSocial::count();
    
//     return view('dashboard', compact('communes', 'infrastructures', 'services'));
// })->name('dashboard');

// Homepage
Route::get('/', function () {
    return view('welcome');
});

// Routes spécifiques pour les ressources par commune
Route::get('communes/{commune}/ressources-etat', [RessourcesTransféréesÉtatController::class, 'byCommune'])->name('communes.ressources-etat');
// Route::get('communes/{commune}/ressources-commune', [RessourcesPropresController::class, 'byCommune'])->name('communes.ressources-commune');
// Route::get('communes/{commune}/dons-exterieurs', [DonExterieureController::class, 'byCommune'])->name('communes.dons-exterieurs');
Route::get('communes/{commune}/autres-ressources', [AutresRessourceController::class, 'byCommune'])->name('communes.autres-ressources');

// Routes pour l'API (si nécessaire)
Route::prefix('api')->group(function () {
    Route::get('communes', [CommunesController::class, 'apiIndex']);
    Route::get('communes/{commune}/ressources', [CommunesController::class, 'apiRessources']);
});
// });

// Routes API pour les données dynamiques
Route::prefix('api')->middleware(['auth'])->group(function () {
    
    // API Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/charts/{type}', [DashboardController::class, 'getChartData']);
    Route::get('/dashboard/alertes', [DashboardController::class, 'getAlertes']);
 });



//  // TERRITOIRE
// Route::prefix('territoire')->group(function () {
//     // Régions
//     Route::resource('regions', RegionController::class)->only(['index', 'show']);
    
//     // Départements
//     Route::resource('departements', DepartementController::class)->only(['index', 'show']);
    
//     // Communes
//     Route::resource('communes', CommuneController::class)->only(['index', 'show']);
//     Route::get('communes/export', [CommuneController::class, 'export'])->name('communes.export');
// });

// // BUDGET ANNUEL
// Route::prefix('budget')->group(function () {
//     // Vue générale des budgets
//     Route::get('/', [BudgetController::class, 'index'])->name('budgets.index');
//     Route::get('/{budget}', [BudgetController::class, 'show'])->name('budgets.show');
    
//     // Ressources transférées par l'État
//     Route::get('/ressources/transferees', [BudgetController::class, 'ressourcesTransferees'])
//         ->name('ressources-transferees.index');
    
//     // Ressources propres des communes
//     Route::get('/ressources/propres', [BudgetController::class, 'ressourcesPropres'])
//         ->name('ressources-propres.index');
    
//     // Donations extérieures
//     Route::get('/donations/exterieures', [BudgetController::class, 'donationsExterieures'])
//         ->name('donations-exterieures.index');
    
//     // Emplois - Infrastructures
//     Route::get('/emplois/infrastructures', [BudgetController::class, 'emploisInfrastructures'])
//         ->name('infrastructures.index');
    
//     // Emplois - Équipements
//     Route::get('/emplois/equipements', [BudgetController::class, 'emploisEquipements'])
//         ->name('equipements.index');
    
//     // Emplois - Services sociaux
//     Route::get('/emplois/services-sociaux', [BudgetController::class, 'emploisServicesSociaux'])
//         ->name('services-sociaux.index');
    
//     // Emplois - Fonctionnement
//     Route::get('/emplois/fonctionnement', [BudgetController::class, 'emploisFonctionnement'])
//         ->name('fonctionnement.index');
    
//     // Exécution budgétaire
//     Route::get('/execution', [BudgetController::class, 'executionBudgetaire'])
//         ->name('execution-budgetaire.index');
// });

// // GOUVERNANCE
// Route::prefix('gouvernance')->group(function () {
//     // Ordonnateurs
//     Route::resource('ordonnateurs', OrdonnateurController::class)->only(['index', 'show']);
    
//     // Receveurs municipaux
//     Route::resource('receveurs', ReceveurController::class)->only(['index', 'show']);
    
//     // Dépôt de comptes
//     Route::resource('depot-comptes', DepotCompteController::class)->only(['index', 'show']);
//     Route::get('depot-comptes/retards/analyse', [DepotCompteController::class, 'analyseRetards'])
//         ->name('depot-comptes.retards');
// });

// // ENDETTEMENT
// Route::prefix('endettement')->group(function () {
//     // Vue générale des dettes
//     Route::get('/', [DetteController::class, 'index'])->name('dettes.index');
    
//     // Dettes par type
//     Route::get('/cnps', [DetteController::class, 'byCnps'])->name('dettes-cnps.index');
//     Route::get('/salariales', [DetteController::class, 'bySalariale'])->name('dettes-salariale.index');
//     Route::get('/fiscales', [DetteController::class, 'byFiscale'])->name('dettes-fiscale.index');
//     Route::get('/feicom', [DetteController::class, 'byFeicom'])->name('dettes-feicom.index');
    
//     // Analyses spécifiques
//     Route::get('/evolution', [DetteController::class, 'evolution'])->name('dettes.evolution');
//     Route::get('/communes-critiques', [DetteController::class, 'communesCritiques'])
//         ->name('dettes.communes-critiques');
// });

// // DÉFAILLANCES
// Route::prefix('defaillances')->group(function () {
//     // Retards de dépôt
//     Route::get('/retards-depot', [DepotCompteController::class, 'retards'])
//         ->name('retards-depot.index');
    
//     // Défaillances de gestion
//     Route::get('/gestion', [IndicateurController::class, 'defaillancesGestion'])
//         ->name('defaillances-gestion.index');
    
//     // Système d'alertes
//     Route::get('/alertes', [IndicateurController::class, 'alertes'])
//         ->name('alertes.index');
// });

// // INDICATEURS & PERFORMANCE
// Route::prefix('indicateurs')->group(function () {
//     Route::get('/', [IndicateurController::class, 'index'])->name('indicateurs.index');
//     Route::get('/performance', [IndicateurController::class, 'performance'])
//         ->name('indicateurs.performance');
//     Route::get('/comparatifs', [IndicateurController::class, 'comparatifs'])
//         ->name('indicateurs.comparatifs');
//     Route::get('/evolution', [IndicateurController::class, 'evolution'])
//         ->name('indicateurs.evolution');
// });

// RAPPORTS & ANALYSES
Route::prefix('rapports')->group(function () {
    // Performance financière
    Route::get('/performance-financiere', [RapportController::class, 'performanceFinanciere'])
        ->name('rapports-performance.index');
    
    // Gouvernance locale
    Route::get('/gouvernance-locale', [RapportController::class, 'gouvernanceLocale'])
        ->name('rapports-gouvernance.index');
    
    // Synthèse annuelle
    Route::get('/synthese-annuelle', [RapportController::class, 'syntheseAnnuelle'])
        ->name('synthese-annuelle.index');
     });

     Route::resource('ressources-commune', RessourcesPropresController::class);
Route::delete('ressources-commune/{ressource}', [RessourcesPropresController::class, 'destroy'])
    ->name('ressources-commune.destroy');
   Route::put('ressources-commune/{id}', [RessourcesPropresController::class, 'update'])
    ->name('ressources-commune.update');

    Route::get('departements/{departement}/export', [DepartementController::class, 'export'])->name('departements.export');
Route::get('departements/search/ajax', [DepartementController::class, 'search'])->name('departements.search');







