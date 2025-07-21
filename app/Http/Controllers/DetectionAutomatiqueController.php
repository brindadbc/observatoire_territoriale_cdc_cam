<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DetectionAutomatiqueService;
use App\Models\Commune;
use App\Models\Retard;
use App\Models\Defaillance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DetectionAutomatiqueController extends Controller
{
    private $detectionService;

    public function __construct(DetectionAutomatiqueService $detectionService)
    {
        $this->detectionService = $detectionService;
    }

    /**
     * Afficher le tableau de bord de détection
     */
    public function index()
    {
        $annee = request('annee', date('Y'));
        
        // Statistiques générales
        $stats = [
            'total_communes' => Commune::count(),
            'retards_annee' => Retard::whereYear('date_constat', $annee)->count(),
            'defaillances_annee' => Defaillance::whereYear('date_constat', $annee)->count(),
            'defaillances_non_resolues' => Defaillance::whereYear('date_constat', $annee)
                ->where('est_resolue', false)->count()
        ];

        // Dernières détections
        $derniersRetards = Retard::with('commune')
            ->whereYear('date_constat', $annee)
            ->orderBy('date_constat', 'desc')
            ->limit(10)
            ->get();

        $dernieresDefaillances = Defaillance::with('commune')
            ->whereYear('date_constat', $annee)
            ->orderBy('date_constat', 'desc')
            ->limit(10)
            ->get();

        // Communes les plus problématiques
        $communesProblematiques = Commune::withCount([
            'retards' => function($query) use ($annee) {
                $query->whereYear('date_constat', $annee);
            },
            'defaillances' => function($query) use ($annee) {
                $query->whereYear('date_constat', $annee);
            }
        ])
        ->having('retards_count', '>', 0)
        ->orHaving('defaillances_count', '>', 0)
        ->orderByRaw('(retards_count + defaillances_count) DESC')
        ->limit(10)
        ->get();

        return view('detection.index', compact(
            'stats', 'derniersRetards', 'dernieresDefaillances', 
            'communesProblematiques', 'annee'
        ));
    }

    /**
     * Lancer la détection complète
     */
    public function lancerDetectionComplete(Request $request)
    {
        $annee = $request->get('annee', date('Y'));

        try {
            set_time_limit(300); // 5 minutes max
            
            $resultats = $this->detectionService->detecterToutesAnomalies($annee);
            
            return response()->json([
                'success' => true,
                'message' => "Détection terminée avec succès pour l'année {$annee}",
                'data' => $resultats
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur détection complète", ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la détection : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Détection spécifique par type
     */
    public function detecterParType(Request $request)
    {
        $type = $request->get('type');
        $annee = $request->get