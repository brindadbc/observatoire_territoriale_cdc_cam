<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Taux_realisation;
use Illuminate\Http\Request;

class Taux_RealisationController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
//       return view('taux_realisation.index');
//     }

//     /**
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         return view('taux_realisation.create');
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         $request->validate([
//             'pourcentage' => 'required|numeric|min:0|max:100',
//             'annee_exercise' => 'required|numeric',
//             'id_commune' => 'required|exists:communes,id'
//         ]);

//         // Logique pour stocker le taux de réalisation
//         // ...

//         return redirect()->route('taux_realisation.index')->with('success', 'Taux de réalisation créé avec succès.');
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
        
//         // Logique pour récupérer le taux de réalisation à éditer
//         // ...

//         return view('taux_realisation.edit', compact('id'));
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, string $id)
//     {
//         $request->validate([
//             'pourcentage' => 'required|numeric|min:0|max:100',
//             'annee_exercise' => 'required|numeric',
//             'id_commune' => 'required|exists:communes,id'
//         ]);
//         // Logique pour mettre à jour le taux de réalisation

            
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
//         $commune = Commune::findOrFail($id);
//         $commune->delete();
//         return redirect()->route('taux_realisation.index')->with('success', 'Taux de réalisation supprimé avec succès.');

//     }
// }

{
    public function index()
    {
        $tauxRealisations = Taux_realisation::with('commune')->get();
        return view('taux_realisations.index', compact('tauxRealisations'));
    }

    public function create()
    {
        $communes = Commune::all();
        return view('taux_realisations.create', compact('communes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pourcentage' => 'required|numeric|between:0,100',
            'annee_exercice' => 'required|string|max:4',
            'commune_id' => 'required|exists:communes,id',
        ]);

        Taux_realisation::create([
            'pourcentage' => $request->pourcentage,
            'annee_exercice' => $request->annee_exercice,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('taux_realisations.index')
            ->with('success', 'Taux de réalisation créé avec succès');
    }

    public function show(Taux_realisation $tauxRealisation)
    {
        $tauxRealisation->load('commune');
        return view('taux_realisations.show', compact('tauxRealisation'));
    }

    public function edit(Taux_realisation $tauxRealisation)
    {
        $communes = Commune::all();
        return view('taux_realisations.edit', compact('tauxRealisation', 'communes'));
    }

    public function update(Request $request, Taux_realisation $tauxRealisation)
    {
        $request->validate([
            'pourcentage' => 'required|numeric|between:0,100',
            'annee_exercice' => 'required|string|max:4',
            'commune_id' => 'required|exists:communes,id',
        ]);

        $tauxRealisation->update([
            'pourcentage' => $request->pourcentage,
            'annee_exercice' => $request->annee_exercice,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('taux_realisations.index')
            ->with('success', 'Taux de réalisation mis à jour avec succès');
    }

    public function destroy(Taux_realisation $tauxRealisation)
    {
        $tauxRealisation->delete();
        return redirect()->route('taux_realisations.index')
            ->with('success', 'Taux de réalisation supprimé avec succès');
    }
}
