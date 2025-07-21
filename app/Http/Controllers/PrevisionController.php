<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prevision;
use App\Models\Commune;
use App\Models\Realisation;
use App\Models\Taux_realisation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrevisionController extends Controller
{
    /**
     * Liste des prévisions avec pagination et filtres
     */
    public function index(Request $request)
    {
        $query = Prevision::with(['commune.departement.region']);
        
        // Filtrage par année
        if ($request->filled('annee_exercice')) {
            $query->where('annee_exercice', $request->annee_exercice);
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
        
        // Recherche par nom de commune
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('commune', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }
        
        // Tri
        $sortBy = $request->get('sort_by', 'annee_exercice');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);
        
        $previsions = $query->paginate(20);
        
        // Enrichir les données avec les statistiques
        $previsions->getCollection()->transform(function($prevision) {
            $prevision->montant_realise = $prevision->realisations->sum('montant');
            $prevision->taux_realisation = $prevision->taux_realisation;
            $prevision->evaluation = $prevision->evaluation;
            $prevision->nb_realisations = $prevision->realisations->count();
            return $prevision;
        });
        
        // Données pour les filtres
        $communes = Commune::with('departement.region')->orderBy('nom')->get();
        $annees = Prevision::distinct()->orderByDesc('annee_exercice')->pluck('annee_exercice');
        
        // Statistiques générales
        $stats = $this->getStatistiquesGenerales($request);
        
        return view('previsions.index', compact(
            'previsions', 'communes', 'annees', 'stats'
        ));
    }

    /**
     * Affichage du formulaire de création
     */
    public function create()
    {
        $communes = Commune::with('departement.region')->orderBy('nom')->get();
        $anneeDefaut = date('Y');
        
        return view('previsions.create', compact('communes', 'anneeDefaut'));
    }

    /**
     * Enregistrement d'une nouvelle prévision
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'annee_exercice' => 'required|integer|min:2000|max:' . (date('Y') + 10),
            'montant' => 'required|numeric|min:0',
            'commune_id' => 'required|exists:communes,id',
        ], [
            'annee_exercice.required' => 'L\'année d\'exercice est obligatoire.',
            'annee_exercice.integer' => 'L\'année d\'exercice doit être un nombre entier.',
            'annee_exercice.min' => 'L\'année d\'exercice ne peut pas être antérieure à 2000.',
            'annee_exercice.max' => 'L\'année d\'exercice ne peut pas dépasser ' . (date('Y') + 10) . '.',
            'montant.required' => 'Le montant est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant ne peut pas être négatif.',
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
        ]);

        // Validation personnalisée pour éviter les doublons
        $validator->after(function ($validator) use ($request) {
            $exists = Prevision::where('commune_id', $request->commune_id)
                              ->where('annee_exercice', $request->annee_exercice)
                              ->exists();
            if ($exists) {
                $validator->errors()->add('commune_id', 'Une prévision existe déjà pour cette commune et cette année.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Créer la prévision
            $prevision = Prevision::create([
                'annee_exercice' => $request->annee_exercice,
                'montant' => $request->montant,
                'commune_id' => $request->commune_id,
            ]);

            // Créer ou mettre à jour le taux de réalisation initial
            $this->updateTauxRealisation($prevision);

            DB::commit();

            return redirect()->route('previsions.show', $prevision)
                ->with('success', 'Prévision créée avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de la prévision: ' . $e->getMessage())
                ->withInput();
        }
    }



 public function show(Prevision $prevision)
{
    // Charger les relations nécessaires
    $prevision->load([
        'commune.departement.region',
        'realisations' => function($query) {
            $query->orderBy('date_realisation', 'desc');
        }
    ]);

    // Calculer le montant réalisé
    $montantRealise = $prevision->realisations->sum('montant');
    
    // Calculer le taux de réalisation
    $tauxRealisation = $prevision->montant > 0 ? ($montantRealise / $prevision->montant) * 100 : 0;
    
    // Déterminer l'évaluation
    $evaluation = $this->getEvaluation($tauxRealisation);

    // Obtenir la dernière réalisation de manière sûre
    $derniereRealisation = $prevision->realisations->first();
    
    // Calculs des statistiques avec vérification null
    $stats = [
        'montant_prevision' => $prevision->montant,
        'montant_realise' => $montantRealise,
        'montant_restant' => $prevision->montant - $montantRealise,
        'taux_realisation' => round($tauxRealisation, 2),
        'evaluation' => $evaluation,
        'nb_realisations' => $prevision->realisations->count(),
        'derniere_realisation' => $derniereRealisation ? $derniereRealisation->date_realisation : null,
    ];

    // Évolution annuelle des réalisations
    $evolutionAnnuelle = $this->getEvolutionAnnuelle($prevision);

    // Comparaison avec d'autres communes du même département
    $comparaison = $this->getComparaisonDepartement($prevision);

    // Historique des prévisions pour cette commune
    $historiquePrevisions = $this->getHistoriquePrevisions($prevision);

    // Taux de réalisation officiel avec vérification null
    $tauxRealisationModel = Taux_realisation::where('commune_id', $prevision->commune_id)
        ->where('annee_exercice', $prevision->annee_exercice)
        ->first();

    return view('previsions.show', compact(
        'prevision', 'stats', 'evolutionAnnuelle', 'comparaison', 
        'historiquePrevisions', 'tauxRealisationModel'
    ));
}
    /**
     * Affichage du formulaire de modification
     */
    public function edit(Prevision $prevision)
    {
        $communes = Commune::with('departement.region')->orderBy('nom')->get();
        return view('previsions.edit', compact('prevision', 'communes'));
    }

    /**
     * Mise à jour d'une prévision
     */
    public function update(Request $request, Prevision $prevision)
    {
        $validator = Validator::make($request->all(), [
            'annee_exercice' => 'required|integer|min:2000|max:' . (date('Y') + 10),
            'montant' => 'required|numeric|min:0',
            'commune_id' => 'required|exists:communes,id',
        ], [
            'annee_exercice.required' => 'L\'année d\'exercice est obligatoire.',
            'montant.required' => 'Le montant est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'commune_id.required' => 'Vous devez sélectionner une commune.',
        ]);

        // Validation pour éviter les doublons (sauf pour la prévision actuelle)
        $validator->after(function ($validator) use ($request, $prevision) {
            $exists = Prevision::where('commune_id', $request->commune_id)
                              ->where('annee_exercice', $request->annee_exercice)
                              ->where('id', '!=', $prevision->id)
                              ->exists();
            if ($exists) {
                $validator->errors()->add('commune_id', 'Une prévision existe déjà pour cette commune et cette année.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Mettre à jour la prévision
            $prevision->update([
                'annee_exercice' => $request->annee_exercice,
                'montant' => $request->montant,
                'commune_id' => $request->commune_id,
            ]);

            // Recalculer le taux de réalisation
            $this->updateTauxRealisation($prevision);

            DB::commit();

            return redirect()->route('previsions.show', $prevision)
                ->with('success', 'Prévision mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }
private function updateTauxRealisation(Prevision $prevision)
{
    // Recharger les réalisations pour être sûr d'avoir les dernières données
    $prevision->load('realisations');
    
    $montantRealise = $prevision->realisations->sum('montant') ?? 0;
    $tauxPourcentage = $prevision->montant > 0 ? ($montantRealise / $prevision->montant) * 100 : 0;
    
    // Déterminer l'évaluation
    $evaluation = $this->getEvaluation($tauxPourcentage);
    
    // Calculer l'écart
    $ecart = $prevision->montant - $montantRealise;

    // Mettre à jour ou créer le taux de réalisation
    Taux_realisation::updateOrCreate(
        [
            'commune_id' => $prevision->commune_id,
            'annee_exercice' => $prevision->annee_exercice,
        ],
        [
            'pourcentage' => round($tauxPourcentage, 2),
            'evaluation' => $evaluation,
            'ecart' => $ecart,
            'date_calcul' => now(),
        ]
    );
}



private function getEvolutionAnnuelle(Prevision $prevision)
{
    // Vérifier si la prévision a des réalisations
    if ($prevision->realisations->isEmpty()) {
        return collect();
    }

    // Grouper les réalisations par année
    return $prevision->realisations
        ->filter(function($realisation) {
            return $realisation->date_realisation !== null;
        })
        ->groupBy(function($realisation) {
            return $realisation->date_realisation->year;
        })
        ->map(function($groupe, $annee) {
            return [
                'annee' => $annee,
                'montant' => $groupe->sum('montant'),
                'nb_realisations' => $groupe->count(),
            ];
        })
        ->sortBy('annee')
        ->values();
}
    /**
     * Suppression d'une prévision
     */
    public function destroy(Prevision $prevision)
    {
        // Vérifier s'il y a des réalisations associées
        if ($prevision->realisations()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette prévision car elle contient des réalisations.');
        }

        DB::beginTransaction();
        try {
            // Supprimer le taux de réalisation associé
            Taux_realisation::where('commune_id', $prevision->commune_id)
                ->where('annee_exercice', $prevision->annee_exercice)
                ->delete();

            // Supprimer la prévision
            $prevision->delete();

            DB::commit();

            return redirect()->route('previsions.index')
                ->with('success', 'Prévision supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Dupliquer une prévision pour une nouvelle année
     */
    public function duplicate(Prevision $prevision, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nouvelle_annee' => 'required|integer|min:2000|max:' . (date('Y') + 10),
            'ajustement_pourcentage' => 'nullable|numeric|min:-100|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Vérifier si une prévision existe déjà pour cette année
        $exists = Prevision::where('commune_id', $prevision->commune_id)
            ->where('annee_exercice', $request->nouvelle_annee)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Une prévision existe déjà pour cette commune et cette année.');
        }

        DB::beginTransaction();
        try {
            // Calculer le nouveau montant avec ajustement
            $nouveauMontant = $prevision->montant;
            if ($request->filled('ajustement_pourcentage')) {
                $ajustement = $request->ajustement_pourcentage / 100;
                $nouveauMontant = $prevision->montant * (1 + $ajustement);
            }

            // Créer la nouvelle prévision
            $nouvellePrevision = Prevision::create([
                'annee_exercice' => $request->nouvelle_annee,
                'montant' => $nouveauMontant,
                'commune_id' => $prevision->commune_id,
            ]);

            // Créer le taux de réalisation initial
            $this->updateTauxRealisation($nouvellePrevision);

            DB::commit();

            return redirect()->route('previsions.show', $nouvellePrevision)
                ->with('success', 'Prévision dupliquée avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la duplication: ' . $e->getMessage());
        }
    }

    /**
     * Analyse des tendances annuelles
     */
    public function analysesTendances(Request $request)
    {
        $communeId = $request->get('commune_id');
        $departementId = $request->get('departement_id');
        $regionId = $request->get('region_id');
        $anneeDebut = $request->get('annee_debut', date('Y') - 5);
        $anneeFin = $request->get('annee_fin', date('Y'));

        // Données des tendances
        $tendancesData = $this->getTendancesData($communeId, $departementId, $regionId, $anneeDebut, $anneeFin);

        // Données pour les filtres
        $communes = Commune::with('departement.region')->orderBy('nom')->get();
        $departements = \App\Models\Departement::with('region')->orderBy('nom')->get();
        $regions = \App\Models\Region::orderBy('nom')->get();

        return view('previsions.analyses', compact(
            'tendancesData', 'communes', 'departements', 'regions',
            'communeId', 'departementId', 'regionId', 'anneeDebut', 'anneeFin'
        ));
    }


   
    /**
     * Obtenir l'évaluation basée sur le pourcentage
     */
    private function getEvaluation($pourcentage)
    {
        if ($pourcentage >= 90) return 'Excellent';
        if ($pourcentage >= 75) return 'Bon';
        if ($pourcentage >= 50) return 'Moyen';
        return 'Insuffisant';
    }

    /**
     * Obtenir les statistiques générales
     */
    private function getStatistiquesGenerales($request)
    {
        $annee = $request->get('annee_exercice', date('Y'));
        
        $query = Prevision::where('annee_exercice', $annee);
        
        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->commune_id);
        }

        $previsions = $query->with('realisations')->get();
        
        return [
            'total_previsions' => $previsions->count(),
            'montant_total_prevu' => $previsions->sum('montant'),
            'montant_total_realise' => $previsions->sum(function($p) { return $p->montant_realise; }),
            'taux_moyen' => $previsions->avg('taux_realisation'),
            'nb_excellents' => $previsions->filter(function($p) { return $p->taux_realisation >= 90; })->count(),
            'nb_bons' => $previsions->filter(function($p) { return $p->taux_realisation >= 75 && $p->taux_realisation < 90; })->count(),
            'nb_moyens' => $previsions->filter(function($p) { return $p->taux_realisation >= 50 && $p->taux_realisation < 75; })->count(),
            'nb_insuffisants' => $previsions->filter(function($p) { return $p->taux_realisation < 50; })->count(),
        ];
    }

   

    /**
     * Obtenir l'historique des prévisions pour une commune
     */
    private function getHistoriquePrevisions(Prevision $prevision)
    {
        return Prevision::where('commune_id', $prevision->commune_id)
            ->where('id', '!=', $prevision->id)
            ->with('realisations')
            ->orderBy('annee_exercice', 'desc')
            ->get()
            ->map(function($prev) {
                return [
                    'annee' => $prev->annee_exercice,
                    'montant_prevu' => $prev->montant,
                    'montant_realise' => $prev->montant_realise,
                    'taux_realisation' => $prev->taux_realisation,
                    'evaluation' => $prev->evaluation,
                    'croissance_prevision' => null, // Sera calculé dans la vue
                ];
            })
            ->values();
    }

   
    private function getComparaisonDepartement(Prevision $prevision)
{
    $departementId = $prevision->commune->departement_id;
    
    return Prevision::whereHas('commune', function($query) use ($departementId) {
            $query->where('departement_id', $departementId);
        })
        ->where('annee_exercice', $prevision->annee_exercice)
        ->with(['commune', 'realisations'])
        ->get()
        ->map(function($p) {
            $montantRealise = $p->realisations->sum('montant');
            $tauxRealisation = $p->montant > 0 ? ($montantRealise / $p->montant) * 100 : 0;
            
            return [
                'commune' => $p->commune->nom,
                'montant_prevu' => $p->montant,
                'montant_realise' => $montantRealise,
                'taux_realisation' => round($tauxRealisation, 2),
                'evaluation' => $this->getEvaluation($tauxRealisation),
            ];
        })
        ->sortByDesc('taux_realisation')
        ->values();
}
    /**
     * Obtenir les données de tendances annuelles
     */
    private function getTendancesData($communeId, $departementId, $regionId, $anneeDebut, $anneeFin)
    {
        $query = Prevision::whereBetween('annee_exercice', [$anneeDebut, $anneeFin])
            ->with(['commune.departement.region', 'realisations']);

        if ($communeId) {
            $query->where('commune_id', $communeId);
        } elseif ($departementId) {
            $query->whereHas('commune', function($q) use ($departementId) {
                $q->where('departement_id', $departementId);
            });
        } elseif ($regionId) {
            $query->whereHas('commune.departement', function($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }

        $previsions = $query->get();

        // Grouper par année
        $tendancesParAnnee = $previsions->groupBy('annee_exercice')
            ->map(function($groupe, $annee) {
                $montantPrevu = $groupe->sum('montant');
                $montantRealise = $groupe->sum('montant_realise');
                $tauxMoyen = $groupe->avg('taux_realisation');

                return [
                    'annee' => $annee,
                    'nb_previsions' => $groupe->count(),
                    'montant_prevu' => $montantPrevu,
                    'montant_realise' => $montantRealise,
                    'taux_realisation_moyen' => round($tauxMoyen, 2),
                    'croissance_prevision' => 0, // Sera calculé après
                    'croissance_realisation' => 0, // Sera calculé après
                ];
            })
            ->sortBy('annee')
            ->values();

        // Calculer les croissances
        $tendancesParAnnee = $tendancesParAnnee->map(function($donnee, $index) use ($tendancesParAnnee) {
            if ($index > 0) {
                $precedent = $tendancesParAnnee[$index - 1];
                
                if ($precedent['montant_prevu'] > 0) {
                    $donnee['croissance_prevision'] = round(
                        (($donnee['montant_prevu'] - $precedent['montant_prevu']) / $precedent['montant_prevu']) * 100, 2
                    );
                }
                
                if ($precedent['montant_realise'] > 0) {
                    $donnee['croissance_realisation'] = round(
                        (($donnee['montant_realise'] - $precedent['montant_realise']) / $precedent['montant_realise']) * 100, 2
                    );
                }
            }
            
            return $donnee;
        });

        return [
            'evolution_annuelle' => $tendancesParAnnee,
            'statistiques_globales' => [
                'annees_analysees' => $anneeFin - $anneeDebut + 1,
                'montant_total_prevu' => $previsions->sum('montant'),
                'montant_total_realise' => $previsions->sum('montant_realise'),
                'taux_realisation_global' => $previsions->avg('taux_realisation'),
                'meilleure_annee' => $tendancesParAnnee->sortByDesc('taux_realisation_moyen')->first(),
                'moins_bonne_annee' => $tendancesParAnnee->sortBy('taux_realisation_moyen')->first(),
            ]
        ];
    }

    
}