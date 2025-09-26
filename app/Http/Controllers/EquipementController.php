<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use App\Models\Infrastructure;
use App\Models\EquipementImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EquipementController extends Controller
{
    /**
     * Affiche la liste des équipements
     */
    public function index()
    {
        $equipements = Equipement::with(['infrastructure.commune', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('equipements.index', compact('equipements'));
    }

    /**
     * Affiche le formulaire de création d'un équipement
     */
    public function create()
    {
        $infrastructures = Infrastructure::with('commune')->orderBy('nom')->get();
        $types = ['Médical', 'Scolaire', 'Bureautique', 'Technique', 'Autre'];
        $etats = ['Bon', 'Moyen', 'Mauvais'];

        return view('equipements.create', compact('infrastructures', 'types', 'etats'));
    }

    /**
     * Enregistre un nouvel équipement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'infrastructure_id' => 'required|exists:infrastructures,id',
            'quantite' => 'required|integer|min:1',
            'etat' => 'required|string|in:Bon,Moyen,Mauvais',
            'date_acquisition' => 'nullable|date',
            'cout' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $equipement = Equipement::create($validated);

        // Gestion des images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('equipements', 'public');
                $equipement->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('equipements.show', $equipement)
            ->with('success', 'Équipement créé avec succès.');
    }

    /**
     * Affiche les détails d'un équipement
     */
    public function show(Equipement $equipement)
    {
        $equipement->load(['infrastructure.commune', 'images']);
        return view('equipements.show', compact('equipement'));
    }

    /**
     * Affiche le formulaire d'édition d'un équipement
     */
    public function edit(Equipement $equipement)
    {
        $infrastructures = Infrastructure::with('commune')->orderBy('nom')->get();
        $types = ['Médical', 'Scolaire', 'Bureautique', 'Technique', 'Autre'];
        $etats = ['Bon', 'Moyen', 'Mauvais'];

        return view('equipements.edit', compact('equipement', 'infrastructures', 'types', 'etats'));
    }

    /**
     * Met à jour un équipement
     */
    public function update(Request $request, Equipement $equipement)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'infrastructure_id' => 'required|exists:infrastructures,id',
            'quantite' => 'required|integer|min:1',
            'etat' => 'required|string|in:Bon,Moyen,Mauvais',
            'date_acquisition' => 'nullable|date',
            'cout' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $equipement->update($validated);

        // Gestion des nouvelles images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('equipements', 'public');
                $equipement->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('equipements.show', $equipement)
            ->with('success', 'Équipement mis à jour avec succès.');
    }

    /**
     * Supprime un équipement
     */
    public function destroy(Equipement $equipement)
    {
        // Suppression des images associées
        foreach ($equipement->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        $equipement->delete();

        return redirect()->route('equipements.index')
            ->with('success', 'Équipement supprimé avec succès.');
    }
}