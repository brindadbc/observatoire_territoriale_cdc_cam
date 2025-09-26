<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use App\Models\Commune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InfrastructureController extends Controller
{
    /**
     * Affiche la liste des infrastructures
     */


public function index(Request $request)
{
    // Get all communes for the dropdown
    $communes = Commune::all();
    
    // Build the query
    $query = Infrastructure::query();
    
    if ($request->filled('commune')) {
        $query->where('commune_id', $request->commune);
    }
    
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }
    
    if ($request->filled('etat')) {
        $query->where('etat', $request->etat);
    }
    
    $infrastructures = $query->paginate(10);
    
    return view('infrastructures.index', [
        'infrastructures' => $infrastructures,
        'communes' => $communes
    ]);
}

    /**
     * Affiche le formulaire d'édition d'une infrastructure
     */
    public function edit(Infrastructure $infrastructure)
    {
        $communes = Commune::orderBy('nom')->get();
        $types = ['Route', 'École', 'Hôpital', 'Pont', 'Marché', 'Parc'];
        $etats = ['Bon', 'Moyen', 'Mauvais'];

        return view('infrastructures.edit', compact('infrastructure', 'communes', 'types', 'etats'));
    }

    /**
     * Met à jour une infrastructure
     */
    public function update(Request $request, Infrastructure $infrastructure)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'commune_id' => 'required|exists:communes,id',
            'etat' => 'required|string|in:Bon,Moyen,Mauvais',
            'description' => 'nullable|string',
            'date_construction' => 'nullable|date',
            'capacite' => 'nullable|integer',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_photo' => 'nullable|boolean',
        ]);

        // Gestion de la photo
        if ($request->has('remove_photo')) {
            // Suppression de la photo existante
            if ($infrastructure->photo) {
                Storage::disk('public')->delete($infrastructure->photo);
                $validated['photo'] = null;
            }
        } elseif ($request->hasFile('photo')) {
            // Upload de la nouvelle photo
            if ($infrastructure->photo) {
                Storage::disk('public')->delete($infrastructure->photo);
            }
            $validated['photo'] = $request->file('photo')->store('infrastructures', 'public');
        } else {
            // Conserver la photo existante
            unset($validated['photo']);
        }

        $infrastructure->update($validated);

        return redirect()->route('infrastructures.index')
            ->with('success', 'Infrastructure mise à jour avec succès.');
    }
   public function store(Request $request)
{
    $validated = $request->validate([
        'commune_id' => 'required|exists:communes,id',
        'type' => 'required|string|max:255',
        'nom' => 'required|string|max:255',
        'localisation' => 'required|string|max:255',
        'etat' => 'required|string|in:Bon,Moyen,Mauvais',
        'description' => 'nullable|string',
        'date_construction' => 'nullable|date',
        'cout_construction' => 'nullable|numeric',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Gestion de l'upload photo
    if ($request->hasFile('photo')) {
        $validated['photo'] = $request->file('photo')->store('infrastructures', 'public');
    }

    Infrastructure::create($validated);

    return redirect()->route('infrastructures.index')
                   ->with('success', 'Infrastructure créée avec succès');
}
    /**
     * Supprime une infrastructure
     */
    public function destroy(Infrastructure $infrastructure)
    {
        // Suppression de la photo si elle existe
        if ($infrastructure->photo) {
            Storage::disk('public')->delete($infrastructure->photo);
        }

        $infrastructure->delete();

        return redirect()->route('infrastructures.index')
            ->with('success', 'Infrastructure supprimée avec succès.');
    }
}