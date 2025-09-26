<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::withCount(['departements', 'communes'])
            ->orderBy('nom')
            ->paginate(10);

        // Statistiques globales
        $stats = [
            'total_regions' => Region::count(),
            'total_departements' => DB::table('departements')->count(),
            'total_communes' => DB::table('communes')->count(),
            'population_totale' => Region::sum('population')
        ];

        // Données pour les graphiques
        $regionsBudget = $this->getBudgetParRegion();
        $performanceRegions = $this->getPerformanceRegions();

        return view('regions.index', compact(
            'regions', 
            'stats', 
            'regionsBudget', 
            'performanceRegions'
        ));
    }

    public function show(Region $region)
    {
        // Charger les relations avec les comptes
        $region->load(['departements.communes']);
        
        // Compter les communes via les départements ou directement selon votre structure
        $nombreCommunes = $region->departements->sum(function ($departement) {
            return $departement->communes->count();
        });
        
        // Alternative si vous avez une relation directe communes() dans le modèle Region
        // $nombreCommunes = $region->communes()->count();

        // Statistiques de la région
        $stats = [
            'nombre_departements' => $region->departements->count(),
            'nombre_communes' => $nombreCommunes,
            'budget_total' => $this->getBudgetRegion($region->id),
            'taux_execution' => $this->getTauxExecutionRegion($region->id),
            'population' => $region->population,
            'superficie' => $region->superficie
        ];

        // Performance des communes de la région
        $communesPerformance = $this->getCommunesPerformance($region->id);
        
        // Evolution budgétaire de la région
        $evolutionBudget = $this->getEvolutionBudgetRegion($region->id);
        
        // Indicateurs de gouvernance
        $gouvernance = $this->getIndicateursGouvernanceRegion($region->id);

        return view('regions.show', compact(
            'region', 
            'stats', 
            'communesPerformance', 
            'evolutionBudget', 
            'gouvernance'
        ));
    }

    public function create()
    {
        return view('regions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:regions',
            'chef_lieu' => 'required|string|max:255',
            'superficie' => 'nullable|numeric|min:0',
            'population' => 'nullable|integer|min:0'
        ]);

        $region = Region::create($validated);

        return redirect()->route('regions.index')
            ->with('success', 'Région créée avec succès.');
    }

    public function edit(Region $region)
    {
        return view('regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:regions,code,' . $region->id,
            'chef_lieu' => 'required|string|max:255',
            'superficie' => 'nullable|numeric|min:0',
            'population' => 'nullable|integer|min:0'
        ]);

        $region->update($validated);

        return redirect()->route('regions.index')
            ->with('success', 'Région mise à jour avec succès.');
    }

    public function destroy(Region $region)
    {
        if ($region->departements()->count() > 0) {
            return redirect()->route('regions.index')
                ->with('error', 'Impossible de supprimer une région qui contient des départements.');
        }

        $region->delete();

        return redirect()->route('regions.index')
            ->with('success', 'Région supprimée avec succès.');
    }

    // API pour récupérer les données d'une région
    public function api(Region $region)
    {
        return response()->json([
            'region' => $region,
            'departements' => $region->departements()->count(),
            'communes' => $region->communes()->count(),
            'budget_total' => $this->getBudgetRegion($region->id),
            'taux_execution' => $this->getTauxExecutionRegion($region->id)
        ]);
    }

    // Méthodes privées pour récupérer les données

    private function getBudgetParRegion()
    {
        return [
            ['region' => 'Centre', 'budget' => 245.8, 'communes' => 71],
            ['region' => 'Littoral', 'budget' => 189.3, 'communes' => 34],
            ['region' => 'Ouest', 'budget' => 156.2, 'communes' => 42],
            ['region' => 'Sud', 'budget' => 134.5, 'communes' => 23],
            ['region' => 'Est', 'budget' => 128.7, 'communes' => 31],
            ['region' => 'Nord', 'budget' => 112.4, 'communes' => 38],
            ['region' => 'Adamaoua', 'budget' => 98.6, 'communes' => 21],
            ['region' => 'Sud-Ouest', 'budget' => 87.3, 'communes' => 33],
            ['region' => 'Nord-Ouest', 'budget' => 76.9, 'communes' => 32],
            ['region' => 'Extrême-Nord', 'budget' => 72.1, 'communes' => 49]
        ];
    }

    private function getPerformanceRegions()
    {
        return [
            ['region' => 'Centre', 'taux_execution' => 78.5, 'depot_comptes' => 85.2, 'score' => 7.8],
            ['region' => 'Littoral', 'taux_execution' => 82.1, 'depot_comptes' => 91.2, 'score' => 8.2],
            ['region' => 'Ouest', 'taux_execution' => 75.3, 'depot_comptes' => 78.6, 'score' => 7.1],
            ['region' => 'Sud', 'taux_execution' => 71.8, 'depot_comptes' => 73.9, 'score' => 6.9],
            ['region' => 'Est', 'taux_execution' => 69.4, 'depot_comptes' => 69.6, 'score' => 6.5],
            ['region' => 'Nord', 'taux_execution' => 67.2, 'depot_comptes' => 65.8, 'score' => 6.2],
            ['region' => 'Adamaoua', 'taux_execution' => 64.8, 'depot_comptes' => 62.1, 'score' => 5.9],
            ['region' => 'Sud-Ouest', 'taux_execution' => 61.5, 'depot_comptes' => 58.7, 'score' => 5.6],
            ['region' => 'Nord-Ouest', 'taux_execution' => 59.3, 'depot_comptes' => 55.4, 'score' => 5.3],
            ['region' => 'Extrême-Nord', 'taux_execution' => 52.1, 'depot_comptes' => 48.9, 'score' => 4.8]
        ];
    }

    private function getBudgetRegion($regionId)
    {
        // Simulation - remplacer par requête réelle
        $budgets = [245.8, 189.3, 156.2, 134.5, 128.7, 112.4, 98.6, 87.3, 76.9, 72.1];
        return $budgets[$regionId % count($budgets)];
    }

    private function getTauxExecutionRegion($regionId)
    {
        // Simulation - remplacer par requête réelle
        $taux = [78.5, 82.1, 75.3, 71.8, 69.4, 67.2, 64.8, 61.5, 59.3, 52.1];
        return $taux[$regionId % count($taux)];
    }

    private function getCommunesPerformance($regionId)
    {
        return [
            ['nom' => 'Commune A', 'budget' => '25.3M', 'taux_execution' => 92.1, 'score' => 8.5],
            ['nom' => 'Commune B', 'budget' => '18.7M', 'taux_execution' => 87.4, 'score' => 7.9],
            ['nom' => 'Commune C', 'budget' => '15.2M', 'taux_execution' => 83.6, 'score' => 7.2],
            ['nom' => 'Commune D', 'budget' => '12.8M', 'taux_execution' => 79.3, 'score' => 6.8],
            ['nom' => 'Commune E', 'budget' => '9.5M', 'taux_execution' => 75.1, 'score' => 6.3]
        ];
    }

    private function getEvolutionBudgetRegion($regionId)
    {
        return [
            ['annee' => '2020', 'budget' => 180.2, 'execution' => 142.5],
            ['annee' => '2021', 'budget' => 195.8, 'execution' => 156.3],
            ['annee' => '2022', 'budget' => 210.4, 'execution' => 168.7],
            ['annee' => '2023', 'budget' => 225.1, 'execution' => 182.4],
            ['annee' => '2024', 'budget' => 245.8, 'execution' => 195.8]
        ];
    }

    private function getIndicateursGouvernanceRegion($regionId)
    {
        return [
            'depot_comptes_a_temps' => 85.2,
            'presence_ordonnateur' => 94.7,
            'presence_receveur' => 89.3,
            'conformite_procedures' => 78.6,
            'communes_defaillantes' => 8
        ];
    }
}





// <?php

// namespace App\Http\Controllers;

// use App\Models\Region;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class RegionController extends Controller
// {
//     public function index()
//     {
//         $regions = Region::withCount(['departements', 'communes'])
//             ->orderBy('nom')
//             ->paginate(10);

//         // Statistiques globales
//         $stats = [
//             'total_regions' => Region::count(),
//             'total_departements' => DB::table('departements')->count(),
//             'total_communes' => DB::table('communes')->count(),
//             'population_totale' => Region::sum('population')
//         ];

//         // Données pour les graphiques
//         $regionsBudget = $this->getBudgetParRegion();
//         $performanceRegions = $this->getPerformanceRegions();

//         return view('regions.index', compact(
//             'regions', 
//             'stats', 
//             'regionsBudget', 
//             'performanceRegions'
//         ));
//     }

//     public function show(Region $region)
//     {
//         $region->load(['departements.communes']);

//         // Statistiques de la région
//         $stats = [
//             'nombre_departements' => $region->departements->count(),
//             'nombre_communes' => $region->communes()->count(),
//             'budget_total' => $this->getBudgetRegion($region->id),
//             'taux_execution' => $this->getTauxExecutionRegion($region->id),
//             'population' => $region->population,
//             'superficie' => $region->superficie
//         ];

//         // Performance des communes de la région
//         $communesPerformance = $this->getCommunesPerformance($region->id);
        
//         // Evolution budgétaire de la région
//         $evolutionBudget = $this->getEvolutionBudgetRegion($region->id);
        
//         // Indicateurs de gouvernance
//         $gouvernance = $this->getIndicateursGouvernanceRegion($region->id);

//         return view('regions.show', compact(
//             'region', 
//             'stats', 
//             'communesPerformance', 
//             'evolutionBudget', 
//             'gouvernance'
//         ));
//     }

//     public function create()
//     {
//         return view('regions.create');
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'nom' => 'required|string|max:255',
//             'code' => 'required|string|max:10|unique:regions',
//             'chef_lieu' => 'required|string|max:255',
//             'superficie' => 'nullable|numeric|min:0',
//             'population' => 'nullable|integer|min:0'
//         ]);

//         $region = Region::create($validated);

//         return redirect()->route('regions.index')
//             ->with('success', 'Région créée avec succès.');
//     }

//     public function edit(Region $region)
//     {
//         return view('regions.edit', compact('region'));
//     }

//     public function update(Request $request, Region $region)
//     {
//         $validated = $request->validate([
//             'nom' => 'required|string|max:255',
//             'code' => 'required|string|max:10|unique:regions,code,' . $region->id,
//             'chef_lieu' => 'required|string|max:255',
//             'superficie' => 'nullable|numeric|min:0',
//             'population' => 'nullable|integer|min:0'
//         ]);

//         $region->update($validated);

//         return redirect()->route('regions.index')
//             ->with('success', 'Région mise à jour avec succès.');
//     }

//     public function destroy(Region $region)
//     {
//         if ($region->departements()->count() > 0) {
//             return redirect()->route('regions.index')
//                 ->with('error', 'Impossible de supprimer une région qui contient des départements.');
//         }

//         $region->delete();

//         return redirect()->route('regions.index')
//             ->with('success', 'Région supprimée avec succès.');
//     }

//     // API pour récupérer les données d'une région
//     public function api(Region $region)
//     {
//         return response()->json([
//             'region' => $region,
//             'departements' => $region->departements()->count(),
//             'communes' => $region->communes()->count(),
//             'budget_total' => $this->getBudgetRegion($region->id),
//             'taux_execution' => $this->getTauxExecutionRegion($region->id)
//         ]);
//     }

//     // Méthodes privées pour récupérer les données

//     private function getBudgetParRegion()
//     {
//         return [
//             ['region' => 'Centre', 'budget' => 245.8, 'communes' => 71],
//             ['region' => 'Littoral', 'budget' => 189.3, 'communes' => 34],
//             ['region' => 'Ouest', 'budget' => 156.2, 'communes' => 42],
//             ['region' => 'Sud', 'budget' => 134.5, 'communes' => 23],
//             ['region' => 'Est', 'budget' => 128.7, 'communes' => 31],
//             ['region' => 'Nord', 'budget' => 112.4, 'communes' => 38],
//             ['region' => 'Adamaoua', 'budget' => 98.6, 'communes' => 21],
//             ['region' => 'Sud-Ouest', 'budget' => 87.3, 'communes' => 33],
//             ['region' => 'Nord-Ouest', 'budget' => 76.9, 'communes' => 32],
//             ['region' => 'Extrême-Nord', 'budget' => 72.1, 'communes' => 49]
//         ];
//     }

//     private function getPerformanceRegions()
//     {
//         return [
//             ['region' => 'Centre', 'taux_execution' => 78.5, 'depot_comptes' => 85.2, 'score' => 7.8],
//             ['region' => 'Littoral', 'taux_execution' => 82.1, 'depot_comptes' => 91.2, 'score' => 8.2],
//             ['region' => 'Ouest', 'taux_execution' => 75.3, 'depot_comptes' => 78.6, 'score' => 7.1],
//             ['region' => 'Sud', 'taux_execution' => 71.8, 'depot_comptes' => 73.9, 'score' => 6.9],
//             ['region' => 'Est', 'taux_execution' => 69.4, 'depot_comptes' => 69.6, 'score' => 6.5],
//             ['region' => 'Nord', 'taux_execution' => 67.2, 'depot_comptes' => 65.8, 'score' => 6.2],
//             ['region' => 'Adamaoua', 'taux_execution' => 64.8, 'depot_comptes' => 62.1, 'score' => 5.9],
//             ['region' => 'Sud-Ouest', 'taux_execution' => 61.5, 'depot_comptes' => 58.7, 'score' => 5.6],
//             ['region' => 'Nord-Ouest', 'taux_execution' => 59.3, 'depot_comptes' => 55.4, 'score' => 5.3],
//             ['region' => 'Extrême-Nord', 'taux_execution' => 52.1, 'depot_comptes' => 48.9, 'score' => 4.8]
//         ];
//     }

//     private function getBudgetRegion($regionId)
//     {
//         // Simulation - remplacer par requête réelle
//         $budgets = [245.8, 189.3, 156.2, 134.5, 128.7, 112.4, 98.6, 87.3, 76.9, 72.1];
//         return $budgets[$regionId % count($budgets)];
//     }

//     private function getTauxExecutionRegion($regionId)
//     {
//         // Simulation - remplacer par requête réelle
//         $taux = [78.5, 82.1, 75.3, 71.8, 69.4, 67.2, 64.8, 61.5, 59.3, 52.1];
//         return $taux[$regionId % count($taux)];
//     }

//     private function getCommunesPerformance($regionId)
//     {
//         return [
//             ['nom' => 'Commune A', 'budget' => '25.3M', 'taux_execution' => 92.1, 'score' => 8.5],
//             ['nom' => 'Commune B', 'budget' => '18.7M', 'taux_execution' => 87.4, 'score' => 7.9],
//             ['nom' => 'Commune C', 'budget' => '15.2M', 'taux_execution' => 83.6, 'score' => 7.2],
//             ['nom' => 'Commune D', 'budget' => '12.8M', 'taux_execution' => 79.3, 'score' => 6.8],
//             ['nom' => 'Commune E', 'budget' => '9.5M', 'taux_execution' => 75.1, 'score' => 6.3]
//         ];
//     }

//     private function getEvolutionBudgetRegion($regionId)
//     {
//         return [
//             ['annee' => '2020', 'budget' => 180.2, 'execution' => 142.5],
//             ['annee' => '2021', 'budget' => 195.8, 'execution' => 156.3],
//             ['annee' => '2022', 'budget' => 210.4, 'execution' => 168.7],
//             ['annee' => '2023', 'budget' => 225.1, 'execution' => 182.4],
//             ['annee' => '2024', 'budget' => 245.8, 'execution' => 195.8]
//         ];
//     }

//     private function getIndicateursGouvernanceRegion($regionId)
//     {
//         return [
//             'depot_comptes_a_temps' => 85.2,
//             'presence_ordonnateur' => 94.7,
//             'presence_receveur' => 89.3,
//             'conformite_procedures' => 78.6,
//             'communes_defaillantes' => 8
//         ];
//     }
// }







// <!-- <?php

// namespace App\Http\Controllers;

// use App\Models\Commune;
// use App\Models\Defaillance;
// use App\Models\Departement;
// use App\Models\Depot_compte;
// use App\Models\dette_cnps;
// use App\Models\Region;
// use App\Models\Taux_realisation;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;

// class RegionController extends Controller


// { 


//     /**
//      * Affichage du formulaire de création d'une région
//      */
//     public function create()
//     {
//         // Débogage - ajoutez ceci temporairement
//         \Log::info('Méthode create() appelée');
        
//         // Vérifiez que la vue existe
//         if (!view()->exists('regions.create')) {
//             \Log::error("La vue regions.create n\'existe pas'");
//             abort(404, 'Vue non trouvée');
//         }
        
//         \Log::info('Tentative de rendu de la vue regions.create');
        
//         try {
//             return view('regions.create');
//         } catch (\Exception $e) {
//             \Log::error('Erreur lors du rendu de la vue: ' . $e->getMessage());
//             throw $e;
//         }


//         //  return view('regions.create');


//     }

//     /**
//      * Affichage de la liste des régions avec possibilité de gestion
//      */
//     public function index()
//     {
//         $annee = request('annee', date('Y'));
        
//         try {
//             $regions = Region::with(['departements.communes'])
//                 ->get()
//                 ->map(function($region) use ($annee) {
//                     return [
//                         'id' => $region->id,
//                         'nom' => $region->nom,
//                         'nb_departements' => $region->departements->count(),
//                         'nb_communes' => $this->getNbCommunesParRegion($region->id),
//                         'taux_moyen_realisation' => $this->getTauxMoyenRealisation($region->id, $annee),
//                         'total_dettes_cnps' => $this->getTotalDettesCNPS($region->id, $annee),
//                         'conformite_depots' => $this->getConformiteDepots($region->id, $annee),
//                         'status' => $this->getStatusRegion($region->id, $annee)
//                     ];
//                 });
            
//             // Variables pour éviter les erreurs dans la vue
//             $budgetData = [];
//             $tauxRealisationData = [];
            
//             return view('regions.index', compact('regions', 'annee', 'budgetData', 'tauxRealisationData'));
            
//         } catch (\Exception $e) {
//             \Log::error('Erreur dans RegionController@index: ' . $e->getMessage());
//             return back()->with('error', 'Erreur lors du chargement des données');
//         }
//     }

//     /**
//      * Enregistrement d'une nouvelle région
//      */
//     public function store(Request $request)
//     {
//         \Log::info('Méthode store() appelée avec données: ', $request->all());
        
//         $validator = Validator::make($request->all(), [
//             'nom' => 'required|string|max:255|unique:regions,nom'
//         ], [
//             'nom.required' => 'Le nom de la région est obligatoire.',
//             'nom.unique' => 'Une région avec ce nom existe déjà.',
//             'nom.max' => 'Le nom de la région ne peut pas dépasser 255 caractères.'
//         ]);

//         if ($validator->fails()) {
//             \Log::warning('Validation échouée: ', $validator->errors()->toArray());
//             return redirect()->back()
//                 ->withErrors($validator)
//                 ->withInput();
//         }

//         try {
//             DB::beginTransaction();

//             $region = Region::create([
//                 'nom' => trim($request->nom)
//             ]);

//             DB::commit();
            
//             \Log::info("'Région créée avec succès: ', $region->toArray()");

//             return redirect()->route('regions.index')
//                 ->with('success', 'Région créée avec succès.');

//         } catch (\Exception $e) {
//             DB::rollback();
//             \Log::error('Erreur lors de la création: ' . $e->getMessage());
//             return redirect()->back()
//                 ->with('error', 'Erreur lors de la création de la région: ' . $e->getMessage())
//                 ->withInput();
//         }
//     }

//     // ... Resto des méthodes identiques ...
    
//     private function getTauxMoyenRealisation($regionId, $annee)
//     {
//         return Taux_realisation::whereHas('commune.departement', function($q) use ($regionId) {
//             $q->where('region_id', $regionId);
//         })->where('annee_exercice', $annee)->avg('pourcentage') ?? 0;
//     }
    
    



//     private function getTotalDettesCNPS($regionId, $annee)
// {
//     return dette_cnps::whereHas('commune.departement', function($q) use ($regionId) {
//         $q->where('region_id', $regionId);
//     })
//     ->whereYear('date_evaluation', $annee)
//     ->sum('montant') ?? 0;
// }


// private function getEtatComptesParCommune($regionId, $annee)
// {
//     return Commune::whereHas('departement', function($q) use ($regionId) {
//         $q->where('region_id', $regionId);
//     })->with(['departement', 'receveurs', 'ordonnateurs', 'depotsComptes', 'dettesCnps', 'previsions', 'realisations', 'tauxRealisations'])
//     ->get()
//     ->map(function($commune) use ($annee) {
//         // Utiliser filter() au lieu de whereYear() sur les collections
//         $depotCompte = $commune->depotsComptes
//             ->where('annee_exercice', $annee)->first();
            
//         // Pour les dates, utiliser filter() avec une fonction de callback
//         $detteCnps = $commune->dettesCnps
//             ->filter(function($dette) use ($annee) {
//                 return $dette->date_evaluation && 
//                        \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
//             })->first();
            
//         return [
//             'id' => $commune->id,
//             'code' => $commune->code,
//             'commune' => $commune->nom,
//             'departement' => $commune->departement->nom,
//             'telephone' => $commune->telephone,
//             'receveur' => $commune->receveurs->first()?->nom,
//             'ordonnateur' => $commune->ordonnateurs->first()?->nom,
//             'depot_date' => $depotCompte?->date_depot,
//             'depot_valide' => $depotCompte?->validation ?? false,
//             'prevision' => $commune->previsions->where('annee_exercice', $annee)->first()?->montant,
//             'realisation' => $commune->realisations->where('annee_exercice', $annee)->sum('montant'),
//             'dette_cnps' => $detteCnps?->montant ?? 0,
//             'taux_realisation' => $commune->tauxRealisations->where('annee_exercice', $annee)->first()?->pourcentage ?? 0,
//             'status' => $this->getStatusCommune($commune, $annee)
//         ];
//     });
// }


// private function getDefaillancesRegion($regionId, $annee)
// {
//     return Defaillance::whereHas('commune.departement', function($q) use ($regionId) {
//         $q->where('region_id', $regionId);
//     })
//     ->whereYear('date_constat', $annee) // whereYear() dans la requête principale
//     ->with('commune')
//     ->get()
//     ->map(function($defaillance) {
//         return [
//             'commune' => $defaillance->commune->nom,
//             'type_defaillance' => $defaillance->type_defaillance,
//             'date_constat' => $defaillance->date_constat,
//             'description' => $defaillance->description,
//             'gravite' => $defaillance->gravite ?? 'normale',
//             'status' => $defaillance->est_resolue ? 'Résolu' : 'Non résolu'
//         ];
//     });
// }


// private function getConformiteDepots($regionId, $annee)
// {
//     // Option 1: Requête directe (plus efficace)
//     $total = Depot_compte::whereHas('commune.departement', function($q) use ($regionId) {
//         $q->where('region_id', $regionId);
//     })->whereYear('date_depot', $annee)->count();
    
//     $conformes = Depot_compte::whereHas('commune.departement', function($q) use ($regionId) {
//         $q->where('region_id', $regionId);
//     })->whereYear('date_depot', $annee)->where('validation', true)->count();
    
//     return $total > 0 ? ($conformes / $total) * 100 : 0;
// }


// private function getConformiteDepotsAvecCollection($regionId, $annee)
// {
//     $depots = Depot_compte::whereHas('commune.departement', function($q) use ($regionId) {
//         $q->where('region_id', $regionId);
//     })->get();
    
//     // Filtrer par année sur la collection
//     $depotsAnnee = $depots->filter(function($depot) use ($annee) {
//         return $depot->date_depot && 
//                \Carbon\Carbon::parse($depot->date_depot)->year == $annee;
//     });
    
//     $total = $depotsAnnee->count();
//     $conformes = $depotsAnnee->where('validation', true)->count();
    
//     return $total > 0 ? ($conformes / $total) * 100 : 0;
// }
    
//     private function getNbCommunesParRegion($regionId)
//     {
//         return Commune::whereHas('departement', function($q) use ($regionId) {
//             $q->where('region_id', $regionId);
//         })->count();
//     }
    
//     private function getStatusRegion($regionId, $annee)
//     {
//         $tauxMoyen = $this->getTauxMoyenRealisation($regionId, $annee);
        
//         if ($tauxMoyen >= 85) return 'Excellent';
//         if ($tauxMoyen >= 70) return 'Bon';
//         if ($tauxMoyen >= 50) return 'Moyen';
//         return 'Faible';
//     }

     

//     /**
//      * Affichage des détails d'une région
//      */
//     public function show(Region $region)
//     {
//         $annee = request('annee', date('Y'));
        
//         // Statistiques de la région
//         $stats = [
//             'nb_communes' => $this->getNbCommunesParRegion($region->id),
//             'taux_moyen_realisation' => $this->getTauxMoyenRealisation($region->id, $annee),
//             'total_dettes_cnps' => $this->getTotalDettesCNPS($region->id, $annee),
//             'conformite_depots' => $this->getConformiteDepots($region->id, $annee)
//         ];
        
//         // Budget Prévisions vs Réalisations par département
//         $budgetData = $this->getBudgetParDepartement($region->id, $annee);
        
//         // Taux de réalisation par département (graphique en donut)
//         $tauxRealisationData = $this->getTauxRealisationParDepartement($region->id, $annee);
        
//         // État des comptes et conformités (tableau)
//         $etatComptes = $this->getEtatComptesParCommune($region->id, $annee);
        
//         // Défaillances et problèmes de conformité
//         $defaillances = $this->getDefaillancesRegion($region->id, $annee);
        
//         return view('regions.show', compact(
//             'region', 'stats', 'budgetData', 'tauxRealisationData', 
//             'etatComptes', 'defaillances', 'annee'
//         ));
//     }

//     /**
//      * Affichage du formulaire d'édition d'une région
//      */
//     public function edit(Region $region)
//     {
//         return view('regions.edit', compact('region'));
//     }

//     /**
//      * Mise à jour d'une région
//      */
//     public function update(Request $request, Region $region)
//     {
//         $validator = Validator::make($request->all(), [
//             'nom' => 'required|string|max:255|unique:regions,nom,' . $region->id
//         ], [
//             'nom.required' => 'Le nom de la région est obligatoire.',
//             'nom.unique' => 'Une région avec ce nom existe déjà.',
//             'nom.max' => 'Le nom de la région ne peut pas dépasser 255 caractères.'
//         ]);

//         if ($validator->fails()) {
//             return redirect()->back()
//                 ->withErrors($validator)
//                 ->withInput();
//         }

//         try {
//             DB::beginTransaction();

//             $region->update([
//                 'nom' => trim($request->nom)
//             ]);

//             DB::commit();

//             return redirect()->route('regions.index')
//                 ->with('success', 'Région modifiée avec succès.');

//         } catch (\Exception $e) {
//             DB::rollback();
//             return redirect()->back()
//                 ->with('error', 'Erreur lors de la modification de la région: ' . $e->getMessage())
//                 ->withInput();
//         }
//     }


//     /**
//      * API pour récupérer les données d'une région (pour AJAX)
//      */
//     public function getRegionData(Region $region)
//     {
//         $annee = request('annee', date('Y'));
        
//         return response()->json([
//             'region' => $region,
//             'stats' => [
//                 'nb_communes' => $this->getNbCommunesParRegion($region->id),
//                 'taux_moyen_realisation' => $this->getTauxMoyenRealisation($region->id, $annee),
//                 'total_dettes_cnps' => $this->getTotalDettesCNPS($region->id, $annee),
//                 'conformite_depots' => $this->getConformiteDepots($region->id, $annee)
//             ],
//             'budgetData' => $this->getBudgetParDepartement($region->id, $annee),
//             'tauxRealisationData' => $this->getTauxRealisationParDepartement($region->id, $annee),
//             'etatComptes' => $this->getEtatComptesParCommune($region->id, $annee),
//             'defaillances' => $this->getDefaillancesRegion($region->id, $annee)
//         ]);
//     }

    
//     private function getBudgetParDepartement($regionId, $annee)
//     {
//         return Departement::where('region_id', $regionId)
//             ->with(['communes.previsions', 'communes.realisations'])
//             ->get()
//             ->map(function($dept) use ($annee) {
//                 $previsions = $dept->communes->flatMap->previsions
//                     ->where('annee_exercice', $annee)->sum('montant');
//                 $realisations = $dept->communes->flatMap->realisations
//                     ->where('annee_exercice', $annee)->sum('montant');
                    
//                 return [
//                     'departement' => $dept->nom,
//                     'previsions' => $previsions,
//                     'realisations' => $realisations
//                 ];
//             });
//     }
    
//     private function getTauxRealisationParDepartement($regionId, $annee)
//     {
//         return Departement::where('region_id', $regionId)
//             ->with(['communes.tauxRealisations'])
//             ->get()
//             ->map(function($dept) use ($annee) {
//                 $tauxMoyen = $dept->communes->flatMap->tauxRealisations
//                     ->where('annee_exercice', $annee)->avg('pourcentage');
                    
//                 return [
//                     'departement' => $dept->nom,
//                     'taux' => round($tauxMoyen ?? 0, 2)
//                 ];
//             });
//     }

//       private function getStatusCommune($commune, $annee)
//     {
//         $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
        
//         if (!$tauxRealisation) return 'Non défini';
        
//         if ($tauxRealisation->pourcentage >= 90) return 'Conforme';
//         if ($tauxRealisation->pourcentage >= 75) return 'Moyen';
//         return 'Non conforme';
//     }
    
   
// }



//      -->