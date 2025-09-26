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

public function show(Depot_compte $depotCompte)
    {
        $depotCompte->load(['commune.departement.region', 'receveur']);

        // Vérifier si le receveur existe
        $nomReceveur = $depotCompte->receveur ? $depotCompte->receveur->nom : 'Non assigné';
        
        // Données financières (vous devrez adapter selon vos modèles)
        $donneesFinancieres = $this->getDonneesFinancieresCommune($depotCompte->commune, $depotCompte->annee_exercice);
        
        // Historique des dépôts de la commune
        $historiqueDepots = $this->getHistoriqueDepots($depotCompte->commune_id);
        
        return view('depot-comptes.show', compact(
            'depotCompte', 'donneesFinancieres', 'historiqueDepots'
        ));
    }


    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'commune_id' => 'required|exists:communes,id',
        'receveur_id' => 'nullable|exists:receveurs,id',
        'date_depot' => 'required|date|before_or_equal:today',
        'annee_exercice' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        'type' => 'required|in:budget_primitif,compte_administratif,budget_supplementaire,decision_modificative',
        'date_limite_depot' => 'required|date',
        'statut' => 'required|in:depose,non_depose,en_attente',
        'jours_retard' => 'nullable|integer|min:0',
        'observations' => 'nullable|string|max:1000',
        'validation' => 'boolean'
    ], [
        'commune_id.required' => 'Vous devez sélectionner une commune.',
        'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
        'receveur_id.exists' => 'Le receveur sélectionné n\'existe pas.',
        'date_depot.required' => 'La date de dépôt est obligatoire.',
        'date_depot.date' => 'La date de dépôt doit être une date valide.',
        'date_depot.before_or_equal' => 'La date de dépôt ne peut pas être dans le futur.',
        'annee_exercice.required' => 'L\'année d\'exercice est obligatoire.',
        'annee_exercice.integer' => 'L\'année d\'exercice doit être un nombre entier.',
        'annee_exercice.min' => 'L\'année d\'exercice ne peut pas être antérieure à 2000.',
        'annee_exercice.max' => 'L\'année d\'exercice ne peut pas dépasser ' . (date('Y') + 1) . '.',
        'type.required' => 'Le type de dépôt est obligatoire.',
        'type.in' => 'Le type de dépôt sélectionné n\'est pas valide.',
        'date_limite_depot.required' => 'La date limite de dépôt est obligatoire.',
        'date_limite_depot.date' => 'La date limite de dépôt doit être une date valide.',
        'statut.required' => 'Le statut est obligatoire.',
        'statut.in' => 'Le statut sélectionné n\'est pas valide.',
        'observations.max' => 'Les observations ne peuvent pas dépasser 1000 caractères.'
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Vérifier s'il existe déjà un dépôt pour cette commune, cette année et ce type
    $existingDepot = Depot_compte::where('commune_id', $request->commune_id)
                                 ->where('annee_exercice', $request->annee_exercice)
                                 ->where('type', $request->type)
                                 ->first();

    if ($existingDepot) {
        return redirect()->back()
            ->with('error', 'Un dépôt de compte de type "' . $request->type . '" existe déjà pour cette commune pour l\'année ' . $request->annee_exercice)
            ->withInput();
    }

    DB::beginTransaction();
    try {
        // Calculer les jours de retard si pas fourni
        $joursRetard = $request->jours_retard;
        if ($joursRetard === null) {
            $dateDepot = Carbon::parse($request->date_depot);
            $dateLimite = Carbon::parse($request->date_limite_depot);
            $joursRetard = $dateDepot > $dateLimite ? $dateDepot->diffInDays($dateLimite) : 0;
        }

        $depotCompte = Depot_compte::create([
            'commune_id' => $request->commune_id,
            'receveur_id' => $request->receveur_id,
            'date_depot' => $request->date_depot,
            'date_depot_effectif' => $request->date_depot, // Utiliser la même date
            'annee_exercice' => $request->annee_exercice,
            'type' => $request->type,
            'date_limite_depot' => $request->date_limite_depot,
            'statut' => $request->statut,
            'jours_retard' => $joursRetard,
            'observations' => $request->observations,
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
 * Affichage du formulaire de modification
 */
public function edit(Depot_compte $depotCompte)
{
    // Charger les relations nécessaires
    $depotCompte->load(['commune.departement.region', 'receveur']);
    
    // Communes avec leurs receveurs
    $communes = Commune::with(['departement.region', 'receveurs'])
                       ->orderBy('nom')
                       ->get();
    
    // Receveurs disponibles
    $receveurs = Receveur::orderBy('nom')->get();
    
    return view('depot-comptes.edit', compact('depotCompte', 'communes', 'receveurs'));
}

// Mettre à jour aussi la méthode update() de la même façon :
public function update(Request $request, Depot_compte $depotCompte)
{
    $validator = Validator::make($request->all(), [
        'commune_id' => 'required|exists:communes,id',
        'receveur_id' => 'nullable|exists:receveurs,id',
        'date_depot' => 'required|date|before_or_equal:today',
        'annee_exercice' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        'type' => 'required|in:budget_primitif,compte_administratif,budget_supplementaire,decision_modificative',
        'date_limite_depot' => 'required|date',
        'statut' => 'required|in:depose,non_depose,en_attente',
        'jours_retard' => 'nullable|integer|min:0',
        'observations' => 'nullable|string|max:1000',
        'validation' => 'boolean'
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Vérifier les doublons (exclure l'enregistrement actuel)
    $existingDepot = Depot_compte::where('commune_id', $request->commune_id)
                                 ->where('annee_exercice', $request->annee_exercice)
                                 ->where('type', $request->type)
                                 ->where('id', '!=', $depotCompte->id)
                                 ->first();

    if ($existingDepot) {
        return redirect()->back()
            ->with('error', 'Un dépôt de compte de type "' . $request->type . '" existe déjà pour cette commune pour l\'année ' . $request->annee_exercice)
            ->withInput();
    }

    DB::beginTransaction();
    try {
        // Calculer les jours de retard
        $joursRetard = $request->jours_retard;
        if ($joursRetard === null) {
            $dateDepot = Carbon::parse($request->date_depot);
            $dateLimite = Carbon::parse($request->date_limite_depot);
            $joursRetard = $dateDepot > $dateLimite ? $dateDepot->diffInDays($dateLimite) : 0;
        }

        $depotCompte->update([
            'commune_id' => $request->commune_id,
            'receveur_id' => $request->receveur_id,
            'date_depot' => $request->date_depot,
            'date_depot_effectif' => $request->date_depot,
            'annee_exercice' => $request->annee_exercice,
            'type' => $request->type,
            'date_limite_depot' => $request->date_limite_depot,
            'statut' => $request->statut,
            'jours_retard' => $joursRetard,
            'observations' => $request->observations,
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
            'depots_par_annee' => $this->getDepotsParAnnee($departementId, $regionId),
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
     * Obtenir les dépôts par année (remplace getDepotsParMois)
     */
    private function getDepotsParAnnee($departementId = null, $regionId = null)
    {
        $query = Depot_compte::select('annee_exercice', DB::raw('count(*) as nombre_depots'))
                            ->groupBy('annee_exercice')
                            ->orderBy('annee_exercice');
        
        // Appliquer les filtres si nécessaire
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
        
        $resultats = $query->get();
        
        // Convertir en tableau associatif année => nombre_depots
        $depotsParAnnee = [];
        foreach ($resultats as $resultat) {
            $depotsParAnnee[$resultat->annee_exercice] = $resultat->nombre_depots;
        }
        
        return $depotsParAnnee;
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