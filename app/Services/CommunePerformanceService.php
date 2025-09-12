<?php

namespace App\Services;

use App\Models\Commune;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CommunePerformanceService
{
    /**
     * Analyse la performance d'une commune pour une année donnée
     */
    public function analyzeCommune(Commune $commune, int $annee = null): array
    {
        $annee = $annee ?: date('Y');
        
        return Cache::remember(
            "commune_performance_{$commune->id}_{$annee}",
            now()->addHours(2),
            function() use ($commune, $annee) {
                // Données de base
                $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
                $realisations = $commune->realisations->where('annee_exercice', $annee);
                $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
                
                // Calculs de performance
                $montantPrevision = $prevision?->montant ?? 0;
                $montantRealisation = $realisations->sum('montant');
                $tauxExecution = $montantPrevision > 0 ? ($montantRealisation / $montantPrevision) * 100 : 0;
                
                // Indicateurs de performance
                $indicateurs = [
                    'execution_budgetaire' => $this->evaluateExecutionBudgetaire($tauxExecution),
                    'gestion_delais' => $this->evaluateGestionDelais($commune, $annee),
                    'gouvernance' => $this->evaluateGouvernance($commune),
                    'conformite' => $this->evaluateConformite($commune, $annee),
                    'impact_projets' => $this->evaluateImpactProjets($commune, $annee)
                ];
                
                // Score global
                $scoreGlobal = $this->calculateScoreGlobal($indicateurs);
                
                // Analyse des tendances
                $tendances = $this->analyzeTendances($commune, $annee);
                
                // Points forts et axes d'amélioration
                $analyse = $this->analyzeStrengthsWeaknesses($indicateurs, $commune);
                
                return [
                    'annee' => $annee,
                    'prevision' => $montantPrevision,
                    'realisation' => $montantRealisation,
                    'taux_execution' => round($tauxExecution, 2),
                    'evaluation_globale' => $tauxRealisation?->evaluation ?? $this->getEvaluationFromScore($scoreGlobal),
                    'score_global' => $scoreGlobal,
                    'indicateurs' => $indicateurs,
                    'tendances' => $tendances,
                    'points_forts' => $analyse['points_forts'],
                    'axes_amelioration' => $analyse['axes_amelioration'],
                    'recommandations' => $this->generateRecommandations($indicateurs, $commune)
                ];
            }
        );
    }

    /**
     * Compare une commune avec ses pairs (communes similaires)
     */
    public function compareWithPeers(Commune $commune, int $annee = null): array
    {
        $annee = $annee ?: date('Y');
        
        return Cache::remember(
            "commune_comparison_{$commune->id}_{$annee}",
            now()->addHours(4),
            function() use ($commune, $annee) {
                // Définir les critères de similarité
                $peers = $this->findSimilarCommunes($commune);
                
                if ($peers->isEmpty()) {
                    return [
                        'message' => 'Aucune commune similaire trouvée pour la comparaison',
                        'comparaisons' => []
                    ];
                }
                
                // Métriques de comparaison
                $metriques = [
                    'taux_execution' => 'Taux d\'exécution budgétaire',
                    'budget_par_habitant' => 'Budget par habitant',
                    'nb_projets_realises' => 'Projets réalisés',
                    'delai_moyen_execution' => 'Délai moyen d\'exécution',
                    'score_gouvernance' => 'Score de gouvernance'
                ];
                
                $comparaisons = [];
                $donneesCommune = $this->getMetriquesCommune($commune, $annee);
                
                foreach ($metriques as $metrique => $libelle) {
                    $donneesPeers = $peers->map(function($peer) use ($metrique, $annee) {
                        return $this->getMetriquesCommune($peer, $annee)[$metrique] ?? 0;
                    });
                    
                    $moyennePeers = $donneesPeers->avg();
                    $valeurCommune = $donneesCommune[$metrique] ?? 0;
                    
                    $comparaisons[$metrique] = [
                        'libelle' => $libelle,
                        'valeur_commune' => $valeurCommune,
                        'moyenne_peers' => round($moyennePeers, 2),
                        'ecart' => round($valeurCommune - $moyennePeers, 2),
                        'percentile' => $this->calculatePercentile($valeurCommune, $donneesPeers),
                        // 'position' => $this->getPositionRelative($valeurCommune, $moyennePeers)
                    ];
                }
                
                return [
                    'nb_communes_comparees' => $peers->count(),
                    'criteres_similarite' => $this->getCriteresSimilarite($commune),
                    'comparaisons' => $comparaisons,
                    'resume' => $this->generateResumeComparaison($comparaisons)
                ];
            }
        );
    }

    /**
     * Calcule les indicateurs de performance clés
     */
    public function getKPI(Commune $commune, int $annee = null): array
    {
        $annee = $annee ?: date('Y');
        
        return [
            // KPI Financiers
            'execution_budgetaire' => $this->getKPIExecutionBudgetaire($commune, $annee),
            'efficience_depenses' => $this->getKPIEfficienceDepenses($commune, $annee),
            'autonomie_financiere' => $this->getKPIAutonomieFinanciere($commune, $annee),
            
            // KPI Opérationnels
            'taux_realisation_projets' => $this->getKPIRealisationProjets($commune, $annee),
            'respect_delais' => $this->getKPIRespectDelais($commune, $annee),
            'satisfaction_usagers' => $this->getKPISatisfactionUsagers($commune, $annee),
            
            // KPI Gouvernance
            'transparence' => $this->getKPITransparence($commune, $annee),
            'participation_citoyenne' => $this->getKPIParticipationCitoyenne($commune, $annee),
            'conformite_procedures' => $this->getKPIConformiteProcedures($commune, $annee),
        ];
    }

    /**
     * Analyse les tendances sur plusieurs années
     */
    public function analyzeTendances(Commune $commune, int $anneeRef = null): array
    {
        $anneeRef = $anneeRef ?: date('Y');
        $anneesAnalyse = range($anneeRef - 4, $anneeRef);
        
        $tendances = [];
        
        foreach ($anneesAnalyse as $annee) {
            $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
            $realisation = $commune->realisations->where('annee_exercice', $annee)->sum('montant');
            $tauxExecution = $prevision && $prevision->montant > 0 ? 
                ($realisation / $prevision->montant) * 100 : 0;
            
            $tendances[$annee] = [
                'annee' => $annee,
                'prevision' => $prevision?->montant ?? 0,
                'realisation' => $realisation,
                'taux_execution' => round($tauxExecution, 2),
                'nb_projets' => $commune->projets()->whereYear('date_debut', $annee)->count(),
                'score_performance' => $this->calculateScoreAnnuel($commune, $annee)
            ];
        }
        
        // Calcul des évolutions
        $evolution = $this->calculateEvolutions($tendances);
        
        return [
            'donnees_annuelles' => $tendances,
            'evolution' => $evolution,
            'tendance_globale' => $this->determineTendanceGlobale($evolution),
            'predictions' => $this->generatePredictions($tendances)
        ];
    }

    /**
     * Méthodes privées pour les calculs spécifiques
     */
    private function evaluateExecutionBudgetaire(float $tauxExecution): array
    {
        $score = min(100, max(0, $tauxExecution));
        
        $evaluation = 'Faible';
        if ($score >= 90) $evaluation = 'Excellente';
        elseif ($score >= 75) $evaluation = 'Bonne';
        elseif ($score >= 50) $evaluation = 'Moyenne';
        
        return [
            'score' => round($score, 2),
            'evaluation' => $evaluation,
            'description' => $this->getDescriptionExecutionBudgetaire($score)
        ];
    }

    private function evaluateGestionDelais(Commune $commune, int $annee): array
    {
        // Calcul basé sur les projets et leurs délais
        $projets = $commune->projets()->whereYear('date_debut', $annee)->get();
        
        if ($projets->isEmpty()) {
            return ['score' => 0, 'evaluation' => 'Non applicable', 'description' => 'Aucun projet pour cette année'];
        }
        
        $projetsEnRetard = $projets->filter(function($projet) {
            return $projet->date_fin_prevue && $projet->date_fin_reelle && 
                   $projet->date_fin_reelle->gt($projet->date_fin_prevue);
        });
        
        $tauxRespectDelais = (($projets->count() - $projetsEnRetard->count()) / $projets->count()) * 100;
        
        $evaluation = 'Faible';
        if ($tauxRespectDelais >= 90) $evaluation = 'Excellente';
        elseif ($tauxRespectDelais >= 75) $evaluation = 'Bonne';
        elseif ($tauxRespectDelais >= 50) $evaluation = 'Moyenne';
        
        return [
            'score' => round($tauxRespectDelais, 2),
            'evaluation' => $evaluation,
            'description' => "Respect des délais sur {$projets->count()} projets",
            'details' => [
                'projets_total' => $projets->count(),
                'projets_retard' => $projetsEnRetard->count(),
                'taux_respect' => round($tauxRespectDelais, 2)
            ]
        ];
    }

    private function evaluateGouvernance(Commune $commune): array
    {
        $criteres = [
            'receveur_assigne' => $commune->receveurs->isNotEmpty(),
            'ordonnateur_assigne' => $commune->ordonnateurs->isNotEmpty(),
            'conseil_municipal' => true, // À adapter selon votre modèle
            'publication_budgets' => true, // À adapter
            'participation_citoyenne' => true // À adapter
        ];
        
        $scoreGouvernance = (array_sum($criteres) / count($criteres)) * 100;
        
        $evaluation = 'Faible';
        if ($scoreGouvernance >= 90) $evaluation = 'Excellente';
        elseif ($scoreGouvernance >= 75) $evaluation = 'Bonne';
        elseif ($scoreGouvernance >= 50) $evaluation = 'Moyenne';
        
        return [
            'score' => round($scoreGouvernance, 2),
            'evaluation' => $evaluation,
            'description' => 'Évaluation des structures de gouvernance',
            'criteres' => $criteres
        ];
    }

    private function evaluateConformite(Commune $commune, int $annee): array
    {
        // Évaluation basée sur les défaillances et retards
        $defaillances = $commune->defaillances()->whereYear('date_constat', $annee)->count();
        $retards = $commune->retards()->whereYear('date_retard', $annee)->count();
        
        $scoreBase = 100;
        $penaliteDefaillance = min(50, $defaillances * 10);
        $penaliteRetard = min(30, $retards * 5);
        
        $scoreConformite = max(0, $scoreBase - $penaliteDefaillance - $penaliteRetard);
        
        $evaluation = 'Faible';
        if ($scoreConformite >= 90) $evaluation = 'Excellente';
        elseif ($scoreConformite >= 75) $evaluation = 'Bonne';
        elseif ($scoreConformite >= 50) $evaluation = 'Moyenne';
        
        return [
            'score' => round($scoreConformite, 2),
            'evaluation' => $evaluation,
            'description' => 'Respect des procédures et obligations',
            'details' => [
                'defaillances' => $defaillances,
                'retards' => $retards,
                'penalites' => $penaliteDefaillance + $penaliteRetard
            ]
        ];
    }

    private function evaluateImpactProjets(Commune $commune, int $annee): array
    {
        // Évaluation simplifiée - à adapter selon vos critères
        $projets = $commune->projets()->whereYear('date_debut', $annee)->get();
        $projetsCompletes = $projets->where('statut', 'termine')->count();
        
        if ($projets->isEmpty()) {
            return ['score' => 0, 'evaluation' => 'Non applicable', 'description' => 'Aucun projet'];
        }
        
        $tauxCompletion = ($projetsCompletes / $projets->count()) * 100;
        
        $evaluation = 'Faible';
        if ($tauxCompletion >= 90) $evaluation = 'Excellente';
        elseif ($tauxCompletion >= 75) $evaluation = 'Bonne';
        elseif ($tauxCompletion >= 50) $evaluation = 'Moyenne';
        
        return [
            'score' => round($tauxCompletion, 2),
            'evaluation' => $evaluation,
            'description' => "Impact des projets réalisés",
            'details' => [
                'projets_total' => $projets->count(),
                'projets_completes' => $projetsCompletes
            ]
        ];
    }

    private function calculateScoreGlobal(array $indicateurs): float
    {
        $poids = [
            'execution_budgetaire' => 0.3,
            'gestion_delais' => 0.2,
            'gouvernance' => 0.2,
            'conformite' => 0.15,
            'impact_projets' => 0.15
        ];
        
        $scoreTotal = 0;
        foreach ($indicateurs as $key => $indicateur) {
            $scoreTotal += ($indicateur['score'] ?? 0) * ($poids[$key] ?? 0);
        }
        
        return round($scoreTotal, 2);
    }

    private function findSimilarCommunes(Commune $commune): Collection
    {
        // Critères de similarité
        $populationMin = $commune->population * 0.7;
        $populationMax = $commune->population * 1.3;
        
        return Commune::where('id', '!=', $commune->id)
            ->where('departement_id', $commune->departement_id) // Même département
            ->when($commune->population, function($query) use ($populationMin, $populationMax) {
                return $query->whereBetween('population', [$populationMin, $populationMax]);
            })
            ->with(['previsions', 'realisations', 'tauxRealisations'])
            ->limit(10)
            ->get();
    }

    private function getMetriquesCommune(Commune $commune, int $annee): array
    {
        $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
        $realisation = $commune->realisations->where('annee_exercice', $annee)->sum('montant');
        
        return [
            'taux_execution' => $prevision && $prevision->montant > 0 ? 
                ($realisation / $prevision->montant) * 100 : 0,
            'budget_par_habitant' => $commune->population > 0 && $prevision ? 
                $prevision->montant / $commune->population : 0,
            'nb_projets_realises' => $commune->projets()
                ->whereYear('date_debut', $annee)
                ->where('statut', 'termine')
                ->count(),
            'delai_moyen_execution' => 30, // À calculer selon vos données
            'score_gouvernance' => $this->evaluateGouvernance($commune)['score']
        ];
    }

    private function getEvaluationFromScore(float $score): string
    {
        if ($score >= 90) return 'Excellente';
        if ($score >= 75) return 'Bonne';
        if ($score >= 50) return 'Moyenne';
        return 'Faible';
    }

    private function generateRecommandations(array $indicateurs, Commune $commune): array
    {
        $recommandations = [];
        
        foreach ($indicateurs as $key => $indicateur) {
            if ($indicateur['score'] < 75) {
                $recommandations[] = $this->getRecommandationForIndicateur($key, $indicateur['score']);
            }
        }
        
        return $recommandations;
    }

    private function getRecommandationForIndicateur(string $indicateur, float $score): array
    {
        $recommandations = [
            'execution_budgetaire' => [
                'titre' => 'Améliorer l\'exécution budgétaire',
                'description' => 'Renforcer le suivi budgétaire et optimiser les processus d\'exécution',
                'priorite' => 'haute'
            ],
            'gestion_delais' => [
                'titre' => 'Optimiser la gestion des délais',
                'description' => 'Mettre en place un système de suivi des projets plus rigoureux',
                'priorite' => 'moyenne'
            ],
            'gouvernance' => [
                'titre' => 'Renforcer la gouvernance',
                'description' => 'Améliorer les structures de gouvernance et la transparence',
                'priorite' => 'haute'
            ]
        ];
        
        return $recommandations[$indicateur] ?? [
            'titre' => 'Améliorer la performance',
            'description' => 'Actions d\'amélioration nécessaires',
            'priorite' => 'moyenne'
        ];
    }

    private function calculatePercentile(float $valeur, Collection $donnees): int
    {
        $sorted = $donnees->sort()->values();
        $position = $sorted->search(function($item) use ($valeur) {
            return $item >= $valeur;
        });
        
        return $position !== false ? round(($position / $sorted->count()) * 100) : 100;
    }

    // private function getPositionRelative(float $valeur, float $moyenne): string
    // {
    //     $ecart = (($valeur - $moyenne) / $moyenne) * 100;
        
    //     if ($ecart > 10) return 'Supérieure';
    //     if ($ecart < -10) return 'Inférieure';
    //     return 'Similaire';
    // }

    private function getDescriptionExecutionBudgetaire(float $score): string
    {
        if ($score >= 90) return 'Exécution budgétaire excellente, très bonne maîtrise financière';
        if ($score >= 75) return 'Bonne exécution budgétaire avec quelques axes d\'amélioration';
        if ($score >= 50) return 'Exécution budgétaire moyenne, des efforts sont nécessaires';
        return 'Exécution budgétaire faible, amélioration urgente requise';
    }

    // Stubs pour les méthodes KPI - à implémenter selon vos besoins
    private function getKPIExecutionBudgetaire(Commune $commune, int $annee): array
    {
        return ['valeur' => 75.5, 'unite' => '%', 'evolution' => '+2.3%'];
    }

    private function getKPIEfficienceDepenses(Commune $commune, int $annee): array
    {
        return ['valeur' => 68.2, 'unite' => '%', 'evolution' => '+1.8%'];
    }

    private function getKPIAutonomieFinanciere(Commune $commune, int $annee): array
    {
        return ['valeur' => 45.7, 'unite' => '%', 'evolution' => '-0.5%'];
    }

    private function getKPIRealisationProjets(Commune $commune, int $annee): array
    {
        return ['valeur' => 82.3, 'unite' => '%', 'evolution' => '+5.2%'];
    }

    private function getKPIRespectDelais(Commune $commune, int $annee): array
    {
        return ['valeur' => 71.8, 'unite' => '%', 'evolution' => '+3.1%'];
    }

    private function getKPISatisfactionUsagers(Commune $commune, int $annee): array
    {
        return ['valeur' => 76.4, 'unite' => '/10', 'evolution' => '+0.8%'];
    }

    private function getKPITransparence(Commune $commune, int $annee): array
    {
        return ['valeur' => 65.9, 'unite' => '%', 'evolution' => '+4.2%'];
    }

    private function getKPIParticipationCitoyenne(Commune $commune, int $annee): array
    {
        return ['valeur' => 42.1, 'unite' => '%', 'evolution' => '+1.5%'];
    }

    private function getKPIConformiteProcedures(Commune $commune, int $annee): array
    {
        return ['valeur' => 88.7, 'unite' => '%', 'evolution' => '+2.9%'];
    }

    private function analyzeStrengthsWeaknesses(array $indicateurs, Commune $commune): array
    {
        $pointsForts = [];
        $axesAmelioration = [];
        
        foreach ($indicateurs as $key => $indicateur) {
            if ($indicateur['score'] >= 80) {
                $pointsForts[] = $indicateur['evaluation'] . ' en ' . str_replace('_', ' ', $key);
            } elseif ($indicateur['score'] < 60) {
                $axesAmelioration[] = 'Améliorer ' . str_replace('_', ' ', $key);
            }
        }
        
        return [
            'points_forts' => $pointsForts,
            'axes_amelioration' => $axesAmelioration
        ];
    }

    private function calculateEvolutions(array $tendances): array
    {
        $evolution = [];
        $annees = array_keys($tendances);
        
        for ($i = 1; $i < count($annees); $i++) {
            $anneeActuelle = $annees[$i];
            $anneePrecedente = $annees[$i-1];
            
            $evolution[$anneeActuelle] = [
                'taux_execution' => $this->calculateEvolutionPercentage(
                    $tendances[$anneePrecedente]['taux_execution'],
                    $tendances[$anneeActuelle]['taux_execution']
                ),
                'realisation' => $this->calculateEvolutionPercentage(
                    $tendances[$anneePrecedente]['realisation'],
                    $tendances[$anneeActuelle]['realisation']
                )
            ];
        }
        
        return $evolution;
    }

    private function calculateEvolutionPercentage(float $valeurPrecedente, float $valeurActuelle): float
    {
        if ($valeurPrecedente == 0) return $valeurActuelle > 0 ? 100 : 0;
        return round((($valeurActuelle - $valeurPrecedente) / $valeurPrecedente) * 100, 2);
    }

    private function determineTendanceGlobale(array $evolution): string
    {
        if (empty($evolution)) return 'Stable';
        
        $moyenneEvolution = collect($evolution)->avg('taux_execution');
        
        if ($moyenneEvolution > 5) return 'Croissante';
        if ($moyenneEvolution < -5) return 'Décroissante';
        return 'Stable';
    }

    private function generatePredictions(array $tendances): array
    {
        // Prédiction simple basée sur la tendance
        $dernieresAnnees = array_slice($tendances, -3, 3, true);
        $moyenneTaux = collect($dernieresAnnees)->avg('taux_execution');
        
        return [
            'taux_execution_prevu' => round($moyenneTaux, 2),
            'confiance' => 'Moyenne',
            'note' => 'Prédiction basée sur les 3 dernières années'
        ];
    }

    private function calculateScoreAnnuel(Commune $commune, int $annee): float
    {
        // Score simplifié - à adapter selon vos critères
        $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
        $realisation = $commune->realisations->where('annee_exercice', $annee)->sum('montant');
        
        if (!$prevision || $prevision->montant == 0) return 0;
        
        $tauxExecution = ($realisation / $prevision->montant) * 100;
        return min(100, max(0, $tauxExecution));
    }

    private function getCriteresSimilarite(Commune $commune): array
    {
        return [
            'Même département',
            'Population similaire (±30%)',
            'Même région économique'
        ];
    }

    private function generateResumeComparaison(array $comparaisons): array
    {
        $performances = collect($comparaisons)->where('position', 'Supérieure')->count();
        $total = count($comparaisons);
        
        return [
            'performances_superieures' => $performances,
            'total_metriques' => $total,
            'pourcentage_superiorite' => round(($performances / $total) * 100, 2)
        ];
    }
}