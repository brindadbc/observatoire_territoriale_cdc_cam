<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commune;
use App\Models\Prevision;
use App\Models\Realisation;
use Illuminate\Support\Facades\DB;

class TauxRealisationController extends Controller
{
    /**
     * Liste des taux de réalisation calculés dynamiquement
     */
    public function index(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        
        // Requête de base pour obtenir toutes les prévisions avec leurs réalisations
        $query = Prevision::with(['commune.departement.region', 'realisations'])
            ->where('annee_exercice', $annee);
        
        // Filtres
        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->commune_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('commune', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }
        
        // Filtrage par évaluation
        if ($request->filled('evaluation')) {
            $evaluation = $request->evaluation;
            $query->whereHas('realisations', function($q) use ($evaluation) {
                // Nous filtrerons après le calcul des taux
            });
        }
        
        $previsions = $query->get();
        
        // Calculer les taux de réalisation
        $tauxRealisations = $previsions->map(function($prevision) {
            $montantRealise = $prevision->realisations->sum('montant');
            $taux = $prevision->montant > 0 ? ($montantRealise / $prevision->montant) * 100 : 0;
            
            return (object) [
                'id' => $prevision->id,
                'commune' => $prevision->commune,
                'annee_exercice' => $prevision->annee_exercice,
                'montant_prevision' => $prevision->montant,
                'montant_realise' => $montantRealise,
                'pourcentage' => round($taux, 2),
                'evaluation' => $this->getEvaluation($taux),
                'ecart' => round($taux - 100, 2),
                'nb_realisations' => $prevision->realisations->count(),
                'derniere_realisation' => $prevision->realisations->sortByDesc('date_realisation')->first()?->date_realisation
            ];
        });
        
        // Filtrer par évaluation si nécessaire
        if ($request->filled('evaluation')) {
            $evaluation = $request->evaluation;
            $tauxRealisations = $tauxRealisations->filter(function($taux) use ($evaluation) {
                return $taux->evaluation === $evaluation;
            });
        }
        
        // Tri
        $sortBy = $request->get('sort_by', 'pourcentage');
        $sortDirection = $request->get('sort_direction', 'desc');
        $tauxRealisations = $tauxRealisations->sortBy($sortBy, SORT_REGULAR, $sortDirection === 'desc');
        
        // Pagination manuelle
        $currentPage = $request->get('page', 1);
        $perPage = 15;
        $total = $tauxRealisations->count();
        $items = $tauxRealisations->forPage($currentPage, $perPage);
        
        $paginatedTaux = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        // Données pour les filtres
        $communes = Commune::orderBy('nom')->get();
        $annees = Prevision::distinct()->pluck('annee_exercice')->sort()->values();
        
        // Statistiques
        $stats = $this->getStatistiques($tauxRealisations);
        
        return view('taux-realisations.index', compact(
            'paginatedTaux', 'communes', 'annees', 'stats', 'annee'
        ));
    }
    
    /**
     * Affichage des détails d'un taux de réalisation
     */
    public function show($previsionId)
    {
        $prevision = Prevision::with(['commune.departement.region', 'realisations'])
            ->findOrFail($previsionId);
        
        $montantRealise = $prevision->realisations->sum('montant');
        $taux = $prevision->montant > 0 ? ($montantRealise / $prevision->montant) * 100 : 0;
        
        $tauxRealisation = (object) [
            'id' => $prevision->id,
            'commune' => $prevision->commune,
            'annee_exercice' => $prevision->annee_exercice,
            'montant_prevision' => $prevision->montant,
            'montant_realise' => $montantRealise,
            'pourcentage' => round($taux, 2),
            'evaluation' => $this->getEvaluation($taux),
            'ecart' => round($taux - 100, 2),
            'realisations' => $prevision->realisations
        ];
        
        // Historique des taux pour cette commune
        $historique = $this->getHistoriqueCommune($prevision->commune_id, $prevision->annee_exercice);
        
        // Comparaison régionale
        $comparaison = $this->getComparaisonRegionale($prevision);
        
        // Évolution
        $evolution = $this->getEvolutionCommune($prevision->commune_id);
        
        return view('taux-realisations.show', compact(
            'tauxRealisation', 'historique', 'comparaison', 'evolution'
        ));
    }
    
    /**
     * Dashboard avec graphiques et analyses
     */
    public function dashboard(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        
        // Données pour les graphiques
        $evolutionAnnuelle = $this->getEvolutionAnnuelle();
        $repartitionEvaluations = $this->getRepartitionEvaluations($annee);
        $topCommunes = $this->getTopCommunes($annee);
        $comparaisonDepartements = $this->getComparaisonDepartements($annee);
        
        return view('taux-realisations.dashboard', compact(
            'evolutionAnnuelle', 'repartitionEvaluations', 'topCommunes', 
            'comparaisonDepartements', 'annee'
        ));
    }
    
    /**
     * Export des taux de réalisation
     */
    public function export(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $format = $request->get('format', 'excel');
        
        $previsions = Prevision::with(['commune.departement.region', 'realisations'])
            ->where('annee_exercice', $annee)
            ->get();
        
        $tauxRealisations = $previsions->map(function($prevision) {
            $montantRealise = $prevision->realisations->sum('montant');
            $taux = $prevision->montant > 0 ? ($montantRealise / $prevision->montant) * 100 : 0;
            
            return [
                'commune' => $prevision->commune->nom,
                'departement' => $prevision->commune->departement->nom,
                'region' => $prevision->commune->departement->region->nom,
                'annee' => $prevision->annee_exercice,
                'montant_prevision' => $prevision->montant,
                'montant_realise' => $montantRealise,
                'pourcentage' => round($taux, 2),
                'evaluation' => $this->getEvaluation($taux),
                'ecart' => round($taux - 100, 2)
            ];
        });
        
        return $this->generateExport($tauxRealisations, $format, $annee);
    }
    
    // ================== MÉTHODES PRIVÉES ==================
    
    private function getEvaluation($pourcentage)
    {
        if ($pourcentage >= 90) return 'Excellent';
        if ($pourcentage >= 75) return 'Bon';
        if ($pourcentage >= 50) return 'Moyen';
        return 'Insuffisant';
    }
    
    private function getStatistiques($tauxRealisations)
    {
        $total = $tauxRealisations->count();
        $moyenne = $tauxRealisations->avg('pourcentage');
        
        return [
            'total' => $total,
            'moyenne' => round($moyenne, 2),
            'excellent' => $tauxRealisations->where('evaluation', 'Excellent')->count(),
            'bon' => $tauxRealisations->where('evaluation', 'Bon')->count(),
            'moyen' => $tauxRealisations->where('evaluation', 'Moyen')->count(),
            'insuffisant' => $tauxRealisations->where('evaluation', 'Insuffisant')->count(),
        ];
    }
    
    private function getHistoriqueCommune($communeId, $anneeExclue)
    {
        return Prevision::with('realisations')
            ->where('commune_id', $communeId)
            ->where('annee_exercice', '!=', $anneeExclue)
            ->orderBy('annee_exercice', 'desc')
            ->get()
            ->map(function($prevision) {
                $montantRealise = $prevision->realisations->sum('montant');
                $taux = $prevision->montant > 0 ? ($montantRealise / $prevision->montant) * 100 : 0;
                
                return [
                    'annee' => $prevision->annee_exercice,
                    'pourcentage' => round($taux, 2),
                    'evaluation' => $this->getEvaluation($taux)
                ];
            });
    }
    
    private function getComparaisonRegionale($prevision)
    {
        return Prevision::with(['commune', 'realisations'])
            ->whereHas('commune.departement', function($q) use ($prevision) {
                $q->where('region_id', $prevision->commune->departement->region_id);
            })
            ->where('annee_exercice', $prevision->annee_exercice)
            ->where('id', '!=', $prevision->id)
            ->get()
            ->map(function($prev) {
                $montantRealise = $prev->realisations->sum('montant');
                $taux = $prev->montant > 0 ? ($montantRealise / $prev->montant) * 100 : 0;
                
                return [
                    'commune' => $prev->commune->nom,
                    'pourcentage' => round($taux, 2),
                    'evaluation' => $this->getEvaluation($taux)
                ];
            })
            ->sortByDesc('pourcentage')
            ->take(10);
    }
    
    private function getEvolutionCommune($communeId)
    {
        return Prevision::with('realisations')
            ->where('commune_id', $communeId)
            ->orderBy('annee_exercice')
            ->get()
            ->map(function($prevision) {
                $montantRealise = $prevision->realisations->sum('montant');
                $taux = $prevision->montant > 0 ? ($montantRealise / $prevision->montant) * 100 : 0;
                
                return [
                    'annee' => $prevision->annee_exercice,
                    'pourcentage' => round($taux, 2),
                    'evaluation' => $this->getEvaluation($taux)
                ];
            });
    }
    
    private function getEvolutionAnnuelle()
    {
        return DB::table('previsions')
            ->join('realisations', 'previsions.id', '=', 'realisations.prevision_id')
            ->selectRaw('
                previsions.annee_exercice,
                SUM(previsions.montant) as total_previsions,
                SUM(realisations.montant) as total_realisations,
                ROUND((SUM(realisations.montant) / SUM(previsions.montant)) * 100, 2) as taux_moyen
            ')
            ->groupBy('previsions.annee_exercice')
            ->orderBy('previsions.annee_exercice')
            ->get();
    }
    
    private function getRepartitionEvaluations($annee)
    {
        $previsions = Prevision::with('realisations')
            ->where('annee_exercice', $annee)
            ->get();
        
        $repartition = ['Excellent' => 0, 'Bon' => 0, 'Moyen' => 0, 'Insuffisant' => 0];
        
        foreach ($previsions as $prevision) {
            $montantRealise = $prevision->realisations->sum('montant');
            $taux = $prevision->montant > 0 ? ($montantRealise / $prevision->montant) * 100 : 0;
            $evaluation = $this->getEvaluation($taux);
            $repartition[$evaluation]++;
        }
        
        return $repartition;
    }
    
    private function getTopCommunes($annee, $limit = 10)
    {
        return Prevision::with(['commune', 'realisations'])
            ->where('annee_exercice', $annee)
            ->get()
            ->map(function($prevision) {
                $montantRealise = $prevision->realisations->sum('montant');
                $taux = $prevision->montant > 0 ? ($montantRealise / $prevision->montant) * 100 : 0;
                
                return [
                    'commune' => $prevision->commune->nom,
                    'pourcentage' => round($taux, 2),
                    'montant_prevision' => $prevision->montant,
                    'montant_realise' => $montantRealise
                ];
            })
            ->sortByDesc('pourcentage')
            ->take($limit);
    }
    
    private function getComparaisonDepartements($annee)
    {
        return DB::table('previsions')
            ->join('communes', 'previsions.commune_id', '=', 'communes.id')
            ->join('departements', 'communes.departement_id', '=', 'departements.id')
            ->leftJoin('realisations', 'previsions.id', '=', 'realisations.prevision_id')
            ->where('previsions.annee_exercice', $annee)
            ->selectRaw('
                departements.nom as departement,
                SUM(previsions.montant) as total_previsions,
                SUM(realisations.montant) as total_realisations,
                ROUND((SUM(realisations.montant) / SUM(previsions.montant)) * 100, 2) as taux_moyen
            ')
            ->groupBy('departements.id', 'departements.nom')
            ->orderByDesc('taux_moyen')
            ->get();
    }
    
    private function generateExport($data, $format, $annee)
    {
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($data, $annee);
            case 'csv':
                return $this->exportToCsv($data, $annee);
            default:
                return $this->exportToExcel($data, $annee);
        }
    }
    
    private function exportToCsv($data, $annee)
    {
        $filename = "taux_realisations_{$annee}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'Commune', 'Département', 'Région', 'Année', 
                'Montant Prévision', 'Montant Réalisé', 
                'Pourcentage', 'Évaluation', 'Écart'
            ]);
            
            // Données
            foreach ($data as $row) {
                fputcsv($file, [
                    $row['commune'],
                    $row['departement'],
                    $row['region'],
                    $row['annee'],
                    number_format($row['montant_prevision'], 2),
                    number_format($row['montant_realise'], 2),
                    $row['pourcentage'] . '%',
                    $row['evaluation'],
                    $row['ecart'] . '%'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

  

    /**
     * Service pour calculer les taux de réalisation
     */
    public function calculerTauxRealisation($prevision)
    {
        $montantRealise = $prevision->realisations->sum('montant');
        $taux = $prevision->montant > 0 ? ($montantRealise / $prevision->montant) * 100 : 0;
        
        return (object) [
            'id' => $prevision->id,
            'commune' => $prevision->commune,
            'annee_exercice' => $prevision->annee_exercice,
            'montant_prevision' => $prevision->montant,
            'montant_realise' => $montantRealise,
            'pourcentage' => round($taux, 2),
            'evaluation' => $this->getEvaluation($taux),
            'ecart' => round($taux - 100, 2),
            'nb_realisations' => $prevision->realisations->count(),
            'derniere_realisation' => $prevision->realisations->sortByDesc('date_realisation')->first()?->date_realisation
        ];
    }

    /**
     * Méthode pour obtenir les taux par commune
     */
    public function getTauxParCommune($communeId, $annee = null)
    {
        $annee = $annee ?? date('Y');
        
        $previsions = Prevision::with(['commune', 'realisations'])
            ->where('commune_id', $communeId)
            ->where('annee_exercice', $annee)
            ->get();
        
        return $previsions->map(function($prevision) {
            return $this->calculerTauxRealisation($prevision);
        });
    }

    /**
     * Méthode pour obtenir les taux par région
     */
    public function getTauxParRegion($regionId, $annee = null)
    {
        $annee = $annee ?? date('Y');
        
        $previsions = Prevision::with(['commune.departement.region', 'realisations'])
            ->whereHas('commune.departement', function($q) use ($regionId) {
                $q->where('region_id', $regionId);
            })
            ->where('annee_exercice', $annee)
            ->get();
        
        return $previsions->map(function($prevision) {
            return $this->calculerTauxRealisation($prevision);
        });
    }

    /**
     * Méthode pour obtenir les taux par département
     */
    public function getTauxParDepartement($departementId, $annee = null)
    {
        $annee = $annee ?? date('Y');
        
        $previsions = Prevision::with(['commune.departement', 'realisations'])
            ->whereHas('commune', function($q) use ($departementId) {
                $q->where('departement_id', $departementId);
            })
            ->where('annee_exercice', $annee)
            ->get();
        
        return $previsions->map(function($prevision) {
            return $this->calculerTauxRealisation($prevision);
        });
    }

    /**
     * Statistiques résumées pour une entité
     */
    public function getStatistiquesResumees($tauxRealisations)
    {
        if ($tauxRealisations->isEmpty()) {
            return [
                'total' => 0,
                'moyenne' => 0,
                'excellent' => 0,
                'bon' => 0,
                'moyen' => 0,
                'insuffisant' => 0,
                'taux_reussite' => 0
            ];
        }

        $total = $tauxRealisations->count();
        $moyenne = $tauxRealisations->avg('pourcentage');
        $excellent = $tauxRealisations->where('evaluation', 'Excellent')->count();
        $bon = $tauxRealisations->where('evaluation', 'Bon')->count();
        
        return [
            'total' => $total,
            'moyenne' => round($moyenne, 2),
            'excellent' => $excellent,
            'bon' => $bon,
            'moyen' => $tauxRealisations->where('evaluation', 'Moyen')->count(),
            'insuffisant' => $tauxRealisations->where('evaluation', 'Insuffisant')->count(),
            'taux_reussite' => round((($excellent + $bon) / $total) * 100, 2)
        ];
    }

}