<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ordonnateur;

class OrdonnateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
   
        // Récupérer tous les ordonnateurs
        $ordonnateurs = Ordonnateur::all();

        // Retourner la vue avec les ordonnateurs
        return view('ordonnateurs.index', compact('ordonnateurs'));

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
        $request->validate([
            'nom'=> 'required|string|max:255',
            'date_prise_fonction'=> 'required|date',
            'fonction'=> 'required|string|max:255',
            'telephone' => 'required|string|max:255',
            'id_commune'=> 'required|exists:communes,id',
        ]);

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
        // Récupérer l'ordonnateur par son ID
        $ordonnateur = Ordonnateur::findOrFail($id);

        // Retourner la vue avec l'ordonnateur
        return view('ordonnateurs.edit', compact('ordonnateur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
       $request->validate([
            'nom'=> 'required|string|max:255',
            'date_prise_fonction'=> 'required|date',
            'fonction'=> 'required|string|max:255',
            'telephone' => 'required|string|max:255',
            'id_commune'=> 'required|exists:communes,id',
        ]);

        // Récupérer l'ordonnateur par son ID
        $ordonnateur = Ordonnateur::findOrFail($id);

        // Mettre à jour les informations de l'ordonnateur
        $ordonnateur->update($request->all());

        // Redirection vers la liste des ordonnateurs avec un message de succès
        return redirect()->route('ordonnateurs.index')->with('success', 'Ordonnateur mis à jour avec succès.'); 

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
