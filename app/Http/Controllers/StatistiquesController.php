<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Defaillance;
use App\Models\Departement;
use App\Models\dette_cnps;
use App\Models\dette_feicom;
use App\Models\dette_fiscale;
use App\Models\dette_salariale;
use App\Models\Prevision;
use App\Models\realisation;
use App\Models\Region;
use App\Models\Retard;
use App\Models\Taux_realisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistiquesController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
//         //
//     }

//     /**
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         //
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         //
//     }

//     /**
//      * Display the specified resource.
//      */
//     public function show(string $id)
//     {
//         //
//     }

//     /**
//      * Show the form for editing the specified resource.
//      */
//     public function edit(string $id)
//     {
//         //
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, string $id)
//     {
//         //
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy(string $id)
//     {
//         //
//     }
// }

{
    public function index()
    {
        $anneeEnCours = date('Y');
        
        // Statistiques globales
        $statGlobales = [
            'nbCommunes' => Commune::count(),
            'nbRegions' => Region::count(),
            'nbDepartements' => Departement::count(),
            'totalPrevisions' => Prevision::where('annee_exercice', $anneeEnCours)->sum('montant'),
            'totalRealisations' => Realisation::where('annee_exercice', $anneeEnCours)->sum('montant'),
            'tauxMoyen' => Taux_realisation::where('annee_exercice', $anneeEnCours)->avg('pourcentage'),
            'nbDefaillances' => Defaillance::whereYear('date_constat', $anneeEnCours)->count(),
            'nbRetards' => Retard::whereYear('date_constat', $anneeEnCours)->count(),
            'totalDettes' => Dette_cnps::whereYear('date_evaluation', $anneeEnCours)->sum('montant'),
            'totalDettes' => dette_fiscale::whereYear('date_evaluation', $anneeEnCours)->sum('montant'),
            'totalDettes' => dette_feicom::whereYear('date_evaluation', $anneeEnCours)->sum('montant'),
            'totalDettes' => dette_salariale::whereYear('date_evaluation', $anneeEnCours)->sum('montant')
        ];
        
        // Top 5 des communes avec le meilleur taux de réalisation
        $topCommunes = Taux_realisation::with('commune')
                        ->where('annee_exercice', $anneeEnCours)
                        ->orderBy('pourcentage', 'desc')
                        ->take(5)
                        ->get();
        
        // Communes avec le plus de défaillances
        $communesDefaillances = DB::table('defaillances')
                                ->select('commune_id', DB::raw('count(*) as total'))
                                ->whereYear('date_constat', $anneeEnCours)
                                ->groupBy('commune_id')
                                ->orderBy('total', 'desc')
                                ->take(5)
                                ->get();
        
        // Répartition des défaillances par gravité
        $defaillancesParGravite = DB::table('defaillances')
                                    ->select('gravite', DB::raw('count(*) as total'))
                                    ->whereYear('date_constat', $anneeEnCours)
                                    ->groupBy('gravite')
                                    ->get();
        
        return view('statistiques.index', compact(
            'statGlobales',
            'topCommunes',
            'communesDefaillances',
            'defaillancesParGravite',
            'anneeEnCours'
        ));
    }
    
    public function comparaison(Request $request)
    {
        $communes = Commune::all();
        $annees = range(date('Y') - 5, date('Y'));
        
        $commune1Id = $request->commune1_id ?? null;
        $commune2Id = $request->commune2_id ?? null;
        $annee = $request->annee ?? date('Y');
        
        $donnees = [];
        
        if ($commune1Id && $commune2Id) {
            $commune1 = Commune::findOrFail($commune1Id);
            $commune2 = Commune::findOrFail($commune2Id);
            
            // Collecte des données pour la comparaison
            $donnees = [
                $commune1->id => [
                    'nom' => $commune1->nom,
                    'previsions' => Prevision::where('commune_id', $commune1->id)
                                    ->where('annee_exercice', $annee)
                                    ->sum('montant'),
                    'realisations' => Realisation::where('commune_id', $commune1->id)
                                    ->where('annee_exercice', $annee)
                                    ->sum('montant'),
                    'taux' => Taux_realisation::where('commune_id', $commune1->id)
                                    ->where('annee_exercice', $annee)
                                    ->value('pourcentage') ?? 0,
                    'defaillances' => Defaillance::where('commune_id', $commune1->id)
                                    ->whereYear('date_constat', $annee)
                                    ->count(),
                    'retards' => Retard::where('commune_id', $commune1->id)
                                    ->whereYear('date_constat', $annee)
                                    ->count(),
                    'dettes' => Dette_cnps::where('commune_id', $commune1->id)
                                    ->whereYear('date_evaluation', $annee)
                                    ->sum('montant'),
                    'dettes' => Dette_fiscale::where('commune_id', $commune1->id)
                                    ->whereYear('date_evaluation', $annee)
                                    ->sum('montant'),
                     'dettes' => Dette_feicom::where('commune_id', $commune1->id)
                                    ->whereYear('date_evaluation', $annee)
                                    ->sum('montant'),
                    'dettes' => Dette_salariale::where('commune_id', $commune1->id)
                                    ->whereYear('date_evaluation', $annee)
                                    ->sum('montant')
                ],
                $commune2->id => [
                    'nom' => $commune2->nom,
                    'previsions' => Prevision::where('commune_id', $commune2->id)
                                    ->where('annee_exercice', $annee)
                                    ->sum('montant'),
                    'realisations' => Realisation::where('commune_id', $commune2->id)
                                    ->where('annee_exercice', $annee)
                                    ->sum('montant'),
                    'taux' => Taux_realisation::where('commune_id', $commune2->id)
                                    ->where('annee_exercice', $annee)
                                    ->value('pourcentage') ?? 0,
                    'defaillances' => Defaillance::where('commune_id', $commune2->id)
                                    ->whereYear('date_constat', $annee)
                                    ->count(),
                    'retards' => Retard::where('commune_id', $commune2->id)
                                    ->whereYear('date_constat', $annee)
                                    ->count(),
                    'dettes' => dette_cnps::where('commune_id', $commune2->id)
                                    ->whereYear('date_evaluation', $annee)
                                    ->sum('montant'),
                    'dettes' => dette_fiscale::where('commune_id', $commune2->id)
                                    ->whereYear('date_evaluation', $annee)
                                    ->sum('montant'),
                     'dettes' => dette_feicom::where('commune_id', $commune2->id)
                                    ->whereYear('date_evaluation', $annee)
                                    ->sum('montant'),
                    'dettes' => dette_salariale::where('commune_id', $commune2->id)
                                    ->whereYear('date_evaluation', $annee)
                                    ->sum('montant')
                ]
            ];
        }
        
        return view('statistiques.comparaison', compact('communes', 'annees', 'donnees', 'commune1Id', 'commune2Id', 'annee'));
    }
    
    public function evolution(Request $request)
    {
        $communes = Commune::all();
        $regions = Region::all();
        $departements = Departement::all();
        
        $entiteType = $request->entite_type ?? 'commune';
        $entiteId = $request->entite_id ?? null;
        $anneeDebut = $request->annee_debut ?? (date('Y') - 5);
        $anneeFin = $request->annee_fin ?? date('Y');
        
        $donnees = [];
        
        if ($entiteId) {
            $annees = range($anneeDebut, $anneeFin);
            
            // Récupération des données d'évolution selon le type d'entité
            switch ($entiteType) {
                case 'commune':
                    $entite = Commune::findOrFail($entiteId);
                    foreach ($annees as $annee) {
                        $donnees[$annee] = [
                            'previsions' => Prevision::where('commune_id', $entiteId)
                                        ->where('annee_exercice', $annee)
                                        ->sum('montant'),
                            'realisations' => realisation::where('commune_id', $entiteId)
                                        ->where('annee_exercice', $annee)
                                        ->sum('montant'),
                            'taux' => Taux_realisation::where('commune_id', $entiteId)
                                        ->where('annee_exercice', $annee)
                                        ->value('pourcentage') ?? 0
                        ];
                    }
                    break;
                    
                case 'departement':
                case 'region':
                    // Logique pour agréger les données par département ou région
                    // À implémenter selon les besoins
                    break;
            }
        }
        
        return view('statistiques.evolution', compact(
            'communes', 
            'regions', 
            'departements', 
            'entiteType', 
            'entiteId', 
            'anneeDebut', 
            'anneeFin',
            'donnees'
        ));
    }
}