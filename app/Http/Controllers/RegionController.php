<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Defaillance;
use App\Models\Departement;
use App\Models\Depot_compte;
use App\Models\dette_cnps;
use App\Models\Region;
use App\Models\Taux_realisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegionController extends Controller


{ 


    /**
     * Affichage du formulaire de création d'une région
     */
    public function create()
    {
        // Débogage - ajoutez ceci temporairement
        \Log::info('Méthode create() appelée');
        
        // Vérifiez que la vue existe
        if (!view()->exists('regions.create')) {
            \Log::error("La vue regions.create n\'existe pas'");
            abort(404, 'Vue non trouvée');
        }
        
        \Log::info('Tentative de rendu de la vue regions.create');
        
        try {
            return view('regions.create');
        } catch (\Exception $e) {
            \Log::error('Erreur lors du rendu de la vue: ' . $e->getMessage());
            throw $e;
        }


        //  return view('regions.create');


    }

    /**
     * Affichage de la liste des régions avec possibilité de gestion
     */
    public function index()
    {
        $annee = request('annee', date('Y'));
        
        try {
            $regions = Region::with(['departements.communes'])
                ->get()
                ->map(function($region) use ($annee) {
                    return [
                        'id' => $region->id,
                        'nom' => $region->nom,
                        'nb_departements' => $region->departements->count(),
                        'nb_communes' => $this->getNbCommunesParRegion($region->id),
                        'taux_moyen_realisation' => $this->getTauxMoyenRealisation($region->id, $annee),
                        'total_dettes_cnps' => $this->getTotalDettesCNPS($region->id, $annee),
                        'conformite_depots' => $this->getConformiteDepots($region->id, $annee),
                        'status' => $this->getStatusRegion($region->id, $annee)
                    ];
                });
            
            // Variables pour éviter les erreurs dans la vue
            $budgetData = [];
            $tauxRealisationData = [];
            
            return view('regions.index', compact('regions', 'annee', 'budgetData', 'tauxRealisationData'));
            
        } catch (\Exception $e) {
            \Log::error('Erreur dans RegionController@index: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du chargement des données');
        }
    }

    /**
     * Enregistrement d'une nouvelle région
     */
    public function store(Request $request)
    {
        \Log::info('Méthode store() appelée avec données: ', $request->all());
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:regions,nom'
        ], [
            'nom.required' => 'Le nom de la région est obligatoire.',
            'nom.unique' => 'Une région avec ce nom existe déjà.',
            'nom.max' => 'Le nom de la région ne peut pas dépasser 255 caractères.'
        ]);

        if ($validator->fails()) {
            \Log::warning('Validation échouée: ', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $region = Region::create([
                'nom' => trim($request->nom)
            ]);

            DB::commit();
            
            \Log::info("'Région créée avec succès: ', $region->toArray()");

            return redirect()->route('regions.index')
                ->with('success', 'Région créée avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur lors de la création: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de la région: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ... Resto des méthodes identiques ...
    
    private function getTauxMoyenRealisation($regionId, $annee)
    {
        return Taux_realisation::whereHas('commune.departement', function($q) use ($regionId) {
            $q->where('region_id', $regionId);
        })->where('annee_exercice', $annee)->avg('pourcentage') ?? 0;
    }
    
    



    private function getTotalDettesCNPS($regionId, $annee)
{
    return dette_cnps::whereHas('commune.departement', function($q) use ($regionId) {
        $q->where('region_id', $regionId);
    })
    ->whereYear('date_evaluation', $annee)
    ->sum('montant') ?? 0;
}


private function getEtatComptesParCommune($regionId, $annee)
{
    return Commune::whereHas('departement', function($q) use ($regionId) {
        $q->where('region_id', $regionId);
    })->with(['departement', 'receveurs', 'ordonnateurs', 'depotsComptes', 'dettesCnps', 'previsions', 'realisations', 'tauxRealisations'])
    ->get()
    ->map(function($commune) use ($annee) {
        // Utiliser filter() au lieu de whereYear() sur les collections
        $depotCompte = $commune->depotsComptes
            ->where('annee_exercice', $annee)->first();
            
        // Pour les dates, utiliser filter() avec une fonction de callback
        $detteCnps = $commune->dettesCnps
            ->filter(function($dette) use ($annee) {
                return $dette->date_evaluation && 
                       \Carbon\Carbon::parse($dette->date_evaluation)->year == $annee;
            })->first();
            
        return [
            'id' => $commune->id,
            'code' => $commune->code,
            'commune' => $commune->nom,
            'departement' => $commune->departement->nom,
            'telephone' => $commune->telephone,
            'receveur' => $commune->receveurs->first()?->nom,
            'ordonnateur' => $commune->ordonnateurs->first()?->nom,
            'depot_date' => $depotCompte?->date_depot,
            'depot_valide' => $depotCompte?->validation ?? false,
            'prevision' => $commune->previsions->where('annee_exercice', $annee)->first()?->montant,
            'realisation' => $commune->realisations->where('annee_exercice', $annee)->sum('montant'),
            'dette_cnps' => $detteCnps?->montant ?? 0,
            'taux_realisation' => $commune->tauxRealisations->where('annee_exercice', $annee)->first()?->pourcentage ?? 0,
            'status' => $this->getStatusCommune($commune, $annee)
        ];
    });
}


private function getDefaillancesRegion($regionId, $annee)
{
    return Defaillance::whereHas('commune.departement', function($q) use ($regionId) {
        $q->where('region_id', $regionId);
    })
    ->whereYear('date_constat', $annee) // whereYear() dans la requête principale
    ->with('commune')
    ->get()
    ->map(function($defaillance) {
        return [
            'commune' => $defaillance->commune->nom,
            'type_defaillance' => $defaillance->type_defaillance,
            'date_constat' => $defaillance->date_constat,
            'description' => $defaillance->description,
            'gravite' => $defaillance->gravite ?? 'normale',
            'status' => $defaillance->est_resolue ? 'Résolu' : 'Non résolu'
        ];
    });
}


private function getConformiteDepots($regionId, $annee)
{
    // Option 1: Requête directe (plus efficace)
    $total = Depot_compte::whereHas('commune.departement', function($q) use ($regionId) {
        $q->where('region_id', $regionId);
    })->whereYear('date_depot', $annee)->count();
    
    $conformes = Depot_compte::whereHas('commune.departement', function($q) use ($regionId) {
        $q->where('region_id', $regionId);
    })->whereYear('date_depot', $annee)->where('validation', true)->count();
    
    return $total > 0 ? ($conformes / $total) * 100 : 0;
}


private function getConformiteDepotsAvecCollection($regionId, $annee)
{
    $depots = Depot_compte::whereHas('commune.departement', function($q) use ($regionId) {
        $q->where('region_id', $regionId);
    })->get();
    
    // Filtrer par année sur la collection
    $depotsAnnee = $depots->filter(function($depot) use ($annee) {
        return $depot->date_depot && 
               \Carbon\Carbon::parse($depot->date_depot)->year == $annee;
    });
    
    $total = $depotsAnnee->count();
    $conformes = $depotsAnnee->where('validation', true)->count();
    
    return $total > 0 ? ($conformes / $total) * 100 : 0;
}
    
    private function getNbCommunesParRegion($regionId)
    {
        return Commune::whereHas('departement', function($q) use ($regionId) {
            $q->where('region_id', $regionId);
        })->count();
    }
    
    private function getStatusRegion($regionId, $annee)
    {
        $tauxMoyen = $this->getTauxMoyenRealisation($regionId, $annee);
        
        if ($tauxMoyen >= 85) return 'Excellent';
        if ($tauxMoyen >= 70) return 'Bon';
        if ($tauxMoyen >= 50) return 'Moyen';
        return 'Faible';
    }

     

    /**
     * Affichage des détails d'une région
     */
    public function show(Region $region)
    {
        $annee = request('annee', date('Y'));
        
        // Statistiques de la région
        $stats = [
            'nb_communes' => $this->getNbCommunesParRegion($region->id),
            'taux_moyen_realisation' => $this->getTauxMoyenRealisation($region->id, $annee),
            'total_dettes_cnps' => $this->getTotalDettesCNPS($region->id, $annee),
            'conformite_depots' => $this->getConformiteDepots($region->id, $annee)
        ];
        
        // Budget Prévisions vs Réalisations par département
        $budgetData = $this->getBudgetParDepartement($region->id, $annee);
        
        // Taux de réalisation par département (graphique en donut)
        $tauxRealisationData = $this->getTauxRealisationParDepartement($region->id, $annee);
        
        // État des comptes et conformités (tableau)
        $etatComptes = $this->getEtatComptesParCommune($region->id, $annee);
        
        // Défaillances et problèmes de conformité
        $defaillances = $this->getDefaillancesRegion($region->id, $annee);
        
        return view('regions.show', compact(
            'region', 'stats', 'budgetData', 'tauxRealisationData', 
            'etatComptes', 'defaillances', 'annee'
        ));
    }

    /**
     * Affichage du formulaire d'édition d'une région
     */
    public function edit(Region $region)
    {
        return view('regions.edit', compact('region'));
    }

    /**
     * Mise à jour d'une région
     */
    public function update(Request $request, Region $region)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:regions,nom,' . $region->id
        ], [
            'nom.required' => 'Le nom de la région est obligatoire.',
            'nom.unique' => 'Une région avec ce nom existe déjà.',
            'nom.max' => 'Le nom de la région ne peut pas dépasser 255 caractères.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $region->update([
                'nom' => trim($request->nom)
            ]);

            DB::commit();

            return redirect()->route('regions.index')
                ->with('success', 'Région modifiée avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification de la région: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * API pour récupérer les données d'une région (pour AJAX)
     */
    public function getRegionData(Region $region)
    {
        $annee = request('annee', date('Y'));
        
        return response()->json([
            'region' => $region,
            'stats' => [
                'nb_communes' => $this->getNbCommunesParRegion($region->id),
                'taux_moyen_realisation' => $this->getTauxMoyenRealisation($region->id, $annee),
                'total_dettes_cnps' => $this->getTotalDettesCNPS($region->id, $annee),
                'conformite_depots' => $this->getConformiteDepots($region->id, $annee)
            ],
            'budgetData' => $this->getBudgetParDepartement($region->id, $annee),
            'tauxRealisationData' => $this->getTauxRealisationParDepartement($region->id, $annee),
            'etatComptes' => $this->getEtatComptesParCommune($region->id, $annee),
            'defaillances' => $this->getDefaillancesRegion($region->id, $annee)
        ]);
    }

    
    private function getBudgetParDepartement($regionId, $annee)
    {
        return Departement::where('region_id', $regionId)
            ->with(['communes.previsions', 'communes.realisations'])
            ->get()
            ->map(function($dept) use ($annee) {
                $previsions = $dept->communes->flatMap->previsions
                    ->where('annee_exercice', $annee)->sum('montant');
                $realisations = $dept->communes->flatMap->realisations
                    ->where('annee_exercice', $annee)->sum('montant');
                    
                return [
                    'departement' => $dept->nom,
                    'previsions' => $previsions,
                    'realisations' => $realisations
                ];
            });
    }
    
    private function getTauxRealisationParDepartement($regionId, $annee)
    {
        return Departement::where('region_id', $regionId)
            ->with(['communes.tauxRealisations'])
            ->get()
            ->map(function($dept) use ($annee) {
                $tauxMoyen = $dept->communes->flatMap->tauxRealisations
                    ->where('annee_exercice', $annee)->avg('pourcentage');
                    
                return [
                    'departement' => $dept->nom,
                    'taux' => round($tauxMoyen ?? 0, 2)
                ];
            });
    }

      private function getStatusCommune($commune, $annee)
    {
        $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
        
        if (!$tauxRealisation) return 'Non défini';
        
        if ($tauxRealisation->pourcentage >= 90) return 'Conforme';
        if ($tauxRealisation->pourcentage >= 75) return 'Moyen';
        return 'Non conforme';
    }
    
   
}



    