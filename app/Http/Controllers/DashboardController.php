<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Defaillance;
use App\Models\Departement;
use App\Models\Depot_compte;
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

class DashboardController extends Controller


// {
//     /**
//      * Affichage du tableau de bord principal
//      */
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
//                 'nb_communes' => $region->departement->communes->count(),
//                 'total_depots' => $this->getDepotsParRegion($region->id, $annee)
//             ];
//         });
        
//         return view('dashboard.index', compact('stats', 'regions', 'annee'));
//     }
    
//    private function getTotalDepots($annee)
//     {
//         return realisation::where('annee_exercice', $annee)
//             ->sum('montant') / 1000000; // En millions
//     }
    
//     private function getDetteMoyenneCNPS($annee)
//     {
//         return Dette_cnps::whereYear('date_evaluation', $annee)->avg('montant');
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
//         return Depot_compte::whereYear('date_depot', $annee)
//             ->whereHas('commune.departement.region', function($q) use ($regionId) {
//                 $q->where('id', $regionId);
//             })->count();
//     }
// }


// {
//     /**
//      * Affichage du tableau de bord principal
//      */
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
    
//     /**
//      * OPTION 1: Compter simplement le nombre de dépôts
//      */
//     // private function getTotalDepots($annee)
//     // {
//     //     return Depot_compte::whereYear('date_depot', $annee)->count();
//     // }
    
//     /**
//      * OPTION 2: Si vous voulez le nombre de communes distinctes ayant fait des dépôts
//      */
//     /*
//     private function getTotalDepots($annee)
//     {
//         return Depot_compte::whereYear('date_depot', $annee)
//             ->distinct('commune_id')
//             ->count('commune_id');
//     }
//     */
    
//     /**
//      * OPTION 3: Si vous voulez le total des prévisions (au lieu des dépôts)
//      */
//     /*
//     private function getTotalDepots($annee)
//     {
//         return Prevision::where('annee_exercice', $annee)
//             ->sum('montant') / 1000000; // En millions
//     }
//     */
    
//     /**
//      * OPTION 4: Si vous voulez le total des réalisations
//      */
    
//     private function getTotalDepots($annee)
//     {
//         return realisation::where('annee_exercice', $annee)
//             ->sum('montant') / 1000000; // En millions
//     }
    
    
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
//         return Depot_compte::whereYear('date_depot', $annee)
//             ->whereHas('commune.departement.region', function($q) use ($regionId) {
//                 $q->where('id', $regionId);
//             })->count();
//     }
    
//     /**
//      * Nouvelle méthode pour compter les communes par région
//      */
//     private function getNbCommunesParRegion($regionId)
//     {
//         return Commune::whereHas('departement', function($q) use ($regionId) {
//             $q->where('region_id', $regionId);
//         })->count();
//     }
// }





{
    /**
     * Affichage du tableau de bord principal
     */
    public function index()
    {
        $annee = request('annee', date('Y'));
        
        // Statistiques générales
        $stats = [
            'total_depots' => $this->getTotalDepots($annee),
            'communes_enregistrees' => Commune::count(),
            'departements' => Departement::count(),
            'dette_moyenne_cnps' => $this->getDetteMoyenneCNPS($annee),
            'evolution_depots' => $this->getEvolutionDepots(),
            'repartition_categories' => $this->getRepartitionCategories($annee)
        ];
        
        // Données pour la carte des régions
        $regions = Region::with(['departements.communes'])->get()->map(function($region) use ($annee) {
            return [
                'id' => $region->id,
                'nom' => $region->nom,
                'nb_departements' => $region->departements->count(),
                'nb_communes' => $this->getNbCommunesParRegion($region->id),
                'total_depots' => $this->getDepotsParRegion($region->id, $annee)
            ];
        });
        
        return view('dashboard.index', compact('stats', 'regions', 'annee'));
    }
    
    /**
     * OPTION 1: Compter simplement le nombre de dépôts
     */
    // private function getTotalDepots($annee)
    // {
    //     return Depot_compte::whereYear('date_depot', $annee)->count();
    // }
    
    /**
     * OPTION 2: Si vous voulez le nombre de communes distinctes ayant fait des dépôts
     */
    /*
    private function getTotalDepots($annee)
    {
        return Depot_compte::whereYear('date_depot', $annee)
            ->distinct('commune_id')
            ->count('commune_id');
    }
    */
    
    /**
     * OPTION 3: Si vous voulez le total des prévisions (au lieu des dépôts)
     */
    /*
    private function getTotalDepots($annee)
    {
        return Prevision::where('annee_exercice', $annee)
            ->sum('montant') / 1000000; // En millions
    }
    */
    
    /**
     * OPTION 4: Si vous voulez le total des réalisations
     */
    
    private function getTotalDepots($annee)
    {
        return realisation::where('annee_exercice', $annee)
            ->sum('montant') / 1000000; // En millions
    }
    
    
    private function getDetteMoyenneCNPS($annee)
    {
        return dette_cnps::whereYear('date_evaluation', $annee)->avg('montant');
    }
    
    private function getEvolutionDepots()
    {
        return Depot_compte::selectRaw('YEAR(date_depot) as annee, COUNT(*) as total')
            ->groupBy('annee')
            ->orderBy('annee')
            ->get();
    }
    
    private function getRepartitionCategories($annee)
    {
        return [
            'depots_compte' => Depot_compte::whereYear('date_depot', $annee)->count(),
            'previsions' => Prevision::where('annee_exercice', $annee)->count(),
            'dettes_cnps' => dette_cnps::whereYear('date_evaluation', $annee)->count(),
            'dettes_fiscale' => dette_fiscale::whereYear('date_evaluation', $annee)->count(),
            'dettes_feicom' => dette_feicom::whereYear('date_evaluation', $annee)->count(),
            'dettes_salariale' => dette_salariale::whereYear('date_evaluation', $annee)->count()
        ];
    }
    
    private function getDepotsParRegion($regionId, $annee)
    {
        // CORRECTION: Utiliser la bonne colonne de clé étrangère
        return Depot_compte::whereYear('date_depot', $annee)
            ->whereHas('commune', function($q) use ($regionId) {
                $q->whereHas('departement', function($q2) use ($regionId) {
                    $q2->where('region_id', $regionId);
                });
            })->count();
    }
    
    /**
     * Nouvelle méthode pour compter les communes par région
     */
    private function getNbCommunesParRegion($regionId)
    {
        // CORRECTION: Utiliser la bonne colonne de clé étrangère
        return Commune::whereHas('departement', function($q) use ($regionId) {
            $q->where('region_id', $regionId);
        })->count();
    }
}