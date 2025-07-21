<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commune;
use App\Models\Departement;
use App\Models\Ordonnateur;
use App\Models\Prevision;
use App\Models\Receveur;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CommunesController extends Controller

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

   //  * Afficher les défaillances et retards d'une commune
 
public function showDefaillancesRetards(Commune $commune)
{
    $annee = request('annee', date('Y'));
    
    $commune->load(['defaillances', 'retards', 'departement.region']);
    
    // Filtrer par année
    $defaillances = $commune->defaillances->filter(function($def) use ($annee) {
        return $def->date_constat && Carbon::parse($def->date_constat)->year == $annee;
    });
    
    $retards = $commune->retards->filter(function($retard) use ($annee) {
        return $retard->date_constat && Carbon::parse($retard->date_constat)->year == $annee;
    });
    
    // Statistiques
    $stats = [
        'defaillances_total' => $defaillances->count(),
        'defaillances_resolues' => $defaillances->where('est_resolue', true)->count(),
        'defaillances_graves' => $defaillances->where('gravite', 'élevée')->count(),
        'retards_total' => $retards->count(),
        'retard_moyen' => $retards->avg('duree_jours'),
        'retard_max' => $retards->max('duree_jours')
    ];
    
    return view('communes.defaillances-retards', compact('commune', 'defaillances', 'retards', 'stats', 'annee'));
}

/**
 * Ajouter une défaillance à une commune
 */
public function addDefaillance(Request $request, Commune $commune)
{
    $validated = $request->validate([
        'type_defaillance' => 'required|string|max:255',
        'description' => 'required|string',
        'date_constat' => 'required|date',
        'gravite' => 'required|in:faible,moyenne,élevée',
        'est_resolue' => 'boolean'
    ]);
    
    try {
        $commune->defaillances()->create($validated);
        return redirect()->back()->with('success', 'Défaillance ajoutée avec succès.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Erreur lors de l\'ajout: ' . $e->getMessage());
    }
}

/**
 * Ajouter un retard à une commune
 */
public function addRetard(Request $request, Commune $commune)
{
    $validated = $request->validate([
        'type_retard' => 'required|string|max:255',
        'duree_jours' => 'required|integer|min:1',
        'date_constat' => 'required|date',
        'date_retard' => 'nullable|date'
    ]);
    
    try {
        $commune->retards()->create($validated);
        return redirect()->back()->with('success', 'Retard ajouté avec succès.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Erreur lors de l\'ajout: ' . $e->getMessage());
    }
}

/**
 * Analyser les performances d'une commune et détecter les problèmes
 */
public function analyserPerformances(Commune $commune)
{
    $annee = request('annee', date('Y'));
    
    $commune->load([
        'previsions', 'realisations', 'tauxRealisations', 'depotsComptes',
        'dettesCnps', 'dettesFiscale', 'dettesFeicom', 'dettesSalariale'
    ]);
    
    $analyse = [
        'indicateurs' => $this->calculerIndicateurs($commune, $annee),
        'problemes_detectes' => $this->detecterProblemes($commune, $annee),
        'recommandations' => $this->genererRecommandations($commune, $annee),
        'tendances' => $this->calculerTendances($commune)
    ];
    
    return view('communes.analyse-performances', compact('commune', 'analyse', 'annee'));
}

/**
 * Calculer les indicateurs de performance
 */
private function calculerIndicateurs($commune, $annee)
{
    $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
    $realisations = $commune->realisations->where('annee_exercice', $annee);
    $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
    $depotCompte = $commune->depotsComptes->where('annee_exercice', $annee)->first();
    
    // Calculer les dettes totales
    $dettesAnnee = [
        'cnps' => $commune->dettesCnps->filter(function($dette) use ($annee) {
            return $dette->date_evaluation && Carbon::parse($dette->date_evaluation)->year == $annee;
        })->sum('montant'),
        'fiscale' => $commune->dettesFiscale->filter(function($dette) use ($annee) {
            return $dette->date_evaluation && Carbon::parse($dette->date_evaluation)->year == $annee;
        })->sum('montant'),
        'feicom' => $commune->dettesFeicom->filter(function($dette) use ($annee) {
            return $dette->date_evaluation && Carbon::parse($dette->date_evaluation)->year == $annee;
        })->sum('montant'),
        'salariale' => $commune->dettesSalariale->filter(function($dette) use ($annee) {
            return $dette->date_evaluation && Carbon::parse($dette->date_evaluation)->year == $annee;
        })->sum('montant')
    ];
    
    $detteTotal = array_sum($dettesAnnee);
    
    return [
        'taux_realisation' => $tauxRealisation?->pourcentage ?? 0,
        'prevision' => $prevision?->montant ?? 0,
        'realisation' => $realisations->sum('montant'),
        'ecart_budgetaire' => $prevision ? (($realisations->sum('montant') - $prevision->montant) / $prevision->montant) * 100 : 0,
        'dette_totale' => $detteTotal,
        'dettes_detail' => $dettesAnnee,
        'depot_conforme' => $depotCompte?->validation ?? false,
        'depot_retard' => $depotCompte ? $this->calculerRetardDepot($depotCompte->date_depot, $annee) : null,
        'ratio_dette_prevision' => $prevision && $prevision->montant > 0 ? ($detteTotal / $prevision->montant) * 100 : 0
    ];
}

/**
 * Détecter les problèmes de performance
 */
private function detecterProblemes($commune, $annee)
{
    $indicateurs = $this->calculerIndicateurs($commune, $annee);
    $problemes = [];
    
    // Problème de taux de réalisation
    if ($indicateurs['taux_realisation'] < 50) {
        $problemes[] = [
            'type' => 'Taux de réalisation faible',
            'gravite' => $indicateurs['taux_realisation'] < 25 ? 'élevée' : 'moyenne',
            'description' => "Taux de réalisation de {$indicateurs['taux_realisation']}%",
            'impact' => 'Faible exécution budgétaire'
        ];
    }
    
    // Problème de dette élevée
    if ($indicateurs['ratio_dette_prevision'] > 50) {
        $problemes[] = [
            'type' => 'Endettement élevé',
            'gravite' => $indicateurs['ratio_dette_prevision'] > 100 ? 'élevée' : 'moyenne',
            'description' => "Ratio dette/prévision de {$indicateurs['ratio_dette_prevision']}%",
            'impact' => 'Contraintes financières importantes'
        ];
    }
    
    // Problème de dépôt de compte
    if (!$indicateurs['depot_conforme']) {
        $problemes[] = [
            'type' => 'Dépôt de compte non conforme',
            'gravite' => 'moyenne',
            'description' => 'Dépôt de compte non validé ou en retard',
            'impact' => 'Non-conformité réglementaire'
        ];
    }
    
    // Problème de dépassement budgétaire
    if ($indicateurs['ecart_budgetaire'] > 20) {
        $problemes[] = [
            'type' => 'Dépassement budgétaire',
            'gravite' => 'moyenne',
            'description' => "Dépassement de {$indicateurs['ecart_budgetaire']}%",
            'impact' => 'Mauvaise planification budgétaire'
        ];
    }
    
    return $problemes;
}

/**
 * Générer des recommandations
 */
private function genererRecommandations($commune, $annee)
{
    $problemes = $this->detecterProblemes($commune, $annee);
    $recommandations = [];
    
    foreach ($problemes as $probleme) {
        switch ($probleme['type']) {
            case 'Taux de réalisation faible':
                $recommandations[] = [
                    'titre' => 'Améliorer l\'exécution budgétaire',
                    'actions' => [
                        'Réviser les procédures de passation des marchés',
                        'Renforcer les capacités des équipes',
                        'Améliorer le suivi des projets'
                    ]
                ];
                break;
                
            case 'Endettement élevé':
                $recommandations[] = [
                    'titre' => 'Réduire l\'endettement',
                    'actions' => [
                        'Établir un plan de remboursement',
                        'Négocier des échéanciers avec les créanciers',
                        'Améliorer la mobilisation des ressources'
                    ]
                ];
                break;
                
            case 'Dépôt de compte non conforme':
                $recommandations[] = [
                    'titre' => 'Améliorer la conformité',
                    'actions' => [
                        'Former les équipes comptables',
                        'Respecter les délais de dépôt',
                        'Améliorer la qualité des comptes'
                    ]
                ];
                break;
        }
    }
    
    return $recommandations;
}

/**
 * Calculer les tendances sur plusieurs années
 */
private function calculerTendances($commune)
{
    $anneeActuelle = date('Y');
    $tendances = [];
    
    for ($i = 2; $i >= 0; $i--) {
        $annee = $anneeActuelle - $i;
        $indicateurs = $this->calculerIndicateurs($commune, $annee);
        
        $tendances[] = [
            'annee' => $annee,
            'taux_realisation' => $indicateurs['taux_realisation'],
            'dette_totale' => $indicateurs['dette_totale'],
            'prevision' => $indicateurs['prevision'],
            'realisation' => $indicateurs['realisation']
        ];
    }
    
    return $tendances;
}

/**
 * Calculer le retard de dépôt
 */
private function calculerRetardDepot($dateDepot, $annee)
{
    $dateLimite = Carbon::create($annee, 3, 31); // 31 mars
    $dateDepotCarbon = Carbon::parse($dateDepot);
    
    return $dateDepotCarbon->gt($dateLimite) ? $dateDepotCarbon->diffInDays($dateLimite) : 0;
}

/**
 * Générer un rapport de performance
 */
public function genererRapportPerformance(Commune $commune)
{
    $annee = request('annee', date('Y'));
    
    $rapport = [
        'commune' => $commune->load(['departement.region', 'receveurs', 'ordonnateurs']),
        'indicateurs' => $this->calculerIndicateurs($commune, $annee),
        'problemes' => $this->detecterProblemes($commune, $annee),
        'defaillances' => $commune->defaillances->filter(function($def) use ($annee) {
            return $def->date_constat && Carbon::parse($def->date_constat)->year == $annee;
        }),
        'retards' => $commune->retards->filter(function($retard) use ($annee) {
            return $retard->date_constat && Carbon::parse($retard->date_constat)->year == $annee;
        }),
        'recommandations' => $this->genererRecommandations($commune, $annee),
        'tendances' => $this->calculerTendances($commune)
    ];
    
    return view('communes.rapport-performance', compact('rapport', 'annee'));
}
  }













