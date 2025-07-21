<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_communes' => $this->getTotalCommunes(),
            'budget_total' => $this->getBudgetTotal(),
            'taux_execution' => $this->getTauxExecution(),
            'communes_defaillantes' => $this->getCommunesDefaillantes()
        ];

        // Données pour les graphiques
        $budgetParRegion = $this->getBudgetParRegion();
        $evolutionBudget = $this->getEvolutionBudget();
        $repartitionRessources = $this->getRepartitionRessources();
        $performanceRegionale = $this->getPerformanceRegionale();
        
        // Alertes et notifications
        $alertes = $this->getAlertes();
        
        // Dernières activités
        $dernieresActivites = $this->getDernieresActivites();

        // Top communes performantes
        $topCommunes = $this->getTopCommunes();

        // Indicateurs de gouvernance
        $gouvernance = $this->getIndicateursGouvernance();

        return view('dashboard.index', compact(
            'stats',
            'budgetParRegion',
            'evolutionBudget',
            'repartitionRessources',
            'performanceRegionale',
            'alertes',
            'dernieresActivites',
            'topCommunes',
            'gouvernance'
        ));
    }

    private function getTotalCommunes()
    {
        // Simulation - remplacez par votre logique de base de données
        return 374;
    }

    private function getBudgetTotal()
    {
        // Simulation - en milliards de FCFA
        return 1250.5;
    }

    private function getTauxExecution()
    {
        // Simulation - pourcentage
        return 73.2;
    }

    private function getCommunesDefaillantes()
    {
        // Simulation
        return 42;
    }

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

    private function getEvolutionBudget()
    {
        return [
            ['annee' => '2019', 'budget' => 980.5, 'execution' => 698.7],
            ['annee' => '2020', 'budget' => 1045.2, 'execution' => 756.3],
            ['annee' => '2021', 'budget' => 1134.8, 'execution' => 823.1],
            ['annee' => '2022', 'budget' => 1187.4, 'execution' => 876.2],
            ['annee' => '2023', 'budget' => 1203.6, 'execution' => 894.5],
            ['annee' => '2024', 'budget' => 1250.5, 'execution' => 915.4]
        ];
    }

    private function getRepartitionRessources()
    {
        return [
            ['type' => 'Ressources transférées État', 'montant' => 687.3, 'pourcentage' => 55.0],
            ['type' => 'Ressources propres', 'montant' => 312.6, 'pourcentage' => 25.0],
            ['type' => 'Donations extérieures', 'montant' => 150.1, 'pourcentage' => 12.0],
            ['type' => 'Autres ressources', 'montant' => 100.5, 'pourcentage' => 8.0]
        ];
    }

    private function getPerformanceRegionale()
    {
        return [
            ['region' => 'Centre', 'taux_execution' => 78.5, 'depot_comptes' => 85.2, 'gouvernance' => 7.8],
            ['region' => 'Littoral', 'taux_execution' => 82.1, 'depot_comptes' => 91.2, 'gouvernance' => 8.2],
            ['region' => 'Ouest', 'taux_execution' => 75.3, 'depot_comptes' => 78.6, 'gouvernance' => 7.1],
            ['region' => 'Sud', 'taux_execution' => 71.8, 'depot_comptes' => 73.9, 'gouvernance' => 6.9],
            ['region' => 'Est', 'taux_execution' => 69.4, 'depot_comptes' => 69.6, 'gouvernance' => 6.5]
        ];
    }

    private function getAlertes()
    {
        return [
            [
                'type' => 'danger',
                'titre' => 'Retard de dépôt critique',
                'message' => '15 communes n\'ont pas déposé leurs comptes administratifs 2023',
                'date' => now()->subDays(2)
            ],
            [
                'type' => 'warning',
                'titre' => 'Taux d\'exécution faible',
                'message' => 'Région Extrême-Nord : taux d\'exécution à 52%',
                'date' => now()->subDays(5)
            ],
            [
                'type' => 'info',
                'titre' => 'Nouveau rapport disponible',
                'message' => 'Synthèse trimestrielle Q1 2024 publiée',
                'date' => now()->subWeek()
            ]
        ];
    }

    private function getDernieresActivites()
    {
        return [
            [
                'action' => 'Mise à jour budget',
                'commune' => 'Douala 1er',
                'montant' => '45.2M FCFA',
                'date' => now()->subHours(2)
            ],
            [
                'action' => 'Dépôt compte administratif',
                'commune' => 'Yaoundé 3ème',
                'montant' => null,
                'date' => now()->subHours(6)
            ],
            [
                'action' => 'Alerte endettement',
                'commune' => 'Bafoussam 2ème',
                'montant' => '12.8M FCFA',
                'date' => now()->subDay()
            ]
        ];
    }

    private function getTopCommunes()
    {
        return [
            [
                'nom' => 'Douala 1er',
                'region' => 'Littoral',
                'budget' => '28.5M',
                'taux_execution' => 94.2,
                'score_gouvernance' => 9.1
            ],
            [
                'nom' => 'Yaoundé 1er',
                'region' => 'Centre',
                'budget' => '32.1M',
                'taux_execution' => 91.8,
                'score_gouvernance' => 8.9
            ],
            [
                'nom' => 'Bafoussam 1er',
                'region' => 'Ouest',
                'budget' => '18.7M',
                'taux_execution' => 89.5,
                'score_gouvernance' => 8.7
            ]
        ];
    }

    private function getIndicateursGouvernance()
    {
        return [
            'depot_comptes_a_temps' => 78.5,
            'presence_ordonnateur' => 92.3,
            'presence_receveur' => 87.8,
            'conformite_procedures' => 73.2
        ];
    }

    public function getRegionStats($regionId)
    {
        // API pour récupérer les statistiques d'une région spécifique
        // Utilisé pour les appels AJAX
        return response()->json([
            'communes' => 42,
            'budget_total' => 156.2,
            'taux_execution' => 75.3
        ]);
    }
}




 //<?php
// namespace App\Http\Controllers;

// use App\Models\Commune;
// use App\Models\Defaillance;
// use App\Models\Departement;
// use App\Models\Depot_compte;
// use App\Models\dette_cnps;
// use App\Models\dette_feicom;
// use App\Models\dette_fiscale;
// use App\Models\dette_salariale;
// use App\Models\Prevision;
// use App\Models\realisation;
// use App\Models\Region;
// use App\Models\Retard;
// use App\Models\Taux_realisation;
// use Illuminate\Http\Request;

// class DashboardController extends Controller



// {
    
//     public function index()
//     {
//         $annee = request('annee', date('Y'));
        
//         // Statistiques générales
//         $stats = [
//             'total_depots' => $this->getTotalDepots($annee),
//             'communes_enregistrees' => Commune::count(),
//             'departements' => Departement::count(),
//             'dette_moyenne_cnps' => $this->getDetteMoyenneCNPS($annee),
//             'evolution_depots' => $this->getEvolutionDepots(),
//             'repartition_categories' => $this->getRepartitionCategories($annee)
//         ];
        
//         // Données pour la carte des régions
//         $regions = Region::with(['departements.communes'])->get()->map(function($region) use ($annee) {
//             return [
//                 'id' => $region->id,
//                 'nom' => $region->nom,
//                 'nb_departements' => $region->departements->count(),
//                 'nb_communes' => $this->getNbCommunesParRegion($region->id),
//                 'total_depots' => $this->getDepotsParRegion($region->id, $annee)
//             ];
//         });
        
//         return view('dashboard.index', compact('stats', 'regions', 'annee'));
//     }
    
 
//     // private function getTotalDepots($annee)
//     // {
//     //     return Depot_compte::whereYear('date_depot', $annee)->count();
//     // }
    
    
    
//     // private function getTotalDepots($annee)
//     // {
//     //     return Depot_compte::whereYear('date_depot', $annee)
//     //         ->distinct('commune_id')
//     //         ->count('commune_id');
//     // }
    
    
    
    
//     private function getTotalDepots($annee)
//     {
//         return Prevision::where('annee_exercice', $annee)
//             ->sum('montant') ;// En millions
//     }
    
    
   
    
//     // private function getTotalDepots($annee)
//     // {
//     //     return realisation::where('annee_exercice', $annee)
//     //         ->sum('montant') ; // En millions
//     // }
    
    
//     private function getDetteMoyenneCNPS($annee)
//     {
//         return dette_cnps::whereYear('date_evaluation', $annee)->avg('montant');
//     }
    
//     private function getEvolutionDepots()
//     {
//         return Depot_compte::selectRaw('YEAR(date_depot) as annee, COUNT(*) as total')
//             ->groupBy('annee')
//             ->orderBy('annee')
//             ->get();
//     }
    
//     private function getRepartitionCategories($annee)
//     {
//         return [
//             'depots_compte' => Depot_compte::whereYear('date_depot', $annee)->count(),
//             'previsions' => Prevision::where('annee_exercice', $annee)->count(),
//             'dettes_cnps' => dette_cnps::whereYear('date_evaluation', $annee)->count(),
//             'dettes_fiscale' => dette_fiscale::whereYear('date_evaluation', $annee)->count(),
//             'dettes_feicom' => dette_feicom::whereYear('date_evaluation', $annee)->count(),
//             'dettes_salariale' => dette_salariale::whereYear('date_evaluation', $annee)->count()
//         ];
//     }
    
//     private function getDepotsParRegion($regionId, $annee)
//     {
//         // CORRECTION: Utiliser la bonne colonne de clé étrangère
//         return Depot_compte::whereYear('date_depot', $annee)
//             ->whereHas('commune', function($q) use ($regionId) {
//                 $q->whereHas('departement', function($q2) use ($regionId) {
//                     $q2->where('region_id', $regionId);
//                 });
//             })->count();
//     }
    
   
//     private function getNbCommunesParRegion($regionId)
//     {
//         // CORRECTION: Utiliser la bonne colonne de clé étrangère
//         return Commune::whereHas('departement', function($q) use ($regionId) {
//             $q->where('region_id', $regionId);
//         })->count();
//     }
// } 