<?php

namespace App\Http\Controllers;

use App\Models\Depot_compte;
use App\Models\Commune;
use App\Models\Departement;
use App\Models\Receveur;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Depot_compteController extends Controller
{
    /**
     * Liste des dépôts de comptes avec pagination et recherche
     */
    public function index(Request $request)
    {
        $query = Depot_compte::with(['commune.departement.region', 'receveur']);
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('commune', function($cq) use ($search) {
                    $cq->where('nom', 'LIKE', "%{$search}%")
                       ->orWhere('code', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('receveur', function($rq) use ($search) {
                    $rq->where('nom', 'LIKE', "%{$search}%");
                })
                ->orWhere('annee_exercice', 'LIKE', "%{$search}%");
            });
        }
        
        // Filtrage par année
        if ($request->filled('annee_exercice')) {
            $query->where('annee_exercice', $request->annee_exercice);
        }
        
        // Filtrage par statut de validation
        if ($request->filled('validation')) {
            $validation = $request->validation === '1' ? true : ($request->validation === '0' ? false : null);
            if ($validation !== null) {
                $query->where('validation', $validation);
            }
        }
        
        // Filtrage par commune
        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->commune_id);
        }
        
        // Tri
        $sortBy = $request->get('sort_by', 'date_depot');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);
        
        $depotComptes = $query->paginate(15);
        
        // Données pour les filtres
        $communes = Commune::with('departement')->orderBy('nom')->get();
        $anneesExercice = Depot_compte::distinct()->orderByDesc('annee_exercice')->pluck('annee_exercice');
        
        // Statistiques pour le tableau de bord
        $stats = $this->getStatistiques($request->get('annee_exercice', date('Y')));
        
        return view('depot-comptes.index', compact(
            'depotComptes', 'communes', 'anneesExercice', 'stats'
        ));
    }

    /**
     * Affichage du formulaire de création
     */
    public function create()
    {
        // Communes avec leurs receveurs
        $communes = Commune::with(['departement.region', 'receveurs'])
                           ->orderBy('nom')
                           ->get();
        
        // Receveurs disponibles
        $receveurs = Receveur::orderBy('nom')->get();
        
        return view('depot-comptes.create', compact('communes', 'receveurs'));
    }

    /**
     * Enregistrement d'un nouveau dépôt de compte
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commune_id' => 'required|exists:communes,id',
            'receveur_id' => 'required|exists:receveurs,id',
            'date_depot' => 'required|date|before_or_equal:today',
            'annee_exercice' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'validation' => 'boolean'
        ], [
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
            'receveur_id.required' => 'Vous devez sélectionner un receveur.',
            'receveur_id.exists' => 'Le receveur sélectionné n\'existe pas.',
            'date_depot.required' => 'La date de dépôt est obligatoire.',
            'date_depot.date' => 'La date de dépôt doit être une date valide.',
            'date_depot.before_or_equal' => 'La date de dépôt ne peut pas être dans le futur.',
            'annee_exercice.required' => 'L\'année d\'exercice est obligatoire.',
            'annee_exercice.integer' => 'L\'année d\'exercice doit être un nombre entier.',
            'annee_exercice.min' => 'L\'année d\'exercice ne peut pas être antérieure à 2000.',
            'annee_exercice.max' => 'L\'année d\'exercice ne peut pas dépasser ' . (date('Y') + 1) . '.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier s'il existe déjà un dépôt pour cette commune et cette année
        $existingDepot = Depot_compte::where('commune_id', $request->commune_id)
                                   ->where('annee_exercice', $request->annee_exercice)
                                   ->first();

        if ($existingDepot) {
            return redirect()->back()
                ->with('error', 'Un dépôt de compte existe déjà pour cette commune pour l\'année ' . $request->annee_exercice)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $depotCompte = Depot_compte::create([
                'commune_id' => $request->commune_id,
                'receveur_id' => $request->receveur_id,
                'date_depot' => $request->date_depot,
                'annee_exercice' => $request->annee_exercice,
                'validation' => $request->has('validation') ? true : false
            ]);

            DB::commit();
            
            return redirect()->route('depot-comptes.show', $depotCompte)
                           ->with('success', 'Dépôt de compte enregistré avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    /**
     * Affichage des détails d'un dépôt de compte
     */
    public function show(Depot_compte $depotCompte)
    {
        $depotCompte->load([
            'commune.departement.region',
            'commune.previsions' => function($query) use ($depotCompte) {
                $query->where('annee_exercice', $depotCompte->annee_exercice);
            },
            'commune.realisations' => function($query) use ($depotCompte) {
                $query->where('annee_exercice', $depotCompte->annee_exercice);
            },
            'commune.tauxRealisations' => function($query) use ($depotCompte) {
                $query->where('annee_exercice', $depotCompte->annee_exercice);
            },
            'receveur'
        ]);
        
        // Données financières de la commune pour l'année du dépôt
        $donneesFinancieres = $this->getDonneesFinancieresCommune($depotCompte->commune, $depotCompte->annee_exercice);
        
        // Historique des dépôts de cette commune
        $historiqueDepots = $this->getHistoriqueDepots($depotCompte->commune_id);
        
        return view('depot-comptes.show', compact(
            'depotCompte', 'donneesFinancieres', 'historiqueDepots'
        ));
    }

    /**
     * Affichage du formulaire de modification
     */
    public function edit(Depot_compte $depotCompte)
    {
        $depotCompte->load(['commune', 'receveur']);
        
        $communes = Commune::with(['departement.region', 'receveurs'])
                           ->orderBy('nom')
                           ->get();
        
        $receveurs = Receveur::orderBy('nom')->get();
        
        return view('depot-comptes.edit', compact('depotCompte', 'communes', 'receveurs'));
    }

    /**
     * Mise à jour d'un dépôt de compte
     */
    public function update(Request $request, Depot_compte $depotCompte)
    {
        $validator = Validator::make($request->all(), [
            'commune_id' => 'required|exists:communes,id',
            'receveur_id' => 'required|exists:receveurs,id',
            'date_depot' => 'required|date|before_or_equal:today',
            'annee_exercice' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'validation' => 'boolean'
        ], [
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
            'receveur_id.required' => 'Vous devez sélectionner un receveur.',
            'receveur_id.exists' => 'Le receveur sélectionné n\'existe pas.',
            'date_depot.required' => 'La date de dépôt est obligatoire.',
            'date_depot.date' => 'La date de dépôt doit être une date valide.',
            'date_depot.before_or_equal' => 'La date de dépôt ne peut pas être dans le futur.',
            'annee_exercice.required' => 'L\'année d\'exercice est obligatoire.',
            'annee_exercice.integer' => 'L\'année d\'exercice doit être un nombre entier.',
            'annee_exercice.min' => 'L\'année d\'exercice ne peut pas être antérieure à 2000.',
            'annee_exercice.max' => 'L\'année d\'exercice ne peut pas dépasser ' . (date('Y') + 1) . '.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier les doublons (exclure l'enregistrement actuel)
        $existingDepot = Depot_compte::where('commune_id', $request->commune_id)
                                   ->where('annee_exercice', $request->annee_exercice)
                                   ->where('id', '!=', $depotCompte->id)
                                   ->first();

        if ($existingDepot) {
            return redirect()->back()
                ->with('error', 'Un dépôt de compte existe déjà pour cette commune pour l\'année ' . $request->annee_exercice)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $depotCompte->update([
                'commune_id' => $request->commune_id,
                'receveur_id' => $request->receveur_id,
                'date_depot' => $request->date_depot,
                'annee_exercice' => $request->annee_exercice,
                'validation' => $request->has('validation') ? true : false
            ]);

            DB::commit();
            
            return redirect()->route('depot-comptes.show', $depotCompte)
                           ->with('success', 'Dépôt de compte mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Suppression d'un dépôt de compte
     */
    public function destroy(Depot_compte $depotCompte)
    {
        try {
            DB::beginTransaction();
            
            $depotCompte->delete();
            
            DB::commit();
            
            return redirect()->route('depot-comptes.index')
                           ->with('success', 'Dépôt de compte supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Validation/Invalidation en masse
     */
    public function bulkValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'depot_ids' => 'required|array',
            'depot_ids.*' => 'exists:depot_comptes,id',
            'action' => 'required|in:validate,invalidate'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        DB::beginTransaction();
        try {
            $validation = $request->action === 'validate' ? true : false;
            
            Depot_compte::whereIn('id', $request->depot_ids)
                       ->update(['validation' => $validation]);

            DB::commit();
            
            $message = $request->action === 'validate' 
                     ? 'Dépôts validés avec succès.' 
                     : 'Dépôts invalidés avec succès.';
            
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de l\'opération: ' . $e->getMessage());
        }
    }

//     /**
//      * Rapport des dépôts par période
//      */
//     public function rapport(Request $request)
//     {

//         $regions = Region::orderBy('nom')->get();
// $departements = Departement::orderBy('nom')->get();
//         $annee = $request->get('annee', date('Y'));
//         $departementId = $request->get('departement_id');
//         $regionId = $request->get('region_id');
        
//         $query = Depot_compte::with(['commune.departement.region', 'receveur'])
//                             ->where('annee_exercice', $annee);
        
//         if ($departementId) {
//             $query->whereHas('commune', function($q) use ($departementId) {
//                 $q->where('departement_id', $departementId);
//             });
//         }
        
//         if ($regionId) {
//             $query->whereHas('commune.departement', function($q) use ($regionId) {
//                 $q->where('region_id', $regionId);
//             });
//         }
        
//         $depots = $query->orderBy('date_depot', 'desc')->get();
        
//         // Statistiques du rapport
//         $statistiques = [
//             'total_depots' => $depots->count(),
//             'depots_valides' => $depots->where('validation', true)->count(),
//             'depots_invalides' => $depots->where('validation', false)->count(),
//             'taux_validation' => $depots->count() > 0 ? 
//                 round(($depots->where('validation', true)->count() / $depots->count()) * 100, 2) : 0,
//             'depots_par_mois' => $this->getDepotsParMois($depots),
//             'communes_sans_depot' => $this->getCommunesSansDepot($annee, $departementId, $regionId)
//         ];
        


// return view('depot-comptes.rapport', compact(
//     'depots', 'statistiques', 'annee', 'regions', 'departements'
// ));

/**
 * Rapport des dépôts par période
 */
public function rapport(Request $request)
{
    // Récupérer toutes les régions et départements pour les filtres
    $regions = Region::orderBy('nom')->get();
    $departements = Departement::orderBy('nom')->get();
    
    $annee = $request->get('annee', date('Y'));
    $departementId = $request->get('departement_id');
    $regionId = $request->get('region_id');
    
    $query = Depot_compte::with(['commune.departement.region', 'receveur'])
                        ->where('annee_exercice', $annee);
    
    if ($departementId) {
        $query->whereHas('commune', function($q) use ($departementId) {
            $q->where('departement_id', $departementId);
        });
    }
    
    if ($regionId) {
        $query->whereHas('commune.departement', function($q) use ($regionId) {
            $q->where('region_id', $regionId);
        });
    }
    
    $depots = $query->orderBy('date_depot', 'desc')->get();
    
    // Statistiques du rapport
    $statistiques = [
        'total_depots' => $depots->count(),
        'depots_valides' => $depots->where('validation', true)->count(),
        'depots_invalides' => $depots->where('validation', false)->count(),
        'taux_validation' => $depots->count() > 0 ? 
            round(($depots->where('validation', true)->count() / $depots->count()) * 100, 2) : 0,
        'depots_par_mois' => $this->getDepotsParMois($depots),
        'communes_sans_depot' => $this->getCommunesSansDepot($annee, $departementId, $regionId)
    ];
    
    return view('depot-comptes.rapport', compact(
        'depots', 'statistiques', 'annee', 'regions', 'departements', 
        'regionId', 'departementId'
    ));
}
        
       
    

    // ================== MÉTHODES PRIVÉES ==================

    /**
     * Obtenir les statistiques générales
     */
    private function getStatistiques($annee)
    {
        $totalDepots = Depot_compte::where('annee_exercice', $annee)->count();
        $depotsValides = Depot_compte::where('annee_exercice', $annee)
                                   ->where('validation', true)
                                   ->count();
        
        return [
            'total_depots' => $totalDepots,
            'depots_valides' => $depotsValides,
            'depots_invalides' => $totalDepots - $depotsValides,
            'taux_validation' => $totalDepots > 0 ? round(($depotsValides / $totalDepots) * 100, 2) : 0,
            'depots_recents' => Depot_compte::where('annee_exercice', $annee)
                                          ->where('date_depot', '>=', Carbon::now()->subDays(30))
                                          ->count()
        ];
    }

    /**
     * Obtenir les données financières d'une commune
     */
    private function getDonneesFinancieresCommune($commune, $annee)
    {
        $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
        $realisations = $commune->realisations->where('annee_exercice', $annee);
        $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
        
        return [
            'prevision' => $prevision?->montant ?? 0,
            'realisation_total' => $realisations->sum('montant'),
            'taux_realisation' => $tauxRealisation?->pourcentage ?? 0,
            'evaluation' => $tauxRealisation?->evaluation ?? 'Non évalué'
        ];
    }

    /**
     * Obtenir l'historique des dépôts d'une commune
     */
    private function getHistoriqueDepots($communeId)
    {
        return Depot_compte::where('commune_id', $communeId)
                          ->with('receveur')
                          ->orderByDesc('annee_exercice')
                          ->get();
    }

    /**
     * Obtenir les dépôts par mois
     */
    private function getDepotsParMois($depots)
    {
        return $depots->groupBy(function($depot) {
            return Carbon::parse($depot->date_depot)->format('m');
        })->map(function($group) {
            return $group->count();
        });
    }

    /**
     * Obtenir les communes sans dépôt pour une année donnée
     */
    private function getCommunesSansDepot($annee, $departementId = null, $regionId = null)
    {
        $query = Commune::with('departement.region')
                       ->whereNotExists(function($q) use ($annee) {
                           $q->select(DB::raw(1))
                             ->from('depot_comptes')
                             ->whereRaw('depot_comptes.commune_id = communes.id')
                             ->where('annee_exercice', $annee);
                       });
        
        if ($departementId) {
            $query->where('departement_id', $departementId);
        }
        
        if ($regionId) {
            $query->whereHas('departement', function($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }
        
        return $query->get();
    }
}