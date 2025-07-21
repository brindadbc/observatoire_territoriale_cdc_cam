<?php

namespace App\Http\Controllers;

use App\Models\Ordonnateur;
use App\Models\Commune;
use App\Models\Departement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrdonnateurController extends Controller
{
    /**
     * Affichage de la liste des ordonnateurs avec recherche et filtrage
     */
    public function index(Request $request)
    {
        $query = Ordonnateur::with(['commune.departement.region']);
        
        // Recherche par nom
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('fonction', 'LIKE', "%{$search}%")
                  ->orWhere('telephone', 'LIKE', "%{$search}%")
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
        
        // Filtrage par statut (assigné ou non)
        if ($request->filled('status')) {
            if ($request->status === 'assigne') {
                $query->whereNotNull('commune_id');
            } elseif ($request->status === 'libre') {
                $query->whereNull('commune_id');
            }
        }
        
        // Tri
        $sortBy = $request->get('sort_by', 'nom');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);
        
        $ordonnateurs = $query->paginate(15);
        
        // Données pour les filtres
        $communes = Commune::with('departement')->orderBy('nom')->get();
        $departements = Departement::with('region')->orderBy('nom')->get();
        
        // Statistiques
        $stats = [
            'total' => Ordonnateur::count(),
            'assignes' => Ordonnateur::whereNotNull('commune_id')->count(),
            'libres' => Ordonnateur::whereNull('commune_id')->count(),
            'communes_sans_ordonnateur' => Commune::whereDoesntHave('ordonnateurs')->count()
        ];
        
        return view('ordonnateurs.index', compact(
            'ordonnateurs', 'communes', 'departements', 'stats'
        ));
    }

    /**
     * Affichage du formulaire de création
     */
    public function create()
    {
        Log::info('Méthode create() appelée pour ordonnateur');
        
        try {
            $communes = Commune::with('departement.region')->orderBy('nom')->get();
            
            return view('ordonnateurs.create', compact('communes'));
        } catch (\Exception $e) {
            Log::error('Erreur dans OrdonnateurController@create: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du chargement du formulaire');
        }
    }

    /**
     * Enregistrement d'un nouvel ordonnateur
     */
    public function store(Request $request)
    {
        Log::info('Méthode store() appelée avec données: ', $request->all());
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'date_prise_fonction' => 'required|date',
            'telephone' => 'nullable|string|max:20',
            'commune_id' => 'nullable|exists:communes,id'
        ], [
            'nom.required' => 'Le nom de l\'ordonnateur est obligatoire.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'fonction.required' => 'La fonction est obligatoire.',
            'fonction.max' => 'La fonction ne peut pas dépasser 255 caractères.',
            'date_prise_fonction.required' => 'La date de prise de fonction est obligatoire.',
            'date_prise_fonction.date' => 'La date de prise de fonction doit être une date valide.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation échouée: ', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $ordonnateur = Ordonnateur::create([
                'nom' => trim($request->nom),
                'fonction' => trim($request->fonction),
                'date_prise_fonction' => $request->date_prise_fonction,
                'telephone' => $request->telephone ? trim($request->telephone) : null,
                'commune_id' => $request->commune_id
            ]);

            DB::commit();
            
            Log::info('Ordonnateur créé avec succès: ', $ordonnateur->toArray());

            return redirect()->route('ordonnateurs.show', $ordonnateur)
                ->with('success', 'Ordonnateur créé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la création: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de l\'ordonnateur: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affichage des détails d'un ordonnateur
     */
    public function show(Ordonnateur $ordonnateur)
    {
        try {
            // Charger les relations nécessaires
            $ordonnateur->load(['commune.departement.region']);
            
            // Historique des communes (si l'ordonnateur a changé de commune)
            $historique = $this->getHistoriqueCommunes($ordonnateur);
            
            // Statistiques si assigné à une commune
            $stats = [];
            if ($ordonnateur->commune) {
                $stats = $this->getStatsCommune($ordonnateur->commune);
            }
            
            return view('ordonnateurs.show', compact('ordonnateur', 'historique', 'stats'));
            
        } catch (\Exception $e) {
            Log::error('Erreur dans OrdonnateurController@show: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du chargement des détails');
        }
    }

    /**
     * Affichage du formulaire d'édition
     */
    public function edit(Ordonnateur $ordonnateur)
    {
        try {
            $ordonnateur->load('commune');
            $communes = Commune::with('departement.region')->orderBy('nom')->get();
            
            return view('ordonnateurs.edit', compact('ordonnateur', 'communes'));
            
        } catch (\Exception $e) {
            Log::error('Erreur dans OrdonnateurController@edit: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du chargement du formulaire d\'édition');
        }
    }

    /**
     * Mise à jour d'un ordonnateur
     */
    public function update(Request $request, Ordonnateur $ordonnateur)
    {
        Log::info('Méthode update() appelée pour ordonnateur ID: ' . $ordonnateur->id);
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'date_prise_fonction' => 'required|date',
            'telephone' => 'nullable|string|max:20',
            'commune_id' => 'nullable|exists:communes,id'
        ], [
            'nom.required' => 'Le nom de l\'ordonnateur est obligatoire.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'fonction.required' => 'La fonction est obligatoire.',
            'fonction.max' => 'La fonction ne peut pas dépasser 255 caractères.',
            'date_prise_fonction.required' => 'La date de prise de fonction est obligatoire.',
            'date_prise_fonction.date' => 'La date de prise de fonction doit être une date valide.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation échouée: ', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $ordonnateur->update([
                'nom' => trim($request->nom),
                'fonction' => trim($request->fonction),
                'date_prise_fonction' => $request->date_prise_fonction,
                'telephone' => $request->telephone ? trim($request->telephone) : null,
                'commune_id' => $request->commune_id
            ]);

            DB::commit();
            
            Log::info('Ordonnateur mis à jour avec succès: ', $ordonnateur->toArray());

            return redirect()->route('ordonnateurs.show', $ordonnateur)
                ->with('success', 'Ordonnateur modifié avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la modification: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification de l\'ordonnateur: ' . $e->getMessage())
                ->withInput();
        }
    }

   



    public function destroy(Ordonnateur $ordonnateur)
    {
        try {
            DB::beginTransaction();

            // Vérifier les contraintes avant suppression
            $peutSupprimer = $this->verifierConstraintesSupression($ordonnateur);
            
            if (!$peutSupprimer['possible']) {
                return redirect()->back()
                    ->with('error', $peutSupprimer['message']);
            }

            // Détacher de la commune si assigné
            if ($ordonnateur->commune_id) {
                $ordonnateur->update(['commune_id' => null]);
            }

            // Supprimer les relations dépendantes si elles existent
            $this->supprimerRelationsDependantes($ordonnateur);

            // Supprimer l'ordonnateur
            $ordonnateur->delete();

            DB::commit();

            Log::info('Ordonnateur supprimé avec succès: ID ' . $ordonnateur->id);

            return redirect()->route('ordonnateurs.index')
                ->with('success', 'Ordonnateur supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la suppression: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de l\'ordonnateur: ' . $e->getMessage());
        }
    }

    /**
     * Vérifier si un ordonnateur peut être supprimé
     */
    private function verifierConstraintesSupression($ordonnateur)
    {
        try {
          

            // Vérifier les contraintes de base de données
            return [
                'possible' => true,
                'message' => ''
            ];

        } catch (\Exception $e) {
            return [
                'possible' => false,
                'message' => 'Erreur lors de la vérification des contraintes: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Supprimer les relations dépendantes
     */
    private function supprimerRelationsDependantes($ordonnateur)
    {
        try {
           
            
            Log::info('Relations dépendantes supprimées pour l\'ordonnateur ID: ' . $ordonnateur->id);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression des relations: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Assigner un ordonnateur à une commune
     */
    public function assignToCommune(Request $request, Ordonnateur $ordonnateur)
    {
        $request->validate([
            'commune_id' => 'required|exists:communes,id'
        ]);

        try {
            DB::beginTransaction();

            $ordonnateur->update(['commune_id' => $request->commune_id]);

            DB::commit();

            return redirect()->route('ordonnateurs.show', $ordonnateur)
                ->with('success', 'Ordonnateur assigné à la commune avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'assignation: ' . $e->getMessage());
        }
    }

    /**
     * Libérer un ordonnateur de sa commune
     */
    public function libererDeCommune(Ordonnateur $ordonnateur)
    {
        try {
            DB::beginTransaction();

            $ordonnateur->update(['commune_id' => null]);

            DB::commit();

            return redirect()->route('ordonnateurs.show', $ordonnateur)
                ->with('success', 'Ordonnateur libéré de sa commune avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la libération: ' . $e->getMessage());
        }
    }

    /**
     * API pour récupérer les ordonnateurs d'une commune
     */
    public function getByCommune(Commune $commune)
    {
        $ordonnateurs = $commune->ordonnateurs;
        
        return response()->json([
            'success' => true,
            'ordonnateurs' => $ordonnateurs
        ]);
    }

    /**
     * API pour récupérer les ordonnateurs libres
     */
    public function getLibres()
    {
        $ordonnateurs = Ordonnateur::whereNull('commune_id')
            ->orderBy('nom')
            ->get();
        
        return response()->json([
            'success' => true,
            'ordonnateurs' => $ordonnateurs
        ]);
    }

    // ================== MÉTHODES PRIVÉES ==================

    /**
     * Obtenir l'historique des communes d'un ordonnateur
     * (Nécessiterait une table d'historique pour un suivi complet)
     */
    private function getHistoriqueCommunes($ordonnateur)
    {
        // Pour l'instant, retourner seulement la commune actuelle
        // Dans une implémentation complète, vous pourriez avoir une table historique_ordonnateurs
        if ($ordonnateur->commune) {
            return collect([
                [
                    'commune' => $ordonnateur->commune->nom,
                    'departement' => $ordonnateur->commune->departement->nom,
                    'region' => $ordonnateur->commune->departement->region->nom,
                    'date_debut' => $ordonnateur->date_prise_fonction,
                    'date_fin' => null,
                    'est_actuel' => true
                ]
            ]);
        }
        
        return collect();
    }

    /**
     * Obtenir les statistiques de la commune de l'ordonnateur
     */
    private function getStatsCommune($commune)
    {
        $anneeActuelle = date('Y');
        
        return [
            'previsions_annee' => $commune->previsions()
                ->where('annee_exercice', $anneeActuelle)
                ->sum('montant'),
            'realisations_annee' => $commune->realisations()
                ->where('annee_exercice', $anneeActuelle)
                ->sum('montant'),
            'taux_realisation' => $commune->tauxRealisations()
                ->where('annee_exercice', $anneeActuelle)
                ->first()?->pourcentage ?? 0,
            'dettes_totales' => $this->getTotalDettes($commune, $anneeActuelle),
            'nb_defaillances' => $commune->defaillances()
                ->where('est_resolue', false)
                ->count()
        ];
    }

    /**
     * Calculer le total des dettes d'une commune
     */
    private function getTotalDettes($commune, $annee)
    {
        $dettesCnps = $commune->dettesCnps()
            ->whereYear('date_evaluation', $annee)
            ->sum('montant');
            
        $dettesFiscale = $commune->dettesFiscale()
            ->whereYear('date_evaluation', $annee)
            ->sum('montant');
            
        $dettesFeicom = $commune->dettesFeicom()
            ->whereYear('date_evaluation', $annee)
            ->sum('montant');
            
        $dettesSalariale = $commune->dettesSalariale()
            ->whereYear('date_evaluation', $annee)
            ->sum('montant');

        return $dettesCnps + $dettesFiscale + $dettesFeicom + $dettesSalariale;
    }
}