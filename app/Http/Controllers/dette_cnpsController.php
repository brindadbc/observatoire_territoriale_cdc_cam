<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commune;
use App\Models\dette_cnps;
use App\Models\DetteCNPS;

class dette_cnpsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dette_cnps.index');

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Afficher le formulaire de création avec la liste des communes
        $communes = Commune::all();
        return view('dette_cnps.create', compact('communes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
           'montant' => 'required|numeric',
            'date_evaluation' => 'required|date',
            'id_commune' => 'required|exists:communes,id'
        ]);

        // Création de la dette CNPS
        // $dette = new DetteCNPS($request->all());
        // $dette->save();

        // Redirection vers la liste des dettes CNPS avec un message de succès
        return redirect()->route('dette_cnps.index')->with('success', 'Dette CNPS créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dette = dette_cnps::findOrFail($id);    
        // Retourner la vue avec la dette CNPS
        return view('dette_cnps.edit', compact('dette'));                                                                                                                                                

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
           'montant' => 'required|numeric',
            'date_evaluation' => 'required|date',
            'id_commune' => 'required|exists:communes,id'
        ]);

        // Récupérer la dette CNPS par son ID
        $dette = dette_cnps::findOrFail($id);

        // Mettre à jour la dette CNPS
        $dette->update($request->all());

        // Redirection vers la liste des dettes CNPS avec un message de succès
        return redirect()->route('dette_cnps.index')->with('success', 'Dette CNPS mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}