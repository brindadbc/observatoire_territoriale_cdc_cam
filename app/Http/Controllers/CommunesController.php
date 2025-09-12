<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commune;
use App\Models\Departement;
use App\Models\Ordonnateur;
use App\Models\Prevision;
use App\Models\Receveur;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use App\Services\CommunePerformanceService;
use App\Services\NotificationService;
use App\Services\ExportService;

class CommunesController extends Controller
{
    protected $performanceService;
    protected $notificationService;
    protected $exportService;

    public function __construct(
        CommunePerformanceService $performanceService,
        NotificationService $notificationService,
        ExportService $exportService
    ) {
        $this->performanceService = $performanceService;
        $this->notificationService = $notificationService;
        $this->exportService = $exportService;
    }

    /**
     * Liste des communes avec pagination, recherche et filtres avancés
     */
    public function index(Request $request)
    {
        $query = Commune::with([
            'departement.region', 
            'receveurs', 
            'ordonnateurs',
            'previsions' => function($q) { $q->latest(); },
            'realisations' => function($q) { $q->latest(); }
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
        $this->applyFilters($query, $request);
        
        // Tri dynamique
        $sortBy = $request->get('sort_by', 'nom');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if (in_array($sortBy, ['nom', 'code', 'population', 'superficie', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        $communes = $query->paginate($request->get('per_page', 15));
        $departements = Departement::with('region')->orderBy('nom')->get();
        
        // Statistiques rapides
        $stats = $this->getCommunesStats();
        
        // Données pour les graphiques
        $chartData = $this->getChartsData();
        
        if ($request->ajax()) {
            return response()->json([
                'html' => view('communes.partials.table', compact('communes'))->render(),
                'pagination' => $communes->appends(request()->query())->links()->render(),
                'stats' => $stats
            ]);
        }
        
        return view('communes.index', compact('communes', 'departements', 'stats', 'chartData'));
    }

    /**
     * API pour récupérer les communes en AJAX
     */
    public function api(Request $request): JsonResponse
    {
        $communes = Commune::with(['departement.region', 'receveurs', 'ordonnateurs'])
            ->when($request->search, function($query, $search) {
                $query->where('nom', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%");
            })
            ->when($request->departement_id, function($query, $departement) {
                $query->where('departement_id', $departement);
            })
            ->paginate(10);
            
        return response()->json($communes);
    }

    /**
     * Affichage du formulaire de création avec validation temps réel
     */
    public function create()
    {
        $departements = Departement::with('region')->orderBy('nom')->get();
        $receveurs = Receveur::whereNull('commune_id')->orderBy('nom')->get();
        $ordonnateurs = Ordonnateur::whereNull('commune_id')->orderBy('nom')->get();
        
        // Données pour l'auto-complétion
        $suggestions = [
            'codes_existants' => Commune::pluck('code')->toArray(),
            'noms_similaires' => Commune::pluck('nom')->toArray()
        ];
        
        return view('communes.create', compact(
            'departements', 
            'receveurs', 
            'ordonnateurs',
            'suggestions'
        ));
    }

    /**
     * Validation en temps réel pour les champs
     */
    public function validateField(Request $request): JsonResponse
    {
        $field = $request->field;
        $value = $request->value;
        $communeId = $request->commune_id;
        
        $rules = [
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:communes,code' . ($communeId ? ",$communeId" : ''),
            'telephone' => 'nullable|regex:/^[0-9+\-\s()]+$/|max:20',
            'email' => 'nullable|email|max:255',
            'population' => 'nullable|integer|min:0|max:10000000',
            'superficie' => 'nullable|numeric|min:0|max:100000'
        ];
        
        $validator = validator([$field => $value], [$field => $rules[$field] ?? 'required']);
        
        return response()->json([
            'valid' => !$validator->fails(),
            'errors' => $validator->errors()->get($field)
        ]);
    }

    /**
     * Enregistrement avec gestion d'erreurs améliorée
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
            'receveur_ids' => 'nullable|array',
            'receveur_ids.*' => 'exists:receveurs,id',
            'ordonnateur_ids' => 'nullable|array',
            'ordonnateur_ids.*' => 'exists:ordonnateurs,id',
            'coordonnees_gps' => 'nullable|string|max:100'
        ], [
            'nom.required' => 'Le nom de la commune est obligatoire.',
            'code.required' => 'Le code de la commune est obligatoire.',
            'code.unique' => 'Ce code est déjà utilisé par une autre commune.',
            'departement_id.required' => 'Vous devez sélectionner un département.',
            'departement_id.exists' => 'Le département sélectionné n\'existe pas.',
            'telephone.regex' => 'Le format du numéro de téléphone est invalide.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'population.integer' => 'La population doit être un nombre entier.',
            'superficie.numeric' => 'La superficie doit être un nombre.',
        ]);

        DB::beginTransaction();
        try {
            // Créer la commune
            $commune = Commune::create($validated);
            
            // Assigner les responsables
            $this->assignResponsables($commune, $request);
            
            // Créer l'audit trail
            $this->createAuditLog('commune_created', $commune->id, null, $validated);
            
            // Notification
            $this->notificationService->communeCreated($commune);
            
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Commune créée avec succès.',
                    'redirect' => route('communes.show', $commune)
                ]);
            }
            
            return redirect()->route('communes.show', $commune)
                           ->with('success', 'Commune créée avec succès.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Erreur création commune: ' . $e->getMessage(), [
                'data' => $validated,
                'user_id' => auth()->id()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->withInput()
                        ->with('error', 'Erreur lors de la création de la commune: ' . $e->getMessage());
        }
    }

    /**
     * Affichage détaillé avec tableaux de bord interactifs
     */
    public function show(Commune $commune, Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $periode = $request->get('periode', 'annuelle');
        
        // Chargement optimisé des relations
        $commune->load([
            'departement.region', 'receveurs', 'ordonnateurs',
            'depotsComptes', 'previsions', 'realisations', 'tauxRealisations',
            'dettesCnps', 'dettesFiscale', 'dettesFeicom', 'dettesSalariale',
            'defaillances', 'retards'
        ]);
        
        // Données avec cache
        $donneesFinancieres = Cache::remember(
            "commune_finances_{$commune->id}_{$annee}_{$periode}",
            now()->addHours(2),
            fn() => $this->getDonneesFinancieres($commune, $annee, $periode)
        );
        
        // Performance et comparaisons
        $performance = $this->performanceService->analyzeCommune($commune, $annee);
        $comparaisons = $this->performanceService->compareWithPeers($commune, $annee);
        
        // Indicateurs clés
        $indicateurs = $this->getIndicateursClés($commune, $annee);
        
        // Évolution historique
        $evolution = $this->getEvolutionHistorique($commune);
        
        // Projets en cours
        $projetsEnCours = $this->getProjetsEnCours($commune);
        
        // Alertes et notifications
        $alertes = $this->getAlertes($commune);
        
        if ($request->ajax()) {
            return response()->json([
                'donneesFinancieres' => $donneesFinancieres,
                'performance' => $performance,
                'indicateurs' => $indicateurs
            ]);
        }
        
        return view('communes.show', compact(
            'commune', 'donneesFinancieres', 'performance', 'comparaisons',
            'indicateurs', 'evolution', 'projetsEnCours', 'alertes', 'annee', 'periode'
        ));
    }

    /**
     * Tableau de bord performance avec métriques avancées
     */
    public function dashboard(Commune $commune, Request $request): JsonResponse
    {
        $annee = $request->get('annee', date('Y'));
        
        $dashboard = [
            'kpi' => $this->getKPI($commune, $annee),
            'tendances' => $this->getTendances($commune),
            'comparaisons' => $this->getComparaisonsRegionales($commune, $annee),
            'objectifs' => $this->getObjectifs($commune, $annee),
            'risques' => $this->getRisques($commune),
        ];
        
        return response()->json($dashboard);
    }

    /**
 * Affichage du formulaire de modification avec validation temps réel
 */
public function edit(Commune $commune)
{
    // Chargement optimisé des relations nécessaires
    $commune->load([
        'departement.region', 
        'receveurs', 
        'ordonnateurs'
    ]);
    
    // Récupération des données pour les selects
    $departements = Departement::with('region')->orderBy('nom')->get();
    
    // Receveurs disponibles (non assignés + ceux déjà assignés à cette commune)
    $receveurs = Receveur::where(function($query) use ($commune) {
        $query->whereNull('commune_id')
              ->orWhere('commune_id', $commune->id);
    })->orderBy('nom')->get();
    
    // Ordonnateurs disponibles (non assignés + ceux déjà assignés à cette commune)
    $ordonnateurs = Ordonnateur::where(function($query) use ($commune) {
        $query->whereNull('commune_id')
              ->orWhere('commune_id', $commune->id);
    })->orderBy('nom')->get();
    
    // Données pour l'auto-complétion et la validation
    $suggestions = [
        'codes_existants' => Commune::where('id', '!=', $commune->id)->pluck('code')->toArray(),
        'noms_similaires' => Commune::where('id', '!=', $commune->id)->pluck('nom')->toArray()
    ];
    
    // Vérifications de sécurité pour l'édition
    $editRestrictions = $this->checkEditRestrictions($commune);
    
    // Historique des modifications récentes (optionnel)
    $recentChanges = $this->getRecentChanges($commune);
    
    return view('communes.edit', compact(
        'commune',
        'departements', 
        'receveurs', 
        'ordonnateurs',
        'suggestions',
        'editRestrictions',
        'recentChanges'
    ));
}

/**
 * Vérification des restrictions d'édition
 */
private function checkEditRestrictions($commune): array
{
    $restrictions = [];
    
    // Vérifier seulement l'existence de prévisions pour l'année courante
    $hasFinancialData = $commune->previsions()
        ->where('annee_exercice', date('Y'))
        ->exists();
        
    if ($hasFinancialData) {
        $restrictions[] = [
            'type' => 'financial',
            'message' => 'Cette commune a des prévisions budgétaires pour l\'année en cours.',
            'level' => 'info'
        ];
    }
    
    return $restrictions;
}

    /**
     * Mise à jour avec validation partielle
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
            'receveur_ids' => 'nullable|array',
            'receveur_ids.*' => 'exists:receveurs,id',
            'ordonnateur_ids' => 'nullable|array',
            'ordonnateur_ids.*' => 'exists:ordonnateurs,id',
            'coordonnees_gps' => 'nullable|string|max:100'
        ]);

        DB::beginTransaction();
        try {
            $originalData = $commune->toArray();
            
            // Mettre à jour la commune
            $commune->update($validated);
            
            // Réassigner les responsables
            $this->reassignResponsables($commune, $request);
            
            // Audit trail
            $this->createAuditLog('commune_updated', $commune->id, $originalData, $validated);
            
            // Notification si changements importants
            $this->notificationService->communeUpdated($commune, $originalData, $validated);
            
            DB::commit();
            
            // Invalider les caches
            $this->invalidateCache($commune);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Commune mise à jour avec succès.',
                    'data' => $commune->fresh(['departement.region', 'receveurs', 'ordonnateurs'])
                ]);
            }
            
            return redirect()->route('communes.show', $commune)
                           ->with('success', 'Commune mise à jour avec succès.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Erreur mise à jour commune: ' . $e->getMessage(), [
                'commune_id' => $commune->id,
                'data' => $validated,
                'user_id' => auth()->id()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->withInput()
                        ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Export de données
     */
    public function export(Request $request, string $format = 'pdf')
    {
        $communeIds = $request->get('commune_ids', []);
        $annee = $request->get('annee', date('Y'));
        $type = $request->get('type', 'complet');
        
        try {
            $fileName = $this->exportService->exportCommunes($communeIds, $format, $annee, $type);
            
            return response()->download(storage_path("app/exports/{$fileName}"))
                           ->deleteFileAfterSend(true);
                           
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Suppression avec vérifications de sécurité
     */
    public function destroy(Commune $commune, Request $request)
    {
        try {
            // Vérifications de sécurité étendues
            $canDelete = $this->canDeleteCommune($commune);
            
            if (!$canDelete['allowed']) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $canDelete['reason']
                    ], 422);
                }
                return back()->with('error', $canDelete['reason']);
            }
            
            DB::beginTransaction();
            
            // Sauvegarder pour l'audit
            $communeData = $commune->toArray();
            
            // Libérer les ressources
            $this->liberateResources($commune);
            
            // Archive avant suppression
            $this->archiveCommune($commune);
            
            // Suppression
            $commune->delete();
            
            // Audit
            $this->createAuditLog('commune_deleted', null, $communeData, null);
            
            // Notification
            $this->notificationService->communeDeleted($communeData);
            
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Commune supprimée avec succès.'
                ]);
            }
            
            return redirect()->route('communes.index')
                           ->with('success', 'Commune supprimée avec succès.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Erreur suppression commune: ' . $e->getMessage(), [
                'commune_id' => $commune->id,
                'user_id' => auth()->id()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Méthodes privées pour la logique métier
     */
    private function applyFilters($query, Request $request)
    {
        // Filtre par département
        if ($request->filled('departement_id')) {
            $query->where('departement_id', $request->departement_id);
        }
        
        // Filtre par région
        if ($request->filled('region_id')) {
            $query->whereHas('departement', function($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }
        
        // Filtre par taille de population
        if ($request->filled('population_min')) {
            $query->where('population', '>=', $request->population_min);
        }
        if ($request->filled('population_max')) {
            $query->where('population', '<=', $request->population_max);
        }
        
        // Filtre par performance
        if ($request->filled('performance')) {
            $performance = $request->performance;
            $query->whereHas('tauxRealisations', function($q) use ($performance) {
                switch ($performance) {
                    case 'excellente':
                        $q->where('pourcentage', '>=', 90);
                        break;
                    case 'bonne':
                        $q->whereBetween('pourcentage', [75, 89]);
                        break;
                    case 'moyenne':
                        $q->whereBetween('pourcentage', [50, 74]);
                        break;
                    case 'faible':
                        $q->where('pourcentage', '<', 50);
                        break;
                }
            });
        }
        
        // Filtre par statut des responsables
        if ($request->filled('avec_receveur')) {
            if ($request->avec_receveur === '1') {
                $query->has('receveurs');
            } else {
                $query->doesntHave('receveurs');
            }
        }
        
        if ($request->filled('avec_ordonnateur')) {
            if ($request->avec_ordonnateur === '1') {
                $query->has('ordonnateurs');
            } else {
                $query->doesntHave('ordonnateurs');
            }
        }
    }

    private function getCommunesStats(): array
    {
        return Cache::remember('communes_stats', now()->addHours(1), function() {
            return [
                'total' => Commune::count(),
                'avec_receveur' => Commune::has('receveurs')->count(),
                'avec_ordonnateur' => Commune::has('ordonnateurs')->count(),
                'avec_prevision_courante' => Commune::whereHas('previsions', function($q) {
                    $q->where('annee_exercice', date('Y'));
                })->count(),
                'performance_moyenne' => DB::table('taux_realisations')
                    ->where('annee_exercice', date('Y'))
                    ->avg('pourcentage') ?? 0,
                'budget_total' => DB::table('previsions')
                    ->where('annee_exercice', date('Y'))
                    ->sum('montant') ?? 0
            ];
        });
    }

    private function getChartsData(): array
    {
        return Cache::remember('communes_charts', now()->addHours(2), function() {
            return [
                'repartition_par_region' => DB::table('communes')
                    ->join('departements', 'communes.departement_id', '=', 'departements.id')
                    ->join('regions', 'departements.region_id', '=', 'regions.id')
                    ->select('regions.nom', DB::raw('count(*) as total'))
                    ->groupBy('regions.id', 'regions.nom')
                    ->get(),
                
                'evolution_creation' => DB::table('communes')
                    ->select(
                        DB::raw('YEAR(created_at) as annee'),
                        DB::raw('count(*) as nombre')
                    )
                    ->groupBy(DB::raw('YEAR(created_at)'))
                    ->orderBy('annee')
                    ->get(),
                
                'performance_regions' => DB::table('taux_realisations')
                    ->join('communes', 'taux_realisations.commune_id', '=', 'communes.id')
                    ->join('departements', 'communes.departement_id', '=', 'departements.id')
                    ->join('regions', 'departements.region_id', '=', 'regions.id')
                    ->where('taux_realisations.annee_exercice', date('Y'))
                    ->select('regions.nom', DB::raw('avg(taux_realisations.pourcentage) as moyenne'))
                    ->groupBy('regions.id', 'regions.nom')
                    ->get()
            ];
        });
    }

    private function getDonneesFinancieres($commune, $annee, $periode = 'annuelle')
    {
        $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
        $realisations = $commune->realisations->where('annee_exercice', $annee);
        $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
        
        // Calculs avancés selon la période
        $donneesBase = [
            'prevision' => $prevision?->montant ?? 0,
            'realisation_total' => $realisations->sum('montant'),
            'taux_realisation' => $tauxRealisation?->pourcentage ?? 0,
            'evaluation' => $tauxRealisation?->evaluation ?? 'Non évalué',
            'ecart' => $tauxRealisation?->ecart ?? 0,
        ];
        
        if ($periode !== 'annuelle') {
            // Ajouter les données périodiques (trimestrielles, mensuelles)
            $donneesBase['donnees_periodiques'] = $this->getDonneesPeriodiques($commune, $annee, $periode);
        }
        
        return $donneesBase;
    }

    private function assignResponsables($commune, $request)
    {
        if ($request->filled('receveur_ids')) {
            Receveur::whereIn('id', $request->receveur_ids)
                ->update(['commune_id' => $commune->id]);
        }
        
        if ($request->filled('ordonnateur_ids')) {
            Ordonnateur::whereIn('id', $request->ordonnateur_ids)
                ->update(['commune_id' => $commune->id]);
        }
    }

    private function reassignResponsables($commune, $request)
    {
        // Libérer les anciens
        Receveur::where('commune_id', $commune->id)->update(['commune_id' => null]);
        Ordonnateur::where('commune_id', $commune->id)->update(['commune_id' => null]);
        
        // Assigner les nouveaux
        $this->assignResponsables($commune, $request);
    }

    // private function canDeleteCommune($commune): array
    // {
    //     // Vérifications étendues
    //     $hasFinancialData = $commune->previsions()->exists() || 
    //                        $commune->realisations()->exists();
                           
    //     $hasActiveProjects = $commune->projets()->where('statut', 'en_cours')->exists();
        
    //     $hasRecentTransactions = $commune->transactions()
    //         ->where('created_at', '>', now()->subDays(30))
    //         ->exists();
            
    //     if ($hasFinancialData) {
    //         return [
    //             'allowed' => false,
    //             'reason' => 'Cette commune contient des données financières et ne peut être supprimée.'
    //         ];
    //     }
        
    //     if ($hasActiveProjects) {
    //         return [
    //             'allowed' => false,
    //             'reason' => 'Cette commune a des projets actifs en cours.'
    //         ];
    //     }
        
    //     if ($hasRecentTransactions) {
    //         return [
    //             'allowed' => false,
    //             'reason' => 'Cette commune a des transactions récentes.'
    //         ];
    //     }
        
    //     return ['allowed' => true, 'reason' => null];
    // }

    private function createAuditLog($action, $communeId, $oldData, $newData)
    {
        DB::table('audit_logs')->insert([
            'user_id' => auth()->id(),
            'action' => $action,
            'model' => 'Commune',
            'model_id' => $communeId,
            'old_data' => json_encode($oldData),
            'new_data' => json_encode($newData),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function invalidateCache($commune)
    {
        Cache::forget("commune_finances_{$commune->id}_" . date('Y') . "_annuelle");
        Cache::forget('communes_stats');
        Cache::forget('communes_charts');
    }

/**
     * Méthodes privées pour la logique métier - CORRECTIONS
     */
    private function canDeleteCommune($commune): array
    {
        // Vérifications étendues
        $hasFinancialData = $commune->previsions()->exists() || 
                           $commune->realisations()->exists();
                           
        // CORRECTION: Vérifier si la méthode projets() existe
        $hasActiveProjects = method_exists($commune, 'projets') && 
                            $commune->projets()->where('statut', 'en_cours')->exists();
        
        // CORRECTION: Vérifier si la méthode transactions() existe  
        $hasRecentTransactions = method_exists($commune, 'transactions') &&
                                $commune->transactions()
                                    ->where('created_at', '>', now()->subDays(30))
                                    ->exists();
            
        if ($hasFinancialData) {
            return [
                'allowed' => false,
                'reason' => 'Cette commune contient des données financières et ne peut être supprimée.'
            ];
        }
        
        if ($hasActiveProjects) {
            return [
                'allowed' => false,
                'reason' => 'Cette commune a des projets actifs en cours.'
            ];
        }
        
        if ($hasRecentTransactions) {
            return [
                'allowed' => false,
                'reason' => 'Cette commune a des transactions récentes.'
            ];
        }
        
        return ['allowed' => true, 'reason' => null];
    }

    /**
     * NOUVELLES MÉTHODES MANQUANTES
     */
    private function getIndicateursClés($commune, $annee)
    {
        return Cache::remember("indicateurs_cles_{$commune->id}_{$annee}", now()->addHours(1), function() use ($commune, $annee) {
            $prevision = $commune->previsions()->where('annee_exercice', $annee)->first();
            $realisations = $commune->realisations()->where('annee_exercice', $annee)->sum('montant');
            $tauxRealisation = $commune->tauxRealisations()->where('annee_exercice', $annee)->first();
            
            $dettes = $commune->getTotalDettes($annee);
            $totalDettes = array_sum($dettes);
            
            return [
                'budget_previsionnel' => $prevision?->montant ?? 0,
                'budget_realise' => $realisations,
                'taux_execution' => $tauxRealisation?->pourcentage ?? 0,
                'total_dettes' => $totalDettes,
                'ratio_dette_budget' => $prevision && $prevision->montant > 0 
                    ? ($totalDettes / $prevision->montant) * 100 
                    : 0,
                'nombre_projets_actifs' => method_exists($commune, 'projets') 
                    ? $commune->projets()->where('statut', 'en_cours')->count() 
                    : 0,
                'nombre_defaillances' => $commune->defaillances()->where('annee_exercice', $annee)->count(),
                'nombre_retards' => $commune->retards()->where('annee_exercice', $annee)->count()
            ];
        });
    }

    private function getEvolutionHistorique($commune, $nbAnnees = 5)
    {
        $anneeActuelle = date('Y');
        $evolution = [];
        
        for ($i = 0; $i < $nbAnnees; $i++) {
            $annee = $anneeActuelle - $i;
            
            $prevision = $commune->previsions()->where('annee_exercice', $annee)->first();
            $realisation = $commune->realisations()->where('annee_exercice', $annee)->sum('montant');
            $tauxRealisation = $commune->tauxRealisations()->where('annee_exercice', $annee)->first();
            
            $evolution[] = [
                'annee' => $annee,
                'prevision' => $prevision?->montant ?? 0,
                'realisation' => $realisation,
                'taux_realisation' => $tauxRealisation?->pourcentage ?? 0,
                'evaluation' => $tauxRealisation?->evaluation ?? 'Non évalué'
            ];
        }
        
        return collect($evolution)->reverse();
    }

    private function getProjetsEnCours($commune)
    {
        if (!method_exists($commune, 'projets')) {
            return collect([]);
        }
        
        return $commune->projets()
            ->where('statut', 'en_cours')
            ->with(['responsable', 'financements'])
            ->orderBy('date_debut', 'desc')
            ->limit(10)
            ->get();
    }

    private function getAlertes($commune)
    {
        $alertes = [];
        $anneeActuelle = date('Y');
        
        // Alertes pour performance faible
        $tauxRealisation = $commune->getTauxRealisationAnnuel($anneeActuelle);
        if ($tauxRealisation < 50) {
            $alertes[] = [
                'type' => 'performance',
                'niveau' => 'danger',
                'message' => "Taux de réalisation très faible: {$tauxRealisation}%",
                'action_recommandee' => 'Révision du budget et des procédures'
            ];
        }
        
        // Alertes pour dettes élevées
        $dettes = $commune->getTotalDettes($anneeActuelle);
        $totalDettes = array_sum($dettes);
        $prevision = $commune->previsions()->where('annee_exercice', $anneeActuelle)->first();
        
        if ($prevision && $totalDettes > ($prevision->montant * 0.3)) {
            $alertes[] = [
                'type' => 'dette',
                'niveau' => 'warning',
                'message' => 'Niveau de dette élevé par rapport au budget',
                'action_recommandee' => 'Plan de réduction des dettes'
            ];
        }
        
        // Alertes pour personnel manquant
        if ($commune->receveurs()->count() === 0) {
            $alertes[] = [
                'type' => 'personnel',
                'niveau' => 'warning',
                'message' => 'Aucun receveur assigné',
                'action_recommandee' => 'Assigner un receveur'
            ];
        }
        
        if ($commune->ordonnateurs()->count() === 0) {
            $alertes[] = [
                'type' => 'personnel',
                'niveau' => 'warning',
                'message' => 'Aucun ordonnateur assigné',
                'action_recommandee' => 'Assigner un ordonnateur'
            ];
        }
        
        // Alertes pour défaillances récentes
        $defaillancesRecentes = $commune->defaillances()
            ->where('date_constat', '>', now()->subDays(30))
            ->where('est_resolue', false)
            ->count();
            
        if ($defaillancesRecentes > 0) {
            $alertes[] = [
                'type' => 'defaillance',
                'niveau' => 'danger',
                'message' => "{$defaillancesRecentes} défaillance(s) non résolue(s)",
                'action_recommandee' => 'Traitement urgent des défaillances'
            ];
        }
        
        return collect($alertes);
    }

    private function getDonneesPeriodiques($commune, $annee, $periode)
    {
        $query = $commune->realisations()->where('annee_exercice', $annee);
        
        switch ($periode) {
            case 'trimestrielle':
                return $query->selectRaw('
                    QUARTER(date_realisation) as periode,
                    SUM(montant) as montant_total,
                    COUNT(*) as nombre_operations
                ')
                ->groupBy(DB::raw('QUARTER(date_realisation)'))
                ->orderBy('periode')
                ->get();
                
            case 'mensuelle':
                return $query->selectRaw('
                    MONTH(date_realisation) as mois,
                    YEAR(date_realisation) as annee,
                    SUM(montant) as montant_total,
                    COUNT(*) as nombre_operations
                ')
                ->groupBy(DB::raw('YEAR(date_realisation), MONTH(date_realisation)'))
                ->orderBy('annee')->orderBy('mois')
                ->get();
                
            default:
                return collect([]);
        }
    }

    private function getKPI($commune, $annee)
    {
        $prevision = $commune->previsions()->where('annee_exercice', $annee)->first();
        $realisations = $commune->realisations()->where('annee_exercice', $annee);
        $tauxRealisation = $commune->tauxRealisations()->where('annee_exercice', $annee)->first();
        
        return [
            'efficacite_budgetaire' => $tauxRealisation?->pourcentage ?? 0,
            'regularite_paiements' => $this->calculerRegularitePaiements($commune, $annee),
            'respect_delais' => $this->calculerRespectDelais($commune, $annee),
            'sante_financiere' => $this->calculerSanteFinanciere($commune, $annee),
            'governance_score' => $this->calculerScoreGouvernance($commune, $annee)
        ];
    }

    private function getTendances($commune, $nbAnnees = 3)
    {
        $anneeActuelle = date('Y');
        $tendances = [];
        
        for ($i = 0; $i < $nbAnnees; $i++) {
            $annee = $anneeActuelle - $i;
            $kpi = $this->getKPI($commune, $annee);
            $tendances[$annee] = $kpi;
        }
        
        return $tendances;
    }

    private function getComparaisonsRegionales($commune, $annee)
    {
        $communesDepartement = Commune::where('departement_id', $commune->departement_id)
            ->where('id', '!=', $commune->id)
            ->withPerformance($annee)
            ->get();
            
        $moyenneDepartement = $communesDepartement->avg(function($c) use ($annee) {
            return $c->getTauxRealisationAnnuel($annee);
        });
        
        return [
            'taux_realisation_commune' => $commune->getTauxRealisationAnnuel($annee),
            'moyenne_departement' => $moyenneDepartement,
            'rang_departement' => $this->calculerRangDepartement($commune, $annee),
            'nombre_communes_departement' => $communesDepartement->count() + 1
        ];
    }

    private function getObjectifs($commune, $annee)
    {
        // À implémenter selon vos besoins métier
        return [
            'taux_realisation_cible' => 85,
            'reduction_dettes_cible' => 15,
            'delai_paiement_cible' => 30
        ];
    }

    private function getRisques($commune)
    {
        $risques = [];
        
        // Analyser les risques financiers
        $dettes = $commune->getTotalDettes();
        $totalDettes = array_sum($dettes);
        
        if ($totalDettes > 1000000) { // 1M FCFA
            $risques[] = [
                'type' => 'Financier',
                'description' => 'Niveau de dette élevé',
                'probabilite' => 'Moyenne',
                'impact' => 'Élevé'
            ];
        }
        
        // Analyser les risques opérationnels
        if ($commune->receveurs()->count() === 0) {
            $risques[] = [
                'type' => 'Opérationnel',
                'description' => 'Absence de receveur',
                'probabilite' => 'Élevée',
                'impact' => 'Élevé'
            ];
        }
        
        return $risques;
    }

    private function liberateResources($commune)
    {
        // Libérer les receveurs et ordonnateurs
        $commune->receveurs()->update(['commune_id' => null]);
        $commune->ordonnateurs()->update(['commune_id' => null]);
    }

    private function archiveCommune($commune)
    {
        // Créer une archive des données importantes
        DB::table('communes_archived')->insert([
            'commune_data' => json_encode($commune->toArray()),
            'archived_at' => now(),
            'archived_by' => auth()->id()
        ]);
    }

    // Méthodes helper pour les calculs KPI
    private function calculerRegularitePaiements($commune, $annee)
    {
        // Logique de calcul de la régularité des paiements
        return 75; // Placeholder
    }

    private function calculerRespectDelais($commune, $annee)
    {
        // Logique de calcul du respect des délais
        return 80; // Placeholder
    }

    private function calculerSanteFinanciere($commune, $annee)
    {
        // Logique de calcul de la santé financière
        return 70; // Placeholder
    }

    private function calculerScoreGouvernance($commune, $annee)
    {
        // Logique de calcul du score de gouvernance
        return 65; // Placeholder
    }

    private function calculerRangDepartement($commune, $annee)
    {
        // Logique de calcul du rang dans le département
        return 3; // Placeholder
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
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\DB;

// class CommunesController extends Controller

// {
//     /**
//      * Liste des communes avec pagination et recherche
//      */
//     public function index(Request $request)
//     {
//         $query = Commune::with(['departement.region', 'receveurs', 'ordonnateurs']);
        
//         // Recherche
//         if ($request->filled('search')) {
//             $search = $request->search;
//             $query->where(function($q) use ($search) {
//                 $q->where('nom', 'LIKE', "%{$search}%")
//                   ->orWhere('code', 'LIKE', "%{$search}%")
//                   ->orWhereHas('departement', function($dq) use ($search) {
//                       $dq->where('nom', 'LIKE', "%{$search}%");
//                   });
//             });
//         }
        
//         // Filtrage par département
//         if ($request->filled('departement_id')) {
//             $query->where('departement_id', $request->departement_id);
//         }
        
//         // Tri
//         $sortBy = $request->get('sort_by', 'nom');
//         $sortDirection = $request->get('sort_direction', 'asc');
//         $query->orderBy($sortBy, $sortDirection);
        
//         $communes = $query->paginate(15);
//         $departements = Departement::with('region')->orderBy('nom')->get();
        
//         return view('communes.index', compact('communes', 'departements'));
//     }

//     /**
//      * Affichage du formulaire de création
//      */
//     public function create()
//     {
//         $departements = Departement::with('region')->orderBy('nom')->get();
//         // Récupérer les receveurs et ordonnateurs qui ne sont pas encore assignés à une commune
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
//             'telephone' => 'nullable|string|max:20',
//             'receveur_ids' => 'nullable|array',
//             'receveur_ids.*' => 'exists:receveurs,id',
//             'ordonnateur_ids' => 'nullable|array',
//             'ordonnateur_ids.*' => 'exists:ordonnateurs,id',
//         ], [
//             'nom.required' => 'Le nom de la commune est obligatoire.',
//             'code.required' => 'Le code de la commune est obligatoire.',
//             'code.unique' => 'Ce code est déjà utilisé par une autre commune.',
//             'departement_id.required' => 'Vous devez sélectionner un département.',
//             'departement_id.exists' => 'Le département sélectionné n\'existe pas.',
//         ]);

//         DB::beginTransaction();
//         try {
//             // Créer la commune
//             $commune = Commune::create($validated);
            
//             // Assigner les receveurs à cette commune
//             if ($request->filled('receveur_ids')) {
//                 Receveur::whereIn('id', $request->receveur_ids)
//                     ->update(['commune_id' => $commune->id]);
//             }
            
//             // Assigner les ordonnateurs à cette commune
//             if ($request->filled('ordonnateur_ids')) {
//                 Ordonnateur::whereIn('id', $request->ordonnateur_ids)
//                     ->update(['commune_id' => $commune->id]);
//             }
            
//             DB::commit();
            
//             return redirect()->route('communes.show', $commune)
//                            ->with('success', 'Commune créée avec succès.');
//         } catch (\Exception $e) {
//             DB::rollback();
//             return back()->withInput()
//                         ->with('error', 'Erreur lors de la création de la commune: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Affichage des détails d'une commune
//      */
//     public function show(Commune $commune)
//     {
//         $annee = request('annee', date('Y'));
       
        
//         // Chargement des relations nécessaires
//         $commune->load([
//             'departement.region', 'receveurs', 'ordonnateurs',
//             'depotsComptes', 'previsions', 'realisations', 'tauxRealisations',
//             'dettesCnps', 'dettesFiscale', 'dettesFeicom', 'dettesSalariale',
//             'defaillances', 'retards'
//         ]);
        
//         // Données financières
//         $donneesFinancieres = $this->getDonneesFinancieres($commune, $annee);
        
//         // Historique des performances
//         $historiquePerformances = $this->getHistoriquePerformances($commune);
        
//         // Détails des dettes
//         $detailsDettes = $this->getDetailsDettes($commune, $annee);
        
//         // Problèmes et défaillances
//         $problemes = $this->getProblemes($commune, $annee);
        
//         return view('communes.show', compact(
//             'commune', 'donneesFinancieres', 'historiquePerformances', 
//             'detailsDettes', 'problemes', 'annee'
//         ));
//     }

//     /**
//      * Affichage du formulaire de modification
//      */
//     public function edit(Commune $commune)
//     {
//         $commune->load(['receveurs', 'ordonnateurs']);
//         $departements = Departement::with('region')->orderBy('nom')->get();
        
//         // Récupérer les receveurs et ordonnateurs disponibles (non assignés ou assignés à cette commune)
//         $receveurs = Receveur::where(function($query) use ($commune) {
//             $query->whereNull('commune_id')
//                   ->orWhere('commune_id', $commune->id);
//         })->orderBy('nom')->get();
        
//         $ordonnateurs = Ordonnateur::where(function($query) use ($commune) {
//             $query->whereNull('commune_id')
//                   ->orWhere('commune_id', $commune->id);
//         })->orderBy('nom')->get();
        
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
//             'telephone' => 'nullable|string|max:20',
//             'receveur_ids' => 'nullable|array',
//             'receveur_ids.*' => 'exists:receveurs,id',
//             'ordonnateur_ids' => 'nullable|array',
//             'ordonnateur_ids.*' => 'exists:ordonnateurs,id',
//         ], [
//             'nom.required' => 'Le nom de la commune est obligatoire.',
//             'code.required' => 'Le code de la commune est obligatoire.',
//             'code.unique' => 'Ce code est déjà utilisé par une autre commune.',
//             'departement_id.required' => 'Vous devez sélectionner un département.',
//             'departement_id.exists' => 'Le département sélectionné n\'existe pas.',
//         ]);

//         DB::beginTransaction();
//         try {
//             // Mettre à jour la commune
//             $commune->update($validated);
            
//             // Libérer les anciens receveurs et ordonnateurs
//             Receveur::where('commune_id', $commune->id)->update(['commune_id' => null]);
//             Ordonnateur::where('commune_id', $commune->id)->update(['commune_id' => null]);
            
//             // Assigner les nouveaux receveurs
//             if ($request->filled('receveur_ids')) {
//                 Receveur::whereIn('id', $request->receveur_ids)
//                     ->update(['commune_id' => $commune->id]);
//             }
            
//             // Assigner les nouveaux ordonnateurs
//             if ($request->filled('ordonnateur_ids')) {
//                 Ordonnateur::whereIn('id', $request->ordonnateur_ids)
//                     ->update(['commune_id' => $commune->id]);
//             }
            
//             DB::commit();
            
//             return redirect()->route('communes.show', $commune)
//                            ->with('success', 'Commune mise à jour avec succès.');
//         } catch (\Exception $e) {
//             DB::rollback();
//             return back()->withInput()
//                         ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Suppression d'une commune
//      */
//     public function destroy(Commune $commune)
//     {
//         try {
//             // Vérifier si la commune a des données liées
//             $hasData = $commune->previsions()->exists() || 
//                       $commune->realisations()->exists() || 
//                       $commune->dettesCnps()->exists() ||
//                       $commune->dettesFiscale()->exists() ||
//                       $commune->dettesFeicom()->exists() ||
//                       $commune->dettesSalariale()->exists();
                      
//             if ($hasData) {
//                 return back()->with('error', 'Impossible de supprimer cette commune car elle contient des données financières.');
//             }
            
//             DB::beginTransaction();
            
//             // Libérer les receveurs et ordonnateurs
//             Receveur::where('commune_id', $commune->id)->update(['commune_id' => null]);
//             Ordonnateur::where('commune_id', $commune->id)->update(['commune_id' => null]);
            
//             // Supprimer la commune
//             $commune->delete();
            
//             DB::commit();
            
//             return redirect()->route('communes.index')
//                            ->with('success', 'Commune supprimée avec succès.');
//         } catch (\Exception $e) {
//             DB::rollback();
//             return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Méthodes privées pour les données additionnelles
//      */
//     private function getDonneesFinancieres($commune, $annee)
//     {
//         $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
//         $realisations = $commune->realisations->where('annee_exercice', $annee);
//         $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
        
//         return [
//             'prevision' => $prevision?->montant ?? 0,
//             'realisation_total' => $realisations->sum('montant'),
//             'taux_realisation' => $tauxRealisation?->pourcentage ?? 0,
//             'evaluation' => $tauxRealisation?->evaluation ?? 'Non évalué',
//             'ecart' => $tauxRealisation?->ecart ?? 100,
//             'realisations_detail' => $realisations->map(function($real) {
//                 return [
//                     'montant' => $real->montant,
//                     'date' => $real->date_realisation,
//                     'ecart_prevision' => $real->ecart_prevision
//                 ];
//             })
//         ];
//     }
    
//     private function getHistoriquePerformances($commune)
//     {
//         return $commune->tauxRealisations()
//             ->orderBy('annee_exercice')
//             ->get()
//             ->map(function($taux) {
//                 return [
//                     'annee' => $taux->annee_exercice,
//                     'pourcentage' => $taux->pourcentage,
//                     'evaluation' => $taux->evaluation
//                 ];
//             });
//     }
    
//     private function getDetailsDettes($commune, $annee)
//     {
//         return [
//             'cnps' => [
//                 'montant' => $commune->dettesCnps->filter(function($dette) use ($annee) {
//                     return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
//                 })->sum('montant'),
//                 'details' => $commune->dettesCnps->filter(function($dette) use ($annee) {
//                     return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
//                 })->values()
//             ],
//             'fiscale' => [
//                 'montant' => $commune->dettesFiscale->filter(function($dette) use ($annee) {
//                     return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
//                 })->sum('montant'),
//                 'details' => $commune->dettesFiscale->filter(function($dette) use ($annee) {
//                     return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
//                 })->values()
//             ],
//             'feicom' => [
//                 'montant' => $commune->dettesFeicom->filter(function($dette) use ($annee) {
//                     return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
//                 })->sum('montant'),
//                 'details' => $commune->dettesFeicom->filter(function($dette) use ($annee) {
//                     return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
//                 })->values()
//             ],
//             'salariale' => [
//                 'montant' => $commune->dettesSalariale->filter(function($dette) use ($annee) {
//                     return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
//                 })->sum('montant'),
//                 'details' => $commune->dettesSalariale->filter(function($dette) use ($annee) {
//                     return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
//                 })->values()
//             ]
//         ];
//     }
    
//     private function getProblemes($commune, $annee)
//     {
//         return [
//             'defaillances' => $commune->defaillances->filter(function($def) use ($annee) {
//                 return $def->date_constat && \Carbon\Carbon::parse($def->date_constat)->year == $annee;
//             })->map(function($def) {
//                 return [
//                     'type' => $def->type_defaillance,
//                     'description' => $def->description,
//                     'date_constat' => $def->date_constat,
//                     'gravite' => $def->gravite,
//                     'est_grave' => $def->est_grave,
//                     'est_resolue' => $def->est_resolue
//                 ];
//             }),
//             'retards' => $commune->retards->filter(function($retard) use ($annee) {
//                 return $retard->date_constat && \Carbon\Carbon::parse($retard->date_constat)->year == $annee;
//             })->map(function($retard) {
//                 return [
//                     'type' => $retard->type_retard,
//                     'duree_jours' => $retard->duree_jours,
//                     'date_constat' => $retard->date_constat,
//                     'gravite' => $retard->gravite
//                 ];
//             })
//         ];
//     }

//    //  * Afficher les défaillances et retards d'une commune
 
// public function showDefaillancesRetards(Commune $commune)
// {
//     $annee = request('annee', date('Y'));
    
//     $commune->load(['defaillances', 'retards', 'departement.region']);
    
//     // Filtrer par année
//     $defaillances = $commune->defaillances->filter(function($def) use ($annee) {
//         return $def->date_constat && Carbon::parse($def->date_constat)->year == $annee;
//     });
    
//     $retards = $commune->retards->filter(function($retard) use ($annee) {
//         return $retard->date_constat && Carbon::parse($retard->date_constat)->year == $annee;
//     });
    
//     // Statistiques
//     $stats = [
//         'defaillances_total' => $defaillances->count(),
//         'defaillances_resolues' => $defaillances->where('est_resolue', true)->count(),
//         'defaillances_graves' => $defaillances->where('gravite', 'élevée')->count(),
//         'retards_total' => $retards->count(),
//         'retard_moyen' => $retards->avg('duree_jours'),
//         'retard_max' => $retards->max('duree_jours')
//     ];
    
//     return view('communes.defaillances-retards', compact('commune', 'defaillances', 'retards', 'stats', 'annee'));
// }

// /**
//  * Ajouter une défaillance à une commune
//  */
// public function addDefaillance(Request $request, Commune $commune)
// {
//     $validated = $request->validate([
//         'type_defaillance' => 'required|string|max:255',
//         'description' => 'required|string',
//         'date_constat' => 'required|date',
//         'gravite' => 'required|in:faible,moyenne,élevée',
//         'est_resolue' => 'boolean'
//     ]);
    
//     try {
//         $commune->defaillances()->create($validated);
//         return redirect()->back()->with('success', 'Défaillance ajoutée avec succès.');
//     } catch (\Exception $e) {
//         return redirect()->back()->with('error', 'Erreur lors de l\'ajout: ' . $e->getMessage());
//     }
// }

// /**
//  * Ajouter un retard à une commune
//  */
// public function addRetard(Request $request, Commune $commune)
// {
//     $validated = $request->validate([
//         'type_retard' => 'required|string|max:255',
//         'duree_jours' => 'required|integer|min:1',
//         'date_constat' => 'required|date',
//         'date_retard' => 'nullable|date'
//     ]);
    
//     try {
//         $commune->retards()->create($validated);
//         return redirect()->back()->with('success', 'Retard ajouté avec succès.');
//     } catch (\Exception $e) {
//         return redirect()->back()->with('error', 'Erreur lors de l\'ajout: ' . $e->getMessage());
//     }
// }

// /**
//  * Analyser les performances d'une commune et détecter les problèmes
//  */
// public function analyserPerformances(Commune $commune)
// {
//     $annee = request('annee', date('Y'));
    
//     $commune->load([
//         'previsions', 'realisations', 'tauxRealisations', 'depotsComptes',
//         'dettesCnps', 'dettesFiscale', 'dettesFeicom', 'dettesSalariale'
//     ]);
    
//     $analyse = [
//         'indicateurs' => $this->calculerIndicateurs($commune, $annee),
//         'problemes_detectes' => $this->detecterProblemes($commune, $annee),
//         'recommandations' => $this->genererRecommandations($commune, $annee),
//         'tendances' => $this->calculerTendances($commune)
//     ];
    
//     return view('communes.analyse-performances', compact('commune', 'analyse', 'annee'));
// }

// /**
//  * Calculer les indicateurs de performance
//  */
// private function calculerIndicateurs($commune, $annee)
// {
//     $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
//     $realisations = $commune->realisations->where('annee_exercice', $annee);
//     $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
//     $depotCompte = $commune->depotsComptes->where('annee_exercice', $annee)->first();
    
//     // Calculer les dettes totales
//     $dettesAnnee = [
//         'cnps' => $commune->dettesCnps->filter(function($dette) use ($annee) {
//             return $dette->date_evaluation && Carbon::parse($dette->date_evaluation)->year == $annee;
//         })->sum('montant'),
//         'fiscale' => $commune->dettesFiscale->filter(function($dette) use ($annee) {
//             return $dette->date_evaluation && Carbon::parse($dette->date_evaluation)->year == $annee;
//         })->sum('montant'),
//         'feicom' => $commune->dettesFeicom->filter(function($dette) use ($annee) {
//             return $dette->date_evaluation && Carbon::parse($dette->date_evaluation)->year == $annee;
//         })->sum('montant'),
//         'salariale' => $commune->dettesSalariale->filter(function($dette) use ($annee) {
//             return $dette->date_evaluation && Carbon::parse($dette->date_evaluation)->year == $annee;
//         })->sum('montant')
//     ];
    
//     $detteTotal = array_sum($dettesAnnee);
    
//     return [
//         'taux_realisation' => $tauxRealisation?->pourcentage ?? 0,
//         'prevision' => $prevision?->montant ?? 0,
//         'realisation' => $realisations->sum('montant'),
//         'ecart_budgetaire' => $prevision ? (($realisations->sum('montant') - $prevision->montant) / $prevision->montant) * 100 : 0,
//         'dette_totale' => $detteTotal,
//         'dettes_detail' => $dettesAnnee,
//         'depot_conforme' => $depotCompte?->validation ?? false,
//         'depot_retard' => $depotCompte ? $this->calculerRetardDepot($depotCompte->date_depot, $annee) : null,
//         'ratio_dette_prevision' => $prevision && $prevision->montant > 0 ? ($detteTotal / $prevision->montant) * 100 : 0
//     ];
// }

// /**
//  * Détecter les problèmes de performance
//  */
// private function detecterProblemes($commune, $annee)
// {
//     $indicateurs = $this->calculerIndicateurs($commune, $annee);
//     $problemes = [];
    
//     // Problème de taux de réalisation
//     if ($indicateurs['taux_realisation'] < 50) {
//         $problemes[] = [
//             'type' => 'Taux de réalisation faible',
//             'gravite' => $indicateurs['taux_realisation'] < 25 ? 'élevée' : 'moyenne',
//             'description' => "Taux de réalisation de {$indicateurs['taux_realisation']}%",
//             'impact' => 'Faible exécution budgétaire'
//         ];
//     }
    
//     // Problème de dette élevée
//     if ($indicateurs['ratio_dette_prevision'] > 50) {
//         $problemes[] = [
//             'type' => 'Endettement élevé',
//             'gravite' => $indicateurs['ratio_dette_prevision'] > 100 ? 'élevée' : 'moyenne',
//             'description' => "Ratio dette/prévision de {$indicateurs['ratio_dette_prevision']}%",
//             'impact' => 'Contraintes financières importantes'
//         ];
//     }
    
//     // Problème de dépôt de compte
//     if (!$indicateurs['depot_conforme']) {
//         $problemes[] = [
//             'type' => 'Dépôt de compte non conforme',
//             'gravite' => 'moyenne',
//             'description' => 'Dépôt de compte non validé ou en retard',
//             'impact' => 'Non-conformité réglementaire'
//         ];
//     }
    
//     // Problème de dépassement budgétaire
//     if ($indicateurs['ecart_budgetaire'] > 20) {
//         $problemes[] = [
//             'type' => 'Dépassement budgétaire',
//             'gravite' => 'moyenne',
//             'description' => "Dépassement de {$indicateurs['ecart_budgetaire']}%",
//             'impact' => 'Mauvaise planification budgétaire'
//         ];
//     }
    
//     return $problemes;
// }

// /**
//  * Générer des recommandations
//  */
// private function genererRecommandations($commune, $annee)
// {
//     $problemes = $this->detecterProblemes($commune, $annee);
//     $recommandations = [];
    
//     foreach ($problemes as $probleme) {
//         switch ($probleme['type']) {
//             case 'Taux de réalisation faible':
//                 $recommandations[] = [
//                     'titre' => 'Améliorer l\'exécution budgétaire',
//                     'actions' => [
//                         'Réviser les procédures de passation des marchés',
//                         'Renforcer les capacités des équipes',
//                         'Améliorer le suivi des projets'
//                     ]
//                 ];
//                 break;
                
//             case 'Endettement élevé':
//                 $recommandations[] = [
//                     'titre' => 'Réduire l\'endettement',
//                     'actions' => [
//                         'Établir un plan de remboursement',
//                         'Négocier des échéanciers avec les créanciers',
//                         'Améliorer la mobilisation des ressources'
//                     ]
//                 ];
//                 break;
                
//             case 'Dépôt de compte non conforme':
//                 $recommandations[] = [
//                     'titre' => 'Améliorer la conformité',
//                     'actions' => [
//                         'Former les équipes comptables',
//                         'Respecter les délais de dépôt',
//                         'Améliorer la qualité des comptes'
//                     ]
//                 ];
//                 break;
//         }
//     }
    
//     return $recommandations;
// }

// /**
//  * Calculer les tendances sur plusieurs années
//  */
// private function calculerTendances($commune)
// {
//     $anneeActuelle = date('Y');
//     $tendances = [];
    
//     for ($i = 2; $i >= 0; $i--) {
//         $annee = $anneeActuelle - $i;
//         $indicateurs = $this->calculerIndicateurs($commune, $annee);
        
//         $tendances[] = [
//             'annee' => $annee,
//             'taux_realisation' => $indicateurs['taux_realisation'],
//             'dette_totale' => $indicateurs['dette_totale'],
//             'prevision' => $indicateurs['prevision'],
//             'realisation' => $indicateurs['realisation']
//         ];
//     }
    
//     return $tendances;
// }

// /**
//  * Calculer le retard de dépôt
//  */
// private function calculerRetardDepot($dateDepot, $annee)
// {
//     $dateLimite = Carbon::create($annee, 3, 31); // 31 mars
//     $dateDepotCarbon = Carbon::parse($dateDepot);
    
//     return $dateDepotCarbon->gt($dateLimite) ? $dateDepotCarbon->diffInDays($dateLimite) : 0;
// }

// /**
//  * Générer un rapport de performance
//  */
// public function genererRapportPerformance(Commune $commune)
// {
//     $annee = request('annee', date('Y'));
    
//     $rapport = [
//         'commune' => $commune->load(['departement.region', 'receveurs', 'ordonnateurs']),
//         'indicateurs' => $this->calculerIndicateurs($commune, $annee),
//         'problemes' => $this->detecterProblemes($commune, $annee),
//         'defaillances' => $commune->defaillances->filter(function($def) use ($annee) {
//             return $def->date_constat && Carbon::parse($def->date_constat)->year == $annee;
//         }),
//         'retards' => $commune->retards->filter(function($retard) use ($annee) {
//             return $retard->date_constat && Carbon::parse($retard->date_constat)->year == $annee;
//         }),
//         'recommandations' => $this->genererRecommandations($commune, $annee),
//         'tendances' => $this->calculerTendances($commune)
//     ];
    
//     return view('communes.rapport-performance', compact('rapport', 'annee'));
// }
//   }













