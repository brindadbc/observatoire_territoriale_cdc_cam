<?php

namespace App\Http\Controllers;

use App\Models\Receveur;
use App\Models\Commune;
use App\Models\Depot_compte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReceveurController extends Controller
{
    /**
     * Liste des receveurs avec pagination et recherche
     */
    public function index(Request $request)
    {
        $query = Receveur::with(['commune.departement.region']);
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('matricule', 'LIKE', "%{$search}%")
                  ->orWhere('statut', 'LIKE', "%{$search}%")
                  ->orWhereHas('commune', function($cq) use ($search) {
                      $cq->where('nom', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Filtrage par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        // Filtrage par commune
        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->commune_id);
        }
        
        // Filtrage par disponibilité (assigné ou non)
        if ($request->filled('disponibilite')) {
            if ($request->disponibilite === 'disponible') {
                $query->whereNull('commune_id');
            } elseif ($request->disponibilite === 'assigne') {
                $query->whereNotNull('commune_id');
            }
        }
        
        // Tri
        $sortBy = $request->get('sort_by', 'nom');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);
        
        $receveurs = $query->paginate(15);
        
        // Données pour les filtres
        $communes = Commune::with('departement.region')->orderBy('nom')->get();
        $statuts = ['Actif', 'Inactif', 'En congé', 'Retraité'];
        
        // Statistiques
        $stats = [
            'total' => Receveur::count(),
            'disponibles' => Receveur::whereNull('commune_id')->count(),
            'assignes' => Receveur::whereNotNull('commune_id')->count(),
            'actifs' => Receveur::where('statut', 'Actif')->count()
        ];
        
        return view('receveurs.index', compact('receveurs', 'communes', 'statuts', 'stats'));
    }


    /**
     * Affichage du formulaire de création - Version alternative
     */
    public function create()
    {
        $communes = $this->getCommunesDisponibles();
        $statuts = $this->getStatutsDisponibles();
        
        return view('receveurs.create', compact('communes', 'statuts'));
    }

    /**
     * Affichage du formulaire de modification - Version alternative
     */
    public function edit(Receveur $receveur)
    {
        $communes = $this->getCommunesDisponibles($receveur->id);
        $statuts = $this->getStatutsDisponibles();
        
        return view('receveurs.edit', compact('receveur', 'communes', 'statuts'));
    }

    /**
     * Obtenir les communes disponibles (sans receveur actif assigné)
     */
    private function getCommunesDisponibles($receveurIdExclu = null)
    {
        return Commune::with('departement.region')
                     ->whereDoesntHave('receveurs', function($query) use ($receveurIdExclu) {
                         $query->where('statut', 'Actif');
                         if ($receveurIdExclu) {
                             $query->where('id', '!=', $receveurIdExclu);
                         }
                     })
                     ->orWhereHas('receveurs', function($query) use ($receveurIdExclu) {
                         // Inclure la commune si elle est assignée au receveur en cours d'édition
                         if ($receveurIdExclu) {
                             $query->where('id', $receveurIdExclu);
                         }
                     })
                     ->orderBy('nom')
                     ->get();
    }

    /**
     * Obtenir les statuts disponibles
     */
    private function getStatutsDisponibles()
    {
        return ['Actif', 'Inactif', 'En congé', 'Retraité'];
    }

    // /**
    //  * Affichage du formulaire de création
    //  */
    // public function create()
    // {
    //     $communes = Commune::with('departement.region')
    //                       ->whereNull('receveur_id')
    //                       ->orWhereDoesntHave('receveurs')
    //                       ->orderBy('nom')
    //                       ->get();
        
    //     $statuts = ['Actif', 'Inactif', 'En congé', 'Retraité'];
        
    //     return view('receveurs.create', compact('communes', 'statuts'));
    // }

    /**
     * Enregistrement d'un nouveau receveur
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'matricule' => 'required|string|max:50|unique:receveurs,matricule',
            'statut' => 'required|in:Actif,Inactif,En congé,Retraité',
            'date_prise_fonction' => 'required|date',
            'telephone' => 'nullable|string|max:20',
            'commune_id' => 'nullable|exists:communes,id'
        ], [
            'nom.required' => 'Le nom du receveur est obligatoire.',
            'matricule.required' => 'Le matricule est obligatoire.',
            'matricule.unique' => 'Ce matricule est déjà utilisé.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
            'date_prise_fonction.required' => 'La date de prise de fonction est obligatoire.',
            'date_prise_fonction.date' => 'La date de prise de fonction doit être une date valide.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Vérifier si la commune n'a pas déjà un receveur actif
            if ($request->filled('commune_id')) {
                $existingReceveur = Receveur::where('commune_id', $request->commune_id)
                                          ->where('statut', 'Actif')
                                          ->first();
                
                if ($existingReceveur) {
                    return back()->withInput()
                                ->with('error', 'Cette commune a déjà un receveur actif assigné.');
                }
            }

            $receveur = Receveur::create([
                'nom' => trim($request->nom),
                'matricule' => strtoupper(trim($request->matricule)),
                'statut' => $request->statut,
                'date_prise_fonction' => $request->date_prise_fonction,
                'telephone' => $request->telephone,
                'commune_id' => $request->commune_id
            ]);

            DB::commit();

            return redirect()->route('receveurs.show', $receveur)
                           ->with('success', 'Receveur créé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Erreur lors de la création du receveur: ' . $e->getMessage());
        }
    }

    /**
     * Affichage des détails d'un receveur
     */
    public function show(Receveur $receveur)
    {
        $annee = request('annee', date('Y'));
        
        // Charger les relations nécessaires
        $receveur->load(['commune.departement.region', 'depotsComptes']);
        
        // Statistiques des dépôts de comptes
        $statsDepots = [
            'total_depots' => $receveur->depotsComptes()->count(),
            'depots_annee' => $receveur->depotsComptes()->whereYear('date_depot', $annee)->count(),
            'depots_valides' => $receveur->depotsComptes()->where('validation', true)->count(),
            'taux_validation' => $this->getTauxValidation($receveur->id),
            'dernier_depot' => $receveur->depotsComptes()->latest('date_depot')->first()
        ];
        
        // Historique des dépôts
        $historiqueDepots = $this->getHistoriqueDepots($receveur->id, $annee);
        
        // Performance annuelle
        $performanceAnnuelle = $this->getPerformanceAnnuelle($receveur->id);
        
        return view('receveurs.show', compact(
            'receveur', 'statsDepots', 'historiqueDepots', 
            'performanceAnnuelle', 'annee'
        ));
    }

    // /**
    //  * Affichage du formulaire de modification
    //  */
    // public function edit(Receveur $receveur)
    // {
    //     $communes = Commune::with('departement.region')
    //                       ->where(function($query) use ($receveur) {
    //                           $query->whereNull('receveur_id')
    //                                 ->orWhereDoesntHave('receveurs')
    //                                 ->orWhere('id', $receveur->commune_id);
    //                       })
    //                       ->orderBy('nom')
    //                       ->get();
        
    //     $statuts = ['Actif', 'Inactif', 'En congé', 'Retraité'];
        
    //     return view('receveurs.edit', compact('receveur', 'communes', 'statuts'));
    // }

    /**
     * Mise à jour d'un receveur
     */
    public function update(Request $request, Receveur $receveur)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'matricule' => 'required|string|max:50|unique:receveurs,matricule,' . $receveur->id,
            'statut' => 'required|in:Actif,Inactif,En congé,Retraité',
            'date_prise_fonction' => 'required|date',
            'telephone' => 'nullable|string|max:20',
            'commune_id' => 'nullable|exists:communes,id'
        ], [
            'nom.required' => 'Le nom du receveur est obligatoire.',
            'matricule.required' => 'Le matricule est obligatoire.',
            'matricule.unique' => 'Ce matricule est déjà utilisé.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
            'date_prise_fonction.required' => 'La date de prise de fonction est obligatoire.',
            'date_prise_fonction.date' => 'La date de prise de fonction doit être une date valide.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Vérifier si la nouvelle commune n'a pas déjà un receveur actif
            if ($request->filled('commune_id') && $request->commune_id != $receveur->commune_id) {
                $existingReceveur = Receveur::where('commune_id', $request->commune_id)
                                          ->where('statut', 'Actif')
                                          ->where('id', '!=', $receveur->id)
                                          ->first();
                
                if ($existingReceveur) {
                    return back()->withInput()
                                ->with('error', 'Cette commune a déjà un receveur actif assigné.');
                }
            }

            $receveur->update([
                'nom' => trim($request->nom),
                'matricule' => strtoupper(trim($request->matricule)),
                'statut' => $request->statut,
                'date_prise_fonction' => $request->date_prise_fonction,
                'telephone' => $request->telephone,
                'commune_id' => $request->commune_id
            ]);

            DB::commit();

            return redirect()->route('receveurs.show', $receveur)
                           ->with('success', 'Receveur modifié avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Erreur lors de la modification: ' . $e->getMessage());
        }
    }

    /**
     * Suppression d'un receveur
     */
    public function destroy(Receveur $receveur)
    {
        try {
            // Vérifier s'il y a des dépôts de comptes liés
            if ($receveur->depotsComptes()->exists()) {
                return back()->with('error', 'Impossible de supprimer ce receveur car il a des dépôts de comptes enregistrés.');
            }

            DB::beginTransaction();
            
            $receveur->delete();
            
            DB::commit();

            return redirect()->route('receveurs.index')
                           ->with('success', 'Receveur supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Changer le statut d'un receveur (AJAX)
     */
    public function changerStatut(Request $request, Receveur $receveur)
    {
        $validator = Validator::make($request->all(), [
            'statut' => 'required|in:Actif,Inactif,En congé,Retraité'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Statut invalide']);
        }

        try {
            $receveur->update(['statut' => $request->statut]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Statut modifié avec succès',
                'nouveau_statut' => $request->statut
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la modification']);
        }
    }

    /**
     * Assigner/Désassigner une commune (AJAX)
     */
    public function assignerCommune(Request $request, Receveur $receveur)
    {
        $validator = Validator::make($request->all(), [
            'commune_id' => 'nullable|exists:communes,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Commune invalide']);
        }

        try {
            DB::beginTransaction();

            // Si on assigne une nouvelle commune, vérifier qu'elle n'a pas déjà un receveur actif
            if ($request->filled('commune_id')) {
                $existingReceveur = Receveur::where('commune_id', $request->commune_id)
                                          ->where('statut', 'Actif')
                                          ->where('id', '!=', $receveur->id)
                                          ->first();
                
                if ($existingReceveur) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Cette commune a déjà un receveur actif assigné.'
                    ]);
                }
            }

            $receveur->update(['commune_id' => $request->commune_id]);
            
            DB::commit();

            $commune = $request->commune_id ? 
                      Commune::with('departement.region')->find($request->commune_id) : 
                      null;

            return response()->json([
                'success' => true,
                'message' => $request->commune_id ? 'Commune assignée avec succès' : 'Receveur libéré avec succès',
                'commune' => $commune
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'assignation']);
        }
    }

    // ================== MÉTHODES PRIVÉES ==================

    /**
     * Calculer le taux de validation des dépôts
     */
    private function getTauxValidation($receveurId)
    {
        $totalDepots = Depot_compte::where('receveur_id', $receveurId)->count();
        
        if ($totalDepots === 0) return 0;

        $depotsValides = Depot_compte::where('receveur_id', $receveurId)
                                   ->where('validation', true)
                                   ->count();

        return round(($depotsValides / $totalDepots) * 100, 2);
    }

    /**
     * Obtenir l'historique des dépôts pour une année
     */
    private function getHistoriqueDepots($receveurId, $annee)
    {
        return Depot_compte::where('receveur_id', $receveurId)
                          ->whereYear('date_depot', $annee)
                          ->with('commune')
                          ->orderBy('date_depot', 'desc')
                          ->get()
                          ->map(function($depot) {
                              return [
                                  'id' => $depot->id,
                                  'commune' => $depot->commune->nom,
                                  'date_depot' => $depot->date_depot,
                                  'validation' => $depot->validation,
                                  'observations' => $depot->observations,
                                  'annee_exercice' => $depot->annee_exercice
                              ];
                          });
    }

    /**
     * Obtenir les performances annuelles
     */
    private function getPerformanceAnnuelle($receveurId)
    {
        return DB::table('depot_comptes')
                ->where('receveur_id', $receveurId)
                ->selectRaw('
                    YEAR(date_depot) as annee,
                    COUNT(*) as total_depots,
                    SUM(CASE WHEN validation = 1 THEN 1 ELSE 0 END) as depots_valides,
                    ROUND((SUM(CASE WHEN validation = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as taux_validation
                ')
                ->groupBy('annee')
                ->orderBy('annee', 'desc')
                ->get();
    }
}