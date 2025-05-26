<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depot_Compte;

class Depot_compteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer tous les dépôts de compte
        $depots = Depot_Compte::all();

        // Retourner la vue avec les dépôts de compte
        return view('depot_compte.index', compact('depots'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Afficher le formulaire de création
        return view('depot_compte.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'nom' => 'required|string|max:255',  
            'annee_exercice' => 'required|integer',
            'validation' => 'required|boolean',
            'id_receveur' => 'required|exists:receveurs,id',
            'id_commune' => 'required|exists:communes,id'
        ]);

        // Création du dépôt de compte
        Depot_Compte::create($request->all());


        // Redirection vers la liste des dépôts de compte avec un message de succès
        return redirect()->route('depot_compte.index')->with('success', 'Dépôt de compte créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Récupérer le dépôt de compte par son ID
        $depot = Depot_Compte::findOrFail($id);

        // Retourner la vue avec le dépôt de compte
        return view('depot_compte.show', compact('depot'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Récupérer le dépôt de compte par son ID
        $depot = Depot_Compte::findOrFail($id);

        // Retourner la vue avec le dépôt de compte
        return view('depot_compte.edit', compact('depot'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
          // Validation des données
        $request->validate([
           'nom' => 'required|string|max:255',  
            'annee_exercice' => 'required|integer',
            'validation' => 'required|boolean',
            'id_receveur' => 'required|exists:receveurs,id',
            'id_commune' => 'required|exists:communes,id'
        ]);

        // Récupérer le dépôt de compte par son ID
        $depot = Depot_Compte::findOrFail($id);

        // Mettre à jour le dépôt de compte
        $depot->update($request->all());

        // Redirection vers la liste des dépôts de compte avec un message de succès
        return redirect()->route('depot_compte.index')->with('success', 'Dépôt de compte mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Récupérer le dépôt de compte par son ID
        $depot = Depot_Compte::findOrFail($id);

        // Supprimer le dépôt de compte
        $depot->delete();

        // Redirection vers la liste des dépôts de compte avec un message de succès
        return redirect()->route('depot_compte.index')->with('success', 'Dépôt de compte supprimé avec succès.');   
    }
}
