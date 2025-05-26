<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Taux_RealisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      return view('taux_realisation.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('taux_realisation.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pourcentage' => 'required|numeric|min:0|max:100',
            'annee_exercise' => 'required|numeric',
            'id_commune' => 'required|exists:communes,id'
        ]);

        // Logique pour stocker le taux de réalisation
        // ...

        return redirect()->route('taux_realisation.index')->with('success', 'Taux de réalisation créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
        // Logique pour récupérer le taux de réalisation à éditer
        // ...

        return view('taux_realisation.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'pourcentage' => 'required|numeric|min:0|max:100',
            'annee_exercise' => 'required|numeric',
            'id_commune' => 'required|exists:communes,id'
        ]);
        // Logique pour mettre à jour le taux de réalisation

            
   // Récupérer la commune par son ID
        $commune = Commune::findOrFail($id);

        // Mettre à jour la commune
        $commune->update($request->all());

        // Redirection vers la liste des communes avec un message de succès
        return redirect()->route('communes.index')->with('success', 'Commune mise à jour avec succès.');

    }

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $commune = Commune::findOrFail($id);
        $commune->delete();
        return redirect()->route('taux_realisation.index')->with('success', 'Taux de réalisation supprimé avec succès.');

    }
}
