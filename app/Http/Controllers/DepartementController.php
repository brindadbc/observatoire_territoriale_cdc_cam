<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departement;

class DepartementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer tous les départements
        $departements = Departement::all();

        // Retourner la vue avec les départements
        return view('departements.index', compact('departements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Récupérer le département par son ID
        $departement = Departement::findOrFail($id);

        // Retourner la vue avec le département
        return view('departements.show', compact('departement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $departement = Departement::findOrFail($id);
        if(!$departement){
            return redirect()->route('departements.index')->with('error', 'Département non trouvé.');
        }
        // Validation des données
        $request->validate([
            'nom' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id'
        ]);
       
        // Vérifier si le nom du département est déjà utilisé
        // Mettre à jour le département
        $departement->update($request->all());
        // Redirection vers la liste des départements avec un message de succès
        return redirect()->route('departements.index')->with('success', 'Département mis à jour avec succès.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
