<?php

namespace App\Http\Controllers;

use App\Models\Retard;
use App\Models\Commune;
use App\Models\Departement;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RetardController extends Controller
{
    /**
     * Affichage de la liste des retards avec pagination et filtres
     */
    public function index(Request $request)
    {
        $query = Retard::with(['commune.departement.region']);
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('type_retard', 'LIKE', "%{$search}%")
                  ->orWhereHas('commune', function($cq) use ($search) {
                      $cq->where('nom', 'LIKE', "%{$search}%");
                  });
            });
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
        
        // Filtrage par type de retard
        if ($request->filled('type_retard')) {
            $query->where('type_retard', $request->type_retard);
        }
        
        // Filtrage par niveau de gravité
        if ($request->filled('gravite')) {
            $gravite = $request->gravite;
            $query->where(function($q) use ($gravite) {
                if ($gravite === 'Faible') {
                    $q->where('duree_jours', '<=', 15);
                } elseif ($gravite === 'Moyenne') {
                    $q->whereBetween('duree_jours', [16, 30]);
                } elseif ($gravite === 'Élevée') {
                    $q->where('duree_jours', '>', 30);
                }
            });
        }
        
        // Filtrage par période
        if ($request->filled('date_debut')) {
            $query->whereDate('date_constat', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_constat', '<=', $request->date_fin);
        }
        
        // Tri
        $sortBy = $request->get('sort_by', 'date_constat');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);
        
        $retards = $query->paginate(15);
        
        // Données pour les filtres
        $communes = Commune::with(['departement.region'])->orderBy('nom')->get();
        $departements = Departement::with('region')->orderBy('nom')->get();
        $regions = Region::orderBy('nom')->get();
        
        // Types de retards disponibles
        $typesRetards = [
            'depot_compte' => 'Dépôt de compte',
            'realisation_budget' => 'Réalisation budgétaire',
            'rapport_activite' => 'Rapport d\'activité',
            'declaration_fiscale' => 'Déclaration fiscale',
            'paiement_salaire' => 'Paiement des salaires',
            'autre' => 'Autre'
        ];
        
        // Statistiques rapides
        $stats = [
            'total_retards' => Retard::count(),
            'retards_graves' => Retard::where('duree_jours', '>', 30)->count(),
            'retards_ce_mois' => Retard::whereMonth('date_constat', Carbon::now()->month)
                                     ->whereYear('date_constat', Carbon::now()->year)
                                     ->count(),
            'duree_moyenne' => round(Retard::avg('duree_jours'), 1)
        ];
        
        return view('retards.index', compact(
            'retards', 'communes', 'departements', 'regions', 
            'typesRetards', 'stats'
        ));
    }

    /**
     * Affichage du formulaire de création d'un retard
     */
    public function create()
    {
        $communes = Commune::with(['departement.region'])->orderBy('nom')->get();
        
        $typesRetards = [
            'depot_compte' => 'Dépôt de compte',
            'realisation_budget' => 'Réalisation budgétaire',
            'rapport_activite' => 'Rapport d\'activité',
            'declaration_fiscale' => 'Déclaration fiscale',
            'paiement_salaire' => 'Paiement des salaires',
            'autre' => 'Autre'
        ];
        
        return view('retards.create', compact('communes', 'typesRetards'));
    }

    /**
     * Enregistrement d'un nouveau retard
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_retard' => 'required|string|max:100',
            'duree_jours' => 'required|integer|min:1|max:365',
            'date_constat' => 'required|date|before_or_equal:today',
            'date_retard' => 'nullable|date|before_or_equal:date_constat',
            'commune_id' => 'required|exists:communes,id'
        ], [
            'type_retard.required' => 'Le type de retard est obligatoire.',
            'duree_jours.required' => 'La durée du retard est obligatoire.',
            'duree_jours.integer' => 'La durée doit être un nombre entier.',
            'duree_jours.min' => 'La durée minimum est de 1 jour.',
            'duree_jours.max' => 'La durée maximum est de 365 jours.',
            'date_constat.required' => 'La date de constat est obligatoire.',
            'date_constat.date' => 'La date de constat doit être une date valide.',
            'date_constat.before_or_equal' => 'La date de constat ne peut pas être dans le futur.',
            'date_retard.date' => 'La date de retard doit être une date valide.',
            'date_retard.before_or_equal' => 'La date de retard ne peut pas être postérieure à la date de constat.',
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $retard = Retard::create([
                'type_retard' => $request->type_retard,
                'duree_jours' => $request->duree_jours,
                'date_constat' => $request->date_constat,
                'date_retard' => $request->date_retard,
                'commune_id' => $request->commune_id
            ]);

            DB::commit();

            return redirect()->route('retards.show', $retard)
                ->with('success', 'Retard enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'enregistrement du retard: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affichage des détails d'un retard
     */
    public function show(Retard $retard)
    {
        $retard->load(['commune.departement.region']);
        
        // Autres retards de la même commune
        $autresRetards = Retard::where('commune_id', $retard->commune_id)
            ->where('id', '!=', $retard->id)
            ->orderBy('date_constat', 'desc')
            ->limit(5)
            ->get();
        
        // Statistiques de la commune
        $statsCommune = [
            'total_retards' => Retard::where('commune_id', $retard->commune_id)->count(),
            'retards_graves' => Retard::where('commune_id', $retard->commune_id)
                                     ->where('duree_jours', '>', 30)->count(),
            'duree_moyenne' => round(
                Retard::where('commune_id', $retard->commune_id)->avg('duree_jours'), 1
            )
        ];
        
        return view('retards.show', compact('retard', 'autresRetards', 'statsCommune'));
    }

    /**
     * Affichage du formulaire de modification d'un retard
     */
    public function edit(Retard $retard)
    {
        $communes = Commune::with(['departement.region'])->orderBy('nom')->get();
        
        $typesRetards = [
            'depot_compte' => 'Dépôt de compte',
            'realisation_budget' => 'Réalisation budgétaire',
            'rapport_activite' => 'Rapport d\'activité',
            'declaration_fiscale' => 'Déclaration fiscale',
            'paiement_salaire' => 'Paiement des salaires',
            'autre' => 'Autre'
        ];
        
        return view('retards.edit', compact('retard', 'communes', 'typesRetards'));
    }

    /**
     * Mise à jour d'un retard
     */
    public function update(Request $request, Retard $retard)
    {
        $validator = Validator::make($request->all(), [
            'type_retard' => 'required|string|max:100',
            'duree_jours' => 'required|integer|min:1|max:365',
            'date_constat' => 'required|date|before_or_equal:today',
            'date_retard' => 'nullable|date|before_or_equal:date_constat',
            'commune_id' => 'required|exists:communes,id'
        ], [
            'type_retard.required' => 'Le type de retard est obligatoire.',
            'duree_jours.required' => 'La durée du retard est obligatoire.',
            'duree_jours.integer' => 'La durée doit être un nombre entier.',
            'duree_jours.min' => 'La durée minimum est de 1 jour.',
            'duree_jours.max' => 'La durée maximum est de 365 jours.',
            'date_constat.required' => 'La date de constat est obligatoire.',
            'date_constat.date' => 'La date de constat doit être une date valide.',
            'date_constat.before_or_equal' => 'La date de constat ne peut pas être dans le futur.',
            'date_retard.date' => 'La date de retard doit être une date valide.',
            'date_retard.before_or_equal' => 'La date de retard ne peut pas être postérieure à la date de constat.',
            'commune_id.required' => 'Vous devez sélectionner une commune.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $retard->update([
                'type_retard' => $request->type_retard,
                'duree_jours' => $request->duree_jours,
                'date_constat' => $request->date_constat,
                'date_retard' => $request->date_retard,
                'commune_id' => $request->commune_id
            ]);

            DB::commit();

            return redirect()->route('retards.show', $retard)
                ->with('success', 'Retard mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du retard: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Suppression d'un retard
     */
    public function destroy(Retard $retard)
    {
        try {
            DB::beginTransaction();

            $retard->delete();

            DB::commit();

            return redirect()->route('retards.index')
                ->with('success', 'Retard supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du retard: ' . $e->getMessage());
        }
    }

    /**
     * Statistiques globales des retards
     */
    public function statistiques()
    {
        $annee = request('annee', date('Y'));
        
        // Statistiques par gravité
        $statsGravite = [
            'faible' => Retard::where('duree_jours', '<=', 15)
                             ->whereYear('date_constat', $annee)->count(),
            'moyenne' => Retard::whereBetween('duree_jours', [16, 30])
                              ->whereYear('date_constat', $annee)->count(),
            'elevee' => Retard::where('duree_jours', '>', 30)
                             ->whereYear('date_constat', $annee)->count()
        ];
        
        // Statistiques par type
        $statsType = Retard::whereYear('date_constat', $annee)
            ->selectRaw('type_retard, COUNT(*) as count')
            ->groupBy('type_retard')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->type_retard => $item->count];
            });
        
        // Évolution mensuelle
        $evolutionMensuelle = [];
        for ($i = 1; $i <= 12; $i++) {
            $evolutionMensuelle[$i] = Retard::whereYear('date_constat', $annee)
                ->whereMonth('date_constat', $i)
                ->count();
        }
        
        // Top 10 des communes avec le plus de retards
        $topCommunes = Retard::with('commune')
            ->whereYear('date_constat', $annee)
            ->selectRaw('commune_id, COUNT(*) as count')
            ->groupBy('commune_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        // Durée moyenne par région
        $dureeParRegion = DB::table('retards')
            ->join('communes', 'retards.commune_id', '=', 'communes.id')
            ->join('departements', 'communes.departement_id', '=', 'departements.id')
            ->join('regions', 'departements.region_id', '=', 'regions.id')
            ->whereYear('retards.date_constat', $annee)
            ->selectRaw('regions.nom as region, AVG(retards.duree_jours) as duree_moyenne')
            ->groupBy('regions.id', 'regions.nom')
            ->get();
        
        return view('retards.statistiques', compact(
            'annee', 'statsGravite', 'statsType', 'evolutionMensuelle', 
            'topCommunes', 'dureeParRegion'
        ));
    }

    /**
     * API pour obtenir les données des retards (pour AJAX)
     */
    public function getRetardsData(Request $request)
    {
        $communeId = $request->get('commune_id');
        $annee = $request->get('annee', date('Y'));
        
        $query = Retard::with(['commune.departement.region'])
            ->whereYear('date_constat', $annee);
        
        if ($communeId) {
            $query->where('commune_id', $communeId);
        }
        
        $retards = $query->get()->map(function($retard) {
            return [
                'id' => $retard->id,
                'type_retard' => $retard->type_retard,
                'duree_jours' => $retard->duree_jours,
                'gravite' => $retard->gravite,
                'date_constat' => $retard->date_constat->format('d/m/Y'),
                'commune' => $retard->commune->nom,
                'departement' => $retard->commune->departement->nom,
                'region' => $retard->commune->departement->region->nom
            ];
        });
        
        return response()->json($retards);
    }

    /**
     * Génération de rapport des retards
     */
    public function rapport(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $regionId = $request->get('region_id');
        $departementId = $request->get('departement_id');
        $communeId = $request->get('commune_id');
        
        $query = Retard::with(['commune.departement.region'])
            ->whereYear('date_constat', $annee);
        
        if ($regionId) {
            $query->whereHas('commune.departement', function($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }
        
        if ($departementId) {
            $query->whereHas('commune', function($q) use ($departementId) {
                $q->where('departement_id', $departementId);
            });
        }
        
        if ($communeId) {
            $query->where('commune_id', $communeId);
        }
        
        $retards = $query->orderBy('date_constat', 'desc')->get();
        
        $regions = Region::orderBy('nom')->get();
        $departements = Departement::orderBy('nom')->get();
        $communes = Commune::orderBy('nom')->get();
        
        return view('retards.rapport', compact(
            'retards', 'annee', 'regions', 'departements', 'communes'
        ));
    }
}