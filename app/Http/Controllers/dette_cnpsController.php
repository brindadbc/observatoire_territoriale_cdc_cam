<?php

namespace App\Http\Controllers;

use App\Models\dette_cnps;
use App\Models\Commune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Dette_CnpsController extends Controller
{
    /**
     * Affichage de la liste des dettes CNPS avec filtres et pagination
     */
    public function index(Request $request)
    {
        $query = dette_cnps::with(['commune.departement.region']);
        
        // Recherche par commune
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('commune', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }
        
        // Filtrage par région
        if ($request->filled('region_id')) {
            $query->whereHas('commune.departement', function($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }
        
        // Filtrage par département
        if ($request->filled('departement_id')) {
            $query->whereHas('commune', function($q) use ($request) {
                $q->where('departement_id', $request->departement_id);
            });
        }
        
        // Filtrage par année
        if ($request->filled('annee')) {
            $query->whereYear('date_evaluation', $request->annee);
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
        
        $dettes = $query->paginate(20);
        
        // Statistiques générales
        $stats = $this->getStatistiquesGenerales($request);
        
        // Données pour les filtres
        $regions = \App\Models\Region::orderBy('nom')->get();
        $departements = \App\Models\Departement::orderBy('nom')->get();
        $annees = dette_cnps::selectRaw('YEAR(date_evaluation) as annee')
            ->distinct()
            ->orderByDesc('annee')
            ->pluck('annee');
        
        return view('dettes-cnps.index', compact(
            'dettes', 'stats', 'regions', 'departements', 'annees'
        ));
    }

    /**
     * Affichage du formulaire de création
     */
    public function create()
    {
        $communes = Commune::with(['departement.region'])
            ->orderBy('nom')
            ->get();
            
        return view('dettes-cnps.create', compact('communes'));
    }

    /**
     * Enregistrement d'une nouvelle dette CNPS
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'montant' => 'required|numeric|min:0|max:999999999999999999999', // Jusqu'à 21 chiffres (quintillions)
            'date_evaluation' => 'required|date',
            'commune_id' => 'required|exists:communes,id',
            'description' => 'nullable|string|max:1000'
        ], [
            'montant.required' => 'Le montant est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre valide.',
            'montant.min' => 'Le montant ne peut pas être négatif.',
            'montant.max' => 'Le montant ne peut pas dépasser 999 quintillions de FCFA.',
            'date_evaluation.required' => 'La date d\'évaluation est obligatoire.',
            'date_evaluation.date' => 'Format de date invalide.',
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Conversion du montant en string pour préserver la précision
            $montant = $this->formatMontantPourBD($request->montant);

            $dette = dette_cnps::create([
                'montant' => $montant,
                'date_evaluation' => $request->date_evaluation,
                'commune_id' => $request->commune_id,
                'description' => $request->description
            ]);

            DB::commit();

            return redirect()->route('dettes-cnps.index')
                ->with('success', 'Dette CNPS de ' . $this->formatMontantAffichage($montant) . ' FCFA enregistrée avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Affichage des détails d'une dette CNPS
     */
    public function show(dette_cnps $detteCnps)
    {
        $detteCnps->load(['commune.departement.region']);
        
        // Historique des dettes CNPS pour cette commune
        $historique = dette_cnps::where('commune_id', $detteCnps->commune_id)
            ->where('id', '!=', $detteCnps->id)
            ->orderByDesc('date_evaluation')
            ->limit(10)
            ->get();
        
        // Évolution des dettes pour cette commune
        $evolution = $this->getEvolutionDettes($detteCnps->commune_id);
        
        return view('dettes-cnps.show', compact('detteCnps', 'historique', 'evolution'));
    }

    /**
     * Affichage du formulaire de modification
     */
    public function edit(dette_cnps $detteCnps)
    {
        $communes = Commune::with(['departement.region'])
            ->orderBy('nom')
            ->get();
            
        return view('dettes-cnps.edit', compact('detteCnps', 'communes'));
    }

    /**
     * Mise à jour d'une dette CNPS
     */
    public function update(Request $request, dette_cnps $detteCnps)
    {
        $validator = Validator::make($request->all(), [
            'montant' => 'required|numeric|min:0|max:999999999999999999999', // Jusqu'à 21 chiffres
            'date_evaluation' => 'required|date',
            'commune_id' => 'required|exists:communes,id',
            'description' => 'nullable|string|max:1000'
        ], [
            'montant.required' => 'Le montant est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre valide.',
            'montant.min' => 'Le montant ne peut pas être négatif.',
            'montant.max' => 'Le montant ne peut pas dépasser 999 quintillions de FCFA.',
            'date_evaluation.required' => 'La date d\'évaluation est obligatoire.',
            'date_evaluation.date' => 'Format de date invalide.',
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Conversion du montant en string pour préserver la précision
            $montant = $this->formatMontantPourBD($request->montant);

            $detteCnps->update([
                'montant' => $montant,
                'date_evaluation' => $request->date_evaluation,
                'commune_id' => $request->commune_id,
                'description' => $request->description
            ]);

            DB::commit();

            return redirect()->route('dettes-cnps.show', $detteCnps)
                ->with('success', 'Dette CNPS mise à jour avec succès. Nouveau montant: ' . $this->formatMontantAffichage($montant) . ' FCFA.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Suppression d'une dette CNPS
     */
    public function destroy(dette_cnps $detteCnps)
    {
        try {
            DB::beginTransaction();

            $montant = $this->formatMontantAffichage($detteCnps->montant);
            $detteCnps->delete();

            DB::commit();

            return redirect()->route('dettes-cnps.index')
                ->with('success', 'Dette CNPS de ' . $montant . ' FCFA supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Export des dettes CNPS
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel'); // excel, pdf, csv
        
        $query = dette_cnps::with(['commune.departement.region']);
        
        // Appliquer les mêmes filtres que dans index()
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('commune', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('region_id')) {
            $query->whereHas('commune.departement', function($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }
        
        if ($request->filled('departement_id')) {
            $query->whereHas('commune', function($q) use ($request) {
                $q->where('departement_id', $request->departement_id);
            });
        }
        
        if ($request->filled('annee')) {
            $query->whereYear('date_evaluation', $request->annee);
        }
        
        $dettes = $query->orderByDesc('date_evaluation')->get();
        
        // Calcul des totaux avec précision
        $totalMontant = $this->calculerSommeTotale($dettes);
        
        $data = [
            'dettes' => $dettes,
            'total' => $totalMontant,
            'total_formate' => $this->formatMontantAffichage($totalMontant),
            'count' => $dettes->count(),
            'date_export' => now()->format('d/m/Y H:i')
        ];

        // Logique d'export selon le format
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($data);
            case 'csv':
                return $this->exportToCsv($data);
            default:
                return $this->exportToExcel($data);
        }
    }

    /**
     * Rapport statistique des dettes CNPS
     */
    public function rapport(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        
        $rapport = [
            'global' => $this->getStatistiquesGlobales($annee),
            'par_region' => $this->getStatistiquesParRegion($annee),
            'par_departement' => $this->getStatistiquesParDepartement($annee),
            'evolution_annuelle' => $this->getEvolutionAnnuelle(),
            'top_communes' => $this->getTopCommunesDettesCnps($annee)
        ];
        
        $annees = dette_cnps::selectRaw('YEAR(date_evaluation) as annee')
            ->distinct()
            ->orderByDesc('annee')
            ->pluck('annee');
        
        return view('dettes-cnps.rapport', compact('rapport', 'annee', 'annees'));
    }

    // =================== MÉTHODES PRIVÉES ===================

    /**
     * Format le montant pour la base de données (préserve la précision)
     */
    private function formatMontantPourBD($montant)
    {
        // Supprime les espaces et convertit en string pour préserver la précision
        return str_replace([' ', ','], ['', '.'], (string)$montant);
    }

    /**
     * Format le montant pour l'affichage (avec séparateurs de milliers)
     */
    private function formatMontantAffichage($montant)
    {
        if (is_numeric($montant)) {
            return number_format((float)$montant, 0, ',', ' ');
        }
        return $montant;
    }

    /**
     * Calcule la somme totale des montants avec précision
     */
    private function calculerSommeTotale($dettes)
    {
        $total = 0;
        foreach ($dettes as $dette) {
            $total = bcadd($total, $dette->montant, 2);
        }
        return $total;
    }

    /**
     * Statistiques générales pour la page index
     */
    private function getStatistiquesGenerales($request)
    {
        $query = dette_cnps::query();
        
        // Appliquer les mêmes filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('commune', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('region_id')) {
            $query->whereHas('commune.departement', function($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }
        
        if ($request->filled('departement_id')) {
            $query->whereHas('commune', function($q) use ($request) {
                $q->where('departement_id', $request->departement_id);
            });
        }
        
        if ($request->filled('annee')) {
            $query->whereYear('date_evaluation', $request->annee);
        }
        
        $dettes = $query->get();
        $totalMontant = $this->calculerSommeTotale($dettes);
        
        return [
            'total_montant' => $totalMontant,
            'total_montant_formate' => $this->formatMontantAffichage($totalMontant),
            'nombre_dettes' => $dettes->count(),
            'montant_moyen' => $dettes->count() > 0 ? bcdiv($totalMontant, $dettes->count(), 2) : 0,
            'montant_moyen_formate' => $this->formatMontantAffichage($dettes->count() > 0 ? bcdiv($totalMontant, $dettes->count(), 2) : 0),
            'communes_concernees' => $query->distinct('commune_id')->count('commune_id')
        ];
    }

    /**
     * Évolution des dettes pour une commune
     */
    private function getEvolutionDettes($communeId)
    {
        $evolution = dette_cnps::where('commune_id', $communeId)
            ->selectRaw('YEAR(date_evaluation) as annee')
            ->orderBy('annee')
            ->get()
            ->groupBy('annee');

        $result = [];
        foreach ($evolution as $annee => $dettes) {
            $total = $this->calculerSommeTotale($dettes);
            $result[] = (object)[
                'annee' => $annee,
                'total' => $total,
                'total_formate' => $this->formatMontantAffichage($total)
            ];
        }

        return collect($result);
    }

    /**
     * Statistiques globales pour une année
     */
    private function getStatistiquesGlobales($annee)
    {
        $dettes = dette_cnps::whereYear('date_evaluation', $annee)->get();
        $totalMontant = $this->calculerSommeTotale($dettes);
        
        $montants = $dettes->pluck('montant')->map(function($m) { return (float)$m; });
        
        return [
            'total_montant' => $totalMontant,
            'total_montant_formate' => $this->formatMontantAffichage($totalMontant),
            'nombre_dettes' => $dettes->count(),
            'montant_moyen' => $dettes->count() > 0 ? bcdiv($totalMontant, $dettes->count(), 2) : 0,
            'montant_moyen_formate' => $this->formatMontantAffichage($dettes->count() > 0 ? bcdiv($totalMontant, $dettes->count(), 2) : 0),
            'communes_concernees' => $dettes->unique('commune_id')->count(),
            'montant_max' => $montants->count() > 0 ? $this->formatMontantAffichage($montants->max()) : 0,
            'montant_min' => $montants->count() > 0 ? $this->formatMontantAffichage($montants->min()) : 0
        ];
    }

    /**
     * Statistiques par région
     */
    private function getStatistiquesParRegion($annee)
    {
        $results = DB::table('dette_cnps')
            ->join('communes', 'dette_cnps.commune_id', '=', 'communes.id')
            ->join('departements', 'communes.departement_id', '=', 'departements.id')
            ->join('regions', 'departements.region_id', '=', 'regions.id')
            ->whereYear('dette_cnps.date_evaluation', $annee)
            ->select([
                'regions.nom as region',
                'regions.id as region_id',
                DB::raw('COUNT(dette_cnps.id) as nombre_dettes'),
                DB::raw('COUNT(DISTINCT dette_cnps.commune_id) as communes_concernees')
            ])
            ->groupBy('regions.id', 'regions.nom')
            ->get();

        // Calcul des totaux par région avec précision
        foreach ($results as $result) {
            $dettesRegion = DB::table('dette_cnps')
                ->join('communes', 'dette_cnps.commune_id', '=', 'communes.id')
                ->join('departements', 'communes.departement_id', '=', 'departements.id')
                ->whereYear('dette_cnps.date_evaluation', $annee)
                ->where('departements.region_id', $result->region_id)
                ->pluck('dette_cnps.montant');

            $totalMontant = '0';
            foreach ($dettesRegion as $montant) {
                $totalMontant = bcadd($totalMontant, $montant, 2);
            }

            $result->total_montant = $totalMontant;
            $result->total_montant_formate = $this->formatMontantAffichage($totalMontant);
            $result->montant_moyen = $result->nombre_dettes > 0 ? bcdiv($totalMontant, $result->nombre_dettes, 2) : 0;
            $result->montant_moyen_formate = $this->formatMontantAffichage($result->montant_moyen);
        }

        return $results->sortByDesc('total_montant')->values();
    }

    /**
     * Statistiques par département
     */
    private function getStatistiquesParDepartement($annee)
    {
        $results = DB::table('dette_cnps')
            ->join('communes', 'dette_cnps.commune_id', '=', 'communes.id')
            ->join('departements', 'communes.departement_id', '=', 'departements.id')
            ->join('regions', 'departements.region_id', '=', 'regions.id')
            ->whereYear('dette_cnps.date_evaluation', $annee)
            ->select([
                'departements.nom as departement',
                'departements.id as departement_id',
                'regions.nom as region',
                DB::raw('COUNT(dette_cnps.id) as nombre_dettes'),
                DB::raw('COUNT(DISTINCT dette_cnps.commune_id) as communes_concernees')
            ])
            ->groupBy('departements.id', 'departements.nom', 'regions.nom')
            ->get();

        // Calcul des totaux par département
        foreach ($results as $result) {
            $dettesDept = DB::table('dette_cnps')
                ->join('communes', 'dette_cnps.commune_id', '=', 'communes.id')
                ->whereYear('dette_cnps.date_evaluation', $annee)
                ->where('communes.departement_id', $result->departement_id)
                ->pluck('dette_cnps.montant');

            $totalMontant = '0';
            foreach ($dettesDept as $montant) {
                $totalMontant = bcadd($totalMontant, $montant, 2);
            }

            $result->total_montant = $totalMontant;
            $result->total_montant_formate = $this->formatMontantAffichage($totalMontant);
            $result->montant_moyen = $result->nombre_dettes > 0 ? bcdiv($totalMontant, $result->nombre_dettes, 2) : 0;
            $result->montant_moyen_formate = $this->formatMontantAffichage($result->montant_moyen);
        }

        return $results->sortByDesc('total_montant')->values();
    }

    /**
     * Évolution annuelle des dettes CNPS
     */
    private function getEvolutionAnnuelle()
    {
        // Récupérer toutes les années où il y a des évaluations
        $anneesData = dette_cnps::selectRaw('YEAR(date_evaluation) as annee')
            ->distinct()
            ->orderBy('annee')
            ->get();

        $evolution = [];
        foreach ($anneesData as $anneeData) {
            $annee = $anneeData->annee;
            
            // Récupérer toutes les dettes pour cette année
            $dettesAnnee = dette_cnps::whereYear('date_evaluation', $annee)->get();
            
            // Calculer le total avec précision
            $totalMontant = $this->calculerSommeTotale($dettesAnnee);

            $evolution[] = (object)[
                'annee' => $annee,
                'total_montant' => $totalMontant,
                'total_montant_formate' => $this->formatMontantAffichage($totalMontant),
                'nombre_dettes' => $dettesAnnee->count(),
                'communes_concernees' => $dettesAnnee->unique('commune_id')->count(),
                'montant_moyen' => $dettesAnnee->count() > 0 ? bcdiv($totalMontant, $dettesAnnee->count(), 2) : 0,
                'montant_moyen_formate' => $this->formatMontantAffichage($dettesAnnee->count() > 0 ? bcdiv($totalMontant, $dettesAnnee->count(), 2) : 0)
            ];
        }

        return collect($evolution);
    }

    /**
     * Top des communes avec le plus de dettes CNPS
     */
    private function getTopCommunesDettesCnps($annee)
    {
        $communes = DB::table('dette_cnps')
            ->join('communes', 'dette_cnps.commune_id', '=', 'communes.id')
            ->join('departements', 'communes.departement_id', '=', 'departements.id')
            ->join('regions', 'departements.region_id', '=', 'regions.id')
            ->whereYear('dette_cnps.date_evaluation', $annee)
            ->select([
                'communes.nom as commune',
                'communes.code as code_commune',
                'communes.id as commune_id',
                'departements.nom as departement',
                'regions.nom as region',
                DB::raw('COUNT(dette_cnps.id) as nombre_evaluations')
            ])
            ->groupBy('communes.id', 'communes.nom', 'communes.code', 'departements.nom', 'regions.nom')
            ->get();

        // Calcul des totaux par commune
        foreach ($communes as $commune) {
            $dettesCommune = dette_cnps::where('commune_id', $commune->commune_id)
                ->whereYear('date_evaluation', $annee)
                ->pluck('montant');

            $totalDette = '0';
            foreach ($dettesCommune as $montant) {
                $totalDette = bcadd($totalDette, $montant, 2);
            }

            $commune->total_dette = $totalDette;
            $commune->total_dette_formate = $this->formatMontantAffichage($totalDette);
        }

        return $communes->sortByDesc('total_dette')->take(20)->values();
    }

    /**
     * Export vers Excel (à implémenter selon vos besoins)
     */
    private function exportToExcel($data)
    {
        // Implémentation avec Laravel Excel ou autre
        return response()->json(['message' => 'Export Excel à implémenter']);
    }

    /**
     * Export vers PDF (à implémenter selon vos besoins)
     */
    private function exportToPdf($data)
    {
        // Implémentation avec DomPDF ou autre
        return response()->json(['message' => 'Export PDF à implémenter']);
    }

    /**
     * Export vers CSV (à implémenter selon vos besoins)
     */
    private function exportToCsv($data)
    {
        // Implémentation CSV
        return response()->json(['message' => 'Export CSV à implémenter']);
    }
}