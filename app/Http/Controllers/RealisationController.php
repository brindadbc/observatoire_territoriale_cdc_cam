<?php

namespace App\Http\Controllers;

use App\Models\Realisation;
use App\Models\Prevision;
use App\Models\Commune;
use App\Models\Taux_realisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class RealisationController extends Controller
{
    /**
     * Liste des réalisations avec pagination et filtres
     */
    public function index(Request $request)
    {
        $query = Realisation::with(['commune.departement.region', 'prevision']);
        
        // Filtrage par année
        if ($request->filled('annee')) {
            $query->where('annee_exercice', $request->annee);
        } else {
            $query->where('annee_exercice', date('Y'));
        }
        
        // Filtrage par commune
        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->commune_id);
        }
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('commune', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }
        
        // Tri
        $sortBy = $request->get('sort_by', 'date_realisation');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);
        
        $realisations = $query->paginate(20);
        
        // Données pour les filtres
        $communes = Commune::with('departement')->orderBy('nom')->get();
        $annees = $this->getAnneesDisponibles();
        $statistiques = $this->getStatistiquesGenerales($request->get('annee', date('Y')));
        
        return view('realisations.index', compact(
            'realisations', 'communes', 'annees', 'statistiques'
        ));
    }

    /**
     * Affichage du formulaire de création
     */
    public function create()
    {
        $communes = Commune::with('departement.region')->orderBy('nom')->get();
        
        // Récupérer toutes les prévisions pour l'affichage initial
        $previsions = Prevision::with('commune')
            ->orderBy('annee_exercice', 'desc')
            ->orderBy('commune_id')
            ->get();
        
        return view('realisations.create', compact('communes', 'previsions'));
    }

    /**
     * API pour obtenir les prévisions d'une commune pour une année donnée
     */
    // public function getPrevisionsByCommune(Request $request)
    // {
    //     $communeId = $request->get('commune_id');
    //     $annee = $request->get('annee', date('Y'));
        
    //     $previsions = Prevision::with('commune')
    //         ->where('commune_id', $communeId)
    //         ->where('annee_exercice', $annee)
    //         ->get();
        
    //     return response()->json($previsions);
    // }

    
    /**
     * Affichage des détails d'une réalisation
     */
    public function show(Realisation $realisation)
    {
        $realisation->load(['commune.departement.region', 'prevision']);
        
        // Statistiques liées
        $statsCommune = $this->getStatistiquesCommune($realisation->commune_id, $realisation->annee_exercice);
        $evolutionCommune = $this->getEvolutionCommune($realisation->commune_id);
        $autresRealisations = $this->getAutresRealisations($realisation);
        
        return view('realisations.show', compact(
            'realisation', 'statsCommune', 'evolutionCommune', 'autresRealisations'
        ));
    }

    /**
     * Affichage du formulaire de modification
     */
    public function edit(Realisation $realisation)
    {
        $realisation->load(['commune.departement.region', 'prevision']);
        
        $communes = Commune::with('departement.region')->orderBy('nom')->get();
        $previsions = Prevision::with('commune')
            ->where('commune_id', $realisation->commune_id)
            ->where('annee_exercice', $realisation->annee_exercice)
            ->get();
        
        return view('realisations.edit', compact('realisation', 'communes', 'previsions'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'annee_exercice' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        'montant' => 'required|numeric|min:0',
        'date_realisation' => 'required|date',
        'commune_id' => 'required|exists:communes,id',
        'prevision_id' => [
            'nullable',
            'exists:previsions,id',
            function ($attribute, $value, $fail) use ($request) {
                if ($value) {
                    $prevision = Prevision::find($value);
                    if (!$prevision || 
                        $prevision->commune_id != $request->commune_id || 
                        $prevision->annee_exercice != $request->annee_exercice) {
                        $fail('La prévision sélectionnée ne correspond pas à la commune ou à l\'année choisie.');
                    }
                }
            }
        ],
        'description' => 'nullable|string|max:500'
    ], [
        'annee_exercice.required' => 'L\'année d\'exercice est obligatoire.',
        'annee_exercice.min' => 'L\'année d\'exercice doit être supérieure ou égale à 2000.',
        'montant.required' => 'Le montant est obligatoire.',
        'montant.numeric' => 'Le montant doit être un nombre.',
        'date_realisation.required' => 'La date de réalisation est obligatoire.',
        'commune_id.required' => 'Vous devez sélectionner une commune.',
        'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
    ]);

    DB::beginTransaction();
    try {
        // Créer la réalisation
        $realisation = Realisation::create($validated);
        
        // Calculer et mettre à jour automatiquement les taux
        $this->calculerTauxRealisation($realisation->commune_id, $realisation->annee_exercice);
        
        DB::commit();
        
        return redirect()->route('realisations.show', $realisation)
                       ->with('success', 'Réalisation créée avec succès. Les taux ont été mis à jour automatiquement.');
    } catch (\Exception $e) {
        DB::rollback();
        return back()->withInput()
                    ->with('error', 'Erreur lors de la création : ' . $e->getMessage());
    }
}

// Dans la méthode update()
public function update(Request $request, Realisation $realisation)
{
    $validated = $request->validate([
        'annee_exercice' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        'montant' => 'required|numeric|min:0',
        'date_realisation' => 'required|date',
        'commune_id' => 'required|exists:communes,id',
        'prevision_id' => [
            'nullable',
            'exists:previsions,id',
            function ($attribute, $value, $fail) use ($request) {
                if ($value) {
                    $prevision = Prevision::find($value);
                    if (!$prevision || 
                        $prevision->commune_id != $request->commune_id || 
                        $prevision->annee_exercice != $request->annee_exercice) {
                        $fail('La prévision sélectionnée ne correspond pas à la commune ou à l\'année choisie.');
                    }
                }
            }
        ],
        'description' => 'nullable|string|max:500'
    ], [
        'annee_exercice.required' => 'L\'année d\'exercice est obligatoire.',
        'annee_exercice.min' => 'L\'année d\'exercice doit être supérieure ou égale à 2000.',
        'montant.required' => 'Le montant est obligatoire.',
        'montant.numeric' => 'Le montant doit être un nombre.',
        'date_realisation.required' => 'La date de réalisation est obligatoire.',
        'commune_id.required' => 'Vous devez sélectionner une commune.',
        'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
    ]);

    DB::beginTransaction();
    try {
        $ancienneCommune = $realisation->commune_id;
        $ancienneAnnee = $realisation->annee_exercice;
        
        // Mettre à jour la réalisation
        $realisation->update($validated);
        
        // Recalculer les taux pour l'ancienne commune/année
        if ($ancienneCommune != $realisation->commune_id || $ancienneAnnee != $realisation->annee_exercice) {
            $this->calculerTauxRealisation($ancienneCommune, $ancienneAnnee);
        }
        
        // Recalculer les taux pour la nouvelle commune/année
        $this->calculerTauxRealisation($realisation->commune_id, $realisation->annee_exercice);
        
        DB::commit();
        
        return redirect()->route('realisations.show', $realisation)
                       ->with('success', 'Réalisation mise à jour avec succès. Les taux ont été recalculés automatiquement.');
    } catch (\Exception $e) {
        DB::rollback();
        return back()->withInput()
                    ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
    }
}

    /**
     * Suppression d'une réalisation
     */
    public function destroy(Realisation $realisation)
    {
        DB::beginTransaction();
        try {
            $communeId = $realisation->commune_id;
            $anneeExercice = $realisation->annee_exercice;
            
            $realisation->delete();
            
            // Recalculer les taux après suppression
            $this->calculerTauxRealisation($communeId, $anneeExercice);
            
            DB::commit();
            
            return redirect()->route('realisations.index')
                           ->with('success', 'Réalisation supprimée avec succès. Les taux ont été recalculés automatiquement.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Calcul automatique des taux de réalisation
     */
    private function calculerTauxRealisation($communeId, $anneeExercice)
    {
        // Récupérer la prévision pour cette commune et cette année
        $prevision = Prevision::where('commune_id', $communeId)
            ->where('annee_exercice', $anneeExercice)
            ->first();
        
        if (!$prevision) {
            // Pas de prévision, supprimer le taux s'il existe
            Taux_Realisation::where('commune_id', $communeId)
                ->where('annee_exercice', $anneeExercice)
                ->delete();
            return;
        }
        
        // Calculer le total des réalisations
        $totalRealisations = Realisation::where('commune_id', $communeId)
            ->where('annee_exercice', $anneeExercice)
            ->sum('montant');
        
        // Calculer le pourcentage
        $pourcentage = $prevision->montant > 0 ? ($totalRealisations / $prevision->montant) * 100 : 0;
        
        // Déterminer l'évaluation
        $evaluation = $this->determinerEvaluation($pourcentage);
        
        // Calculer l'écart
        $ecart = $totalRealisations - $prevision->montant;
        
        // Mettre à jour ou créer le taux de réalisation
        Taux_Realisation::updateOrCreate(
            [
                'commune_id' => $communeId,
                'annee_exercice' => $anneeExercice
            ],
            [
                'pourcentage' => round($pourcentage, 2),
                'evaluation' => $evaluation,
                'ecart' => $ecart,
                'date_calcul' => now()
            ]
        );
    }

    /**
     * Déterminer l'évaluation basée sur le pourcentage
     */
    private function determinerEvaluation($pourcentage)
    {
        if ($pourcentage >= 90) return 'Excellent';
        if ($pourcentage >= 75) return 'Bon';
        if ($pourcentage >= 50) return 'Moyen';
        return 'Insuffisant';
    }

    /**
     * Obtenir les années disponibles
     */
    private function getAnneesDisponibles()
    {
        return Realisation::distinct()
            ->orderByDesc('annee_exercice')
            ->pluck('annee_exercice');
    }

    /**
     * Obtenir les statistiques générales
     */
    private function getStatistiquesGenerales($annee)
    {
        return [
            'total_realisations' => Realisation::where('annee_exercice', $annee)->count(),
            'montant_total' => Realisation::where('annee_exercice', $annee)->sum('montant'),
            'taux_moyen' =>Taux_Realisation::where('annee_exercice', $annee)->avg('pourcentage'),
            'communes_excellentes' =>Taux_Realisation::where('annee_exercice', $annee)
                ->where('evaluation', 'Excellent')->count(),
            'communes_bonnes' =>Taux_Realisation::where('annee_exercice', $annee)
                ->where('evaluation', 'Bon')->count(),
            'communes_moyennes' =>Taux_Realisation::where('annee_exercice', $annee)
                ->where('evaluation', 'Moyen')->count(),
            'communes_insuffisantes' =>Taux_Realisation::where('annee_exercice', $annee)
                ->where('evaluation', 'Insuffisant')->count()
        ];
    }

    /**
     * Obtenir les statistiques d'une commune
     */
    private function getStatistiquesCommune($communeId, $anneeExercice)
    {
        $prevision = Prevision::where('commune_id', $communeId)
            ->where('annee_exercice', $anneeExercice)
            ->first();
        
        $totalRealisations = Realisation::where('commune_id', $communeId)
            ->where('annee_exercice', $anneeExercice)
            ->sum('montant');
        
        $tauxRealisation =Taux_Realisation::where('commune_id', $communeId)
            ->where('annee_exercice', $anneeExercice)
            ->first();
        
        return [
            'prevision' => $prevision?->montant ?? 0,
            'total_realisations' => $totalRealisations,
            'taux_realisation' => $tauxRealisation?->pourcentage ?? 0,
            'evaluation' => $tauxRealisation?->evaluation ?? 'Non calculé',
            'ecart' => $tauxRealisation?->ecart ?? 0,
            'nb_realisations' => Realisation::where('commune_id', $communeId)
                ->where('annee_exercice', $anneeExercice)
                ->count()
        ];
    }

    /**
     * Obtenir l'évolution d'une commune
     */
    private function getEvolutionCommune($communeId)
    {
        return Taux_Realisation::where('commune_id', $communeId)
            ->orderBy('annee_exercice')
            ->get()
            ->map(function($taux) {
                return [
                    'annee' => $taux->annee_exercice,
                    'pourcentage' => $taux->pourcentage,
                    'evaluation' => $taux->evaluation
                ];
            });
    }

    /**
     * Obtenir les autres réalisations de la même commune
     */
    private function getAutresRealisations($realisation)
    {
        return Realisation::where('commune_id', $realisation->commune_id)
            ->where('annee_exercice', $realisation->annee_exercice)
            ->where('id', '!=', $realisation->id)
            ->with('prevision')
            ->orderBy('date_realisation', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Recalculer tous les taux pour une année donnée
     */
    public function recalculerTauxAnnee(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        
        DB::beginTransaction();
        try {
            $communes = Commune::all();
            $count = 0;
            
            foreach ($communes as $commune) {
                $this->calculerTauxRealisation($commune->id, $annee);
                $count++;
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Taux recalculés pour {$count} communes de l'année {$annee}"
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du recalcul : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export des réalisations
     */
    public function export(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $format = $request->get('format', 'excel');
        
        $realisations = Realisation::with(['commune.departement.region', 'prevision'])
            ->where('annee_exercice', $annee)
            ->orderBy('date_realisation', 'desc')
            ->get();
        
        // Logique d'export selon le format
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($realisations, $annee);
            case 'csv':
                return $this->exportToCsv($realisations, $annee);
            default:
                return $this->exportToExcel($realisations, $annee);
        }
    }

    // Méthodes d'export à implémenter selon vos besoins
    private function exportToExcel($realisations, $annee)
    {
        // Implémentation avec Laravel Excel
        return response()->json(['message' => 'Export Excel à implémenter']);
    }

    private function exportToPdf($realisations, $annee)
    {
        // Implémentation avec DomPDF
        return response()->json(['message' => 'Export PDF à implémenter']);
    }

    private function exportToCsv($realisations, $annee)
    {
        // Implémentation CSV
        return response()->json(['message' => 'Export CSV à implémenter']);
    }

    public function getPrevisionsByCommune(Request $request)
{
    try {
        $communeId = $request->commune_id;
        $annee = $request->annee;
        
        // Log pour débugger
        \Log::info('Recherche prévisions', [
            'commune_id' => $communeId,
            'annee' => $annee
        ]);
        
        // Requête pour récupérer les prévisions
        $previsions = Prevision::with('commune')
            ->where('commune_id', $communeId)
            ->where('annee_exercice', $annee)
            ->get();
        
        // Log du résultat
        \Log::info('Prévisions trouvées', [
            'count' => $previsions->count(),
            'previsions' => $previsions->toArray()
        ]);
        
        return response()->json($previsions);
        
    } catch (\Exception $e) {
        \Log::error('Erreur lors de la recherche de prévisions', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json(['error' => 'Erreur serveur'], 500);
    }
}
}

