<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commune;
use App\Models\Departement;
use App\Models\Ordonnateur;
use App\Models\Receveur;
use Illuminate\Support\Facades\DB;

class CommunesController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {

//         // Retourner la vue avec les communes
//         return view('communes.index', compact('communes'));
//     }

//     /**
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         //
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         // Validation des données
//         $request->validate([
//             'nom' => 'required|string|max:255',
//             'type' => 'required|string|max:255',
//             'population' => 'required|integer',
//             'id_departement' => 'required|exists:departements,id'
//         ]);

       
//         // Redirection vers la liste des  avec un message de succès
//         return redirect()->route('communes.index')->with('success', 'Commune créée avec succès.');
//     }

//     /**
//      * Display the specified resource.
//      */
//     public function show(string $id)
//     {
//         // Récupérer la commune par son ID
//         $commune = Commune::findOrFail($id);

//         // Retourner la vue avec la commune
//         return view('communes.show', compact('commune'));
//     }

//     /**
//      * Show the form for editing the specified resource.
//      */
//     public function edit(string $id)
//     {
//         // Récupérer la commune par son ID
//         $commune = Commune::findOrFail($id);

//         // Retourner la vue avec la commune
//         return view('communes.edit', compact('commune'));
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, string $id)
//     {
//         // Validation des données
//         $request->validate([
//             'nom' => 'required|string|max:255',
//             'type' => 'required|string|max:255',
//             'population' => 'required|integer',
//             'id_departement' => 'required|exists:departements,id'
//         ]);

//         // Récupérer la commune par son ID
//         $commune = Commune::findOrFail($id);

//         // Mettre à jour la commune
//         $commune->update($request->all());

//         // Redirection vers la liste des communes avec un message de succès
//         return redirect()->route('communes.index')->with('success', 'Commune mise à jour avec succès.');
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy(string $id)
//     {
//         //
//     }
// }

// {
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
        
//         return view('observatoire.commune', compact(
//             'commune', 'donneesFinancieres', 'historiquePerformances', 
//             'detailsDettes', 'problemes', 'annee'
//         ));
//     }
    
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
//                 'montant' => $commune->dettesCnps->whereYear('date_evaluation', $annee)->sum('montant'),
//                 'details' => $commune->dettesCnps->whereYear('date_evaluation', $annee)->values()
//             ],
//             'fiscale' => [
//                 'montant' => $commune->dettesFiscale->whereYear('date_evaluation', $annee)->sum('montant'),
//                 'details' => $commune->dettesFiscale->whereYear('date_evaluation', $annee)->values()
//             ],
//             'feicom' => [
//                 'montant' => $commune->dettesFeicom->whereYear('date_evaluation', $annee)->sum('montant'),
//                 'details' => $commune->dettesFeicom->whereYear('date_evaluation', $annee)->values()
//             ],
//             'salariale' => [
//                 'montant' => $commune->dettesSalariale->whereYear('date_evaluation', $annee)->sum('montant'),
//                 'details' => $commune->dettesSalariale->whereYear('date_evaluation', $annee)->values()
//             ]
//         ];
//     }
    
//     private function getProblemes($commune, $annee)
//     {
//         return [
//             'defaillances' => $commune->defaillances->whereYear('date_constat', $annee)->map(function($def) {
//                 return [
//                     'type' => $def->type_defaillance,
//                     'description' => $def->description,
//                     'date_constat' => $def->date_constat,
//                     'gravite' => $def->gravite,
//                     'est_grave' => $def->est_grave,
//                     'est_resolue' => $def->est_resolue
//                 ];
//             }),
//             'retards' => $commune->retards->whereYear('date_constat', $annee)->map(function($retard) {
//                 return [
//                     'type' => $retard->type_retard,
//                     'duree_jours' => $retard->duree_jours,
//                     'date_constat' => $retard->date_constat,
//                     'gravite' => $retard->gravite
//                 ];
//             })
//         ];
//     }
// }





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
//         $receveurs = Receveur::where('est_actif', true)->orderBy('nom')->get();
//         $ordonnateurs = Ordonnateur::where('est_actif', true)->orderBy('nom')->get();
        
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
//             // 'email' => 'nullable|email|max:255',
//             // 'adresse' => 'nullable|string|max:500',
//             // 'population' => 'nullable|integer|min:0',
//             // 'superficie' => 'nullable|numeric|min:0',
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
//         //     'email.email' => 'L\'adresse email n\'est pas valide.',
//         //     'population.integer' => 'La population doit être un nombre entier.',
//         //     'superficie.numeric' => 'La superficie doit être un nombre valide.',
//          ]);

//         DB::beginTransaction();
//         try {
//             $commune = Commune::create($validated);
            
//             // Associer les receveurs
//             if ($request->filled('receveur_ids')) {
//                 $commune->receveurs()->attach($request->receveur_ids);
//             }
            
//             // Associer les ordonnateurs
//             if ($request->filled('ordonnateur_ids')) {
//                 $commune->ordonnateurs()->attach($request->ordonnateur_ids);
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
//         $receveurs = Receveur::where('est_actif', true)->orderBy('nom')->get();
//         $ordonnateurs = Ordonnateur::where('est_actif', true)->orderBy('nom')->get();
        
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
//             // 'email' => 'nullable|email|max:255',
//             // 'adresse' => 'nullable|string|max:500',
//             // 'population' => 'nullable|integer|min:0',
//             // 'superficie' => 'nullable|numeric|min:0',
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
//             // 'email.email' => 'L\'adresse email n\'est pas valide.',
//             // 'population.integer' => 'La population doit être un nombre entier.',
//             // 'superficie.numeric' => 'La superficie doit être un nombre valide.',
//         ]);

//         DB::beginTransaction();
//         try {
//             $commune->update($validated);
            
//             // Mettre à jour les receveurs
//             if ($request->has('receveur_ids')) {
//                 $commune->receveurs()->sync($request->receveur_ids ?? []);
//             }
            
//             // Mettre à jour les ordonnateurs
//             if ($request->has('ordonnateur_ids')) {
//                 $commune->ordonnateurs()->sync($request->ordonnateur_ids ?? []);
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
            
//             // Détacher les relations many-to-many
//             $commune->receveurs()->detach();
//             $commune->ordonnateurs()->detach();
            
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

//     // Méthodes privées existantes...
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
//                 'montant' => $commune->dettesCnps->whereYear('date_evaluation', $annee)->sum('montant'),
//                 'details' => $commune->dettesCnps->whereYear('date_evaluation', $annee)->values()
//             ],
//             'fiscale' => [
//                 'montant' => $commune->dettesFiscale->whereYear('date_evaluation', $annee)->sum('montant'),
//                 'details' => $commune->dettesFiscale->whereYear('date_evaluation', $annee)->values()
//             ],
//             'feicom' => [
//                 'montant' => $commune->dettesFeicom->whereYear('date_evaluation', $annee)->sum('montant'),
//                 'details' => $commune->dettesFeicom->whereYear('date_evaluation', $annee)->values()
//             ],
//             'salariale' => [
//                 'montant' => $commune->dettesSalariale->whereYear('date_evaluation', $annee)->sum('montant'),
//                 'details' => $commune->dettesSalariale->whereYear('date_evaluation', $annee)->values()
//             ]
//         ];
//     }
    
//     private function getProblemes($commune, $annee)
//     {
//         return [
//             'defaillances' => $commune->defaillances->whereYear('date_constat', $annee)->map(function($def) {
//                 return [
//                     'type' => $def->type_defaillance,
//                     'description' => $def->description,
//                     'date_constat' => $def->date_constat,
//                     'gravite' => $def->gravite,
//                     'est_grave' => $def->est_grave,
//                     'est_resolue' => $def->est_resolue
//                 ];
//             }),
//             'retards' => $commune->retards->whereYear('date_constat', $annee)->map(function($retard) {
//                 return [
//                     'type' => $retard->type_retard,
//                     'duree_jours' => $retard->duree_jours,
//                     'date_constat' => $retard->date_constat,
//                     'gravite' => $retard->gravite
//                 ];
//             })
//         ];
//     }
// }




{
    /**
     * Liste des communes avec pagination et recherche
     */
    public function index(Request $request)
    {
        $query = Commune::with(['departement.region', 'receveurs', 'ordonnateurs']);
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhereHas('departement', function($dq) use ($search) {
                      $dq->where('nom', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Filtrage par département
        if ($request->filled('departement_id')) {
            $query->where('departement_id', $request->departement_id);
        }
        
        // Tri
        $sortBy = $request->get('sort_by', 'nom');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);
        
        $communes = $query->paginate(15);
        $departements = Departement::with('region')->orderBy('nom')->get();
        
        return view('communes.index', compact('communes', 'departements'));
    }

    /**
     * Affichage du formulaire de création
     */
    public function create()
    {
        $departements = Departement::with('region')->orderBy('nom')->get();
        // Récupérer les receveurs et ordonnateurs qui ne sont pas encore assignés à une commune
        $receveurs = Receveur::whereNull('commune_id')->orderBy('nom')->get();
        $ordonnateurs = Ordonnateur::whereNull('commune_id')->orderBy('nom')->get();
        
        return view('communes.create', compact('departements', 'receveurs', 'ordonnateurs'));
    }

    /**
     * Enregistrement d'une nouvelle commune
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:communes,code',
            'departement_id' => 'required|exists:departements,id',
            'telephone' => 'nullable|string|max:20',
            'receveur_ids' => 'nullable|array',
            'receveur_ids.*' => 'exists:receveurs,id',
            'ordonnateur_ids' => 'nullable|array',
            'ordonnateur_ids.*' => 'exists:ordonnateurs,id',
        ], [
            'nom.required' => 'Le nom de la commune est obligatoire.',
            'code.required' => 'Le code de la commune est obligatoire.',
            'code.unique' => 'Ce code est déjà utilisé par une autre commune.',
            'departement_id.required' => 'Vous devez sélectionner un département.',
            'departement_id.exists' => 'Le département sélectionné n\'existe pas.',
        ]);

        DB::beginTransaction();
        try {
            // Créer la commune
            $commune = Commune::create($validated);
            
            // Assigner les receveurs à cette commune
            if ($request->filled('receveur_ids')) {
                Receveur::whereIn('id', $request->receveur_ids)
                    ->update(['commune_id' => $commune->id]);
            }
            
            // Assigner les ordonnateurs à cette commune
            if ($request->filled('ordonnateur_ids')) {
                Ordonnateur::whereIn('id', $request->ordonnateur_ids)
                    ->update(['commune_id' => $commune->id]);
            }
            
            DB::commit();
            
            return redirect()->route('communes.show', $commune)
                           ->with('success', 'Commune créée avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Erreur lors de la création de la commune: ' . $e->getMessage());
        }
    }

    /**
     * Affichage des détails d'une commune
     */
    public function show(Commune $commune)
    {
        $annee = request('annee', date('Y'));
        
        // Chargement des relations nécessaires
        $commune->load([
            'departement.region', 'receveurs', 'ordonnateurs',
            'depotsComptes', 'previsions', 'realisations', 'tauxRealisations',
            'dettesCnps', 'dettesFiscale', 'dettesFeicom', 'dettesSalariale',
            'defaillances', 'retards'
        ]);
        
        // Données financières
        $donneesFinancieres = $this->getDonneesFinancieres($commune, $annee);
        
        // Historique des performances
        $historiquePerformances = $this->getHistoriquePerformances($commune);
        
        // Détails des dettes
        $detailsDettes = $this->getDetailsDettes($commune, $annee);
        
        // Problèmes et défaillances
        $problemes = $this->getProblemes($commune, $annee);
        
        return view('communes.show', compact(
            'commune', 'donneesFinancieres', 'historiquePerformances', 
            'detailsDettes', 'problemes', 'annee'
        ));
    }

    /**
     * Affichage du formulaire de modification
     */
    public function edit(Commune $commune)
    {
        $commune->load(['receveurs', 'ordonnateurs']);
        $departements = Departement::with('region')->orderBy('nom')->get();
        
        // Récupérer les receveurs et ordonnateurs disponibles (non assignés ou assignés à cette commune)
        $receveurs = Receveur::where(function($query) use ($commune) {
            $query->whereNull('commune_id')
                  ->orWhere('commune_id', $commune->id);
        })->orderBy('nom')->get();
        
        $ordonnateurs = Ordonnateur::where(function($query) use ($commune) {
            $query->whereNull('commune_id')
                  ->orWhere('commune_id', $commune->id);
        })->orderBy('nom')->get();
        
        return view('communes.edit', compact('commune', 'departements', 'receveurs', 'ordonnateurs'));
    }

    /**
     * Mise à jour d'une commune
     */
    public function update(Request $request, Commune $commune)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:communes,code,' . $commune->id,
            'departement_id' => 'required|exists:departements,id',
            'telephone' => 'nullable|string|max:20',
            'receveur_ids' => 'nullable|array',
            'receveur_ids.*' => 'exists:receveurs,id',
            'ordonnateur_ids' => 'nullable|array',
            'ordonnateur_ids.*' => 'exists:ordonnateurs,id',
        ], [
            'nom.required' => 'Le nom de la commune est obligatoire.',
            'code.required' => 'Le code de la commune est obligatoire.',
            'code.unique' => 'Ce code est déjà utilisé par une autre commune.',
            'departement_id.required' => 'Vous devez sélectionner un département.',
            'departement_id.exists' => 'Le département sélectionné n\'existe pas.',
        ]);

        DB::beginTransaction();
        try {
            // Mettre à jour la commune
            $commune->update($validated);
            
            // Libérer les anciens receveurs et ordonnateurs
            Receveur::where('commune_id', $commune->id)->update(['commune_id' => null]);
            Ordonnateur::where('commune_id', $commune->id)->update(['commune_id' => null]);
            
            // Assigner les nouveaux receveurs
            if ($request->filled('receveur_ids')) {
                Receveur::whereIn('id', $request->receveur_ids)
                    ->update(['commune_id' => $commune->id]);
            }
            
            // Assigner les nouveaux ordonnateurs
            if ($request->filled('ordonnateur_ids')) {
                Ordonnateur::whereIn('id', $request->ordonnateur_ids)
                    ->update(['commune_id' => $commune->id]);
            }
            
            DB::commit();
            
            return redirect()->route('communes.show', $commune)
                           ->with('success', 'Commune mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Suppression d'une commune
     */
    public function destroy(Commune $commune)
    {
        try {
            // Vérifier si la commune a des données liées
            $hasData = $commune->previsions()->exists() || 
                      $commune->realisations()->exists() || 
                      $commune->dettesCnps()->exists() ||
                      $commune->dettesFiscale()->exists() ||
                      $commune->dettesFeicom()->exists() ||
                      $commune->dettesSalariale()->exists();
                      
            if ($hasData) {
                return back()->with('error', 'Impossible de supprimer cette commune car elle contient des données financières.');
            }
            
            DB::beginTransaction();
            
            // Libérer les receveurs et ordonnateurs
            Receveur::where('commune_id', $commune->id)->update(['commune_id' => null]);
            Ordonnateur::where('commune_id', $commune->id)->update(['commune_id' => null]);
            
            // Supprimer la commune
            $commune->delete();
            
            DB::commit();
            
            return redirect()->route('communes.index')
                           ->with('success', 'Commune supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Méthodes privées pour les données additionnelles
     */
    private function getDonneesFinancieres($commune, $annee)
    {
        $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
        $realisations = $commune->realisations->where('annee_exercice', $annee);
        $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
        
        return [
            'prevision' => $prevision?->montant ?? 0,
            'realisation_total' => $realisations->sum('montant'),
            'taux_realisation' => $tauxRealisation?->pourcentage ?? 0,
            'evaluation' => $tauxRealisation?->evaluation ?? 'Non évalué',
            'ecart' => $tauxRealisation?->ecart ?? 100,
            'realisations_detail' => $realisations->map(function($real) {
                return [
                    'montant' => $real->montant,
                    'date' => $real->date_realisation,
                    'ecart_prevision' => $real->ecart_prevision
                ];
            })
        ];
    }
    
    private function getHistoriquePerformances($commune)
    {
        return $commune->tauxRealisations()
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
    
    private function getDetailsDettes($commune, $annee)
    {
        return [
            'cnps' => [
                'montant' => $commune->dettesCnps->filter(function($dette) use ($annee) {
                    return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
                })->sum('montant'),
                'details' => $commune->dettesCnps->filter(function($dette) use ($annee) {
                    return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
                })->values()
            ],
            'fiscale' => [
                'montant' => $commune->dettesFiscale->filter(function($dette) use ($annee) {
                    return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
                })->sum('montant'),
                'details' => $commune->dettesFiscale->filter(function($dette) use ($annee) {
                    return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
                })->values()
            ],
            'feicom' => [
                'montant' => $commune->dettesFeicom->filter(function($dette) use ($annee) {
                    return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
                })->sum('montant'),
                'details' => $commune->dettesFeicom->filter(function($dette) use ($annee) {
                    return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
                })->values()
            ],
            'salariale' => [
                'montant' => $commune->dettesSalariale->filter(function($dette) use ($annee) {
                    return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
                })->sum('montant'),
                'details' => $commune->dettesSalariale->filter(function($dette) use ($annee) {
                    return $dette->date_evaluation && \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
                })->values()
            ]
        ];
    }
    
    private function getProblemes($commune, $annee)
    {
        return [
            'defaillances' => $commune->defaillances->filter(function($def) use ($annee) {
                return $def->date_constat && \Carbon\Carbon::parse($def->date_constat)->year == $annee;
            })->map(function($def) {
                return [
                    'type' => $def->type_defaillance,
                    'description' => $def->description,
                    'date_constat' => $def->date_constat,
                    'gravite' => $def->gravite,
                    'est_grave' => $def->est_grave,
                    'est_resolue' => $def->est_resolue
                ];
            }),
            'retards' => $commune->retards->filter(function($retard) use ($annee) {
                return $retard->date_constat && \Carbon\Carbon::parse($retard->date_constat)->year == $annee;
            })->map(function($retard) {
                return [
                    'type' => $retard->type_retard,
                    'duree_jours' => $retard->duree_jours,
                    'date_constat' => $retard->date_constat,
                    'gravite' => $retard->gravite
                ];
            })
        ];
    }
}












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
//                 'montant' => $commune->dettesCnps->whereYear('date_evaluation', $annee)->sum('montant'),
//                 'details' => $commune->dettesCnps->whereYear('date_evaluation', $annee)->values()
//             ],
//             'fiscale' => [
//                 'montant' => $commune->dettesFiscale->whereYear('date_evaluation', $annee)->sum('montant'),
//                 'details' => $commune->dettesFiscale->whereYear('date_evaluation', $annee)->values()
//             ],
//             'feicom' => [
//                 'montant' => $commune->dettesFeicom->whereYear('date_evaluation', $annee)->sum('montant'),
//                 'details' => $commune->dettesFeicom->whereYear('date_evaluation', $annee)->values()
//             ],
//             'salariale' => [
//                 'montant' => $commune->dettesSalariale->whereYear('date_evaluation', $annee)->sum('montant'),
//                 'details' => $commune->dettesSalariale->whereYear('date_evaluation', $annee)->values()
//             ]
//         ];
//     }
    
//     private function getProblemes($commune, $annee)
//     {
//         return [
//             'defaillances' => $commune->defaillances->whereYear('date_constat', $annee)->map(function($def) {
//                 return [
//                     'type' => $def->type_defaillance,
//                     'description' => $def->description,
//                     'date_constat' => $def->date_constat,
//                     'gravite' => $def->gravite,
//                     'est_grave' => $def->est_grave,
//                     'est_resolue' => $def->est_resolue
//                 ];
//             }),
//             'retards' => $commune->retards->whereYear('date_constat', $annee)->map(function($retard) {
//                 return [
//                     'type' => $retard->type_retard,
//                     'duree_jours' => $retard->duree_jours,
//                     'date_constat' => $retard->date_constat,
//                     'gravite' => $retard->gravite
//                 ];
//             })
//         ];
//     }
// }