<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commune;
use App\Models\Departement;
use App\Models\Ordonnateur;
use App\Models\Prevision;
use App\Models\Receveur;
use App\Models\dette_cnps;
use App\Models\dette_feicom;
use App\Models\dette_fiscale;
use App\Models\dette_salariale;
use App\Models\Taux_realisation;
use App\Models\Realisation;
use App\Models\Defaillance;
use App\Models\Retard;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class CommunesController extends Controller
{
    /**
     * Liste des communes avec pagination, recherche et filtres avancés
     */
    public function index(Request $request)
    {
        // Récupérer l'année sélectionnée, par défaut l'année courante
        $annee = $request->get('annee', date('Y'));
        
        $query = Commune::with([
            'departement.region', 
            'receveurs', 
            'ordonnateurs',
            'tauxRealisations' => function($q) use ($annee) { 
                $q->where('annee_exercice', $annee)->latest(); 
            },
            'previsions' => function($q) use ($annee) {
                $q->where('annee_exercice', $annee);
            },
            'realisations' => function($q) use ($annee) {
                $q->where('annee_exercice', $annee);
            }
        ]);
        
        // Recherche avancée
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhereHas('departement', function($dq) use ($search) {
                      $dq->where('nom', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('departement.region', function($rq) use ($search) {
                      $rq->where('nom', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Filtres avancés
        $this->applyFilters($query, $request, $annee);
        
        // Tri dynamique
        $sortBy = $request->get('sort_by', 'nom');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if (in_array($sortBy, ['nom', 'code', 'population', 'superficie', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        $communes = $query->paginate($request->get('per_page', 25));
        $departements = Departement::with('region')->orderBy('nom')->get();
        
        // Statistiques pour l'année sélectionnée
        $stats = $this->getCommunesStats($annee);
        
        // Années disponibles
        $anneesDisponibles = $this->getAnneesDisponibles();
        
        if ($request->ajax()) {
            return response()->json([
                'html' => view('communes.partials.table', compact('communes', 'annee'))->render(),
                'pagination' => (string) $communes->appends(request()->query())->links(),
                'stats' => $stats
            ]);
        }
        
        return view('communes.index', compact('communes', 'departements', 'stats', 'annee', 'anneesDisponibles'));
    }

    /**
     * Affichage détaillé d'une commune
     */
    public function show(Commune $commune, Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        
        // Chargement optimisé de toutes les relations nécessaires
        $commune->load([
            'departement.region', 
            'receveurs', 
            'ordonnateurs',
        ]);
        
        // Statistiques de la commune pour l'année donnée
        $stats = $this->getCommuneStats($commune, $annee);
        
        // Données financières détaillées
        $donneesFinancieres = $this->getDonneesFinancieres($commune, $annee);
        
        // Détails des dettes par type
        $detailsDettes = $this->getDetailsDettes($commune, $annee);
        
        // Évolution des performances sur plusieurs années
        $evolutionPerformances = $this->getEvolutionPerformancesCommune($commune);
        
        // Projets en cours
        $projetsActifs = $this->getProjetsActifs($commune, $annee);
        
        // Défaillances et retards
        $defaillancesRetards = $this->getDefaillancesRetards($commune, $annee);
        
        // Comparaisons avec le département
        $comparaisons = $this->getComparaisonsDepartement($commune, $annee);
        
        // Années disponibles pour le filtre
        $anneesDisponibles = $this->getAnneesDisponiblesCommune($commune);
        
        return view('communes.show', compact(
            'commune', 
            'stats', 
            'donneesFinancieres',
            'detailsDettes',
            'evolutionPerformances',
            'projetsActifs',
            'defaillancesRetards',
            'comparaisons',
            'annee',
            'anneesDisponibles'
        ));
    }

    /**
     * Affichage du formulaire de création
     */
    public function create()
    {
        $departements = Departement::with('region')->orderBy('nom')->get();
        $receveurs = Receveur::whereNull('commune_id')->orderBy('nom')->get();
        $ordonnateurs = Ordonnateur::whereNull('commune_id')->orderBy('nom')->get();
        
        return view('communes.create', compact('departements', 'receveurs', 'ordonnateurs'));
    }

    /**
     * Enregistrement d'une nouvelle commune
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:communes,code',
            'departement_id' => 'required|exists:departements,id',
            'telephone' => 'nullable|regex:/^[0-9+\-\s()]+$/|max:20',
            'email' => 'nullable|email|max:255',
            'population' => 'nullable|integer|min:0|max:10000000',
            'superficie' => 'nullable|numeric|min:0|max:100000',
            'adresse' => 'nullable|string|max:500',
            'coordonnees_gps' => 'nullable|string|max:100'
        ]);

        DB::beginTransaction();
        try {
            $commune = Commune::create($validated);
            
            // Assigner les responsables si fournis
            if ($request->filled('receveur_ids')) {
                Receveur::whereIn('id', $request->receveur_ids)
                    ->update(['commune_id' => $commune->id]);
            }
            
            if ($request->filled('ordonnateur_ids')) {
                Ordonnateur::whereIn('id', $request->ordonnateur_ids)
                    ->update(['commune_id' => $commune->id]);
            }
            
            DB::commit();
            
            return redirect()->route('communes.show', $commune)
                           ->with('success', 'Commune créée avec succès.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Erreur création commune: ' . $e->getMessage());
            
            return back()->withInput()
                        ->with('error', 'Erreur lors de la création de la commune: ' . $e->getMessage());
        }
    }

    /**
     * Affichage du formulaire de modification
     */
    public function edit(Commune $commune)
    {
        $departements = Departement::with('region')->orderBy('nom')->get();
        $receveurs = Receveur::all();
        $ordonnateurs = Ordonnateur::all();

        return view('communes.edit', compact('commune', 'departements', 'receveurs', 'ordonnateurs'));
    }

    /**
     * Mise à jour d'une commune
     */
    public function update(Request $request, Commune $commune)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:communes,code,' . $commune->id,
            'departement_id' => 'required|exists:departements,id',
            'telephone' => 'nullable|regex:/^[0-9+\-\s()]+$/|max:20',
            'email' => 'nullable|email|max:255',
            'population' => 'nullable|integer|min:0|max:10000000',
            'superficie' => 'nullable|numeric|min:0|max:100000',
            'adresse' => 'nullable|string|max:500',
            'coordonnees_gps' => 'nullable|string|max:100'
        ]);

        try {
            $commune->update($validated);

            return redirect()->route('communes.show', $commune)
                           ->with('success', 'Commune mise à jour avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Suppression d'une commune
     */
    public function destroy(Commune $commune)
    {
        // Vérifications des données associées
        $hasPrevisions = Prevision::where('commune_id', $commune->id)->exists();
        $hasRealisations = Realisation::where('commune_id', $commune->id)->exists();
        $hasReceveurs = Receveur::where('commune_id', $commune->id)->exists();
        $hasOrdonnateurs = Ordonnateur::where('commune_id', $commune->id)->exists();

        if ($hasPrevisions || $hasRealisations || $hasReceveurs || $hasOrdonnateurs) {
            return redirect()->route('communes.index')
                ->with('error', 'Impossible de supprimer la commune car elle contient des données associées.');
        }

        try {
            $commune->delete();
            return redirect()->route('communes.index')
                ->with('success', 'Commune supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('communes.index')
                ->with('error', 'Erreur lors de la suppression.');
        }
    }

    // ================== MÉTHODES PRIVÉES ==================

    /**
     * Récupérer les statistiques d'une commune pour une année donnée
     */
    private function getCommuneStats($commune, $annee)
    {
        $prevision = Prevision::where('commune_id', $commune->id)
            ->where('annee_exercice', $annee)
            ->first();
            
        $realisationTotal = Realisation::where('commune_id', $commune->id)
            ->where('annee_exercice', $annee)
            ->sum('montant');
            
        $tauxRealisation = Taux_realisation::where('commune_id', $commune->id)
            ->where('annee_exercice', $annee)
            ->first();
            
        $totalDettes = $this->calculateTotalDettes($commune->id, $annee);

        return [
            'budget_previsionnel' => $prevision ? $prevision->montant : 0,
            'realisation_total' => $realisationTotal,
            'taux_realisation' => $tauxRealisation ? $tauxRealisation->pourcentage : 0,
            'evaluation' => $tauxRealisation ? $tauxRealisation->evaluation : 'Non évalué',
            'total_dettes' => $totalDettes,
            'nombre_receveurs' => $commune->receveurs()->count(),
            'nombre_ordonnateurs' => $commune->ordonnateurs()->count()
        ];
    }

    /**
     * Récupérer les données financières détaillées
     */
    private function getDonneesFinancieres($commune, $annee)
    {
        $prevision = Prevision::where('commune_id', $commune->id)
            ->where('annee_exercice', $annee)
            ->first();
            
        $realisations = Realisation::where('commune_id', $commune->id)
            ->where('annee_exercice', $annee)
            ->get();
            
        $tauxRealisation = Taux_realisation::where('commune_id', $commune->id)
            ->where('annee_exercice', $annee)
            ->first();

        return [
            'prevision' => $prevision,
            'realisations' => $realisations,
            'realisation_total' => $realisations->sum('montant'),
            'taux_realisation' => $tauxRealisation,
            'ecart' => $prevision ? ($realisations->sum('montant') - $prevision->montant) : 0
        ];
    }

    /**
     * Récupérer les détails des dettes par type
     */
    private function getDetailsDettes($commune, $annee)
    {
        return [
            'cnps' => [
                'montant' => dette_cnps::where('commune_id', $commune->id)
                    ->whereYear('date_evaluation', $annee)
                    ->sum('montant'),
                'details' => dette_cnps::where('commune_id', $commune->id)
                    ->whereYear('date_evaluation', $annee)
                    ->orderBy('date_evaluation', 'desc')
                    ->get()
            ],
            'fiscale' => [
                'montant' => dette_fiscale::where('commune_id', $commune->id)
                    ->whereYear('date_evaluation', $annee)
                    ->sum('montant'),
                'details' => dette_fiscale::where('commune_id', $commune->id)
                    ->whereYear('date_evaluation', $annee)
                    ->orderBy('date_evaluation', 'desc')
                    ->get()
            ],
            'feicom' => [
                'montant' => dette_feicom::where('commune_id', $commune->id)
                    ->whereYear('date_evaluation', $annee)
                    ->sum('montant'),
                'details' => dette_feicom::where('commune_id', $commune->id)
                    ->whereYear('date_evaluation', $annee)
                    ->orderBy('date_evaluation', 'desc')
                    ->get()
            ],
            'salariale' => [
                'montant' => dette_salariale::where('commune_id', $commune->id)
                    ->whereYear('date_evaluation', $annee)
                    ->sum('montant'),
                'details' => dette_salariale::where('commune_id', $commune->id)
                    ->whereYear('date_evaluation', $annee)
                    ->orderBy('date_evaluation', 'desc')
                    ->get()
            ]
        ];
    }

    /**
     * Calculer le total des dettes
     */
    private function calculateTotalDettes($communeId, $annee)
    {
        $dettesCnps = dette_cnps::where('commune_id', $communeId)
            ->whereYear('date_evaluation', $annee)
            ->sum('montant');

        $dettesFiscale = dette_fiscale::where('commune_id', $communeId)
            ->whereYear('date_evaluation', $annee)
            ->sum('montant');

        $dettesFeicom = dette_feicom::where('commune_id', $communeId)
            ->whereYear('date_evaluation', $annee)
            ->sum('montant');

        $dettesSalariale = dette_salariale::where('commune_id', $communeId)
            ->whereYear('date_evaluation', $annee)
            ->sum('montant');

        return $dettesCnps + $dettesFiscale + $dettesFeicom + $dettesSalariale;
    }

    /**
     * Récupérer l'évolution des performances
     */
    private function getEvolutionPerformancesCommune($commune)
    {
        return Taux_realisation::where('commune_id', $commune->id)
            ->select('annee_exercice', 'pourcentage', 'evaluation')
            ->orderBy('annee_exercice')
            ->get();
    }

    /**
     * Récupérer les projets actifs
     */
    private function getProjetsActifs($commune, $annee)
    {
        // Adapter selon votre modèle de projet
        return collect(); // Retourner une collection vide si pas de modèle Projet
    }

    /**
     * Récupérer les défaillances et retards
     */
    private function getDefaillancesRetards($commune, $annee)
    {
        $defaillances = [];
        $retards = [];

        // Adapter selon vos modèles Defaillance et Retard
        try {
            $defaillances = Defaillance::where('commune_id', $commune->id)
                ->where('annee_exercice', $annee)
                ->get();
        } catch (\Exception $e) {
            $defaillances = collect();
        }

        try {
            $retards = Retard::where('commune_id', $commune->id)
                ->whereYear('date_constat', $annee)
                ->get();
        } catch (\Exception $e) {
            $retards = collect();
        }

        return [
            'defaillances' => $defaillances,
            'retards' => $retards,
            'nb_defaillances' => is_countable($defaillances) ? count($defaillances) : 0,
            'nb_retards' => is_countable($retards) ? count($retards) : 0
        ];
    }

    /**
     * Récupérer les comparaisons avec le département
     */
    private function getComparaisonsDepartement($commune, $annee)
    {
        $departement = $commune->departement;
        $communesDuDept = Commune::where('departement_id', $departement->id)->pluck('id');
        
        $tauxMoyenDept = Taux_realisation::whereIn('commune_id', $communesDuDept)
            ->where('annee_exercice', $annee)
            ->avg('pourcentage');
            
        $tauxCommune = Taux_realisation::where('commune_id', $commune->id)
            ->where('annee_exercice', $annee)
            ->first();

        return [
            'taux_commune' => $tauxCommune ? $tauxCommune->pourcentage : 0,
            'taux_moyen_departement' => $tauxMoyenDept ?? 0,
            'nombre_communes_departement' => $communesDuDept->count(),
            'rang_departement' => $this->getRangDansLeDepartement($commune, $annee)
        ];
    }

    /**
     * Récupérer le rang de la commune dans son département
     */
    private function getRangDansLeDepartement($commune, $annee)
    {
        $communesDuDept = Commune::where('departement_id', $commune->departement_id)->pluck('id');
        
        $classement = Taux_realisation::whereIn('commune_id', $communesDuDept)
            ->where('annee_exercice', $annee)
            ->orderByDesc('pourcentage')
            ->get(['commune_id', 'pourcentage'])
            ->pluck('commune_id')
            ->toArray();
            
        $rang = array_search($commune->id, $classement);
        return $rang !== false ? $rang + 1 : null;
    }

    /**
     * Récupérer les années disponibles pour une commune
     */
    private function getAnneesDisponiblesCommune($commune)
    {
        return collect(range(2016, date('Y')))
            ->reverse()
            ->values()
            ->filter(function ($annee) use ($commune) {
                // Vérifier s'il y a des données pour cette année
                return Taux_realisation::where('commune_id', $commune->id)
                    ->where('annee_exercice', $annee)
                    ->exists() ||
                    Prevision::where('commune_id', $commune->id)
                    ->where('annee_exercice', $annee)
                    ->exists() ||
                    Realisation::where('commune_id', $commune->id)
                    ->where('annee_exercice', $annee)
                    ->exists();
            });
    }

    /**
     * Récupérer toutes les années disponibles
     */
    private function getAnneesDisponibles()
    {
        return collect(range(2016, date('Y')))
            ->reverse()
            ->values();
    }

    /**
     * Appliquer les filtres pour la liste
     */
    private function applyFilters($query, Request $request, $annee)
    {
        if ($request->filled('departement_id')) {
            $query->where('departement_id', $request->departement_id);
        }
        
        if ($request->filled('region_id')) {
            $query->whereHas('departement', function($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }
        
        if ($request->filled('population_min')) {
            $query->where('population', '>=', $request->population_min);
        }
        
        if ($request->filled('avec_receveur')) {
            if ($request->avec_receveur === '1') {
                $query->has('receveurs');
            } else {
                $query->doesntHave('receveurs');
            }
        }

        // Filtre par performance pour l'année sélectionnée
        if ($request->filled('performance')) {
            $performance = $request->performance;
            $query->whereHas('tauxRealisations', function($q) use ($annee, $performance) {
                $q->where('annee_exercice', $annee);
                switch ($performance) {
                    case 'excellente':
                        $q->where('pourcentage', '>=', 90);
                        break;
                    case 'bonne':
                        $q->where('pourcentage', '>=', 75)->where('pourcentage', '<', 90);
                        break;
                    case 'moyenne':
                        $q->where('pourcentage', '>=', 50)->where('pourcentage', '<', 75);
                        break;
                    case 'faible':
                        $q->where('pourcentage', '<', 50);
                        break;
                }
            });
        }
    }

    /**
     * Récupérer les statistiques générales pour une année
     */
    private function getCommunesStats($annee): array
    {
        return Cache::remember("communes_stats_{$annee}", now()->addHours(1), function() use ($annee) {
            return [
                'total' => Commune::count(),
                'avec_receveur' => Commune::has('receveurs')->count(),
                'avec_ordonnateur' => Commune::has('ordonnateurs')->count(),
                'performance_moyenne' => Taux_realisation::where('annee_exercice', $annee)
                    ->avg('pourcentage') ?? 0,
                'budget_total' => Prevision::where('annee_exercice', $annee)
                    ->sum('montant') ?? 0
            ];
        });
    }
}





// <?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Commune;
// use App\Models\Departement;
// use App\Models\Ordonnateur;
// use App\Models\Prevision;
// use App\Models\Receveur;
// use App\Models\dette_cnps;
// use App\Models\dette_feicom;
// use App\Models\dette_fiscale;
// use App\Models\dette_salariale;
// use App\Models\Taux_realisation;
// use App\Models\Realisation;
// use App\Models\Defaillance;
// use App\Models\Retard;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Cache;
// use Illuminate\Http\JsonResponse;

// class CommunesController extends Controller
// {
//     /**
//      * Liste des communes avec pagination, recherche et filtres avancés
//      */
//     public function index(Request $request)
//     {
//         $query = Commune::with([
//             'departement.region', 
//             'receveurs', 
//             'ordonnateurs',
//             'tauxRealisations' => function($q) { 
//                 $q->where('annee_exercice', date('Y'))->latest(); 
//             }
//         ]);
        
//         // Recherche avancée
//         if ($request->filled('search')) {
//             $search = $request->search;
//             $query->where(function($q) use ($search) {
//                 $q->where('nom', 'LIKE', "%{$search}%")
//                   ->orWhere('code', 'LIKE', "%{$search}%")
//                   ->orWhereHas('departement', function($dq) use ($search) {
//                       $dq->where('nom', 'LIKE', "%{$search}%");
//                   })
//                   ->orWhereHas('departement.region', function($rq) use ($search) {
//                       $rq->where('nom', 'LIKE', "%{$search}%");
//                   });
//             });
//         }
        
//         // Filtres avancés
//         $this->applyFilters($query, $request);
        
//         // Tri dynamique
//         $sortBy = $request->get('sort_by', 'nom');
//         $sortDirection = $request->get('sort_direction', 'asc');
        
//         if (in_array($sortBy, ['nom', 'code', 'population', 'superficie', 'created_at'])) {
//             $query->orderBy($sortBy, $sortDirection);
//         }
        
//         $communes = $query->paginate($request->get('per_page', 25));
//         $departements = Departement::with('region')->orderBy('nom')->get();
        
//         // Statistiques rapides
//         $stats = $this->getCommunesStats();
        
//         if ($request->ajax()) {
//             return response()->json([
//                 'html' => view('communes.partials.table', compact('communes'))->render(),
//                 'pagination' => (string) $communes->appends(request()->query())->links(),
//                 'stats' => $stats
//             ]);
//         }
        
//         return view('communes.index', compact('communes', 'departements', 'stats'));
//     }

//     /**
//      * Affichage détaillé d'une commune
//      */
//     public function show(Commune $commune, Request $request)
//     {
//         $annee = $request->get('annee', date('Y'));
        
//         // Chargement optimisé de toutes les relations nécessaires
//         $commune->load([
//             'departement.region', 
//             'receveurs', 
//             'ordonnateurs',
//         ]);
        
//         // Statistiques de la commune pour l'année donnée
//         $stats = $this->getCommuneStats($commune, $annee);
        
//         // Données financières détaillées
//         $donneesFinancieres = $this->getDonneesFinancieres($commune, $annee);
        
//         // Détails des dettes par type
//         $detailsDettes = $this->getDetailsDettes($commune, $annee);
        
//         // Évolution des performances sur plusieurs années
//         $evolutionPerformances = $this->getEvolutionPerformancesCommune($commune);
        
//         // Projets en cours
//         $projetsActifs = $this->getProjetsActifs($commune, $annee);
        
//         // Défaillances et retards
//         $defaillancesRetards = $this->getDefaillancesRetards($commune, $annee);
        
//         // Comparaisons avec le département
//         $comparaisons = $this->getComparaisonsDepartement($commune, $annee);
        
//         // Années disponibles pour le filtre
//         $anneesDisponibles = $this->getAnneesDisponibles($commune);
        
//         return view('communes.show', compact(
//             'commune', 
//             'stats', 
//             'donneesFinancieres',
//             'detailsDettes',
//             'evolutionPerformances',
//             'projetsActifs',
//             'defaillancesRetards',
//             'comparaisons',
//             'annee',
//             'anneesDisponibles'
//         ));
//     }

//     /**
//      * Affichage du formulaire de création
//      */
//     public function create()
//     {
//         $departements = Departement::with('region')->orderBy('nom')->get();
//         $receveurs = Receveur::whereNull('commune_id')->orderBy('nom')->get();
//         $ordonnateurs = Ordonnateur::whereNull('commune_id')->orderBy('nom')->get();
        
//         return view('communes.create', compact('departements', 'receveurs', 'ordonnateurs'));
//     }

//     /**
//      * Enregistrement d'une nouvelle commune
//      */
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'nom' => 'required|string|max:255',
//             'code' => 'required|string|max:10|unique:communes,code',
//             'departement_id' => 'required|exists:departements,id',
//             'telephone' => 'nullable|regex:/^[0-9+\-\s()]+$/|max:20',
//             'email' => 'nullable|email|max:255',
//             'population' => 'nullable|integer|min:0|max:10000000',
//             'superficie' => 'nullable|numeric|min:0|max:100000',
//             'adresse' => 'nullable|string|max:500',
//             'coordonnees_gps' => 'nullable|string|max:100'
//         ]);

//         DB::beginTransaction();
//         try {
//             $commune = Commune::create($validated);
            
//             // Assigner les responsables si fournis
//             if ($request->filled('receveur_ids')) {
//                 Receveur::whereIn('id', $request->receveur_ids)
//                     ->update(['commune_id' => $commune->id]);
//             }
            
//             if ($request->filled('ordonnateur_ids')) {
//                 Ordonnateur::whereIn('id', $request->ordonnateur_ids)
//                     ->update(['commune_id' => $commune->id]);
//             }
            
//             DB::commit();
            
//             return redirect()->route('communes.show', $commune)
//                            ->with('success', 'Commune créée avec succès.');
                           
//         } catch (\Exception $e) {
//             DB::rollback();
            
//             \Log::error('Erreur création commune: ' . $e->getMessage());
            
//             return back()->withInput()
//                         ->with('error', 'Erreur lors de la création de la commune: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Affichage du formulaire de modification
//      */
//     public function edit(Commune $commune)
//     {
//         $departements = Departement::with('region')->orderBy('nom')->get();
//         $receveurs = Receveur::all();
//         $ordonnateurs = Ordonnateur::all();

//         return view('communes.edit', compact('commune', 'departements', 'receveurs', 'ordonnateurs'));
//     }

//     /**
//      * Mise à jour d'une commune
//      */
//     public function update(Request $request, Commune $commune)
//     {
//         $validated = $request->validate([
//             'nom' => 'required|string|max:255',
//             'code' => 'required|string|max:10|unique:communes,code,' . $commune->id,
//             'departement_id' => 'required|exists:departements,id',
//             'telephone' => 'nullable|regex:/^[0-9+\-\s()]+$/|max:20',
//             'email' => 'nullable|email|max:255',
//             'population' => 'nullable|integer|min:0|max:10000000',
//             'superficie' => 'nullable|numeric|min:0|max:100000',
//             'adresse' => 'nullable|string|max:500',
//             'coordonnees_gps' => 'nullable|string|max:100'
//         ]);

//         try {
//             $commune->update($validated);

//             return redirect()->route('communes.show', $commune)
//                            ->with('success', 'Commune mise à jour avec succès.');

//         } catch (\Exception $e) {
//             return back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage())
//                         ->withInput();
//         }
//     }

//     /**
//      * Suppression d'une commune
//      */
//     public function destroy(Commune $commune)
//     {
//         // Vérifications des données associées
//         $hasPrevisions = Prevision::where('commune_id', $commune->id)->exists();
//         $hasRealisations = Realisation::where('commune_id', $commune->id)->exists();
//         $hasReceveurs = Receveur::where('commune_id', $commune->id)->exists();
//         $hasOrdonnateurs = Ordonnateur::where('commune_id', $commune->id)->exists();

//         if ($hasPrevisions || $hasRealisations || $hasReceveurs || $hasOrdonnateurs) {
//             return redirect()->route('communes.index')
//                 ->with('error', 'Impossible de supprimer la commune car elle contient des données associées.');
//         }

//         try {
//             $commune->delete();
//             return redirect()->route('communes.index')
//                 ->with('success', 'Commune supprimée avec succès.');
//         } catch (\Exception $e) {
//             return redirect()->route('communes.index')
//                 ->with('error', 'Erreur lors de la suppression.');
//         }
//     }

//     // ================== MÉTHODES PRIVÉES ==================

//     /**
//      * Récupérer les statistiques d'une commune pour une année donnée
//      */
//     private function getCommuneStats($commune, $annee)
//     {
//         $prevision = Prevision::where('commune_id', $commune->id)
//             ->where('annee_exercice', $annee)
//             ->first();
            
//         $realisationTotal = Realisation::where('commune_id', $commune->id)
//             ->where('annee_exercice', $annee)
//             ->sum('montant');
            
//         $tauxRealisation = Taux_realisation::where('commune_id', $commune->id)
//             ->where('annee_exercice', $annee)
//             ->first();
            
//         $totalDettes = $this->calculateTotalDettes($commune->id, $annee);

//         return [
//             'budget_previsionnel' => $prevision ? $prevision->montant : 0,
//             'realisation_total' => $realisationTotal,
//             'taux_realisation' => $tauxRealisation ? $tauxRealisation->pourcentage : 0,
//             'evaluation' => $tauxRealisation ? $tauxRealisation->evaluation : 'Non évalué',
//             'total_dettes' => $totalDettes,
//             'nombre_receveurs' => $commune->receveurs()->count(),
//             'nombre_ordonnateurs' => $commune->ordonnateurs()->count()
//         ];
//     }

//     /**
//      * Récupérer les données financières détaillées
//      */
//     private function getDonneesFinancieres($commune, $annee)
//     {
//         $prevision = Prevision::where('commune_id', $commune->id)
//             ->where('annee_exercice', $annee)
//             ->first();
            
//         $realisations = Realisation::where('commune_id', $commune->id)
//             ->where('annee_exercice', $annee)
//             ->get();
            
//         $tauxRealisation = Taux_realisation::where('commune_id', $commune->id)
//             ->where('annee_exercice', $annee)
//             ->first();

//         return [
//             'prevision' => $prevision,
//             'realisations' => $realisations,
//             'realisation_total' => $realisations->sum('montant'),
//             'taux_realisation' => $tauxRealisation,
//             'ecart' => $prevision ? ($realisations->sum('montant') - $prevision->montant) : 0
//         ];
//     }

//     /**
//      * Récupérer les détails des dettes par type
//      */
//     private function getDetailsDettes($commune, $annee)
//     {
//         return [
//             'cnps' => [
//                 'montant' => dette_cnps::where('commune_id', $commune->id)
//                     ->whereYear('date_evaluation', $annee)
//                     ->sum('montant'),
//                 'details' => dette_cnps::where('commune_id', $commune->id)
//                     ->whereYear('date_evaluation', $annee)
//                     ->orderBy('date_evaluation', 'desc')
//                     ->get()
//             ],
//             'fiscale' => [
//                 'montant' => dette_fiscale::where('commune_id', $commune->id)
//                     ->whereYear('date_evaluation', $annee)
//                     ->sum('montant'),
//                 'details' => dette_fiscale::where('commune_id', $commune->id)
//                     ->whereYear('date_evaluation', $annee)
//                     ->orderBy('date_evaluation', 'desc')
//                     ->get()
//             ],
//             'feicom' => [
//                 'montant' => dette_feicom::where('commune_id', $commune->id)
//                     ->whereYear('date_evaluation', $annee)
//                     ->sum('montant'),
//                 'details' => dette_feicom::where('commune_id', $commune->id)
//                     ->whereYear('date_evaluation', $annee)
//                     ->orderBy('date_evaluation', 'desc')
//                     ->get()
//             ],
//             'salariale' => [
//                 'montant' => dette_salariale::where('commune_id', $commune->id)
//                     ->whereYear('date_evaluation', $annee)
//                     ->sum('montant'),
//                 'details' => dette_salariale::where('commune_id', $commune->id)
//                     ->whereYear('date_evaluation', $annee)
//                     ->orderBy('date_evaluation', 'desc')
//                     ->get()
//             ]
//         ];
//     }

//     /**
//      * Calculer le total des dettes
//      */
//     private function calculateTotalDettes($communeId, $annee)
//     {
//         $dettesCnps = dette_cnps::where('commune_id', $communeId)
//             ->whereYear('date_evaluation', $annee)
//             ->sum('montant');

//         $dettesFiscale = dette_fiscale::where('commune_id', $communeId)
//             ->whereYear('date_evaluation', $annee)
//             ->sum('montant');

//         $dettesFeicom = dette_feicom::where('commune_id', $communeId)
//             ->whereYear('date_evaluation', $annee)
//             ->sum('montant');

//         $dettesSalariale = dette_salariale::where('commune_id', $communeId)
//             ->whereYear('date_evaluation', $annee)
//             ->sum('montant');

//         return $dettesCnps + $dettesFiscale + $dettesFeicom + $dettesSalariale;
//     }

//     /**
//      * Récupérer l'évolution des performances
//      */
//     private function getEvolutionPerformancesCommune($commune)
//     {
//         return Taux_realisation::where('commune_id', $commune->id)
//             ->select('annee_exercice', 'pourcentage', 'evaluation')
//             ->orderBy('annee_exercice')
//             ->get();
//     }

//     /**
//      * Récupérer les projets actifs
//      */
//     private function getProjetsActifs($commune, $annee)
//     {
//         // Adapter selon votre modèle de projet
//         return collect(); // Retourner une collection vide si pas de modèle Projet
//     }

//     /**
//      * Récupérer les défaillances et retards
//      */
//     private function getDefaillancesRetards($commune, $annee)
//     {
//         $defaillances = [];
//         $retards = [];

//         // Adapter selon vos modèles Defaillance et Retard
//         try {
//             $defaillances = Defaillance::where('commune_id', $commune->id)
//                 ->where('annee_exercice', $annee)
//                 ->get();
//         } catch (\Exception $e) {
//             $defaillances = collect();
//         }

//         try {
//             $retards = Retard::where('commune_id', $commune->id)
//                 ->whereYear('date_constat', $annee)
//                 ->get();
//         } catch (\Exception $e) {
//             $retards = collect();
//         }

//         return [
//             'defaillances' => $defaillances,
//             'retards' => $retards,
//             'nb_defaillances' => is_countable($defaillances) ? count($defaillances) : 0,
//             'nb_retards' => is_countable($retards) ? count($retards) : 0
//         ];
//     }

//     /**
//      * Récupérer les comparaisons avec le département
//      */
//     private function getComparaisonsDepartement($commune, $annee)
//     {
//         $departement = $commune->departement;
//         $communesDuDept = Commune::where('departement_id', $departement->id)->pluck('id');
        
//         $tauxMoyenDept = Taux_realisation::whereIn('commune_id', $communesDuDept)
//             ->where('annee_exercice', $annee)
//             ->avg('pourcentage');
            
//         $tauxCommune = Taux_realisation::where('commune_id', $commune->id)
//             ->where('annee_exercice', $annee)
//             ->first();

//         return [
//             'taux_commune' => $tauxCommune ? $tauxCommune->pourcentage : 0,
//             'taux_moyen_departement' => $tauxMoyenDept ?? 0,
//             'nombre_communes_departement' => $communesDuDept->count(),
//             'rang_departement' => $this->getRangDansLeDepartement($commune, $annee)
//         ];
//     }

//     /**
//      * Récupérer le rang de la commune dans son département
//      */
//     private function getRangDansLeDepartement($commune, $annee)
//     {
//         $communesDuDept = Commune::where('departement_id', $commune->departement_id)->pluck('id');
        
//         $classement = Taux_realisation::whereIn('commune_id', $communesDuDept)
//             ->where('annee_exercice', $annee)
//             ->orderByDesc('pourcentage')
//             ->get(['commune_id', 'pourcentage'])
//             ->pluck('commune_id')
//             ->toArray();
            
//         $rang = array_search($commune->id, $classement);
//         return $rang !== false ? $rang + 1 : null;
//     }

//     /**
//      * Récupérer les années disponibles
//      */
//     private function getAnneesDisponibles($commune)
//     {
//         return Taux_realisation::where('commune_id', $commune->id)
//             ->distinct()
//             ->orderByDesc('annee_exercice')
//             ->pluck('annee_exercice');
//     }

//     /**
//      * Appliquer les filtres pour la liste
//      */
//     private function applyFilters($query, Request $request)
//     {
//         if ($request->filled('departement_id')) {
//             $query->where('departement_id', $request->departement_id);
//         }
        
//         if ($request->filled('region_id')) {
//             $query->whereHas('departement', function($q) use ($request) {
//                 $q->where('region_id', $request->region_id);
//             });
//         }
        
//         if ($request->filled('population_min')) {
//             $query->where('population', '>=', $request->population_min);
//         }
        
//         if ($request->filled('avec_receveur')) {
//             if ($request->avec_receveur === '1') {
//                 $query->has('receveurs');
//             } else {
//                 $query->doesntHave('receveurs');
//             }
//         }
//     }

//     /**
//      * Récupérer les statistiques générales
//      */
//     private function getCommunesStats(): array
//     {
//         return Cache::remember('communes_stats', now()->addHours(1), function() {
//             return [
//                 'total' => Commune::count(),
//                 'avec_receveur' => Commune::has('receveurs')->count(),
//                 'avec_ordonnateur' => Commune::has('ordonnateurs')->count(),
//                 'performance_moyenne' => Taux_realisation::where('annee_exercice', date('Y'))
//                     ->avg('pourcentage') ?? 0,
//                 'budget_total' => Prevision::where('annee_exercice', date('Y'))
//                     ->sum('montant') ?? 0
//             ];
//         });
//     }
// }







// <?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Commune;
// use App\Models\Departement;
// use App\Models\Ordonnateur;
// use App\Models\Prevision;
// use App\Models\Receveur;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Cache;
// use Illuminate\Http\JsonResponse;

// class CommunesController extends Controller
// {
//     /**
//      * Liste des communes avec pagination, recherche et filtres avancés
//      */
//     public function index(Request $request)
//     {
//         $query = Commune::with([
//             'departement.region', 
//             'receveurs', 
//             'ordonnateurs',
//             'previsions' => function($q) { $q->latest(); },
//             'realisations' => function($q) { $q->latest(); }
//         ]);
        
//         // Recherche avancée
//         if ($request->filled('search')) {
//             $search = $request->search;
//             $query->where(function($q) use ($search) {
//                 $q->where('nom', 'LIKE', "%{$search}%")
//                   ->orWhere('code', 'LIKE', "%{$search}%")
//                   ->orWhereHas('departement', function($dq) use ($search) {
//                       $dq->where('nom', 'LIKE', "%{$search}%");
//                   })
//                   ->orWhereHas('departement.region', function($rq) use ($search) {
//                       $rq->where('nom', 'LIKE', "%{$search}%");
//                   });
//             });
//         }
        
//         // Filtres avancés
//         $this->applyFilters($query, $request);
        
//         // Tri dynamique
//         $sortBy = $request->get('sort_by', 'nom');
//         $sortDirection = $request->get('sort_direction', 'asc');
        
//         if (in_array($sortBy, ['nom', 'code', 'population', 'superficie', 'created_at'])) {
//             $query->orderBy($sortBy, $sortDirection);
//         }
        
//         $communes = $query->paginate($request->get('per_page', 15));
//         $departements = Departement::with('region')->orderBy('nom')->get();
        
//         // Statistiques rapides
//         $stats = $this->getCommunesStats();
        
//         // Données pour les graphiques
//         $chartData = $this->getChartsData();
        
//         if ($request->ajax()) {
//             return response()->json([
//                 'html' => view('communes.partials.table', compact('communes'))->render(),
//                 'pagination' => (string) $communes->appends(request()->query())->links(),
//                 'stats' => $stats
//             ]);
//         }
        
//         return view('communes.index', compact('communes', 'departements', 'stats', 'chartData'));
//     }

//     /**
//      * API pour récupérer les communes en AJAX
//      */
//     public function api(Request $request): JsonResponse
//     {
//         $communes = Commune::with(['departement.region', 'receveurs', 'ordonnateurs'])
//             ->when($request->search, function($query, $search) {
//                 $query->where('nom', 'LIKE', "%{$search}%")
//                       ->orWhere('code', 'LIKE', "%{$search}%");
//             })
//             ->when($request->departement_id, function($query, $departement) {
//                 $query->where('departement_id', $departement);
//             })
//             ->paginate(10);
            
//         return response()->json($communes);
//     }

//     /**
//      * Affichage du formulaire de création
//      */
//     public function create()
//     {
//         $departements = Departement::with('region')->orderBy('nom')->get();
//         $receveurs = Receveur::whereNull('commune_id')->orderBy('nom')->get();
//         $ordonnateurs = Ordonnateur::whereNull('commune_id')->orderBy('nom')->get();
        
//         return view('communes.create', compact(
//             'departements', 
//             'receveurs', 
//             'ordonnateurs'
//         ));
//     }

//     /**
//      * Validation en temps réel pour les champs
//      */
//     public function validateField(Request $request): JsonResponse
//     {
//         $field = $request->field;
//         $value = $request->value;
//         $communeId = $request->commune_id;
        
//         $rules = [
//             'nom' => 'required|string|max:255',
//             'code' => 'required|string|max:10|unique:communes,code' . ($communeId ? ",$communeId" : ''),
//             'telephone' => 'nullable|regex:/^[0-9+\-\s()]+$/|max:20',
//             'email' => 'nullable|email|max:255',
//             'population' => 'nullable|integer|min:0|max:10000000',
//             'superficie' => 'nullable|numeric|min:0|max:100000'
//         ];
        
//         $validator = validator([$field => $value], [$field => $rules[$field] ?? 'required']);
        
//         return response()->json([
//             'valid' => !$validator->fails(),
//             'errors' => $validator->errors()->get($field)
//         ]);
//     }

//     /**
//      * Enregistrement
//      */
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'nom' => 'required|string|max:255',
//             'code' => 'required|string|max:10|unique:communes,code',
//             'departement_id' => 'required|exists:departements,id',
//             'telephone' => 'nullable|regex:/^[0-9+\-\s()]+$/|max:20',
//             'email' => 'nullable|email|max:255',
//             'population' => 'nullable|integer|min:0|max:10000000',
//             'superficie' => 'nullable|numeric|min:0|max:100000',
//             'adresse' => 'nullable|string|max:500',
//             'receveur_ids' => 'nullable|array',
//             'receveur_ids.*' => 'exists:receveurs,id',
//             'ordonnateur_ids' => 'nullable|array', 
//             'ordonnateur_ids.*' => 'exists:ordonnateurs,id',
//             'coordonnees_gps' => 'nullable|string|max:100'
//         ]);

//         DB::beginTransaction();
//         try {
//             // Créer la commune
//             $commune = Commune::create($validated);
            
//             // Assigner les responsables
//             if ($request->filled('receveur_ids')) {
//                 Receveur::whereIn('id', $request->receveur_ids)
//                     ->update(['commune_id' => $commune->id]);
//             }
            
//             if ($request->filled('ordonnateur_ids')) {
//                 Ordonnateur::whereIn('id', $request->ordonnateur_ids)
//                     ->update(['commune_id' => $commune->id]);
//             }
            
//             DB::commit();
            
//             return redirect()->route('communes.show', $commune)
//                            ->with('success', 'Commune créée avec succès.');
                           
//         } catch (\Exception $e) {
//             DB::rollback();
            
//             \Log::error('Erreur création commune: ' . $e->getMessage());
            
//             return back()->withInput()
//                         ->with('error', 'Erreur lors de la création de la commune: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Affichage détaillé
//      */
//    public function show(Commune $commune, Request $request)
// {
//     $annee = $request->get('annee', date('Y'));
//     $periode = $request->get('periode', 'annuelle');
    
//     // Chargement optimisé des relations INCLUANT les dettes
//     $commune->load([
//         'departement.region', 
//         'receveurs', 
//         'ordonnateurs',
//         'depotsComptes', 
//         'previsions', 
//         'realisations', 
//         'tauxRealisations',
//         'dettesCnps', 
//         'dettesFiscale', 
//         'dettesFeicom', 
//         'dettesSalariale',
//         'defaillances', 
//         'retards', 
//         'projets'
//     ]);
    
//     // Données financières
//     $donneesFinancieres = Cache::remember(
//         "commune_finances_{$commune->id}_{$annee}_{$periode}",
//         now()->addHours(2),
//         fn() => $this->getDonneesFinancieres($commune, $annee, $periode)
//     );
    
//     // CORRECTION: Récupération des dettes avec les détails complets
//     $detailsDettes = $this->getDetailsDettes($commune, $annee);
//     $totalDettes = $detailsDettes['cnps']['montant'] + 
//                    $detailsDettes['fiscale']['montant'] + 
//                    $detailsDettes['feicom']['montant'] + 
//                    $detailsDettes['salariale']['montant'];
    
//     // Indicateurs
//     $indicateurs = [
//         'taux_execution' => $donneesFinancieres['taux_realisation'] ?? 0,
//         'ratio_dette_budget' => ($donneesFinancieres['prevision'] ?? 0) > 0 ? 
//             ($totalDettes / ($donneesFinancieres['prevision'] ?? 1)) * 100 : 0,
//         'nombre_projets_actifs' => $commune->projets()->where('statut', 'en_cours')->count(),
//         'nombre_defaillances' => $commune->defaillances()->where('annee_exercice', $annee)->count(),
//         'nombre_retards' => $commune->retards()->where('annee_exercice', $annee)->count(),
//         'total_dettes' => $totalDettes
//     ];
    
//     // Projets en cours
//     $projetsEnCours = $commune->projets()->where('statut', 'en_cours')->get();
    
//     // Évolution des performances
//     $evolution = $this->getEvolutionPerformance($commune);
    
//     // Comparaisons
//     $comparaisons = $this->getComparaisonsRegional($commune, $annee);
    
//     if ($request->ajax()) {
//         return response()->json([
//             'donneesFinancieres' => $donneesFinancieres,
//             'detailsDettes' => $detailsDettes
//         ]);
//     }
    
//     return view('communes.show', compact(
//         'commune', 
//         'donneesFinancieres', 
//         'annee', 
//         'periode',
//         'detailsDettes', 
//         'totalDettes', 
//         'indicateurs', 
//         'projetsEnCours',
//         'evolution', 
//         'comparaisons'
//     ));
// }

// /**
//  * CORRECTION: Méthode pour récupérer les détails des dettes avec gestion d'erreurs
//  */
// private function getDetailsDettes($commune, $annee)
// {
//     try {
//         // Récupération avec eager loading pour optimiser les performances
//         $commune->load([
//             'dettesCnps' => function($query) use ($annee) {
//                 $query->where('annee_exercice', $annee)->orderBy('date_evaluation', 'desc');
//             },
//             'dettesFiscale' => function($query) use ($annee) {
//                 $query->where('annee_exercice', $annee)->orderBy('date_evaluation', 'desc');
//             },
//             'dettesFeicom' => function($query) use ($annee) {
//                 $query->where('annee_exercice', $annee)->orderBy('date_evaluation', 'desc');
//             },
//             'dettesSalariale' => function($query) use ($annee) {
//                 $query->where('annee_exercice', $annee)->orderBy('date_evaluation', 'desc');
//             }
//         ]);

//         return [
//             'cnps' => [
//                 'montant' => $commune->dettesCnps->sum('montant'),
//                 'details' => $commune->dettesCnps,
//                 'count' => $commune->dettesCnps->count()
//             ],
//             'fiscale' => [
//                 'montant' => $commune->dettesFiscale->sum('montant'),
//                 'details' => $commune->dettesFiscale,
//                 'count' => $commune->dettesFiscale->count()
//             ],
//             'feicom' => [
//                 'montant' => $commune->dettesFeicom->sum('montant'),
//                 'details' => $commune->dettesFeicom,
//                 'count' => $commune->dettesFeicom->count()
//             ],
//             'salariale' => [
//                 'montant' => $commune->dettesSalariale->sum('montant'),
//                 'details' => $commune->dettesSalariale,
//                 'count' => $commune->dettesSalariale->count()
//             ]
//         ];
        
//     } catch (\Exception $e) {
//         \Log::error("Erreur lors de la récupération des dettes pour la commune {$commune->id}: " . $e->getMessage());
        
//         // Retourner une structure par défaut en cas d'erreur
//         return [
//             'cnps' => ['montant' => 0, 'details' => collect(), 'count' => 0],
//             'fiscale' => ['montant' => 0, 'details' => collect(), 'count' => 0],
//             'feicom' => ['montant' => 0, 'details' => collect(), 'count' => 0],
//             'salariale' => ['montant' => 0, 'details' => collect(), 'count' => 0]
//         ];
//     }
// }
// /**
//  * AJOUT: Méthode pour récupérer l'évolution des performances
//  */
// private function getEvolutionPerformance($commune)
// {
//     $annees = range(date('Y') - 5, date('Y'));
//     $evolution = [];
    
//     foreach ($annees as $annee) {
//         $taux = $commune->tauxRealisations()->where('annee_exercice', $annee)->first();
//         if ($taux) {
//             $evolution[] = [
//                 'annee' => $annee,
//                 'taux_realisation' => $taux->pourcentage,
//                 'evaluation' => $taux->evaluation
//             ];
//         }
//     }
    
//     return $evolution;
// }

// /**
//  * AJOUT: Méthode pour les comparaisons régionales
//  */
// private function getComparaisonsRegional($commune, $annee)
// {
//     // Exemple basique - à adapter selon votre logique métier
//     $tauxCommune = $commune->getTauxRealisationAnnuel($annee);
    
//     return [
//         'rang_departement' => 'N/A', // À implémenter
//         'nombre_communes_departement' => $commune->departement->communes()->count(),
//         'taux_realisation_commune' => $tauxCommune,
//         'moyenne_departement' => 65.5, // Exemple
//     ];
// }



// /**
//  * CORRECTION DÉFINITIVE de la méthode getDonneesFinancieres
//  */
// private function getDonneesFinancieres($commune, $annee, $periode = 'annuelle')
// {
//     $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
//     $realisations = $commune->realisations->where('annee_exercice', $annee);
//     $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
    
//     // CORRECTION: Toujours retourner une collection, même vide
//     $donneesPeriodiques = collect(); // Collection vide par défaut
    
//     if ($periode !== 'annuelle') {
//         $donneesPeriodiques = $commune->realisations()
//             ->where('annee_exercice', $annee)
//             ->select('periode', DB::raw('SUM(montant) as montant_total'), DB::raw('COUNT(*) as nombre_operations'))
//             ->groupBy('periode')
//             ->get(); // Ceci retourne une collection Laravel
//     }
    
//     return [
//         'prevision' => $prevision?->montant ?? 0,
//         'realisation_total' => $realisations->sum('montant'),
//         'taux_realisation' => $tauxRealisation?->pourcentage ?? 0,
//         'evaluation' => $tauxRealisation?->evaluation ?? 'Non évalué',
//         'ecart' => $tauxRealisation?->ecart ?? 0,
//         'donnees_periodiques' => $donneesPeriodiques // ← Toujours une collection
//     ];
// }


//     /**
//      * Affichage du formulaire de modification
//      */
//     public function edit(Commune $commune)
//     {
//         $receveurs = Receveur::all();
//         $ordonnateurs = Ordonnateur::all();
//         $departements = Departement::with('region')->get();

//         return view('communes.edit', compact(
//             'commune', 
//             'receveurs', 
//             'ordonnateurs', 
//             'departements'
//         ));
//     }

//     /**
//      * Mise à jour
//      */
//     public function update(Request $request, Commune $commune)
//     {
//         $validated = $request->validate([
//             'nom' => 'required|string|max:255',
//         'code' => 'required|string|max:10',
//         'departement_id' => 'required|exists:departements,id',
//         'telephone' => 'nullable|string|max:20',
//             'email' => 'nullable|email|max:255',
//             'population' => 'nullable|integer|min:0|max:10000000',
//             'superficie' => 'nullable|numeric|min:0|max:100000',
//             'adresse' => 'nullable|string|max:500',
//             'receveur_ids' => 'nullable|array',
//             'receveur_ids.*' => 'exists:receveurs,id',
//             'ordonnateur_ids' => 'nullable|array',
//             'ordonnateur_ids.*' => 'exists:ordonnateurs,id',
//             'coordonnees_gps' => 'nullable|string|max:100'
//         ]);

//         try {
//         // Mettre à jour UNIQUEMENT les informations de base de la commune
//         $commune->update($request->only(['nom', 'code', 'departement_id', 'telephone']));

//         return redirect()->route('communes.show', $commune)
//             ->with('success', 'Commune mise à jour avec succès.');

//     } catch (\Exception $e) {
//         return back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage())
//             ->withInput();
//     }
//     }

//     /**
//      * Suppression
//      */
//     public function destroy(Commune $commune)
//     {
//         // Vérifications détaillées des données associées
//         $hasPrevisions = $commune->previsions()->count() > 0;
//         $hasRealisations = $commune->realisations()->count() > 0;
//         $hasReceveurs = $commune->receveurs()->count() > 0;
//         $hasOrdonnateurs = $commune->ordonnateurs()->count() > 0;

//         if ($hasPrevisions || $hasRealisations || $hasReceveurs || $hasOrdonnateurs) {
//             $errorMessage = 'Impossible de supprimer la commune "' . $commune->nom . '" car elle contient : ';
//             $reasons = [];

//             if ($hasPrevisions) {
//                 $reasons[] = 'des prévisions budgétaires';
//             }
//             if ($hasRealisations) {
//                 $reasons[] = 'des réalisations';
//             }
//             if ($hasReceveurs) {
//                 $reasons[] = 'des receveurs assignés';
//             }
//             if ($hasOrdonnateurs) {
//                 $reasons[] = 'des ordonnateurs assignés';
//             }

//             $errorMessage .= implode(', ', $reasons) . '.';

//             return redirect()->route('communes.index')
//                 ->with('error', $errorMessage);
//         }

//         // Supprimer la commune
//         $commune->delete();

//         return redirect()->route('communes.index')
//             ->with('success', 'Commune "' . $commune->nom . '" supprimée avec succès.');
//     }

//     /**
//      * Méthodes privées pour la logique métier
//      */
//     private function applyFilters($query, Request $request)
//     {
//         // Filtre par département
//         if ($request->filled('departement_id')) {
//             $query->where('departement_id', $request->departement_id);
//         }
        
//         // Filtre par région
//         if ($request->filled('region_id')) {
//             $query->whereHas('departement', function($q) use ($request) {
//                 $q->where('region_id', $request->region_id);
//             });
//         }
        
//         // Filtre par taille de population
//         if ($request->filled('population_min')) {
//             $query->where('population', '>=', $request->population_min);
//         }
//         if ($request->filled('population_max')) {
//             $query->where('population', '<=', $request->population_max);
//         }
        
//         // Filtre par statut des responsables
//         if ($request->filled('avec_receveur')) {
//             if ($request->avec_receveur === '1') {
//                 $query->has('receveurs');
//             } else {
//                 $query->doesntHave('receveurs');
//             }
//         }
        
//         if ($request->filled('avec_ordonnateur')) {
//             if ($request->avec_ordonnateur === '1') {
//                 $query->has('ordonnateurs');
//             } else {
//                 $query->doesntHave('ordonnateurs');
//             }
//         }
//     }

//     private function getCommunesStats(): array
//     {
//         return Cache::remember('communes_stats', now()->addHours(1), function() {
//             return [
//                 'total' => Commune::count(),
//                 'avec_receveur' => Commune::has('receveurs')->count(),
//                 'avec_ordonnateur' => Commune::has('ordonnateurs')->count(),
//                 'avec_prevision_courante' => Commune::whereHas('previsions', function($q) {
//                     $q->where('annee_exercice', date('Y'));
//                 })->count(),
//                 'performance_moyenne' => DB::table('taux_realisations')
//                     ->where('annee_exercice', date('Y'))
//                     ->avg('pourcentage') ?? 0,
//                 'budget_total' => DB::table('previsions')
//                     ->where('annee_exercice', date('Y'))
//                     ->sum('montant') ?? 0
//             ];
//         });
//     }

//     private function getChartsData(): array
//     {
//         return Cache::remember('communes_charts', now()->addHours(2), function() {
//             return [
//                 'repartition_par_region' => DB::table('communes')
//                     ->join('departements', 'communes.departement_id', '=', 'departements.id')
//                     ->join('regions', 'departements.region_id', '=', 'regions.id')
//                     ->select('regions.nom', DB::raw('count(*) as total'))
//                     ->groupBy('regions.id', 'regions.nom')
//                     ->get()
//             ];
//         });
//     }



//     private function reassignResponsables($commune, $request)
//     {
//         // Si les relations sont hasMany (one-to-many)
//         // Libérer les anciens receveurs et ordonnateurs
//         $commune->receveurs()->update(['commune_id' => null]);
//         $commune->ordonnateurs()->update(['commune_id' => null]);
        
//         // Assigner les nouveaux receveurs
//         if ($request->filled('receveur_ids')) {
//             Receveur::whereIn('id', $request->receveur_ids)
//                 ->update(['commune_id' => $commune->id]);
//         }
        
//         // Assigner les nouveaux ordonnateurs
//         if ($request->filled('ordonnateur_ids')) {
//             Ordonnateur::whereIn('id', $request->ordonnateur_ids)
//                 ->update(['commune_id' => $commune->id]);
//         }
//     }

//     private function invalidateCache($commune)
//     {
//         Cache::forget("commune_finances_{$commune->id}_" . date('Y') . "_annuelle");
//         Cache::forget('communes_stats');
//         Cache::forget('communes_charts');
//     }
// }








// <?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Commune;
// use App\Models\Departement;
// use App\Models\Ordonnateur;
// use App\Models\Prevision;
// use App\Models\Receveur;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Cache;
// use Illuminate\Http\JsonResponse;
// use App\Services\CommunePerformanceService;
// use App\Services\NotificationService;
// use App\Services\ExportService;

// class CommunesController extends Controller
// {
//     protected $performanceService;
//     protected $notificationService;
//     protected $exportService;

//     public function __construct(
//         CommunePerformanceService $performanceService,
//         NotificationService $notificationService,
//         ExportService $exportService
//     ) {
//         $this->performanceService = $performanceService;
//         $this->notificationService = $notificationService;
//         $this->exportService = $exportService;
//     }

//     /**
//      * Liste des communes avec pagination, recherche et filtres avancés
//      */
//     public function index(Request $request)
//     {
//         $query = Commune::with([
//             'departement.region', 
//             'receveurs', 
//             'ordonnateurs',
//             'previsions' => function($q) { $q->latest(); },
//             'realisations' => function($q) { $q->latest(); }
//         ]);
        
//         // Recherche avancée
//         if ($request->filled('search')) {
//             $search = $request->search;
//             $query->where(function($q) use ($search) {
//                 $q->where('nom', 'LIKE', "%{$search}%")
//                   ->orWhere('code', 'LIKE', "%{$search}%")
//                   ->orWhereHas('departement', function($dq) use ($search) {
//                       $dq->where('nom', 'LIKE', "%{$search}%");
//                   })
//                   ->orWhereHas('departement.region', function($rq) use ($search) {
//                       $rq->where('nom', 'LIKE', "%{$search}%");
//                   });
//             });
//         }
        
//         // Filtres avancés
//         $this->applyFilters($query, $request);
        
//         // Tri dynamique
//         $sortBy = $request->get('sort_by', 'nom');
//         $sortDirection = $request->get('sort_direction', 'asc');
        
//         if (in_array($sortBy, ['nom', 'code', 'population', 'superficie', 'created_at'])) {
//             $query->orderBy($sortBy, $sortDirection);
//         }
        
//         $communes = $query->paginate($request->get('per_page', 15));
//         $departements = Departement::with('region')->orderBy('nom')->get();
        
//         // Statistiques rapides
//         $stats = $this->getCommunesStats();
        
//         // Données pour les graphiques
//         $chartData = $this->getChartsData();
        
//         if ($request->ajax()) {
//             return response()->json([
//                 'html' => view('communes.partials.table', compact('communes'))->render(),
//                 'pagination' => $communes->appends(request()->query())->links()->render(),
//                 'stats' => $stats
//             ]);
//         }
        
//         return view('communes.index', compact('communes', 'departements', 'stats', 'chartData'));
//     }

//     /**
//      * API pour récupérer les communes en AJAX
//      */
//     public function api(Request $request): JsonResponse
//     {
//         $communes = Commune::with(['departement.region', 'receveurs', 'ordonnateurs'])
//             ->when($request->search, function($query, $search) {
//                 $query->where('nom', 'LIKE', "%{$search}%")
//                       ->orWhere('code', 'LIKE', "%{$search}%");
//             })
//             ->when($request->departement_id, function($query, $departement) {
//                 $query->where('departement_id', $departement);
//             })
//             ->paginate(10);
            
//         return response()->json($communes);
//     }

//     /**
//      * Affichage du formulaire de création avec validation temps réel
//      */
//     public function create()
//     {
//         $departements = Departement::with('region')->orderBy('nom')->get();
//         $receveurs = Receveur::whereNull('commune_id')->orderBy('nom')->get();
//         $ordonnateurs = Ordonnateur::whereNull('commune_id')->orderBy('nom')->get();
        
//         // Données pour l'auto-complétion
//         $suggestions = [
//             'codes_existants' => Commune::pluck('code')->toArray(),
//             'noms_similaires' => Commune::pluck('nom')->toArray()
//         ];
        
//         return view('communes.create', compact(
//             'departements', 
//             'receveurs', 
//             'ordonnateurs',
//             'suggestions'
//         ));
//     }

//     /**
//      * Validation en temps réel pour les champs
//      */
//     public function validateField(Request $request): JsonResponse
//     {
//         $field = $request->field;
//         $value = $request->value;
//         $communeId = $request->commune_id;
        
//         $rules = [
//             'nom' => 'required|string|max:255',
//             'code' => 'required|string|max:10|unique:communes,code' . ($communeId ? ",$communeId" : ''),
//             'telephone' => 'nullable|regex:/^[0-9+\-\s()]+$/|max:20',
//             'email' => 'nullable|email|max:255',
//             'population' => 'nullable|integer|min:0|max:10000000',
//             'superficie' => 'nullable|numeric|min:0|max:100000'
//         ];
        
//         $validator = validator([$field => $value], [$field => $rules[$field] ?? 'required']);
        
//         return response()->json([
//             'valid' => !$validator->fails(),
//             'errors' => $validator->errors()->get($field)
//         ]);
//     }

//     /**
//      * Enregistrement avec gestion d'erreurs améliorée
//      */
//     // public function store(Request $request)
//     // {
//     //     $validated = $request->validate([
//     //         'nom' => 'required|string|max:255',
//     //         'code' => 'required|string|max:10|unique:communes,code',
//     //         'departement_id' => 'required|exists:departements,id',
//     //         'telephone' => 'nullable|regex:/^[0-9+\-\s()]+$/|max:20',
//     //         'email' => 'nullable|email|max:255',
//     //         'population' => 'nullable|integer|min:0|max:10000000',
//     //         'superficie' => 'nullable|numeric|min:0|max:100000',
//     //         'adresse' => 'nullable|string|max:500',
//     //         'receveur_ids' => 'nullable|array',
//     //         'receveur_ids.*' => 'exists:receveurs,id',
//     //         'ordonnateur_ids' => 'nullable|array',
//     //         'ordonnateur_ids.*' => 'exists:ordonnateurs,id',
//     //         'coordonnees_gps' => 'nullable|string|max:100'
//     //     ], [
//     //         'nom.required' => 'Le nom de la commune est obligatoire.',
//     //         'code.required' => 'Le code de la commune est obligatoire.',
//     //         'code.unique' => 'Ce code est déjà utilisé par une autre commune.',
//     //         'departement_id.required' => 'Vous devez sélectionner un département.',
//     //         'departement_id.exists' => 'Le département sélectionné n\'existe pas.',
//     //         'telephone.regex' => 'Le format du numéro de téléphone est invalide.',
//     //         'email.email' => 'L\'adresse email n\'est pas valide.',
//     //         'population.integer' => 'La population doit être un nombre entier.',
//     //         'superficie.numeric' => 'La superficie doit être un nombre.',
//     //     ]);

//     //     DB::beginTransaction();
//     //     try {
//     //         // Créer la commune
//     //         $commune = Commune::create($validated);
            
//     //         // Assigner les responsables
//     //         $this->assignResponsables($commune, $request);
            
//     //         // Créer l'audit trail
//     //         $this->createAuditLog('commune_created', $commune->id, null, $validated);
            
//     //         // Notification
//     //         $this->notificationService->communeCreated($commune);
            
//     //         DB::commit();
            
//     //         if ($request->ajax()) {
//     //             return response()->json([
//     //                 'success' => true,
//     //                 'message' => 'Commune créée avec succès.',
//     //                 'redirect' => route('communes.show', $commune)
//     //             ]);
//     //         }
            
//     //         return redirect()->route('communes.show', $commune)
//     //                        ->with('success', 'Commune créée avec succès.');
                           
//     //     } catch (\Exception $e) {
//     //         DB::rollback();
            
//     //         \Log::error('Erreur création commune: ' . $e->getMessage(), [
//     //             'data' => $validated,
//     //             'user_id' => auth()->id()
//     //         ]);
            
//     //         if ($request->ajax()) {
//     //             return response()->json([
//     //                 'success' => false,
//     //                 'message' => 'Erreur lors de la création: ' . $e->getMessage()
//     //             ], 422);
//     //         }
            
//     //         return back()->withInput()
//     //                     ->with('error', 'Erreur lors de la création de la commune: ' . $e->getMessage());
//     //     }
//     // }

//     /**
//      * Affichage détaillé avec tableaux de bord interactifs
//      */
//     public function show(Commune $commune, Request $request)
//     {
//         $annee = $request->get('annee', date('Y'));
//         $periode = $request->get('periode', 'annuelle');
        
//         // Chargement optimisé des relations
//         $commune->load([
//             'departement.region', 'receveurs', 'ordonnateurs',
//             'depotsComptes', 'previsions', 'realisations', 'tauxRealisations',
//             'dettesCnps', 'dettesFiscale', 'dettesFeicom', 'dettesSalariale',
//             'defaillances', 'retards'
//         ]);
        
//         // Données avec cache
//         $donneesFinancieres = Cache::remember(
//             "commune_finances_{$commune->id}_{$annee}_{$periode}",
//             now()->addHours(2),
//             fn() => $this->getDonneesFinancieres($commune, $annee, $periode)
//         );
        
//         // Performance et comparaisons
//         $performance = $this->performanceService->analyzeCommune($commune, $annee);
//         $comparaisons = $this->performanceService->compareWithPeers($commune, $annee);
        
//         // Indicateurs clés
//         $indicateurs = $this->getIndicateursClés($commune, $annee);
        
//         // Évolution historique
//         $evolution = $this->getEvolutionHistorique($commune);
        
//         // Projets en cours
//         $projetsEnCours = $this->getProjetsEnCours($commune);
        
//         // Alertes et notifications
//         $alertes = $this->getAlertes($commune);
        
//         if ($request->ajax()) {
//             return response()->json([
//                 'donneesFinancieres' => $donneesFinancieres,
//                 'performance' => $performance,
//                 'indicateurs' => $indicateurs
//             ]);
//         }
        
//         return view('communes.show', compact(
//             'commune', 'donneesFinancieres', 'performance', 'comparaisons',
//             'indicateurs', 'evolution', 'projetsEnCours', 'alertes', 'annee', 'periode'
//         ));
//     }


// public function store(Request $request)
// {
//     $validated = $request->validate([
//         'nom' => 'required|string|max:255',
//         'code' => 'required|string|max:10|unique:communes,code',
//         'departement_id' => 'required|exists:departements,id',
//         'telephone' => 'nullable|regex:/^[0-9+\-\s()]+$/|max:20',
//         'email' => 'nullable|email|max:255',
//         'population' => 'nullable|integer|min:0|max:10000000',
//         'superficie' => 'nullable|numeric|min:0|max:100000',
//         'adresse' => 'nullable|string|max:500',
//         'receveur_ids' => 'nullable|array',
//         'receveur_ids.*' => 'exists:receveurs,id',
//         'ordonnateur_ids' => 'nullable|array', 
//         'ordonnateur_ids.*' => 'exists:ordonnateurs,id',
//         'coordonnees_gps' => 'nullable|string|max:100'
//     ]);

//     DB::beginTransaction();
//     try {
//         // Créer la commune
//         $commune = Commune::create($validated);
        
//         // Assigner les responsables via les relations
//         if ($request->filled('receveur_ids')) {
//             $commune->receveurs()->attach($request->receveur_ids);
//         }
        
//         if ($request->filled('ordonnateur_ids')) {
//             $commune->ordonnateurs()->attach($request->ordonnateur_ids);
//         }
        
//         DB::commit();
        
//         return redirect()->route('communes.show', $commune)
//                        ->with('success', 'Commune créée avec succès.');
                       
//     } catch (\Exception $e) {
//         DB::rollback();
        
//         \Log::error('Erreur création commune: ' . $e->getMessage());
        
//         return back()->withInput()
//                     ->with('error', 'Erreur lors de la création de la commune: ' . $e->getMessage());
//     }
// }

//     /**
//      * Tableau de bord performance avec métriques avancées
//      */
//     public function dashboard(Commune $commune, Request $request): JsonResponse
//     {
//         $annee = $request->get('annee', date('Y'));
        
//         $dashboard = [
//             'kpi' => $this->getKPI($commune, $annee),
//             'tendances' => $this->getTendances($commune),
//             'comparaisons' => $this->getComparaisonsRegionales($commune, $annee),
//             'objectifs' => $this->getObjectifs($commune, $annee),
//             'risques' => $this->getRisques($commune),
//         ];
        
//         return response()->json($dashboard);
//     }

//     /**
//  * Affichage du formulaire de modification avec validation temps réel
//  */
// // public function edit(Commune $commune)
// // {
// //     $departements = Departement::orderBy('nom')->get();
// //     return view('communes.edit', compact('commune', 'departements'));
// // }


// public function edit(Commune $commune)
// {
//     // Récupérer tous les receveurs et ordonnateurs (sans filtre actif/inactif)
//     $receveurs = Receveur::all();
//     $ordonnateurs = Ordonnateur::all();
//     $departements = Departement::with('region')->get();

//     return view('communes.edit', compact(
//         'commune', 
//         'receveurs', 
//         'ordonnateurs', 
//         'departements'
//     ));
// }

// /**
//  * Vérification des restrictions d'édition
//  */
// private function checkEditRestrictions($commune): array
// {
//     $restrictions = [];
    
//     // Vérifier seulement l'existence de prévisions pour l'année courante
//     $hasFinancialData = $commune->previsions()
//         ->where('annee_exercice', date('Y'))
//         ->exists();
        
//     if ($hasFinancialData) {
//         $restrictions[] = [
//             'type' => 'financial',
//             'message' => 'Cette commune a des prévisions budgétaires pour l\'année en cours.',
//             'level' => 'info'
//         ];
//     }
    
//     return $restrictions;
// }

//     /**
//      * Mise à jour avec validation partielle
//      */
//     public function update(Request $request, Commune $commune)
//     {
//         $validated = $request->validate([
//             'nom' => 'required|string|max:255',
//             'code' => 'required|string|max:10|unique:communes,code,' . $commune->id,
//             'departement_id' => 'required|exists:departements,id',
//             'telephone' => 'nullable|regex:/^[0-9+\-\s()]+$/|max:20',
//             'email' => 'nullable|email|max:255',
//             'population' => 'nullable|integer|min:0|max:10000000',
//             'superficie' => 'nullable|numeric|min:0|max:100000',
//             'adresse' => 'nullable|string|max:500',
//             'receveur_ids' => 'nullable|array',
//             'receveur_ids.*' => 'exists:receveurs,id',
//             'ordonnateur_ids' => 'nullable|array',
//             'ordonnateur_ids.*' => 'exists:ordonnateurs,id',
//             'coordonnees_gps' => 'nullable|string|max:100'
//         ]);

//         DB::beginTransaction();
//         try {
//             $originalData = $commune->toArray();
            
//             // Mettre à jour la commune
//             $commune->update($validated);
            
//             // Réassigner les responsables
//             $this->reassignResponsables($commune, $request);
            
//             // Audit trail
//             $this->createAuditLog('commune_updated', $commune->id, $originalData, $validated);
            
//             // Notification si changements importants
//             $this->notificationService->communeUpdated($commune, $originalData, $validated);
            
//             DB::commit();
            
//             // Invalider les caches
//             $this->invalidateCache($commune);
            
//             if ($request->ajax()) {
//                 return response()->json([
//                     'success' => true,
//                     'message' => 'Commune mise à jour avec succès.',
//                     'data' => $commune->fresh(['departement.region', 'receveurs', 'ordonnateurs'])
//                 ]);
//             }
            
//             return redirect()->route('communes.show', $commune)
//                            ->with('success', 'Commune mise à jour avec succès.');
                           
//         } catch (\Exception $e) {
//             DB::rollback();
            
//             \Log::error('Erreur mise à jour commune: ' . $e->getMessage(), [
//                 'commune_id' => $commune->id,
//                 'data' => $validated,
//                 'user_id' => auth()->id()
//             ]);
            
//             if ($request->ajax()) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
//                 ], 422);
//             }
            
//             return back()->withInput()
//                         ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Export de données
//      */
//     public function export(Request $request, string $format = 'pdf')
//     {
//         $communeIds = $request->get('commune_ids', []);
//         $annee = $request->get('annee', date('Y'));
//         $type = $request->get('type', 'complet');
        
//         try {
//             $fileName = $this->exportService->exportCommunes($communeIds, $format, $annee, $type);
            
//             return response()->download(storage_path("app/exports/{$fileName}"))
//                            ->deleteFileAfterSend(true);
                           
//         } catch (\Exception $e) {
//             return back()->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Suppression avec vérifications de sécurité
//      */
// public function destroy(Commune $commune)
// {
//     // Vérifications détaillées des données associées
//     $hasPrevisions = $commune->previsions()->count() > 0;
//     $hasRealisations = $commune->realisations()->count() > 0;
//     $hasReceveurs = $commune->receveurs()->count() > 0;
//     $hasOrdonnateurs = $commune->ordonnateurs()->count() > 0;

//     if ($hasPrevisions || $hasRealisations || $hasReceveurs || $hasOrdonnateurs) {
//         $errorMessage = 'Impossible de supprimer la commune "' . $commune->nom . '" car elle contient : ';
//         $reasons = [];

//         if ($hasPrevisions) {
//             $reasons[] = 'des prévisions budgétaires';
//         }
//         if ($hasRealisations) {
//             $reasons[] = 'des réalisations';
//         }
//         if ($hasReceveurs) {
//             $reasons[] = 'des receveurs assignés';
//         }
//         if ($hasOrdonnateurs) {
//             $reasons[] = 'des ordonnateurs assignés';
//         }

//         $errorMessage .= implode(', ', $reasons) . '.';

//         return redirect()->route('communes.index')
//             ->with('error', $errorMessage);
//     }

//     // Supprimer la commune
//     $commune->delete();

//     return redirect()->route('communes.index')
//         ->with('success', 'Commune "' . $commune->nom . '" supprimée avec succès.');
// }

//     /**
//      * Méthodes privées pour la logique métier
//      */
//     private function applyFilters($query, Request $request)
//     {
//         // Filtre par département
//         if ($request->filled('departement_id')) {
//             $query->where('departement_id', $request->departement_id);
//         }
        
//         // Filtre par région
//         if ($request->filled('region_id')) {
//             $query->whereHas('departement', function($q) use ($request) {
//                 $q->where('region_id', $request->region_id);
//             });
//         }
        
//         // Filtre par taille de population
//         if ($request->filled('population_min')) {
//             $query->where('population', '>=', $request->population_min);
//         }
//         if ($request->filled('population_max')) {
//             $query->where('population', '<=', $request->population_max);
//         }
        
//         // Filtre par performance
//         if ($request->filled('performance')) {
//             $performance = $request->performance;
//             $query->whereHas('tauxRealisations', function($q) use ($performance) {
//                 switch ($performance) {
//                     case 'excellente':
//                         $q->where('pourcentage', '>=', 90);
//                         break;
//                     case 'bonne':
//                         $q->whereBetween('pourcentage', [75, 89]);
//                         break;
//                     case 'moyenne':
//                         $q->whereBetween('pourcentage', [50, 74]);
//                         break;
//                     case 'faible':
//                         $q->where('pourcentage', '<', 50);
//                         break;
//                 }
//             });
//         }
        
//         // Filtre par statut des responsables
//         if ($request->filled('avec_receveur')) {
//             if ($request->avec_receveur === '1') {
//                 $query->has('receveurs');
//             } else {
//                 $query->doesntHave('receveurs');
//             }
//         }
        
//         if ($request->filled('avec_ordonnateur')) {
//             if ($request->avec_ordonnateur === '1') {
//                 $query->has('ordonnateurs');
//             } else {
//                 $query->doesntHave('ordonnateurs');
//             }
//         }
//     }

//     private function getCommunesStats(): array
//     {
//         return Cache::remember('communes_stats', now()->addHours(1), function() {
//             return [
//                 'total' => Commune::count(),
//                 'avec_receveur' => Commune::has('receveurs')->count(),
//                 'avec_ordonnateur' => Commune::has('ordonnateurs')->count(),
//                 'avec_prevision_courante' => Commune::whereHas('previsions', function($q) {
//                     $q->where('annee_exercice', date('Y'));
//                 })->count(),
//                 'performance_moyenne' => DB::table('taux_realisations')
//                     ->where('annee_exercice', date('Y'))
//                     ->avg('pourcentage') ?? 0,
//                 'budget_total' => DB::table('previsions')
//                     ->where('annee_exercice', date('Y'))
//                     ->sum('montant') ?? 0
//             ];
//         });
//     }

//     private function getChartsData(): array
//     {
//         return Cache::remember('communes_charts', now()->addHours(2), function() {
//             return [
//                 'repartition_par_region' => DB::table('communes')
//                     ->join('departements', 'communes.departement_id', '=', 'departements.id')
//                     ->join('regions', 'departements.region_id', '=', 'regions.id')
//                     ->select('regions.nom', DB::raw('count(*) as total'))
//                     ->groupBy('regions.id', 'regions.nom')
//                     ->get(),
                
//                 'evolution_creation' => DB::table('communes')
//                     ->select(
//                         DB::raw('YEAR(created_at) as annee'),
//                         DB::raw('count(*) as nombre')
//                     )
//                     ->groupBy(DB::raw('YEAR(created_at)'))
//                     ->orderBy('annee')
//                     ->get(),
                
//                 'performance_regions' => DB::table('taux_realisations')
//                     ->join('communes', 'taux_realisations.commune_id', '=', 'communes.id')
//                     ->join('departements', 'communes.departement_id', '=', 'departements.id')
//                     ->join('regions', 'departements.region_id', '=', 'regions.id')
//                     ->where('taux_realisations.annee_exercice', date('Y'))
//                     ->select('regions.nom', DB::raw('avg(taux_realisations.pourcentage) as moyenne'))
//                     ->groupBy('regions.id', 'regions.nom')
//                     ->get()
//             ];
//         });
//     }

//     private function getDonneesFinancieres($commune, $annee, $periode = 'annuelle')
//     {
//         $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
//         $realisations = $commune->realisations->where('annee_exercice', $annee);
//         $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
        
//         // Calculs avancés selon la période
//         $donneesBase = [
//             'prevision' => $prevision?->montant ?? 0,
//             'realisation_total' => $realisations->sum('montant'),
//             'taux_realisation' => $tauxRealisation?->pourcentage ?? 0,
//             'evaluation' => $tauxRealisation?->evaluation ?? 'Non évalué',
//             'ecart' => $tauxRealisation?->ecart ?? 0,
//         ];
        
//         if ($periode !== 'annuelle') {
//             // Ajouter les données périodiques (trimestrielles, mensuelles)
//             $donneesBase['donnees_periodiques'] = $this->getDonneesPeriodiques($commune, $annee, $periode);
//         }
        
//         return $donneesBase;
//     }

//     private function assignResponsables($commune, $request)
//     {
//         if ($request->filled('receveur_ids')) {
//             Receveur::whereIn('id', $request->receveur_ids)
//                 ->update(['commune_id' => $commune->id]);
//         }
        
//         if ($request->filled('ordonnateur_ids')) {
//             Ordonnateur::whereIn('id', $request->ordonnateur_ids)
//                 ->update(['commune_id' => $commune->id]);
//         }
//     }

//     private function reassignResponsables($commune, $request)
//     {
//         // Libérer les anciens
//         Receveur::where('commune_id', $commune->id)->update(['commune_id' => null]);
//         Ordonnateur::where('commune_id', $commune->id)->update(['commune_id' => null]);
        
//         // Assigner les nouveaux
//         $this->assignResponsables($commune, $request);
//     }

//     // private function canDeleteCommune($commune): array
//     // {
//     //     // Vérifications étendues
//     //     $hasFinancialData = $commune->previsions()->exists() || 
//     //                        $commune->realisations()->exists();
                           
//     //     $hasActiveProjects = $commune->projets()->where('statut', 'en_cours')->exists();
        
//     //     $hasRecentTransactions = $commune->transactions()
//     //         ->where('created_at', '>', now()->subDays(30))
//     //         ->exists();
            
//     //     if ($hasFinancialData) {
//     //         return [
//     //             'allowed' => false,
//     //             'reason' => 'Cette commune contient des données financières et ne peut être supprimée.'
//     //         ];
//     //     }
        
//     //     if ($hasActiveProjects) {
//     //         return [
//     //             'allowed' => false,
//     //             'reason' => 'Cette commune a des projets actifs en cours.'
//     //         ];
//     //     }
        
//     //     if ($hasRecentTransactions) {
//     //         return [
//     //             'allowed' => false,
//     //             'reason' => 'Cette commune a des transactions récentes.'
//     //         ];
//     //     }
        
//     //     return ['allowed' => true, 'reason' => null];
//     // }

//     private function createAuditLog($action, $communeId, $oldData, $newData)
//     {
//         DB::table('audit_logs')->insert([
//             'user_id' => auth()->id(),
//             'action' => $action,
//             'model' => 'Commune',
//             'model_id' => $communeId,
//             'old_data' => json_encode($oldData),
//             'new_data' => json_encode($newData),
//             'ip_address' => request()->ip(),
//             'user_agent' => request()->userAgent(),
//             'created_at' => now(),
//             'updated_at' => now()
//         ]);
//     }

//     private function invalidateCache($commune)
//     {
//         Cache::forget("commune_finances_{$commune->id}_" . date('Y') . "_annuelle");
//         Cache::forget('communes_stats');
//         Cache::forget('communes_charts');
//     }

// /**
//      * Méthodes privées pour la logique métier - CORRECTIONS
//      */
//     private function canDeleteCommune($commune): array
//     {
//         // Vérifications étendues
//         $hasFinancialData = $commune->previsions()->exists() || 
//                            $commune->realisations()->exists();
                           
//         // CORRECTION: Vérifier si la méthode projets() existe
//         $hasActiveProjects = method_exists($commune, 'projets') && 
//                             $commune->projets()->where('statut', 'en_cours')->exists();
        
//         // CORRECTION: Vérifier si la méthode transactions() existe  
//         $hasRecentTransactions = method_exists($commune, 'transactions') &&
//                                 $commune->transactions()
//                                     ->where('created_at', '>', now()->subDays(30))
//                                     ->exists();
            
//         if ($hasFinancialData) {
//             return [
//                 'allowed' => false,
//                 'reason' => 'Cette commune contient des données financières et ne peut être supprimée.'
//             ];
//         }
        
//         if ($hasActiveProjects) {
//             return [
//                 'allowed' => false,
//                 'reason' => 'Cette commune a des projets actifs en cours.'
//             ];
//         }
        
//         if ($hasRecentTransactions) {
//             return [
//                 'allowed' => false,
//                 'reason' => 'Cette commune a des transactions récentes.'
//             ];
//         }
        
//         return ['allowed' => true, 'reason' => null];
//     }

//     /**
//      * NOUVELLES MÉTHODES MANQUANTES
//      */
//     private function getIndicateursClés($commune, $annee)
//     {
//         return Cache::remember("indicateurs_cles_{$commune->id}_{$annee}", now()->addHours(1), function() use ($commune, $annee) {
//             $prevision = $commune->previsions()->where('annee_exercice', $annee)->first();
//             $realisations = $commune->realisations()->where('annee_exercice', $annee)->sum('montant');
//             $tauxRealisation = $commune->tauxRealisations()->where('annee_exercice', $annee)->first();
            
//             $dettes = $commune->getTotalDettes($annee);
//             $totalDettes = array_sum($dettes);
            
//             return [
//                 'budget_previsionnel' => $prevision?->montant ?? 0,
//                 'budget_realise' => $realisations,
//                 'taux_execution' => $tauxRealisation?->pourcentage ?? 0,
//                 'total_dettes' => $totalDettes,
//                 'ratio_dette_budget' => $prevision && $prevision->montant > 0 
//                     ? ($totalDettes / $prevision->montant) * 100 
//                     : 0,
//                 'nombre_projets_actifs' => method_exists($commune, 'projets') 
//                     ? $commune->projets()->where('statut', 'en_cours')->count() 
//                     : 0,
//                 'nombre_defaillances' => $commune->defaillances()->where('annee_exercice', $annee)->count(),
//                 'nombre_retards' => $commune->retards()->where('annee_exercice', $annee)->count()
//             ];
//         });
//     }

//     private function getEvolutionHistorique($commune, $nbAnnees = 5)
//     {
//         $anneeActuelle = date('Y');
//         $evolution = [];
        
//         for ($i = 0; $i < $nbAnnees; $i++) {
//             $annee = $anneeActuelle - $i;
            
//             $prevision = $commune->previsions()->where('annee_exercice', $annee)->first();
//             $realisation = $commune->realisations()->where('annee_exercice', $annee)->sum('montant');
//             $tauxRealisation = $commune->tauxRealisations()->where('annee_exercice', $annee)->first();
            
//             $evolution[] = [
//                 'annee' => $annee,
//                 'prevision' => $prevision?->montant ?? 0,
//                 'realisation' => $realisation,
//                 'taux_realisation' => $tauxRealisation?->pourcentage ?? 0,
//                 'evaluation' => $tauxRealisation?->evaluation ?? 'Non évalué'
//             ];
//         }
        
//         return collect($evolution)->reverse();
//     }

//     private function getProjetsEnCours($commune)
//     {
//         if (!method_exists($commune, 'projets')) {
//             return collect([]);
//         }
        
//         return $commune->projets()
//             ->where('statut', 'en_cours')
//             ->with(['responsable', 'financements'])
//             ->orderBy('date_debut', 'desc')
//             ->limit(10)
//             ->get();
//     }

//     private function getAlertes($commune)
//     {
//         $alertes = [];
//         $anneeActuelle = date('Y');
        
//         // Alertes pour performance faible
//         $tauxRealisation = $commune->getTauxRealisationAnnuel($anneeActuelle);
//         if ($tauxRealisation < 50) {
//             $alertes[] = [
//                 'type' => 'performance',
//                 'niveau' => 'danger',
//                 'message' => "Taux de réalisation très faible: {$tauxRealisation}%",
//                 'action_recommandee' => 'Révision du budget et des procédures'
//             ];
//         }
        
//         // Alertes pour dettes élevées
//         $dettes = $commune->getTotalDettes($anneeActuelle);
//         $totalDettes = array_sum($dettes);
//         $prevision = $commune->previsions()->where('annee_exercice', $anneeActuelle)->first();
        
//         if ($prevision && $totalDettes > ($prevision->montant * 0.3)) {
//             $alertes[] = [
//                 'type' => 'dette',
//                 'niveau' => 'warning',
//                 'message' => 'Niveau de dette élevé par rapport au budget',
//                 'action_recommandee' => 'Plan de réduction des dettes'
//             ];
//         }
        
//         // Alertes pour personnel manquant
//         if ($commune->receveurs()->count() === 0) {
//             $alertes[] = [
//                 'type' => 'personnel',
//                 'niveau' => 'warning',
//                 'message' => 'Aucun receveur assigné',
//                 'action_recommandee' => 'Assigner un receveur'
//             ];
//         }
        
//         if ($commune->ordonnateurs()->count() === 0) {
//             $alertes[] = [
//                 'type' => 'personnel',
//                 'niveau' => 'warning',
//                 'message' => 'Aucun ordonnateur assigné',
//                 'action_recommandee' => 'Assigner un ordonnateur'
//             ];
//         }
        
//         // Alertes pour défaillances récentes
//         $defaillancesRecentes = $commune->defaillances()
//             ->where('date_constat', '>', now()->subDays(30))
//             ->where('est_resolue', false)
//             ->count();
            
//         if ($defaillancesRecentes > 0) {
//             $alertes[] = [
//                 'type' => 'defaillance',
//                 'niveau' => 'danger',
//                 'message' => "{$defaillancesRecentes} défaillance(s) non résolue(s)",
//                 'action_recommandee' => 'Traitement urgent des défaillances'
//             ];
//         }
        
//         return collect($alertes);
//     }

//     private function getDonneesPeriodiques($commune, $annee, $periode)
//     {
//         $query = $commune->realisations()->where('annee_exercice', $annee);
        
//         switch ($periode) {
//             case 'trimestrielle':
//                 return $query->selectRaw('
//                     QUARTER(date_realisation) as periode,
//                     SUM(montant) as montant_total,
//                     COUNT(*) as nombre_operations
//                 ')
//                 ->groupBy(DB::raw('QUARTER(date_realisation)'))
//                 ->orderBy('periode')
//                 ->get();
                
//             case 'mensuelle':
//                 return $query->selectRaw('
//                     MONTH(date_realisation) as mois,
//                     YEAR(date_realisation) as annee,
//                     SUM(montant) as montant_total,
//                     COUNT(*) as nombre_operations
//                 ')
//                 ->groupBy(DB::raw('YEAR(date_realisation), MONTH(date_realisation)'))
//                 ->orderBy('annee')->orderBy('mois')
//                 ->get();
                
//             default:
//                 return collect([]);
//         }
//     }

//     private function getKPI($commune, $annee)
//     {
//         $prevision = $commune->previsions()->where('annee_exercice', $annee)->first();
//         $realisations = $commune->realisations()->where('annee_exercice', $annee);
//         $tauxRealisation = $commune->tauxRealisations()->where('annee_exercice', $annee)->first();
        
//         return [
//             'efficacite_budgetaire' => $tauxRealisation?->pourcentage ?? 0,
//             'regularite_paiements' => $this->calculerRegularitePaiements($commune, $annee),
//             'respect_delais' => $this->calculerRespectDelais($commune, $annee),
//             'sante_financiere' => $this->calculerSanteFinanciere($commune, $annee),
//             'governance_score' => $this->calculerScoreGouvernance($commune, $annee)
//         ];
//     }

//     private function getTendances($commune, $nbAnnees = 3)
//     {
//         $anneeActuelle = date('Y');
//         $tendances = [];
        
//         for ($i = 0; $i < $nbAnnees; $i++) {
//             $annee = $anneeActuelle - $i;
//             $kpi = $this->getKPI($commune, $annee);
//             $tendances[$annee] = $kpi;
//         }
        
//         return $tendances;
//     }

//     private function getComparaisonsRegionales($commune, $annee)
//     {
//         $communesDepartement = Commune::where('departement_id', $commune->departement_id)
//             ->where('id', '!=', $commune->id)
//             ->withPerformance($annee)
//             ->get();
            
//         $moyenneDepartement = $communesDepartement->avg(function($c) use ($annee) {
//             return $c->getTauxRealisationAnnuel($annee);
//         });
        
//         return [
//             'taux_realisation_commune' => $commune->getTauxRealisationAnnuel($annee),
//             'moyenne_departement' => $moyenneDepartement,
//             'rang_departement' => $this->calculerRangDepartement($commune, $annee),
//             'nombre_communes_departement' => $communesDepartement->count() + 1
//         ];
//     }

//     private function getObjectifs($commune, $annee)
//     {
//         // À implémenter selon vos besoins métier
//         return [
//             'taux_realisation_cible' => 85,
//             'reduction_dettes_cible' => 15,
//             'delai_paiement_cible' => 30
//         ];
//     }

//     private function getRisques($commune)
//     {
//         $risques = [];
        
//         // Analyser les risques financiers
//         $dettes = $commune->getTotalDettes();
//         $totalDettes = array_sum($dettes);
        
//         if ($totalDettes > 1000000) { // 1M FCFA
//             $risques[] = [
//                 'type' => 'Financier',
//                 'description' => 'Niveau de dette élevé',
//                 'probabilite' => 'Moyenne',
//                 'impact' => 'Élevé'
//             ];
//         }
        
//         // Analyser les risques opérationnels
//         if ($commune->receveurs()->count() === 0) {
//             $risques[] = [
//                 'type' => 'Opérationnel',
//                 'description' => 'Absence de receveur',
//                 'probabilite' => 'Élevée',
//                 'impact' => 'Élevé'
//             ];
//         }
        
//         return $risques;
//     }

//     private function liberateResources($commune)
//     {
//         // Libérer les receveurs et ordonnateurs
//         $commune->receveurs()->update(['commune_id' => null]);
//         $commune->ordonnateurs()->update(['commune_id' => null]);
//     }

//     private function archiveCommune($commune)
//     {
//         // Créer une archive des données importantes
//         DB::table('communes_archived')->insert([
//             'commune_data' => json_encode($commune->toArray()),
//             'archived_at' => now(),
//             'archived_by' => auth()->id()
//         ]);
//     }

//     // Méthodes helper pour les calculs KPI
//     private function calculerRegularitePaiements($commune, $annee)
//     {
//         // Logique de calcul de la régularité des paiements
//         return 75; // Placeholder
//     }

//     private function calculerRespectDelais($commune, $annee)
//     {
//         // Logique de calcul du respect des délais
//         return 80; // Placeholder
//     }

//     private function calculerSanteFinanciere($commune, $annee)
//     {
//         // Logique de calcul de la santé financière
//         return 70; // Placeholder
//     }

//     private function calculerScoreGouvernance($commune, $annee)
//     {
//         // Logique de calcul du score de gouvernance
//         return 65; // Placeholder
//     }

//     private function calculerRangDepartement($commune, $annee)
//     {
//         // Logique de calcul du rang dans le département
//         return 3; // Placeholder
//     }
// }

