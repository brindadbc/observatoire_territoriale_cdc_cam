<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Retard;
use Illuminate\Http\Request;

class RetardController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
//         return view('retards.index');
//     }

//     /**
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         return view('retards.create');
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         $request->validate([
//         'type_retard' => 'required|string',
//         'duree_jours' => 'required|integer',
//         'date_constat' => 'required|date',
//         'date_retard' => 'required|date',
//         'id_commune' => 'required|integer',
//         ]);
//     }

//     /**
//      * Display the specified resource.
//      */
//     public function show(string $id)
//     {
//         //
//     }

//     /**
//      * Show the form for editing the specified resource.
//      */
//     public function edit(string $id)
//     {
//         return view('edit.index');
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, string $id)
//     {
//         $request->validate([
//             'type_retard' => 'required|string',
//             'duree_jours' => 'required|integer',
//             'date_constat' => 'required|date',
//             'date_retard' => 'required|date',
//         'id_commune' => 'required|integer',
//         ]);
//    // Récupérer la commune par son ID
//         $commune = Commune::findOrFail($id);

//         // Mettre à jour la commune
//         $commune->update($request->all());

//         // Redirection vers la liste des communes avec un message de succès
//         return redirect()->route('communes.index')->with('success', 'Commune mise à jour avec succès.');

//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy(string $id)
//     {
//         //
//     }
// }

{
    public function index()
    {
        $retards = Retard::with('commune')->get();
        return view('retards.index', compact('retards'));
    }

    public function create()
    {
        $communes = Commune::all();
        return view('retards.create', compact('communes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_retard' => 'required|string|max:255',
            'date_constat' => 'required|date',
            'date_retard' => 'required|date',
            'duree_jours' => 'required|integer|min:1',
            'commune_id' => 'required|exists:communes,id',
        ]);

        Retard::create([
            'type_retard' => $request->type_retard,
            'date_constat' => $request->date_constat,
            'date_retard' => $request->date_retard,
            'duree_jours' => $request->duree_jours,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('retards.index')
            ->with('success', 'Retard créé avec succès');
    }

    public function show(Retard $retard)
    {
        $retard->load('commune');
        return view('retards.show', compact('retard'));
    }

    public function edit(Retard $retard)
    {
        $communes = Commune::all();
        return view('retards.edit', compact('retard', 'communes'));
    }

    public function update(Request $request, Retard $retard)
    {
        $request->validate([
            'type_retard' => 'required|string|max:255',
            'date_constat' => 'required|date',
            'date_retard' => 'required|date',
            'duree_jours' => 'required|integer|min:1',
            'commune_id' => 'required|exists:communes,id',
        ]);

        $retard->update([
            'type_retard' => $request->type_retard,
            'date_constat' => $request->date_constat,
            'date_retard' => $request->date_retard,
            'duree_jours' => $request->duree_jours,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('retards.index')
            ->with('success', 'Retard mis à jour avec succès');
    }

    public function destroy(Retard $retard)
    {
        $retard->delete();
        return redirect()->route('retards.index')
            ->with('success', 'Retard supprimé avec succès');
    }
}
