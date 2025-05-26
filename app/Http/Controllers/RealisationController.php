<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RealisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('realisation.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Afficher le formulaire de création
        return view('realisation.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'annee_exercise' => 'required|numeric',
            'montant_exercise' => 'required|numeric',
            'date_realisation' => 'required|date',
            'id_prevision' => 'required|exists:prevision,id',
            'id_commune' => 'required|exists:communes,id'
        ]);

        // Création de la prévision
        // $prevision = new Prevision($request->all());
        // $prevision->save();

        // Redirection vers la liste des prévisions avec un message de succès
        return redirect()->route('previsions.index')->with('success', 'Prévision créée avec succès.');
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
         return redirect()->route('previsions.edit');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
         'annee_exercise' => 'required|numeric',
            'montant_exercise' => 'required|numeric',
            'date_realisation' => 'required|date',
            'id_prevision' => 'required|exists:prevision,id',
            'id_commune' => 'required|exists:communes,id'   
        ]);

        // Récupérer la realisation par son ID
         $realisation = Realisation::findOrFail($id);
        // Mettre à jour les données de la realisation
        $realisation->update($request->all());
        // Redirection vers la liste des prévisions avec un message de succès
        return redirect()->route('previsions.index')->with('success', 'Prévision mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Récupérer la prévision par son ID
        // $prevision = Prevision::findOrFail($id);
        // $prevision->delete();

        // Redirection vers la liste des prévisions avec un message de succès
        return redirect()->route('previsions.index')->with('success', 'Prévision supprimée avec succès.');
    }
}
