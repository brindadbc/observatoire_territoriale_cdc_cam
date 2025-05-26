<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrevisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('previsions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Afficher le formulaire de création
        return view('previsions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'montant_exercise' => 'required|numeric',
            'date_evaluation' => 'required|date',
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
        // Récupérer la prévision par son ID
        // $prevision = Prevision::findOrFail($id);

        // Retourner la vue avec la prévision
        return view('previsions.edit', compact('prevision'));
    // Afficher le formulaire d'édition avec la prévision

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validation des données
        $request->validate([
            'montant_exercise' => 'required|numeric',
            'date_evaluation' => 'required|date',
            'id_commune' => 'required|exists:communes,id'
        ]);


        // Mettre à jour la prévision
        // $prevision = Prevision::findOrFail($id);
        $prevision->update($request->all());

        // Récupérer la prévision par son ID
        // $prevision = Prevision::findOrFail($id);
        // $prevision->update($request->all());
           
        // Redirection vers la liste des prévisions avec un message de succès
        return redirect()->route('previsions.index')->with('success', 'Prévision mise à jour avec succès.');
    // Mettre à jour la prévision
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
        // Supprimer la prévision

    }
}
