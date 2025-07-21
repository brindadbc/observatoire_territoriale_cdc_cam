<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Defaillance;
use App\Models\Retard;
use App\Models\Depot_compte;
use App\Models\Realisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DefaillanceRetardController extends Controller
{
    /**
     * Afficher la liste des défaillances avec filtres
     */
    public function indexDefaillances(Request $request)
    {
        $query = Defaillance::with(['commune.departement.region']);
        
        // Filtres
        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->commune_id);
        }
        
        if ($request->filled('type_defaillance')) {
            $query->where('type_defaillance', $request->type_defaillance);
        }
        
        if ($request->filled('gravite')) {
            $query->where('gravite', $request->gravite);
        }
        
        if ($request->filled('est_resolue')) {
            $query->where('est_resolue', $request->est_resolue);
        }
        
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_constat', [$request->date_debut, $request->date_fin]);
        }
        
        $defaillances = $query->orderBy('date_constat', 'desc')->paginate(20);
        
        // Données pour les filtres
        $communes = Commune::with('departement')->orderBy('nom')->get();
        $typesDefaillance = Defaillance::distinct()->pluck('type_defaillance');
        $gravites = ['faible', 'moyenne', 'élevée'];
        
        return view('defaillances.index', compact('defaillances', 'communes', 'typesDefaillance', 'gravites'));
    }
    
    /**
     * Afficher la liste des retards avec filtres
     */
    public function indexRetards(Request $request)
    {
        $query = Retard::with(['commune.departement.region']);
        
        // Filtres
        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->commune_id);
        }
        
        if ($request->filled('type_retard')) {
            $query->where('type_retard', $request->type_retard);
        }
        
        if ($request->filled('duree_min')) {
            $query->where('duree_jours', '>=', $request->duree_min);
        }
        
        if ($request->filled('duree_max')) {
            $query->where('duree_jours', '<=', $request->duree_max);
        }
        
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_constat', [$request->date_debut, $request->date_fin]);
        }
        
        $retards = $query->orderBy('date_constat', 'desc')->paginate(20);
        
        // Données pour les filtres
        $communes = Commune::with('departement')->orderBy('nom')->get();
        $typesRetard = Retard::distinct()->pluck('type_retard');
        
        return view('retards.index', compact('retards', 'communes', 'typesRetard'));
    }
    
    /**
     * Créer une défaillance
     */
    public function storeDefaillance(Request $request)
    {
        $validated = $request->validate([
            'commune_id' => 'required|exists:communes,id',
            'type_defaillance' => 'required|string|max:255',
            'description' => 'required|string',
            'date_constat' => 'required|date',
            'gravite' => 'required|in:faible,moyenne,élevée',
            'est_resolue' => 'boolean'
        ]);
        
        try {
            Defaillance::create($validated);
            return redirect()->back()->with('success', 'Défaillance enregistrée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }
    
    /**
     * Créer un retard
     */
    public function storeRetard(Request $request)
    {
        $validated = $request->validate([
            'commune_id' => 'required|exists:communes,id',
            'type_retard' => 'required|string|max:255',
            'duree_jours' => 'required|integer|min:1',
            'date_constat' => 'required|date',
            'date_retard' => 'nullable|date'
        ]);
        
        try {
            Retard::create($validated);
            return redirect()->back()->with('success', 'Retard enregistré avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }
    
    /**
     * Marquer une défaillance comme résolue
     */
    public function resolveDefaillance(Defaillance $defaillance)
    {
        try {
            $defaillance->update(['est_resolue' => true]);
            return redirect()->back()->with('success', 'Défaillance marquée comme résolue.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }
    
    /**
     * Supprimer une défaillance
     */
    public function destroyDefaillance(Defaillance $defaillance)
    {
        try {
            $defaillance->delete();
            return redirect()->back()->with('success', 'Défaillance supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
    
    /**
     * Supprimer un retard
     */
    public function destroyRetard(Retard $retard)
    {
        try {
            $retard->delete();
            return redirect()->back()->with('success', 'Retard supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
    
    /**
     * Détecter automatiquement les retards de dépôt de comptes
     */
    public function detecterRetardsDepots()
    {
        $anneeActuelle = date('Y');
        $dateLimite = Carbon::create($anneeActuelle, 3, 31); // 31 mars comme date limite
        
        try {
            DB::beginTransaction();
            
            $communes = Commune::with('depotsComptes')->get();
            $retardsDetectes = 0;
            
            foreach ($communes as $commune) {
                $depotAnnee = $commune->depotsComptes->where('annee_exercice', $anneeActuelle)->first();
                
                if (!$depotAnnee) {
                    // Aucun dépôt pour cette année
                    $dureeRetard = Carbon::now()->diffInDays($dateLimite);
                    
                    if ($dureeRetard > 0) {
                        // Vérifier si le retard n'existe pas déjà
                        $retardExiste = Retard::where('commune_id', $commune->id)
                            ->where('type_retard', 'Dépôt de compte')
                            ->whereYear('date_constat', $anneeActuelle)
                            ->exists();
                        
                        if (!$retardExiste) {
                            Retard::create([
                                'commune_id' => $commune->id,
                                'type_retard' => 'Dépôt de compte',
                                'duree_jours' => $dureeRetard,
                                'date_constat' => Carbon::now(),
                                'date_retard' => $dateLimite
                            ]);
                            $retardsDetectes++;
                        }
                    }
                } elseif ($depotAnnee->date_depot > $dateLimite) {
                    // Dépôt en retard
                    $dureeRetard = Carbon::parse($depotAnnee->date_depot)->diffInDays($dateLimite);
                    
                    $retardExiste = Retard::where('commune_id', $commune->id)
                        ->where('type_retard', 'Dépôt de compte')
                        ->whereYear('date_constat', $anneeActuelle)
                        ->exists();
                    
                    if (!$retardExiste) {
                        Retard::create([
                            'commune_id' => $commune->id,
                            'type_retard' => 'Dépôt de compte',
                            'duree_jours' => $dureeRetard,
                            'date_constat' => $depotAnnee->date_depot,
                            'date_retard' => $dateLimite
                        ]);
                        $retardsDetectes++;
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "$retardsDetectes retards de dépôt détectés et enregistrés."
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la détection: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Détecter automatiquement les défaillances de réalisation budgétaire
     */
    public function detecterDefaillancesRealisation()
    {
        $anneeActuelle = date('Y');
        $seuilDefaillance = 50; // Taux de réalisation en dessous duquel c'est une défaillance
        
        try {
            DB::beginTransaction();
            
            $communes = Commune::with(['tauxRealisations', 'previsions', 'realisations'])->get();
            $defaillancesDetectees = 0;
            
            foreach ($communes as $commune) {
                $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $anneeActuelle)->first();
                
                if ($tauxRealisation && $tauxRealisation->pourcentage < $seuilDefaillance) {
                    // Vérifier si la défaillance n'existe pas déjà
                    $defaillanceExiste = Defaillance::where('commune_id', $commune->id)
                        ->where('type_defaillance', 'Faible taux de réalisation')
                        ->whereYear('date_constat', $anneeActuelle)
                        ->exists();
                    
                    if (!$defaillanceExiste) {
                        $gravite = $tauxRealisation->pourcentage < 25 ? 'élevée' : 
                                  ($tauxRealisation->pourcentage < 40 ? 'moyenne' : 'faible');
                        
                        Defaillance::create([
                            'commune_id' => $commune->id,
                            'type_defaillance' => 'Faible taux de réalisation',
                            'description' => "Taux de réalisation de {$tauxRealisation->pourcentage}% pour l'année {$anneeActuelle}",
                            'date_constat' => Carbon::now(),
                            'gravite' => $gravite,
                            'est_resolue' => false
                        ]);
                        $defaillancesDetectees++;
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "$defaillancesDetectees défaillances de réalisation détectées et enregistrées."
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la détection: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Obtenir les statistiques des défaillances et retards
     */
    public function getStatistiques()
    {
        $anneeActuelle = date('Y');
        
        $stats = [
            'defaillances' => [
                'total' => Defaillance::whereYear('date_constat', $anneeActuelle)->count(),
                'resolues' => Defaillance::whereYear('date_constat', $anneeActuelle)->where('est_resolue', true)->count(),
                'par_gravite' => Defaillance::whereYear('date_constat', $anneeActuelle)
                    ->selectRaw('gravite, COUNT(*) as count')
                    ->groupBy('gravite')
                    ->pluck('count', 'gravite')
                    ->toArray()
            ],
            'retards' => [
                'total' => Retard::whereYear('date_constat', $anneeActuelle)->count(),
                'par_type' => Retard::whereYear('date_constat', $anneeActuelle)
                    ->selectRaw('type_retard, COUNT(*) as count')
                    ->groupBy('type_retard')
                    ->pluck('count', 'type_retard')
                    ->toArray(),
                'duree_moyenne' => Retard::whereYear('date_constat', $anneeActuelle)->avg('duree_jours')
            ]
        ];
        
        return response()->json($stats);
    }
}