<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RessourcesTransfereesEtat;
use App\Models\Commune;

class RessourcesTransfereesEtatController extends Controller
{
    // Constants for resource types to maintain consistency
    const TYPES_RESSOURCES = [
        'Subvention',
        'Dotation',
        'Fonds Spécial',
        'Projet Gouvernemental',
        'Autre'
    ];

   public function index()
{
    $ressources = RessourcesTransfereesEtat::with('commune')
                    ->orderBy('date_reception', 'desc')
                    ->paginate(20);

    // Calculate statistics
    $totalRessources = RessourcesTransfereesEtat::sum('montant');
    $communesCount = Commune::has('ressourcesTransfereesEtat')->count();
    $averagePerCommune = $communesCount > 0 ? $totalRessources / $communesCount : 0;
    $lastUpdated = RessourcesTransfereesEtat::latest()->first()->updated_at ?? now();

    return view('ressources-etat.index', compact(
        'ressources',
        'totalRessources',
        'communesCount',
        'averagePerCommune',
        'lastUpdated'
    ));
}

    public function create()
    {
        $communes = Commune::orderBy('nom')->get();
        
        return view('ressources-etat.create', [
            'communes' => $communes,
            'types' => self::TYPES_RESSOURCES
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'commune_id' => 'required|exists:communes,id',
            'type_ressource' => 'required|string|max:255|in:'.implode(',', self::TYPES_RESSOURCES),
            'description' => 'required|string',
            'montant' => 'required|numeric|min:0',
            'date_reception' => 'required|date|before_or_equal:today',
            'projet_associe' => 'nullable|string|max:255',
            'reference' => 'required|string|max:100|unique:ressources_transferees_etats,reference',
        ]);

        RessourcesTransfereesEtat::create($validated);

        return redirect()->route('ressources-etat.index')
                         ->with('success', 'Ressource de l\'état enregistrée avec succès.');
    }

  // app/Http/Controllers/RessourcesTransfereesEtatController.php

public function show(RessourcesTransfereesEtat $ressources_etat)
{
    return view('ressources-etat.show', [
        'ressource' => $ressources_etat->load('commune')
    ]);
}

  // app/Http/Controllers/RessourcesTransfereesEtatController.php

public function edit(RessourcesTransfereesEtat $ressources_etat)
{
    $communes = Commune::orderBy('nom')->get();
    $types = [
        'Subvention', 
        'Dotation', 
        'Fonds Spécial',
        'Projet Gouvernemental', 
        'Autre'
    ];
    
    return view('ressources-etat.edit', [
        'ressource' => $ressources_etat,
        'communes' => $communes,
        'types' => $types
    ]);
}

    public function update(Request $request, RessourcesTransfereesEtat $ressources_transferees_etat)
    {
        $validated = $request->validate([
            'commune_id' => 'required|exists:communes,id',
            'type_ressource' => 'required|string|max:255|in:'.implode(',', self::TYPES_RESSOURCES),
            'description' => 'required|string',
            'montant' => 'required|numeric|min:0',
            'date_reception' => 'required|date|before_or_equal:today',
            'projet_associe' => 'nullable|string|max:255',
            'reference' => 'required|string|max:100|unique:ressources_transferees_etats,reference,'.$ressources_transferees_etat->id,
        ]);

        $ressources_transferees_etat->update($validated);

        return redirect()->route('ressources-etat.index')
                         ->with('success', 'Ressource de l\'état mise à jour avec succès.');
    }

   // app/Http/Controllers/RessourcesTransfereesEtatController.php

public function destroy(RessourcesTransfereesEtat $ressources_etat)
{
    try {
        $ressources_etat->delete();
        
        return redirect()->route('ressources-etat.index')
            ->with('success', 'La ressource a été supprimée avec succès');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Erreur lors de la suppression : '.$e->getMessage());
    }
}

    public function byCommune(Commune $commune)
    {
        $ressources = $commune->ressourcesTransfereesEtat()
                        ->orderBy('date_reception', 'desc')
                        ->paginate(20);
        
        return view('ressources-etat.by-commune', [
            'commune' => $commune,
            'ressources' => $ressources
        ]);
    }
}