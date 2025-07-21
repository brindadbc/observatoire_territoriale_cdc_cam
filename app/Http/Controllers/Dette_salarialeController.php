<?php

namespace App\Http\Controllers;

use App\Models\dette_salariale;
use App\Models\Commune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class Dette_SalarialeController extends Controller
{
    /**
     * Affichage de la liste des dettes salariales avec filtres et recherche
     */
    public function index(Request $request)
    {
        $query = dette_salariale::with(['commune.departement.region']);
        
        // Recherche par commune
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('commune', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }
        
        // Filtrage par commune
        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->commune_id);
        }
        
        // Filtrage par département
        if ($request->filled('departement_id')) {
            $query->whereHas('commune', function($q) use ($request) {
                $q->where('departement_id', $request->departement_id);
            });
        }
        
        // Filtrage par région
        if ($request->filled('region_id')) {
            $query->whereHas('commune.departement', function($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }
        
        // Filtrage par période
        if ($request->filled('date_debut')) {
            $query->where('date_evaluation', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->where('date_evaluation', '<=', $request->date_fin);
        }
        
        // Filtrage par montant
        if ($request->filled('montant_min')) {
            $query->where('montant', '>=', $request->montant_min);
        }

        if ($request->filled('montant_max')) {
            $query->where('montant', '<=', $request->montant_max);
        }
        
        // Tri
        $sortBy = $request->get('sort_by', 'date_evaluation');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);
        
        $dettes = $query->paginate(15);
        
        // Données pour les filtres
        $communes = Commune::with('departement.region')->orderBy('nom')->get();
        $departements = \App\Models\Departement::with('region')->orderBy('nom')->get();
        $regions = \App\Models\Region::orderBy('nom')->get();
        
        // Statistiques
        $stats = [
            'total_dettes' => dette_salariale::sum('montant'),
            'nb_communes_concernees' => dette_salariale::distinct('commune_id')->count(),
            'dette_moyenne' => dette_salariale::avg('montant'),
            'dette_max' => dette_salariale::max('montant')
        ];
        
        return view('dettes-salariale.index', compact(
            'dettes', 'communes', 'departements', 'regions', 'stats'
        ));
    }

    /**
     * Affichage du formulaire de création
     */
    public function create()
    {
        $communes = Commune::with('departement.region')
            ->orderBy('nom')
            ->get();
            
        return view('dettes-salariale.create', compact('communes'));
    }

    /**
     * Enregistrement d'une nouvelle dette salariale
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commune_id' => 'required|exists:communes,id',
            'montant' => 'required|numeric|min:0|max:999999999999999999.99',
            'date_evaluation' => 'required|date|before_or_equal:today',
        ], [
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
            'montant.required' => 'Le montant de la dette est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant ne peut pas être négatif.',
            'montant.max' => 'Le montant ne peut pas dépasser 999,999,999,999,999,999.99.',
            'date_evaluation.required' => 'La date d\'évaluation est obligatoire.',
            'date_evaluation.date' => 'La date d\'évaluation doit être une date valide.',
            'date_evaluation.before_or_equal' => 'La date d\'évaluation ne peut pas être dans le futur.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            $dette = dette_salariale::create([
                'commune_id' => $request->commune_id,
                'montant' => $request->montant,
                'date_evaluation' => $request->date_evaluation,
            ]);
            
            DB::commit();
            
            return redirect()->route('dettes-salariale.show', $dette)
                ->with('success', 'Dette salariale enregistrée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement de la dette : ' . $e->getMessage());
        }
    }

    /**
     * Affichage des détails d'une dette salariale
     */
    public function show(dette_salariale $detteSalariale)
    {
        $detteSalariale->load(['commune.departement.region']);
        
        // Historique des dettes salariales de cette commune
        $historique = dette_salariale::where('commune_id', $detteSalariale->commune_id)
            ->where('id', '!=', $detteSalariale->id)
            ->orderBy('date_evaluation', 'desc')
            ->take(10)
            ->get();
        
        // Comparaison avec d'autres dettes de la commune
        $autresDettes = [
            'cnps' => \App\Models\dette_cnps::where('commune_id', $detteSalariale->commune_id)
                ->whereYear('date_evaluation', Carbon::parse($detteSalariale->date_evaluation)->year)
                ->sum('montant'),
            'feicom' => \App\Models\dette_feicom::where('commune_id', $detteSalariale->commune_id)
                ->whereYear('date_evaluation', Carbon::parse($detteSalariale->date_evaluation)->year)
                ->sum('montant'),
            'fiscale' => \App\Models\dette_fiscale::where('commune_id', $detteSalariale->commune_id)
                ->whereYear('date_evaluation', Carbon::parse($detteSalariale->date_evaluation)->year)
                ->sum('montant'),
        ];
        
        // Analyse des charges salariales
        $analyseSalariale = $this->getAnalyseSalariale($detteSalariale);
        
        return view('dettes-salariale.show', compact(
            'detteSalariale', 'historique', 'autresDettes', 'analyseSalariale'
        ));
    }

    /**
     * Affichage du formulaire de modification
     */
    public function edit(dette_salariale $detteSalariale)
    {
        $communes = Commune::with('departement.region')
            ->orderBy('nom')
            ->get();
            
        return view('dettes-salariale.edit', compact('detteSalariale', 'communes'));
    }

    /**
     * Mise à jour d'une dette salariale
     */
    public function update(Request $request, dette_salariale $detteSalariale)
    {
        $validator = Validator::make($request->all(), [
            'commune_id' => 'required|exists:communes,id',
            'montant' => 'required|numeric|min:0|max:999999999999999999.99',
            'date_evaluation' => 'required|date|before_or_equal:today',
        ], [
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
            'montant.required' => 'Le montant de la dette est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant ne peut pas être négatif.',
            'montant.max' => 'Le montant ne peut pas dépasser 999,999,999,999,999,999.99.',
            'date_evaluation.required' => 'La date d\'évaluation est obligatoire.',
            'date_evaluation.date' => 'La date d\'évaluation doit être une date valide.',
            'date_evaluation.before_or_equal' => 'La date d\'évaluation ne peut pas être dans le futur.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            $detteSalariale->update([
                'commune_id' => $request->commune_id,
                'montant' => $request->montant,
                'date_evaluation' => $request->date_evaluation,
            ]);
            
            DB::commit();
            
            return redirect()->route('dettes-salariale.show', $detteSalariale)
                ->with('success', 'Dette salariale mise à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la dette : ' . $e->getMessage());
        }
    }

    /**
     * Suppression d'une dette salariale
     */
    public function destroy(dette_salariale $detteSalariale)
    {
        try {
            DB::beginTransaction();
            
            $detteSalariale->delete();
            
            DB::commit();
            
            return redirect()->route('dettes-salariale.index')
                ->with('success', 'Dette salariale supprimée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de la dette : ' . $e->getMessage());
        }
    }

    /**
     * Tableau de bord des dettes salariales
     */
    public function dashboard(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        
        // Statistiques générales avec cache
        $stats = Cache::remember("salariale_stats_{$annee}", 3600, function () use ($annee) {
            return [
                'total_dettes' => dette_salariale::whereYear('date_evaluation', $annee)->sum('montant'),
                'nb_communes_concernees' => dette_salariale::whereYear('date_evaluation', $annee)->distinct('commune_id')->count(),
                'dette_moyenne' => dette_salariale::whereYear('date_evaluation', $annee)->avg('montant'),
                'dette_mediane' => $this->getMedianeSalariale($annee),
                'croissance_annuelle' => $this->getCroissanceSalariale($annee),
                'communes_critiques' => $this->getCommunesCritiques($annee),
                'repartition_regionale' => $this->getRepartitionRegionale($annee)
            ];
        });
        
        // Évolution annuelle (remplace évolution mensuelle)
        $evolutionAnnuelle = $this->getEvolutionAnnuelle();
        
        // Top 10 des communes les plus endettées
        $topCommunes = $this->getTopCommunesEndettees($annee);
        
        // Répartition par tranche de montant
        $repartitionTranches = $this->getRepartitionTranches($annee);
        
        // Analyse comparative avec autres types de dettes
        $comparatifDettes = $this->getComparatifDettes($annee);
        
        // Années disponibles pour le filtre
        $anneesDisponibles = dette_salariale::selectRaw('YEAR(date_evaluation) as annee')
            ->distinct()
            ->orderByDesc('annee')
            ->pluck('annee');
        
        return view('dettes-salariale.dashboard', compact(
            'stats', 'evolutionAnnuelle', 'topCommunes', 'repartitionTranches',
            'comparatifDettes', 'anneesDisponibles', 'annee'
        ));
    }

    /**
     * Export des données
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $annee = $request->get('annee', date('Y'));
        $filters = $request->except(['format', 'annee']);
        
        $query = dette_salariale::with(['commune.departement.region']);
        
        // Appliquer les filtres
        if ($annee) {
            $query->whereYear('date_evaluation', $annee);
        }
        
        if (!empty($filters['commune_id'])) {
            $query->where('commune_id', $filters['commune_id']);
        }
        
        if (!empty($filters['departement_id'])) {
            $query->whereHas('commune', function($q) use ($filters) {
                $q->where('departement_id', $filters['departement_id']);
            });
        }
        
        if (!empty($filters['region_id'])) {
            $query->whereHas('commune.departement', function($q) use ($filters) {
                $q->where('region_id', $filters['region_id']);
            });
        }
        
        $dettes = $query->orderBy('date_evaluation', 'desc')->get();
        
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($dettes, $annee);
            case 'csv':
                return $this->exportToCsv($dettes, $annee);
            default:
                return $this->exportToExcel($dettes, $annee);
        }
    }

    /**
     * API pour les données de graphiques
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type', 'evolution');
        $annee = $request->get('annee', date('Y'));
        
        switch ($type) {
            case 'evolution':
                return response()->json($this->getEvolutionAnnuelle());
            case 'repartition':
                return response()->json($this->getRepartitionRegionale($annee));
            case 'tranches':
                return response()->json($this->getRepartitionTranches($annee));
            case 'comparatif':
                return response()->json($this->getComparatifDettes($annee));
            default:
                return response()->json(['error' => 'Type de graphique non supporté'], 400);
        }
    }

    // ================== MÉTHODES PRIVÉES ==================

    /**
     * Analyse spécifique aux dettes salariales
     */
    private function getAnalyseSalariale($detteSalariale)
    {
        $commune = $detteSalariale->commune;
        $annee = Carbon::parse($detteSalariale->date_evaluation)->year;
        
        // Calculer le ratio dette/personnel estimé
        $effectifEstime = $this->estimeEffectifCommune($commune);
        $detteParAgent = $effectifEstime > 0 ? $detteSalariale->montant / $effectifEstime : 0;
        
        // Évolution sur 3 ans
        $evolutionTroisAns = dette_salariale::where('commune_id', $commune->id)
            ->whereYear('date_evaluation', '>=', $annee - 2)
            ->whereYear('date_evaluation', '<=', $annee)
            ->orderBy('date_evaluation')
            ->get();
        
        // Tendance
        $tendance = $this->calculerTendance($evolutionTroisAns);
        
        // Comparaison avec communes similaires
        $communesSimilaires = $this->getCommunesSimilaires($commune, $annee);
        
        return [
            'effectif_estime' => $effectifEstime,
            'dette_par_agent' => $detteParAgent,
            'evolution_trois_ans' => $evolutionTroisAns,
            'tendance' => $tendance,
            'communes_similaires' => $communesSimilaires,
            'niveau_criticite' => $this->evaluerCriticite($detteSalariale),
            'recommandations' => $this->genererRecommandations($detteSalariale)
        ];
    }

    /**
     * Estimation de l'effectif d'une commune
     */
    private function estimeEffectifCommune($commune)
    {
        // Estimation basée sur la taille de la commune
        // À adapter selon vos critères
        $baseEffectif = 10; // Effectif de base
        
        // Vous pouvez affiner selon d'autres critères
        // comme la population, le budget, etc.
        
        return $baseEffectif;
    }

    /**
     * Calculer la tendance d'évolution
     */
    private function calculerTendance($evolution)
    {
        if ($evolution->count() < 2) {
            return 'stable';
        }
        
        $premier = $evolution->first()->montant;
        $dernier = $evolution->last()->montant;
        
        $variation = (($dernier - $premier) / $premier) * 100;
        
        if ($variation > 10) return 'hausse';
        if ($variation < -10) return 'baisse';
        return 'stable';
    }

    /**
     * Obtenir les communes similaires
     */
    private function getCommunesSimilaires($commune, $annee)
    {
        return dette_salariale::whereHas('commune', function($q) use ($commune) {
            $q->where('departement_id', $commune->departement_id)
              ->where('id', '!=', $commune->id);
        })
        ->whereYear('date_evaluation', $annee)
        ->with('commune')
        ->orderBy('montant', 'desc')
        ->take(5)
        ->get();
    }

    /**
     * Évaluer le niveau de criticité
     */
    private function evaluerCriticite($detteSalariale)
    {
        $montant = $detteSalariale->montant;
        
        if ($montant >= 50000000) return 'critique';
        if ($montant >= 10000000) return 'élevé';
        if ($montant >= 1000000) return 'moyen';
        return 'faible';
    }

    /**
     * Générer des recommandations
     */
    private function genererRecommandations($detteSalariale)
    {
        $recommandations = [];
        $niveau = $this->evaluerCriticite($detteSalariale);
        
        switch ($niveau) {
            case 'critique':
                $recommandations[] = 'Mise en place urgente d\'un plan de redressement';
                $recommandations[] = 'Négociation d\'un échéancier avec les créanciers';
                $recommandations[] = 'Audit complet de la masse salariale';
                break;
            case 'élevé':
                $recommandations[] = 'Révision de la politique salariale';
                $recommandations[] = 'Optimisation des effectifs';
                break;
            case 'moyen':
                $recommandations[] = 'Surveillance renforcée';
                $recommandations[] = 'Amélioration des procédures de paiement';
                break;
            default:
                $recommandations[] = 'Maintenir la vigilance';
        }
        
        return $recommandations;
    }

    /**
     * Obtenir la médiane des dettes salariales
     */
    private function getMedianeSalariale($annee)
    {
        $montants = dette_salariale::whereYear('date_evaluation', $annee)
            ->orderBy('montant')
            ->pluck('montant')
            ->toArray();
        
        $count = count($montants);
        if ($count === 0) return 0;
        
        $middle = floor($count / 2);
        
        if ($count % 2 === 1) {
            return $montants[$middle];
        } else {
            return ($montants[$middle - 1] + $montants[$middle]) / 2;
        }
    }

    /**
     * Calculer la croissance annuelle
     */
    private function getCroissanceSalariale($annee)
    {
        $anneeActuelle = dette_salariale::whereYear('date_evaluation', $annee)->sum('montant');
        $anneePrecedente = dette_salariale::whereYear('date_evaluation', $annee - 1)->sum('montant');
        
        if ($anneePrecedente == 0) return 0;
        
        return round((($anneeActuelle - $anneePrecedente) / $anneePrecedente) * 100, 2);
    }

    /**
     * Identifier les communes critiques
     */
    private function getCommunesCritiques($annee)
    {
        return dette_salariale::whereYear('date_evaluation', $annee)
            ->where('montant', '>=', 10000000) // Seuil critique
            ->distinct('commune_id')
            ->count();
    }

    /**
     * Répartition régionale
     */
    private function getRepartitionRegionale($annee)
    {
        return DB::table('dette_salariales')
            ->join('communes', 'dette_salariales.commune_id', '=', 'communes.id')
            ->join('departements', 'communes.departement_id', '=', 'departements.id')
            ->join('regions', 'departements.region_id', '=', 'regions.id')
            ->whereYear('dette_salariales.date_evaluation', $annee)
            ->select('regions.nom as region', DB::raw('SUM(dette_salariales.montant) as total'))
            ->groupBy('regions.nom')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Évolution annuelle (remplace l'évolution mensuelle)
     */
    private function getEvolutionAnnuelle()
    {
        return DB::table('dette_salariales')
            ->selectRaw('YEAR(date_evaluation) as annee, SUM(montant) as total, COUNT(*) as nombre')
            ->groupBy(DB::raw('YEAR(date_evaluation)'))
            ->orderBy('annee')
            ->get();
    }

    /**
     * Top communes endettées
     */
    private function getTopCommunesEndettees($annee)
    {
        return dette_salariale::with(['commune.departement.region'])
            ->whereYear('date_evaluation', $annee)
            ->orderByDesc('montant')
            ->take(10)
            ->get();
    }

    /**
     * Répartition par tranches
     */
    private function getRepartitionTranches($annee)
    {
        $tranches = [
            '0-1M' => [0, 1000000],
            '1M-5M' => [1000000, 5000000],
            '5M-10M' => [5000000, 10000000],
            '10M-50M' => [10000000, 50000000],
            '50M+' => [50000000, PHP_INT_MAX]
        ];
        
        $repartition = [];
        
        foreach ($tranches as $label => $range) {
            $count = dette_salariale::whereYear('date_evaluation', $annee)
                ->whereBetween('montant', $range)
                ->count();
            
            $repartition[] = [
                'tranche' => $label,
                'nombre' => $count
            ];
        }
        
        return $repartition;
    }

    /**
     * Comparatif avec autres types de dettes
     */
    private function getComparatifDettes($annee)
    {
        return [
            'salariale' => dette_salariale::whereYear('date_evaluation', $annee)->sum('montant'),
            'cnps' => \App\Models\dette_cnps::whereYear('date_evaluation', $annee)->sum('montant'),
            'fiscale' => \App\Models\dette_fiscale::whereYear('date_evaluation', $annee)->sum('montant'),
            'feicom' => \App\Models\dette_feicom::whereYear('date_evaluation', $annee)->sum('montant')
        ];
    }

    /**
     * Export vers Excel
     */
    private function exportToExcel($dettes, $annee)
    {
        $filename = "dettes_salariales_{$annee}.xlsx";
        
        // Implémentation avec Laravel Excel
        // return Excel::download(new DetteSalarialeExport($dettes), $filename);
        
        // Pour l'instant, retourner un message
        return response()->json(['message' => 'Export Excel à implémenter avec Laravel Excel']);
    }

    /**
     * Export vers PDF
     */
    private function exportToPdf($dettes, $annee)
    {
        $filename = "dettes_salariales_{$annee}.pdf";
        
        // Implémentation avec DomPDF
        // $pdf = PDF::loadView('dettes-salariale.export-pdf', compact('dettes', 'annee'));
        // return $pdf->download($filename);
        
        return response()->json(['message' => 'Export PDF à implémenter']);
    }

    /**
     * Export vers CSV
     */
    private function exportToCsv($dettes, $annee)
    {
        $filename = "dettes_salariales_{$annee}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($dettes) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, [
                'ID', 'Commune', 'Département', 'Région', 'Montant', 'Date Évaluation'
            ]);
            
            // Données
            foreach ($dettes as $dette) {
                fputcsv($file, [
                    $dette->id,
                    $dette->commune->nom,
                    $dette->commune->departement->nom,
                    $dette->commune->departement->region->nom,
                    $dette->montant,
                    $dette->date_evaluation
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}