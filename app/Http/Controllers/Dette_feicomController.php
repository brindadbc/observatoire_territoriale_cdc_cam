<?php

namespace App\Http\Controllers;

use App\Models\dette_feicom;
use App\Models\Commune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Dette_FeicomController extends Controller
{
    /**
     * Affichage de la liste des dettes FEICOM avec filtres et recherche
     */
    public function index(Request $request)
    {
        $query = dette_feicom::with(['commune.departement.region']);
        
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
            'total_dettes' => dette_feicom::sum('montant'),
            'nb_communes_concernees' => dette_feicom::distinct('commune_id')->count(),
            'dette_moyenne' => dette_feicom::avg('montant'),
            'dette_max' => dette_feicom::max('montant')
        ];
        
        return view('dettes-feicom.index', compact(
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
            
        return view('dettes-feicom.create', compact('communes'));
    }

    /**
     * Enregistrement d'une nouvelle dette FEICOM
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commune_id' => 'required|exists:communes,id',
            'montant' => 'required|numeric|min:0|max:999999999999999999999.99',
            'date_evaluation' => 'required|date|before_or_equal:today',
        ], [
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
            'montant.required' => 'Le montant de la dette est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant ne peut pas être négatif.',
            'montant.max' => 'Le montant ne peut pas dépasser 999,999,999,999,999,999,999.99.',
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
            
            $dette = dette_feicom::create([
                'commune_id' => $request->commune_id,
                'montant' => $request->montant,
                'date_evaluation' => $request->date_evaluation,
            ]);
            
            DB::commit();
            
            return redirect()->route('dettes-feicom.show', $dette)
                ->with('success', 'Dette FEICOM enregistrée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement de la dette : ' . $e->getMessage());
        }
    }

    /**
     * Affichage des détails d'une dette FEICOM
     */
    public function show(dette_feicom $detteFeicom)
    {
        $detteFeicom->load(['commune.departement.region']);
        
        // Historique des dettes FEICOM de cette commune
        $historique = dette_feicom::where('commune_id', $detteFeicom->commune_id)
            ->where('id', '!=', $detteFeicom->id)
            ->orderBy('date_evaluation', 'desc')
            ->take(10)
            ->get();
        
        // Comparaison avec d'autres dettes de la commune
        $autresDettes = [
            'cnps' => \App\Models\dette_cnps::where('commune_id', $detteFeicom->commune_id)
                ->whereYear('date_evaluation', Carbon::parse($detteFeicom->date_evaluation)->year)
                ->sum('montant'),
            'fiscale' => \App\Models\dette_fiscale::where('commune_id', $detteFeicom->commune_id)
                ->whereYear('date_evaluation', Carbon::parse($detteFeicom->date_evaluation)->year)
                ->sum('montant'),
            'salariale' => \App\Models\dette_salariale::where('commune_id', $detteFeicom->commune_id)
                ->whereYear('date_evaluation', Carbon::parse($detteFeicom->date_evaluation)->year)
                ->sum('montant'),
        ];
        
        return view('dettes-feicom.show', compact(
            'detteFeicom', 'historique', 'autresDettes'
        ));
    }

    /**
     * Affichage du formulaire de modification
     */
    public function edit(dette_feicom $detteFeicom)
    {
        $communes = Commune::with('departement.region')
            ->orderBy('nom')
            ->get();
            
        return view('dettes-feicom.edit', compact('detteFeicom', 'communes'));
    }

    /**
     * Mise à jour d'une dette FEICOM
     */
    public function update(Request $request, dette_feicom $detteFeicom)
    {
        $validator = Validator::make($request->all(), [
            'commune_id' => 'required|exists:communes,id',
            'montant' => 'required|numeric|min:0|max:999999999999999999999.99',
            'date_evaluation' => 'required|date|before_or_equal:today',
        ], [
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
            'montant.required' => 'Le montant de la dette est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant ne peut pas être négatif.',
            'montant.max' => 'Le montant ne peut pas dépasser 999,999,999,999,999,999,999.99.',
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
            
            $detteFeicom->update([
                'commune_id' => $request->commune_id,
                'montant' => $request->montant,
                'date_evaluation' => $request->date_evaluation,
            ]);
            
            DB::commit();
            
            return redirect()->route('dettes-feicom.show', $detteFeicom)
                ->with('success', 'Dette FEICOM mise à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la dette : ' . $e->getMessage());
        }
    }

    
   public function destroy(dette_feicom $detteFeicom)
    {
        try {
            DB::beginTransaction();
            
            $detteFeicom->delete();
            
            DB::commit();
            
            return redirect()->route('dettes-feicom.index')
                ->with('success', 'Dette FEICOM supprimée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de la dette : ' . $e->getMessage());
        }
    }
      

    /**
     * Rapport des dettes FEICOM par région
     */
    public function rapportParRegion(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        
        $regions = \App\Models\Region::with(['departements.communes'])
            ->get()
            ->map(function($region) use ($annee) {
                $dettes = dette_feicom::whereHas('commune.departement', function($q) use ($region) {
                    $q->where('region_id', $region->id);
                })->whereYear('date_evaluation', $annee);
                
                return [
                    'region' => $region->nom,
                    'total_dette' => $dettes->sum('montant'),
                    'nb_communes_concernees' => $dettes->distinct('commune_id')->count(),
                    'dette_moyenne' => $dettes->avg('montant') ?? 0,
                ];
            })
            ->sortByDesc('total_dette');
            
        return view('dettes-feicom.rapport-regions', compact('regions', 'annee'));
    }

    /**
     * Rapport des dettes FEICOM par département
     */
    public function rapportParDepartement(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $regionId = $request->get('region_id');
        
        $query = \App\Models\Departement::with(['region', 'communes']);
        
        if ($regionId) {
            $query->where('region_id', $regionId);
        }
        
        $departements = $query->get()
            ->map(function($departement) use ($annee) {
                $dettes = dette_feicom::whereHas('commune', function($q) use ($departement) {
                    $q->where('departement_id', $departement->id);
                })->whereYear('date_evaluation', $annee);
                
                return [
                    'departement' => $departement->nom,
                    'region' => $departement->region->nom,
                    'total_dette' => $dettes->sum('montant'),
                    'nb_communes_concernees' => $dettes->distinct('commune_id')->count(),
                    'dette_moyenne' => $dettes->avg('montant') ?? 0,
                ];
            })
            ->sortByDesc('total_dette');
            
        $regions = \App\Models\Region::orderBy('nom')->get();
            
        return view('dettes-feicom.rapport-departements', compact(
            'departements', 'regions', 'annee', 'regionId'
        ));
    }

    /**
     * Export des données
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $annee = $request->get('annee', date('Y'));
        
        $dettes = dette_feicom::with(['commune.departement.region'])
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

    /**
     * Méthodes privées d'export (à implémenter selon vos besoins)
     */
    private function exportToExcel($dettes, $annee)
    {
        // Implémentation avec Laravel Excel
        return response()->json(['message' => 'Export Excel des dettes FEICOM à implémenter']);
    }

    private function exportToPdf($dettes, $annee)
    {
        // Implémentation avec DomPDF
        return response()->json(['message' => 'Export PDF des dettes FEICOM à implémenter']);
    }

    private function exportToCsv($dettes, $annee)
    {
        // Implémentation CSV
        return response()->json(['message' => 'Export CSV des dettes FEICOM à implémenter']);
    }

    /**
     * API pour obtenir les dettes d'une commune (AJAX)
     */
    public function getDettesByCommune(Request $request)
    {
        $communeId = $request->get('commune_id');
        $annee = $request->get('annee', date('Y'));
        
        if (!$communeId) {
            return response()->json(['error' => 'ID commune requis'], 400);
        }
        
        $dettes = dette_feicom::where('commune_id', $communeId)
            ->whereYear('date_evaluation', $annee)
            ->orderBy('date_evaluation', 'desc')
            ->get();
            
        return response()->json([
            'dettes' => $dettes,
            'total' => $dettes->sum('montant'),
            'count' => $dettes->count()
        ]);
    }
}