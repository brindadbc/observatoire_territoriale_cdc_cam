<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RetardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('retards.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('retards.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'type_retard' => 'required|string',
        'duree_jours' => 'required|integer',
        'date_constat' => 'required|date',
        'date_retard' => 'required|date',
        'id_commune' => 'required|integer',
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
        return view('edit.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'type_retard' => 'required|string',
            'duree_jours' => 'required|integer',
            'date_constat' => 'required|date',
            'date_retard' => 'required|date',
        'id_commune' => 'required|integer',
        ]);
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
        //
    }
}
