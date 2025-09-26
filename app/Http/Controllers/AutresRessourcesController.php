<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\AutresRessources;
use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Cache;
class AutresRessourcesController extends Controller
{
 

public function index()
{
    // 1. Récupération des données principales
    $ressources = AutresRessources::orderBy('created_at', 'desc')->paginate(20);
    
    // 2. Calcul des statistiques
    $totalRessources = AutresRessources::sum('montant') ?? 0;
    $sourcesCount = AutresRessources::distinct('source')->count('source') ?? 0;
    $lastRecord = AutresRessources::latest()->first();

    // 3. Passage des données à la vue
    return view('autres-ressources.index', [
        'ressources' => $ressources,
        'totalRessources' => $totalRessources,
        'sourcesCount' => $sourcesCount,
        'lastAdded' => $lastRecord ? $lastRecord->created_at : null
    ]);
}

    public function create()
    {
        $communes = Commune::orderBy('nom')->get();
        $types = [
            'Legs', 'Dons privés', 'Revenus d\'investissements',
            'Subventions internationales', 'Autres'
        ];
        
        return view('autres-ressources.create', compact('communes', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'commune_id' => 'required|exists:communes,id',
            'source' => 'required|string|max:255',
            'type_ressource' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date_reception' => 'required|date',
            'description' => 'required|string',
        ]);

        AutresRessources::create($validated);

        return redirect()->route('autres-ressources.index')
                         ->with('success', 'Autre ressource enregistrée avec succès.');
    }

    public function show(AutresRessources $autreRessource)
    {
        return view('autres-ressources.show', compact('autreRessource'));
    }

    public function edit(AutresRessources $autreRessource)
    {
        $communes = Commune::orderBy('nom')->get();
        $types = [
            'Legs', 'Dons privés', 'Revenus d\'investissements',
            'Subventions internationales', 'Autres'
        ];
        
        return view('autres-ressources.edit', compact('autreRessource', 'communes', 'types'));
    }

    public function update(Request $request, AutresRessources $autreRessource)
    {
        $validated = $request->validate([
            'commune_id' => 'required|exists:communes,id',
            'source' => 'required|string|max:255',
            'type_ressource' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date_reception' => 'required|date',
            'description' => 'required|string',
        ]);

        $autreRessource->update($validated);

        return redirect()->route('autres-ressources.index')
                         ->with('success', 'Autre ressource mise à jour avec succès.');
    }

    public function destroy(AutresRessources $autreRessource)
    {
        $autreRessource->delete();

        return redirect()->route('autres-ressources.index')
                         ->with('success', 'Autre ressource supprimée avec succès.');
    }

    public function byCommune(Commune $commune)
    {
        $ressources = $commune->autresRessources()
                        ->orderBy('date_reception', 'desc')
                        ->paginate(20);
        
        return view('autres-ressources.by-commune', compact('commune', 'ressources'));
    }
}