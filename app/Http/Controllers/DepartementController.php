<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use Illuminate\Http\Request;
use App\Models\Departement;
use App\Models\dette_cnps;
use App\Models\dette_feicom;
use App\Models\dette_fiscale;
use App\Models\dette_salariale;
use App\Models\Region;
use App\Models\Taux_realisation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class DepartementController extends Controller
{

    

    /**
     * Affichage de la liste des départements avec statistiques
     */
    public function index()
    {
        $departements = Departement::with(['region', 'communes'])
            ->withCount('communes')
            ->orderBy('nom')
            ->paginate(20);

        // Ajouter les statistiques pour chaque département
        $departements->getCollection()->transform(function ($departement) {
            $annee = date('Y');
            $departement->stats = [
                'taux_moyen_realisation' => $this->getTauxMoyenRealisationDept($departement->id, $annee),
                'total_dettes' => $this->getTotalDettesDept($departement->id, $annee),
                'communes_conformes' => $this->getCommunesConformes($departement->id, $annee)
            ];
            return $departement;
        });

        return view('departements.index', compact('departements'));
    }

    /**
     * Affichage du formulaire de création
     */
    public function create()
    {
        $regions = Region::orderBy('nom')->get();
        return view('departements.create', compact('regions'));
    }

    /**
     * Enregistrement d'un nouveau département
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255|unique:departements',
            'region_id' => 'required|exists:regions,id',
        ]);

        try {
            $departement = Departement::create($validatedData);
            
            // Vider le cache si utilisé
            $this->clearDepartementCache();

            return redirect()->route('departements.show', $departement)
                ->with('success', 'Département créé avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du département.');
        }
    }

    /**
     * Affichage des détails d'un département
     */
    public function show(Departement $departement)
    {
        $annee = request('annee', date('Y'));
        
        // Charger les relations nécessaires
        $departement->load(['region', 'communes']);
        
        // Statistiques du département (avec cache pour optimisation)
        $stats = Cache::remember("dept_stats_{$departement->id}_{$annee}", 3600, function () use ($departement, $annee) {
            return [
                'nb_communes' => $departement->communes()->count(),
                'taux_moyen_realisation' => $this->getTauxMoyenRealisationDept($departement->id, $annee),
                'total_dettes' => $this->getTotalDettesDept($departement->id, $annee),
                'communes_conformes' => $this->getCommunesConformes($departement->id, $annee)
            ];
        });
        
        // Détails des communes du département
        $communes = $this->getDetailsCommunesDepartement($departement->id, $annee);
        
        // Évolution des performances
        $evolutionPerformances = $this->getEvolutionPerformancesDept($departement->id);
        
        // Années disponibles pour le filtre
        $anneesDisponibles = $this->getAnneesDisponibles($departement->id);
        
        return view('departements.show', compact(
            'departement', 'stats', 'communes', 'evolutionPerformances', 'annee', 'anneesDisponibles'
        ));
    }

    /**
     * Affichage du formulaire de modification
     */
    public function edit(Departement $departement)
    {
        $regions = Region::orderBy('nom')->get();
        return view('departements.edit', compact('departement', 'regions'));
    }

    /**
     * Mise à jour d'un département
     */
    public function update(Request $request, Departement $departement)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255|unique:departements,nom,' . $departement->id,
            'region_id' => 'required|exists:regions,id',
        ]);

        try {
            $departement->update($validatedData);
            
            // Vider le cache
            $this->clearDepartementCache($departement->id);

            return redirect()->route('departements.show', $departement)
                ->with('success', 'Département modifié avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification du département.');
        }
    }

    /**
     * Suppression d'un département
     */
    public function destroy(Departement $departement)
    {
        // Vérifier s'il y a des communes associées
        if ($departement->communes()->count() > 0) {
            return redirect()->route('departements.index')
                ->with('error', 'Impossible de supprimer ce département car il contient des communes.');
        }

        try {
            $departement->delete();
            
            // Vider le cache
            $this->clearDepartementCache($departement->id);

            return redirect()->route('departements.index')
                ->with('success', 'Département supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('departements.index')
                ->with('error', 'Erreur lors de la suppression du département.');
        }
    }

    /**
     * Export des données du département
     */
    public function export(Departement $departement, Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $format = $request->get('format', 'excel'); // excel, pdf, csv
        
        $data = [
            'departement' => $departement->load('region'),
            'stats' => [
                'nb_communes' => $departement->communes()->count(),
                'taux_moyen_realisation' => $this->getTauxMoyenRealisationDept($departement->id, $annee),
                'total_dettes' => $this->getTotalDettesDept($departement->id, $annee),
                'communes_conformes' => $this->getCommunesConformes($departement->id, $annee)
            ],
            'communes' => $this->getDetailsCommunesDepartement($departement->id, $annee),
            'annee' => $annee
        ];

        // Logique d'export selon le format
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($data);
            case 'csv':
                return $this->exportToCsv($data);
            default:
                return $this->exportToExcel($data);
        }
    }

    // ================== MÉTHODES PRIVÉES ==================

    /**
     * Calculer le taux moyen de réalisation d'un département
     */
    private function getTauxMoyenRealisationDept($departementId, $annee)
    {
        return Taux_realisation::whereHas('commune', function($query) use ($departementId) {
            $query->where('departement_id', $departementId);
        })
        ->where('annee_exercice', $annee)
        ->avg('pourcentage') ?? 0;
    }

    /**
     * Calculer le total des dettes d'un département
     */
    private function getTotalDettesDept($departementId, $annee)
    {
        $communeIds = Commune::where('departement_id', $departementId)->pluck('id');

        if ($communeIds->isEmpty()) return 0;

        $dettesCnps = dette_cnps::whereIn('commune_id', $communeIds)
            ->whereYear('date_evaluation', $annee)
            ->sum('montant');

        $dettesFiscale = dette_fiscale::whereIn('commune_id', $communeIds)
            ->whereYear('date_evaluation', $annee)
            ->sum('montant');

        $dettesFeicom = dette_feicom::whereIn('commune_id', $communeIds)
            ->whereYear('date_evaluation', $annee)
            ->sum('montant');

        $dettesSalariale = dette_salariale::whereIn('commune_id', $communeIds)
            ->whereYear('date_evaluation', $annee)
            ->sum('montant');

        return $dettesCnps + $dettesFiscale + $dettesFeicom + $dettesSalariale;
    }

    /**
     * Calculer le pourcentage de communes conformes
     */
    private function getCommunesConformes($departementId, $annee)
    {
        $total = Commune::where('departement_id', $departementId)->count();
        
        if ($total === 0) return 0;

        $conformes = Commune::where('departement_id', $departementId)
            ->whereHas('tauxRealisations', function($query) use ($annee) {
                $query->where('annee_exercice', $annee)
                      ->where('pourcentage', '>=', 75);
            })
            ->count();

        return round(($conformes / $total) * 100, 2);
    }

    /**
     * Obtenir les détails des communes d'un département
     */
    private function getDetailsCommunesDepartement($departementId, $annee)
    {
        return Commune::where('departement_id', $departementId)
            ->with([
                'receveurs' => function($query) {
                    $query->latest()->limit(1);
                },
                'ordonnateurs' => function($query) {
                    $query->latest()->limit(1);
                },
                'depotsComptes' => function($query) use ($annee) {
                    $query->where('annee_exercice', $annee);
                },
                'previsions' => function($query) use ($annee) {
                    $query->where('annee_exercice', $annee);
                },
                'realisations' => function($query) use ($annee) {
                    $query->where('annee_exercice', $annee);
                },
                'tauxRealisations' => function($query) use ($annee) {
                    $query->where('annee_exercice', $annee);
                }
            ])
            ->get()
            ->map(function($commune) use ($annee) {
                return $this->formatCommuneData($commune, $annee);
            });
    }

    /**
     * Formater les données d'une commune
     */
    private function formatCommuneData($commune, $annee)
    {
        $depotCompte = $commune->depotsComptes->first();
        $prevision = $commune->previsions->first();
        $realisationTotal = $commune->realisations->sum('montant');
        $tauxRealisation = $commune->tauxRealisations->first();

        // Calcul des dettes totales optimisé
        $dettesTotal = $this->calculateCommuneDettes($commune->id, $annee);

        return [
            'id' => $commune->id,
            'nom' => $commune->nom,
            'code' => $commune->code,
            'telephone' => $commune->telephone,
            'receveur' => $commune->receveurs->first()?->nom,
            'ordonnateur' => $commune->ordonnateurs->first()?->nom,
            'depot_date' => $depotCompte?->date_depot,
            'depot_valide' => $depotCompte?->validation ?? false,
            'prevision' => $prevision?->montant ?? 0,
            'realisation' => $realisationTotal,
            'taux_realisation' => $tauxRealisation?->pourcentage ?? 0,
            'evaluation' => $tauxRealisation?->evaluation ?? 'Non évalué',
            'dettes_total' => $dettesTotal,
            'nb_defaillances' => $commune->defaillances()->where('est_resolue', false)->count(),
            'nb_retards' => $commune->retards()->whereYear('date_constat', $annee)->count(),
            'status' => $this->determinerStatusCommune($commune, $annee, $tauxRealisation)
        ];
    }

    /**
     * Calculer les dettes d'une commune
     */
    private function calculateCommuneDettes($communeId, $annee)
    {
        return DB::table('dette_cnps')->where('commune_id', $communeId)->whereYear('date_evaluation', $annee)->sum('montant') +
               DB::table('dette_fiscales')->where('commune_id', $communeId)->whereYear('date_evaluation', $annee)->sum('montant') +
               DB::table('dette_feicoms')->where('commune_id', $communeId)->whereYear('date_evaluation', $annee)->sum('montant') +
               DB::table('dette_salariales')->where('commune_id', $communeId)->whereYear('date_evaluation', $annee)->sum('montant');
    }

    /**
     * Obtenir l'évolution des performances d'un département
     */
    private function getEvolutionPerformancesDept($departementId)
    {
        return DB::table('taux_realisations')
            ->join('communes', 'taux_realisations.commune_id', '=', 'communes.id')
            ->where('communes.departement_id', $departementId)
            ->selectRaw('annee_exercice, AVG(pourcentage) as taux_moyen, COUNT(*) as nb_communes')
            ->groupBy('annee_exercice')
            ->orderBy('annee_exercice')
            ->get();
    }

    /**
     * Déterminer le statut d'une commune
     */
    private function determinerStatusCommune($commune, $annee, $tauxRealisation = null)
    {
        $defaillances = $commune->defaillances()->where('est_resolue', false)->count();
        $retards = $commune->retards()->whereYear('date_constat', $annee)->count();
        
        if ($defaillances > 0 || $retards > 0) {
            return 'Non conforme';
        }

        if (!$tauxRealisation || $tauxRealisation->pourcentage < 50) {
            return 'Non conforme';
        }

        if ($tauxRealisation->pourcentage >= 90) {
            return 'Excellent';
        }

        if ($tauxRealisation->pourcentage >= 75) {
            return 'Conforme';
        }

        return 'Moyen';
    }

    /**
     * Obtenir les années disponibles pour un département
     */
    private function getAnneesDisponibles($departementId)
    {
        return DB::table('taux_realisations')
            ->join('communes', 'taux_realisations.commune_id', '=', 'communes.id')
            ->where('communes.departement_id', $departementId)
            ->distinct()
            ->orderByDesc('annee_exercice')
            ->pluck('annee_exercice');
    }

    /**
     * Vider le cache des départements
     */
    private function clearDepartementCache($departementId = null)
    {
        if ($departementId) {
            Cache::forget("dept_stats_{$departementId}_" . date('Y'));
            // Supprimer pour les autres années si nécessaire
            for ($year = 2020; $year <= date('Y'); $year++) {
                Cache::forget("dept_stats_{$departementId}_{$year}");
            }
        } else {
            Cache::flush(); // Attention: ceci vide tout le cache
        }
    }

    /**
     * Export vers Excel (à implémenter selon vos besoins)
     */
    private function exportToExcel($data)
    {
        // Implémentation avec Laravel Excel ou autre
        return response()->json(['message' => 'Export Excel à implémenter']);
    }

    /**
     * Export vers PDF (à implémenter selon vos besoins)
     */
    private function exportToPdf($data)
    {
        // Implémentation avec DomPDF ou autre
        return response()->json(['message' => 'Export PDF à implémenter']);
    }

    /**
     * Export vers CSV (à implémenter selon vos besoins)
     */
    private function exportToCsv($data)
    {
        // Implémentation CSV
        return response()->json(['message' => 'Export CSV à implémenter']);
    }
}