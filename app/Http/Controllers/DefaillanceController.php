<?php

namespace App\Http\Controllers;

use App\Models\Defaillance;
use App\Models\Commune;
use App\Models\Departement;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DefaillanceController extends Controller
{
    /**
     * Liste des défaillances avec filtres et recherche
     */
    public function index(Request $request)
    {
        $query = Defaillance::with(['commune.departement.region']);
        
        // Filtres
        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->commune_id);
        }
        
        if ($request->filled('departement_id')) {
            $query->whereHas('commune', function($q) use ($request) {
                $q->where('departement_id', $request->departement_id);
            });
        }
        
        if ($request->filled('region_id')) {
            $query->whereHas('commune.departement', function($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }
        
        if ($request->filled('type_defaillance')) {
            $query->where('type_defaillance', $request->type_defaillance);
        }
        
        if ($request->filled('gravite')) {
            $query->where('gravite', $request->gravite);
        }
        
        if ($request->filled('statut')) {
            $query->where('est_resolue', $request->statut === 'resolue');
        }
        
        if ($request->filled('annee')) {
            $query->whereYear('date_constat', $request->annee);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_constat', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_constat', '<=', $request->date_fin);
        }
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('type_defaillance', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('commune', function($cq) use ($search) {
                      $cq->where('nom', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Tri
        $sortBy = $request->get('sort_by', 'date_constat');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);
        
        $defaillances = $query->paginate(20);
        
        // Données pour les filtres
        $communes = Commune::orderBy('nom')->get();
        $departements = Departement::with('region')->orderBy('nom')->get();
        $regions = Region::orderBy('nom')->get();
        $typesDefaillance = Defaillance::select('type_defaillance')
            ->distinct()
            ->whereNotNull('type_defaillance')
            ->pluck('type_defaillance');
        
        // Statistiques
        $stats = $this->getStatistiques($request);
        
        return view('defaillances.index', compact(
            'defaillances', 'communes', 'departements', 'regions', 
            'typesDefaillance', 'stats'
        ));
    }
    
    /**
     * Affichage du formulaire de création
     */
    public function create()
    {
        $communes = Commune::with(['departement.region'])->orderBy('nom')->get();
        $typesDefaillance = [
            'Retard de dépôt de compte',
            'Non-conformité comptable',
            'Défaut de transmission',
            'Irrégularité budgétaire',
            'Manquement réglementaire',
            'Défaillance technique',
            'Autre'
        ];
        
        return view('defaillances.create', compact('communes', 'typesDefaillance'));
    }
    
    /**
     * Enregistrement d'une nouvelle défaillance
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commune_id' => 'required|exists:communes,id',
            'type_defaillance' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'date_constat' => 'required|date',
            'gravite' => 'required|in:faible,moyenne,élevée',
            'est_resolue' => 'boolean'
        ], [
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
            'type_defaillance.required' => 'Le type de défaillance est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'date_constat.required' => 'La date de constat est obligatoire.',
            'date_constat.date' => 'La date de constat doit être une date valide.',
            'gravite.required' => 'Le niveau de gravité est obligatoire.',
            'gravite.in' => 'Le niveau de gravité doit être: faible, moyenne ou élevée.'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            $defaillance = Defaillance::create([
                'commune_id' => $request->commune_id,
                'type_defaillance' => $request->type_defaillance,
                'description' => $request->description,
                'date_constat' => $request->date_constat,
                'gravite' => $request->gravite,
                'est_resolue' => $request->has('est_resolue')
            ]);
            
            DB::commit();
            
            return redirect()->route('defaillances.index')
                ->with('success', 'Défaillance enregistrée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Affichage des détails d'une défaillance
     */
    public function show(Defaillance $defaillance)
    {
        $defaillance->load(['commune.departement.region']);
        
        return view('defaillances.show', compact('defaillance'));
    }
    
    /**
     * Affichage du formulaire d'édition
     */
    public function edit(Defaillance $defaillance)
    {
        $communes = Commune::with(['departement.region'])->orderBy('nom')->get();
        $typesDefaillance = [
            'Retard de dépôt de compte',
            'Non-conformité comptable',
            'Défaut de transmission',
            'Irrégularité budgétaire',
            'Manquement réglementaire',
            'Défaillance technique',
            'Autre'
        ];
        
        return view('defaillances.edit', compact('defaillance', 'communes', 'typesDefaillance'));
    }
    
    /**
     * Mise à jour d'une défaillance
     */
    public function update(Request $request, Defaillance $defaillance)
    {
        $validator = Validator::make($request->all(), [
            'commune_id' => 'required|exists:communes,id',
            'type_defaillance' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'date_constat' => 'required|date',
            'gravite' => 'required|in:faible,moyenne,élevée',
            'est_resolue' => 'boolean'
        ], [
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'type_defaillance.required' => 'Le type de défaillance est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'date_constat.required' => 'La date de constat est obligatoire.',
            'gravite.required' => 'Le niveau de gravité est obligatoire.'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            $defaillance->update([
                'commune_id' => $request->commune_id,
                'type_defaillance' => $request->type_defaillance,
                'description' => $request->description,
                'date_constat' => $request->date_constat,
                'gravite' => $request->gravite,
                'est_resolue' => $request->has('est_resolue')
            ]);
            
            DB::commit();
            
            return redirect()->route('defaillances.index')
                ->with('success', 'Défaillance mise à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Suppression d'une défaillance
     */
    public function destroy(Defaillance $defaillance)
    {
        try {
            DB::beginTransaction();
            
            $defaillance->delete();
            
            DB::commit();
            
            return redirect()->route('defaillances.index')
                ->with('success', 'Défaillance supprimée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
    
    /**
     * Marquer une défaillance comme résolue
     */
    public function resoudre(Defaillance $defaillance)
    {
        try {
            DB::beginTransaction();
            
            $defaillance->update(['est_resolue' => true]);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Défaillance marquée comme résolue.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }
    
    /**
     * Rouvrir une défaillance
     */
    public function rouvrir(Defaillance $defaillance)
    {
        try {
            DB::beginTransaction();
            
            $defaillance->update(['est_resolue' => false]);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Défaillance rouverte.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }
    
    /**
     * Génération d'un rapport de défaillances
     */
    public function rapport(Request $request)
    {
        $query = Defaillance::with(['commune.departement.region']);
        
        // Appliquer les mêmes filtres que dans l'index
        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->commune_id);
        }
        
        if ($request->filled('departement_id')) {
            $query->whereHas('commune', function($q) use ($request) {
                $q->where('departement_id', $request->departement_id);
            });
        }
        
        if ($request->filled('region_id')) {
            $query->whereHas('commune.departement', function($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }
        
        if ($request->filled('annee')) {
            $query->whereYear('date_constat', $request->annee);
        }
        
        $defaillances = $query->orderBy('date_constat', 'desc')->get();
        $stats = $this->getStatistiques($request);
        
        return view('defaillances.rapport', compact('defaillances', 'stats'));
    }
    
    /**
     * Statistiques des défaillances
     */
    private function getStatistiques(Request $request)
    {
        $query = Defaillance::query();
        
        // Appliquer les filtres de date si présents
        if ($request->filled('annee')) {
            $query->whereYear('date_constat', $request->annee);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_constat', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_constat', '<=', $request->date_fin);
        }
        
        $total = $query->count();
        $resolues = $query->where('est_resolue', true)->count();
        $nonResolues = $total - $resolues;
        
        // Par gravité
        $parGravite = $query->select('gravite', DB::raw('count(*) as count'))
            ->groupBy('gravite')
            ->pluck('count', 'gravite')
            ->toArray();
        
        // Par type
        $parType = $query->select('type_defaillance', DB::raw('count(*) as count'))
            ->groupBy('type_defaillance')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'type_defaillance')
            ->toArray();
        
        return [
            'total' => $total,
            'resolues' => $resolues,
            'non_resolues' => $nonResolues,
            'taux_resolution' => $total > 0 ? round(($resolues / $total) * 100, 2) : 0,
            'par_gravite' => $parGravite,
            'par_type' => $parType
        ];
    }
    
    /**
     * API pour récupérer les communes d'un département
     */
    public function getCommunesByDepartement($departementId)
    {
        $communes = Commune::where('departement_id', $departementId)
            ->orderBy('nom')
            ->get(['id', 'nom']);
            
        return response()->json($communes);
    }
    
    /**
     * API pour récupérer les départements d'une région
     */
    public function getDepartementsByRegion($regionId)
    {
        $departements = Departement::where('region_id', $regionId)
            ->orderBy('nom')
            ->get(['id', 'nom']);
            
        return response()->json($departements);
    }
}