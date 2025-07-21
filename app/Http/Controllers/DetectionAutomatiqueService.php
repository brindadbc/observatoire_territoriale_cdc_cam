<?php

namespace App\Services;

use App\Models\Commune;
use App\Models\Retard;
use App\Models\Defaillance;
use App\Models\Depot_compte;
use App\Models\Realisation;
use App\Models\Prevision;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetectionAutomatiqueService
{
    // Dates limites pour les différents types de dépôts
    private const DATES_LIMITES = [
        'depot_compte' => ['mois' => 3, 'jour' => 31], // 31 mars
        'realisation_budget' => ['mois' => 12, 'jour' => 31], // 31 décembre
        'rapport_activite' => ['mois' => 2, 'jour' => 28], // 28 février
        'declaration_fiscale' => ['mois' => 4, 'jour' => 15], // 15 avril
        'paiement_salaire' => ['mois' => 1, 'jour' => 31], // 31 janvier
    ];

    // Seuils pour les défaillances
    private const SEUILS_DEFAILLANCE = [
        'taux_realisation_faible' => 60,
        'taux_realisation_critique' => 40,
        'baisse_prevision_significative' => 20, // pourcentage
        'baisse_realisation_significative' => 15,
        'retard_grave' => 30, // jours
        'retard_critique' => 60
    ];

    /**
     * Détection complète de tous les retards et défaillances
     */
    public function detecterToutesAnomalies($annee = null)
    {
        $annee = $annee ?? date('Y');
        
        DB::beginTransaction();
        try {
            $resultats = [
                'retards' => 0,
                'defaillances' => 0,
                'details' => []
            ];

            // 1. Détection des retards
            $resultats['retards'] += $this->detecterRetardsDepots($annee);
            $resultats['retards'] += $this->detecterRetardsRealisations($annee);
            $resultats['retards'] += $this->detecterRetardsRapports($annee);
            $resultats['retards'] += $this->detecterRetardsDeclarations($annee);
            $resultats['retards'] += $this->detecterRetardsPaiements($annee);

            // 2. Détection des défaillances
            $resultats['defaillances'] += $this->detecterDefaillancesRealisation($annee);
            $resultats['defaillances'] += $this->detecterDefaillancesPrevision($annee);
            $resultats['defaillances'] += $this->detecterDefaillancesComparatives($annee);
            $resultats['defaillances'] += $this->detecterDefaillancesRecurrentes($annee);

            DB::commit();
            
            Log::info("Détection automatique terminée", $resultats);
            return $resultats;
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Erreur lors de la détection automatique", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Détection des retards de dépôt de comptes
     */
    public function detecterRetardsDepots($annee)
    {
        $dateLimite = Carbon::create($annee, 3, 31);
        $aujourdhui = Carbon::now();
        $retardsDetectes = 0;

        $communes = Commune::with(['depotsComptes' => function($query) use ($annee) {
            $query->where('annee_exercice', $annee);
        }])->get();

        foreach ($communes as $commune) {
            $depot = $commune->depotsComptes->first();
            $dureeRetard = 0;
            $dateConstat = null;

            if (!$depot) {
                // Pas de dépôt du tout
                if ($aujourdhui->gt($dateLimite)) {
                    $dureeRetard = $aujourdhui->diffInDays($dateLimite);
                    $dateConstat = $aujourdhui;
                }
            } else {
                // Dépôt en retard
                $dateDepot = Carbon::parse($depot->date_depot);
                if ($dateDepot->gt($dateLimite)) {
                    $dureeRetard = $dateDepot->diffInDays($dateLimite);
                    $dateConstat = $dateDepot;
                }
            }

            if ($dureeRetard > 0) {
                $retardExiste = Retard::where('commune_id', $commune->id)
                    ->where('type_retard', 'depot_compte')
                    ->whereYear('date_constat', $annee)
                    ->exists();

                if (!$retardExiste) {
                    Retard::create([
                        'commune_id' => $commune->id,
                        'type_retard' => 'depot_compte',
                        'duree_jours' => $dureeRetard,
                        'date_constat' => $dateConstat,
                        'date_retard' => $dateLimite
                    ]);
                    $retardsDetectes++;

                    // Créer une défaillance si retard grave
                    if ($dureeRetard >= self::SEUILS_DEFAILLANCE['retard_grave']) {
                        $this->creerDefaillanceRetardGrave($commune, $dureeRetard, $annee);
                    }
                }
            }
        }

        return $retardsDetectes;
    }

    /**
     * Détection des retards de réalisation budgétaire
     */
    public function detecterRetardsRealisations($annee)
    {
        $dateLimite = Carbon::create($annee, 12, 31);
        $aujourdhui = Carbon::now();
        $retardsDetectes = 0;

        if ($aujourdhui->year <= $annee) {
            return 0; // Pas encore la fin de l'année
        }

        $communes = Commune::with(['realisations' => function($query) use ($annee) {
            $query->where('annee_exercice', $annee);
        }])->get();

        foreach ($communes as $commune) {
            $derniereRealisation = $commune->realisations
                ->where('annee_exercice', $annee)
                ->sortByDesc('date_realisation')
                ->first();

            if (!$derniereRealisation || Carbon::parse($derniereRealisation->date_realisation)->lt($dateLimite)) {
                $dateConstat = $aujourdhui;
                $dureeRetard = $dateConstat->diffInDays($dateLimite);

                $retardExiste = Retard::where('commune_id', $commune->id)
                    ->where('type_retard', 'realisation_budget')
                    ->whereYear('date_constat', $annee)
                    ->exists();

                if (!$retardExiste) {
                    Retard::create([
                        'commune_id' => $commune->id,
                        'type_retard' => 'realisation_budget',
                        'duree_jours' => $dureeRetard,
                        'date_constat' => $dateConstat,
                        'date_retard' => $dateLimite
                    ]);
                    $retardsDetectes++;
                }
            }
        }

        return $retardsDetectes;
    }

    /**
     * Détection des retards de rapports d'activité
     */
    public function detecterRetardsRapports($annee)
    {
        $dateLimite = Carbon::create($annee + 1, 2, 28);
        $aujourdhui = Carbon::now();
        $retardsDetectes = 0;

        if ($aujourdhui->lt($dateLimite)) {
            return 0;
        }

        $communes = Commune::all();

        foreach ($communes as $commune) {
            // Vérifier si le rapport existe (assumant une table rapports_activite)
            $rapportExiste = DB::table('rapports_activite')
                ->where('commune_id', $commune->id)
                ->where('annee_exercice', $annee)
                ->exists();

            if (!$rapportExiste) {
                $dureeRetard = $aujourdhui->diffInDays($dateLimite);

                $retardExiste = Retard::where('commune_id', $commune->id)
                    ->where('type_retard', 'rapport_activite')
                    ->whereYear('date_constat', $annee)
                    ->exists();

                if (!$retardExiste) {
                    Retard::create([
                        'commune_id' => $commune->id,
                        'type_retard' => 'rapport_activite',
                        'duree_jours' => $dureeRetard,
                        'date_constat' => $aujourdhui,
                        'date_retard' => $dateLimite
                    ]);
                    $retardsDetectes++;
                }
            }
        }

        return $retardsDetectes;
    }

    /**
     * Détection des retards de déclarations fiscales
     */
    public function detecterRetardsDeclarations($annee)
    {
        $dateLimite = Carbon::create($annee + 1, 4, 15);
        $aujourdhui = Carbon::now();
        $retardsDetectes = 0;

        if ($aujourdhui->lt($dateLimite)) {
            return 0;
        }

        $communes = Commune::all();

        foreach ($communes as $commune) {
            $declarationExiste = DB::table('declarations_fiscales')
                ->where('commune_id', $commune->id)
                ->where('annee_exercice', $annee)
                ->exists();

            if (!$declarationExiste) {
                $dureeRetard = $aujourdhui->diffInDays($dateLimite);

                $retardExiste = Retard::where('commune_id', $commune->id)
                    ->where('type_retard', 'declaration_fiscale')
                    ->whereYear('date_constat', $annee)
                    ->exists();

                if (!$retardExiste) {
                    Retard::create([
                        'commune_id' => $commune->id,
                        'type_retard' => 'declaration_fiscale',
                        'duree_jours' => $dureeRetard,
                        'date_constat' => $aujourdhui,
                        'date_retard' => $dateLimite
                    ]);
                    $retardsDetectes++;
                }
            }
        }

        return $retardsDetectes;
    }

    /**
     * Détection des retards de paiement des salaires
     */
    public function detecterRetardsPaiements($annee)
    {
        $dateLimite = Carbon::create($annee + 1, 1, 31);
        $aujourdhui = Carbon::now();
        $retardsDetectes = 0;

        if ($aujourdhui->lt($dateLimite)) {
            return 0;
        }

        $communes = Commune::all();

        foreach ($communes as $commune) {
            $paiementEffectue = DB::table('paiements_salaires')
                ->where('commune_id', $commune->id)
                ->where('annee_exercice', $annee)
                ->exists();

            if (!$paiementEffectue) {
                $dureeRetard = $aujourdhui->diffInDays($dateLimite);

                $retardExiste = Retard::where('commune_id', $commune->id)
                    ->where('type_retard', 'paiement_salaire')
                    ->whereYear('date_constat', $annee)
                    ->exists();

                if (!$retardExiste) {
                    Retard::create([
                        'commune_id' => $commune->id,
                        'type_retard' => 'paiement_salaire',
                        'duree_jours' => $dureeRetard,
                        'date_constat' => $aujourdhui,
                        'date_retard' => $dateLimite
                    ]);
                    $retardsDetectes++;
                }
            }
        }

        return $retardsDetectes;
    }

    /**
     * Détection des défaillances de réalisation budgétaire
     */
    public function detecterDefaillancesRealisation($annee)
    {
        $defaillancesDetectees = 0;

        $communes = Commune::with([
            'previsions' => function($query) use ($annee) {
                $query->where('annee_exercice', $annee);
            },
            'realisations' => function($query) use ($annee) {
                $query->where('annee_exercice', $annee);
            }
        ])->get();

        foreach ($communes as $commune) {
            $prevision = $commune->previsions->first();
            $realisationTotale = $commune->realisations->sum('montant');

            if ($prevision && $prevision->montant > 0) {
                $tauxRealisation = ($realisationTotale / $prevision->montant) * 100;

                $typeDefaillance = null;
                $gravite = null;

                if ($tauxRealisation < self::SEUILS_DEFAILLANCE['taux_realisation_critique']) {
                    $typeDefaillance = 'Taux de réalisation critique';
                    $gravite = 'élevée';
                } elseif ($tauxRealisation < self::SEUILS_DEFAILLANCE['taux_realisation_faible']) {
                    $typeDefaillance = 'Taux de réalisation faible';
                    $gravite = 'moyenne';
                }

                if ($typeDefaillance) {
                    $defaillanceExiste = Defaillance::where('commune_id', $commune->id)
                        ->where('type_defaillance', $typeDefaillance)
                        ->whereYear('date_constat', $annee)
                        ->exists();

                    if (!$defaillanceExiste) {
                        Defaillance::create([
                            'commune_id' => $commune->id,
                            'type_defaillance' => $typeDefaillance,
                            'description' => "Taux de réalisation de {$tauxRealisation}% (Prévu: {$prevision->montant}, Réalisé: {$realisationTotale})",
                            'date_constat' => Carbon::now(),
                            'gravite' => $gravite,
                            'est_resolue' => false
                        ]);
                        $defaillancesDetectees++;
                    }
                }
            }
        }

        return $defaillancesDetectees;
    }

    /**
     * Détection des défaillances de prévision
     */
    public function detecterDefaillancesPrevision($annee)
    {
        $defaillancesDetectees = 0;
        $annePrecedente = $annee - 1;

        $communes = Commune::with([
            'previsions' => function($query) use ($annee, $annePrecedente) {
                $query->whereIn('annee_exercice', [$annee, $annePrecedente]);
            }
        ])->get();

        foreach ($communes as $commune) {
            $previsionActuelle = $commune->previsions->where('annee_exercice', $annee)->first();
            $previsionPrecedente = $commune->previsions->where('annee_exercice', $annePrecedente)->first();

            if ($previsionActuelle && $previsionPrecedente && $previsionPrecedente->montant > 0) {
                $baisse = (($previsionPrecedente->montant - $previsionActuelle->montant) / $previsionPrecedente->montant) * 100;

                if ($baisse > self::SEUILS_DEFAILLANCE['baisse_prevision_significative']) {
                    $defaillanceExiste = Defaillance::where('commune_id', $commune->id)
                        ->where('type_defaillance', 'Baisse significative de prévision')
                        ->whereYear('date_constat', $annee)
                        ->exists();

                    if (!$defaillanceExiste) {
                        $gravite = $baisse > 40 ? 'élevée' : ($baisse > 25 ? 'moyenne' : 'faible');
                        
                        Defaillance::create([
                            'commune_id' => $commune->id,
                            'type_defaillance' => 'Baisse significative de prévision',
                            'description' => "Baisse de {$baisse}% par rapport à {$annePrecedente} (De {$previsionPrecedente->montant} à {$previsionActuelle->montant})",
                            'date_constat' => Carbon::now(),
                            'gravite' => $gravite,
                            'est_resolue' => false
                        ]);
                        $defaillancesDetectees++;
                    }
                }
            }
        }

        return $defaillancesDetectees;
    }

    /**
     * Détection des défaillances comparatives
     */
    public function detecterDefaillancesComparatives($annee)
    {
        $defaillancesDetectees = 0;
        $annePrecedente = $annee - 1;

        $communes = Commune::with([
            'realisations' => function($query) use ($annee, $annePrecedente) {
                $query->whereIn('annee_exercice', [$annee, $annePrecedente]);
            }
        ])->get();

        foreach ($communes as $commune) {
            $realisationActuelle = $commune->realisations->where('annee_exercice', $annee)->sum('montant');
            $realisationPrecedente = $commune->realisations->where('annee_exercice', $annePrecedente)->sum('montant');

            if ($realisationPrecedente > 0) {
                $baisse = (($realisationPrecedente - $realisationActuelle) / $realisationPrecedente) * 100;

                if ($baisse > self::SEUILS_DEFAILLANCE['baisse_realisation_significative']) {
                    $defaillanceExiste = Defaillance::where('commune_id', $commune->id)
                        ->where('type_defaillance', 'Baisse significative de réalisation')
                        ->whereYear('date_constat', $annee)
                        ->exists();

                    if (!$defaillanceExiste) {
                        $gravite = $baisse > 30 ? 'élevée' : ($baisse > 20 ? 'moyenne' : 'faible');
                        
                        Defaillance::create([
                            'commune_id' => $commune->id,
                            'type_defaillance' => 'Baisse significative de réalisation',
                            'description' => "Baisse de {$baisse}% par rapport à {$annePrecedente} (De {$realisationPrecedente} à {$realisationActuelle})",
                            'date_constat' => Carbon::now(),
                            'gravite' => $gravite,
                            'est_resolue' => false
                        ]);
                        $defaillancesDetectees++;
                    }
                }
            }
        }

        return $defaillancesDetectees;
    }

    /**
     * Détection des défaillances récurrentes
     */
    public function detecterDefaillancesRecurrentes($annee)
    {
        $defaillancesDetectees = 0;

        $communes = Commune::with([
            'retards' => function($query) use ($annee) {
                $query->whereYear('date_constat', $annee);
            }
        ])->get();

        foreach ($communes as $commune) {
            $retardsGraves = $commune->retards->filter(function($retard) {
                return $retard->duree_jours >= self::SEUILS_DEFAILLANCE['retard_grave'];
            });

            if ($retardsGraves->count() >= 3) {
                $defaillanceExiste = Defaillance::where('commune_id', $commune->id)
                    ->where('type_defaillance', 'Retards récurrents')
                    ->whereYear('date_constat', $annee)
                    ->exists();

                if (!$defaillanceExiste) {
                    Defaillance::create([
                        'commune_id' => $commune->id,
                        'type_defaillance' => 'Retards récurrents',
                        'description' => "Commune ayant {$retardsGraves->count()} retards graves ou plus en {$annee}",
                        'date_constat' => Carbon::now(),
                        'gravite' => 'élevée',
                        'est_resolue' => false
                    ]);
                    $defaillancesDetectees++;
                }
            }
        }

        return $defaillancesDetectees;
    }

    /**
     * Créer une défaillance pour retard grave
     */
    private function creerDefaillanceRetardGrave($commune, $dureeRetard, $annee)
    {
        $defaillanceExiste = Defaillance::where('commune_id', $commune->id)
            ->where('type_defaillance', 'Retard grave')
            ->whereYear('date_constat', $annee)
            ->exists();

        if (!$defaillanceExiste) {
            $gravite = $dureeRetard >= self::SEUILS_DEFAILLANCE['retard_critique'] ? 'élevée' : 'moyenne';
            
            Defaillance::create([
                'commune_id' => $commune->id,
                'type_defaillance' => 'Retard grave',
                'description' => "Retard de {$dureeRetard} jours considéré comme grave",
                'date_constat' => Carbon::now(),
                'gravite' => $gravite,
                'est_resolue' => false
            ]);
        }
    }

    /**
     * Nettoyer les anciennes détections
     */
    public function nettoyerAnciennesDetections($annee)
    {
        // Supprimer les retards et défaillances automatiques de l'année précédente
        Retard::whereYear('date_constat', $annee - 1)
            ->where('detection_automatique', true)
            ->delete();

        Defaillance::whereYear('date_constat', $annee - 1)
            ->where('detection_automatique', true)
            ->delete();
    }

    /**
     * Générer un rapport de détection
     */
    public function genererRapportDetection($resultats)
    {
        return [
            'date_detection' => Carbon::now(),
            'total_retards' => $resultats['retards'],
            'total_defaillances' => $resultats['defaillances'],
            'resume' => "Détection automatique terminée: {$resultats['retards']} retards et {$resultats['defaillances']} défaillances détectés."
        ];
    }
}