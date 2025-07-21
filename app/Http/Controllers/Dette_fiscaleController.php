<?php

namespace App\Http\Controllers;

use App\Models\dette_fiscale;
use App\Models\Commune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class Dette_FiscaleController extends Controller
{
    /**
     * Affichage de la liste des dettes fiscales avec filtres et recherche
     */
    public function index(Request $request)
    {
        $query = dette_fiscale::with(['commune.departement.region']);
        
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
            'total_dettes' => dette_fiscale::sum('montant'),
            'nb_communes_concernees' => dette_fiscale::distinct('commune_id')->count(),
            'dette_moyenne' => dette_fiscale::avg('montant'),
            'dette_max' => dette_fiscale::max('montant')
        ];
        
        return view('dettes-fiscale.index', compact(
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
            
        return view('dettes-fiscale.create', compact('communes'));
    }

    /**
     * Enregistrement d'une nouvelle dette fiscale
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
            
            $dette = dette_fiscale::create([
                'commune_id' => $request->commune_id,
                'montant' => $request->montant,
                'date_evaluation' => $request->date_evaluation,
            ]);
            
            DB::commit();
            
            return redirect()->route('dettes-fiscale.show', $dette)
                ->with('success', 'Dette fiscale enregistrée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement de la dette : ' . $e->getMessage());
        }
    }

    /**
     * Affichage des détails d'une dette fiscale
     */
    public function show(dette_fiscale $detteFiscale)
    {
        $detteFiscale->load(['commune.departement.region']);
        
        // Historique des dettes fiscales de cette commune
        $historique = dette_fiscale::where('commune_id', $detteFiscale->commune_id)
            ->where('id', '!=', $detteFiscale->id)
            ->orderBy('date_evaluation', 'desc')
            ->take(10)
            ->get();
        
        // Comparaison avec d'autres dettes de la commune
        $autresDettes = [
            'cnps' => \App\Models\dette_cnps::where('commune_id', $detteFiscale->commune_id)
                ->whereYear('date_evaluation', Carbon::parse($detteFiscale->date_evaluation)->year)
                ->sum('montant'),
            'feicom' => \App\Models\dette_feicom::where('commune_id', $detteFiscale->commune_id)
                ->whereYear('date_evaluation', Carbon::parse($detteFiscale->date_evaluation)->year)
                ->sum('montant'),
            'salariale' => \App\Models\dette_salariale::where('commune_id', $detteFiscale->commune_id)
                ->whereYear('date_evaluation', Carbon::parse($detteFiscale->date_evaluation)->year)
                ->sum('montant'),
        ];
        
        return view('dettes-fiscale.show', compact(
            'detteFiscale', 'historique', 'autresDettes'
        ));
    }

    /**
     * Affichage du formulaire de modification
     */
    public function edit(dette_fiscale $detteFiscale)
    {
        $communes = Commune::with('departement.region')
            ->orderBy('nom')
            ->get();
            
        return view('dettes-fiscale.edit', compact('detteFiscale', 'communes'));
    }

    /**
     * Mise à jour d'une dette fiscale
     */
    public function update(Request $request, dette_fiscale $detteFiscale)
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
            
            $detteFiscale->update([
                'commune_id' => $request->commune_id,
                'montant' => $request->montant,
                'date_evaluation' => $request->date_evaluation,
            ]);
            
            DB::commit();
            
            return redirect()->route('dettes-fiscale.show', $detteFiscale)
                ->with('success', 'Dette fiscale mise à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la dette : ' . $e->getMessage());
        }
    }

    /**
     * Suppression d'une dette fiscale
     */
    public function destroy(dette_fiscale $detteFiscale)
    {
        try {
            DB::beginTransaction();
            
            $detteFiscale->delete();
            
            DB::commit();
            
            return redirect()->route('dettes-fiscale.index')
                ->with('success', 'Dette fiscale supprimée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de la dette : ' . $e->getMessage());
        }
    }

    /**
     * Tableau de bord des dettes fiscales
     */
    public function dashboard(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        
        // Statistiques générales avec cache
        $stats = Cache::remember("fiscale_stats_{$annee}", 3600, function () use ($annee) {
            return [
                'total_dettes' => dette_fiscale::whereYear('date_evaluation', $annee)->sum('montant'),
                'nb_communes_concernees' => dette_fiscale::whereYear('date_evaluation', $annee)->distinct('commune_id')->count(),
                'dette_moyenne' => dette_fiscale::whereYear('date_evaluation', $annee)->avg('montant') ?? 0,
                'dette_max' => dette_fiscale::whereYear('date_evaluation', $annee)->max('montant') ?? 0,
            ];
        });
        
        // Évolution annuelle (au lieu de mensuelle)
        $evolutionAnnuelle = dette_fiscale::selectRaw('YEAR(date_evaluation) as annee, SUM(montant) as total, COUNT(DISTINCT commune_id) as nb_communes')
            ->where('date_evaluation', '>=', Carbon::now()->subYears(10))
            ->groupBy('annee')
            ->orderBy('annee')
            ->get();
        
        // Top 10 des communes avec le plus de dettes
        $topCommunes = dette_fiscale::with('commune')
            ->whereYear('date_evaluation', $annee)
            ->selectRaw('commune_id, SUM(montant) as total_dette')
            ->groupBy('commune_id')
            ->orderByDesc('total_dette')
            ->take(10)
            ->get();
        
        // Répartition par région
        $repartitionRegions = dette_fiscale::with(['commune.departement.region'])
            ->whereYear('date_evaluation', $annee)
            ->get()
            ->groupBy('commune.departement.region.nom')
            ->map(function($dettes, $region) {
                return [
                    'region' => $region,
                    'total' => $dettes->sum('montant'),
                    'nb_communes' => $dettes->pluck('commune_id')->unique()->count()
                ];
            });
        
        // Années disponibles pour le filtre
        $anneesDisponibles = dette_fiscale::selectRaw('YEAR(date_evaluation) as annee')
            ->distinct()
            ->orderByDesc('annee')
            ->pluck('annee');
        
        return view('dettes-fiscale.dashboard', compact(
            'stats', 'evolutionAnnuelle', 'topCommunes', 'repartitionRegions', 'annee', 'anneesDisponibles'
        ));
    }

    /**
     * Rapport comparatif des dettes fiscales
     */
    public function rapportComparatif(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $anneePrecedente = $annee - 1;
        
        // Comparaison avec l'année précédente
        $donneesAnneeActuelle = dette_fiscale::whereYear('date_evaluation', $annee)
            ->selectRaw('SUM(montant) as total, COUNT(*) as nb_dettes, COUNT(DISTINCT commune_id) as nb_communes')
            ->first();
            
        $donneesAnneePrecedente = dette_fiscale::whereYear('date_evaluation', $anneePrecedente)
            ->selectRaw('SUM(montant) as total, COUNT(*) as nb_dettes, COUNT(DISTINCT commune_id) as nb_communes')
            ->first();
        
        $comparaison = [
            'evolution_montant' => $this->calculateEvolution($donneesAnneePrecedente->total ?? 0, $donneesAnneeActuelle->total ?? 0),
            'evolution_nb_dettes' => $this->calculateEvolution($donneesAnneePrecedente->nb_dettes ?? 0, $donneesAnneeActuelle->nb_dettes ?? 0),
            'evolution_nb_communes' => $this->calculateEvolution($donneesAnneePrecedente->nb_communes ?? 0, $donneesAnneeActuelle->nb_communes ?? 0),
        ];

        // Évolution sur 5 ans
        $evolutionCinqAns = dette_fiscale::selectRaw('YEAR(date_evaluation) as annee, SUM(montant) as total, COUNT(DISTINCT commune_id) as nb_communes')
            ->where('date_evaluation', '>=', Carbon::now()->subYears(5))
            ->groupBy('annee')
            ->orderBy('annee')
            ->get();

        // Analyse par département
        $analyseDepartements = dette_fiscale::with(['commune.departement'])
            ->whereYear('date_evaluation', $annee)
            ->get()
            ->groupBy('commune.departement.nom')
            ->map(function($dettes, $departement) {
                return [
                    'departement' => $departement,
                    'total' => $dettes->sum('montant'),
                    'nb_communes' => $dettes->pluck('commune_id')->unique()->count(),
                    'moyenne' => $dettes->avg('montant')
                ];
            })
            ->sortByDesc('total');

        return view('dettes-fiscale.rapport-comparatif', compact(
            'comparaison', 'evolutionCinqAns', 'analyseDepartements', 'annee', 'anneePrecedente'
        ));
    }

    /**
     * Export des données comparatives
     */
    public function exportComparatif(Request $request)
    {
        $format = $request->get('format', 'excel');
        $annee = $request->get('annee', date('Y'));
        
        // Récupération des données pour l'export
        $comparaison = $this->getComparaisonData($annee);
        $evolutionCinqAns = $this->getEvolutionCinqAns();
        $analyseDepartements = $this->getAnalyseDepartements($annee);

        switch ($format) {
            case 'pdf':
                return $this->exportComparatifToPdf($comparaison, $evolutionCinqAns, $analyseDepartements, $annee);
            case 'csv':
                return $this->exportComparatifToCsv($comparaison, $evolutionCinqAns, $analyseDepartements, $annee);
            default:
                return $this->exportComparatifToExcel($comparaison, $evolutionCinqAns, $analyseDepartements, $annee);
        }
    }

    /**
     * Export des données
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $annee = $request->get('annee', date('Y'));
        
        $dettes = dette_fiscale::with(['commune.departement.region'])
            ->whereYear('date_evaluation', $annee)
            ->orderBy('date_evaluation', 'desc')
            ->get();

        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($dettes, $annee);
            case 'csv':
                return $this->exportToCsv($dettes, $annee);
            default:
                return $this->exportToExcel($dettes, $annee);
        }
    }

    // ================== MÉTHODES PRIVÉES ==================

    /**
     * Calculer l'évolution en pourcentage
     */
    private function calculateEvolution($ancienneValeur, $nouvelleValeur)
    {
        if ($ancienneValeur == 0) {
            return $nouvelleValeur > 0 ? 100 : 0;
        }
        
        return round((($nouvelleValeur - $ancienneValeur) / $ancienneValeur) * 100, 2);
    }

    /**
     * Récupérer les données de comparaison
     */
    private function getComparaisonData($annee)
    {
        $anneePrecedente = $annee - 1;
        
        $donneesAnneeActuelle = dette_fiscale::whereYear('date_evaluation', $annee)
            ->selectRaw('SUM(montant) as total, COUNT(*) as nb_dettes, COUNT(DISTINCT commune_id) as nb_communes')
            ->first();
            
        $donneesAnneePrecedente = dette_fiscale::whereYear('date_evaluation', $anneePrecedente)
            ->selectRaw('SUM(montant) as total, COUNT(*) as nb_dettes, COUNT(DISTINCT commune_id) as nb_communes')
            ->first();
        
        return [
            'evolution_montant' => $this->calculateEvolution($donneesAnneePrecedente->total ?? 0, $donneesAnneeActuelle->total ?? 0),
            'evolution_nb_dettes' => $this->calculateEvolution($donneesAnneePrecedente->nb_dettes ?? 0, $donneesAnneeActuelle->nb_dettes ?? 0),
            'evolution_nb_communes' => $this->calculateEvolution($donneesAnneePrecedente->nb_communes ?? 0, $donneesAnneeActuelle->nb_communes ?? 0),
        ];
    }

    /**
     * Récupérer l'évolution sur 5 ans
     */
    private function getEvolutionCinqAns()
    {
        return dette_fiscale::selectRaw('YEAR(date_evaluation) as annee, SUM(montant) as total, COUNT(DISTINCT commune_id) as nb_communes')
            ->where('date_evaluation', '>=', Carbon::now()->subYears(5))
            ->groupBy('annee')
            ->orderBy('annee')
            ->get();
    }

    /**
     * Récupérer l'analyse par département
     */
    private function getAnalyseDepartements($annee)
    {
        return dette_fiscale::with(['commune.departement'])
            ->whereYear('date_evaluation', $annee)
            ->get()
            ->groupBy('commune.departement.nom')
            ->map(function($dettes, $departement) {
                return [
                    'departement' => $departement,
                    'total' => $dettes->sum('montant'),
                    'nb_communes' => $dettes->pluck('commune_id')->unique()->count(),
                    'moyenne' => $dettes->avg('montant')
                ];
            })
            ->sortByDesc('total');
    }

    /**
     * Export comparatif vers Excel
     */
    private function exportComparatifToExcel($comparaison, $evolutionCinqAns, $analyseDepartements, $annee)
    {
        // À implémenter avec Laravel Excel
        return response()->json(['message' => 'Export Excel du rapport comparatif à implémenter']);
    }

    /**
     * Export comparatif vers PDF
     */
    private function exportComparatifToPdf($comparaison, $evolutionCinqAns, $analyseDepartements, $annee)
    {
        // À implémenter avec DomPDF
        return response()->json(['message' => 'Export PDF du rapport comparatif à implémenter']);
    }

    /**
     * Export comparatif vers CSV
     */
    private function exportComparatifToCsv($comparaison, $evolutionCinqAns, $analyseDepartements, $annee)
    {
        // À implémenter
        return response()->json(['message' => 'Export CSV du rapport comparatif à implémenter']);
    }

    /**
     * Export vers Excel
     */
    private function exportToExcel($dettes, $annee)
    {
        // À implémenter avec Laravel Excel
        return response()->json(['message' => 'Export Excel des dettes fiscales à implémenter']);
    }

    /**
     * Export vers PDF
     */
    private function exportToPdf($dettes, $annee)
    {
        // À implémenter avec DomPDF
        return response()->json(['message' => 'Export PDF des dettes fiscales à implémenter']);
    }

    /**
     * Export vers CSV
     */
    private function exportToCsv($dettes, $annee)
    {
        // À implémenter
        return response()->json(['message' => 'Export CSV des dettes fiscales à implémenter']);
    }
}