<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use Illuminate\Http\Request;
use App\Models\Ordonnateur;

class OrdonnateurController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
   
//         // Récupérer tous les ordonnateurs
//         $ordonnateurs = Ordonnateur::all();

//         // Retourner la vue avec les ordonnateurs
//         return view('ordonnateurs.index', compact('ordonnateurs'));

//     }

//     /**
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         //
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         $request->validate([
//             'nom'=> 'required|string|max:255',
//             'date_prise_fonction'=> 'required|date',
//             'fonction'=> 'required|string|max:255',
//             'telephone' => 'required|string|max:255',
//             'id_commune'=> 'required|exists:communes,id',
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
//         // Récupérer l'ordonnateur par son ID
//         $ordonnateur = Ordonnateur::findOrFail($id);

//         // Retourner la vue avec l'ordonnateur
//         return view('ordonnateurs.edit', compact('ordonnateur'));
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, string $id)
//     {
        
//        $request->validate([
//             'nom'=> 'required|string|max:255',
//             'date_prise_fonction'=> 'required|date',
//             'fonction'=> 'required|string|max:255',
//             'telephone' => 'required|string|max:255',
//             'id_commune'=> 'required|exists:communes,id',
//         ]);

//         // Récupérer l'ordonnateur par son ID
//         $ordonnateur = Ordonnateur::findOrFail($id);

//         // Mettre à jour les informations de l'ordonnateur
//         $ordonnateur->update($request->all());

//         // Redirection vers la liste des ordonnateurs avec un message de succès
//         return redirect()->route('ordonnateurs.index')->with('success', 'Ordonnateur mis à jour avec succès.'); 

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
        $ordonnateurs = Ordonnateur::with('commune')->get();
        return view('ordonnateurs.index', compact('ordonnateurs'));
    }

    public function create()
    {
        $communes = Commune::all();
        return view('ordonnateurs.create', compact('communes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'date_prise_fonction' => 'required|date',
            'telephone' => 'nullable|string|max:20',
            'commune_id' => 'required|exists:communes,id',
        ]);

        Ordonnateur::create([
            'nom' => $request->nom,
            'fonction' => $request->fonction,
            'date_prise_fonction' => $request->date_prise_fonction,
            'telephone' => $request->telephone,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('ordonnateurs.index')
            ->with('success', 'Ordonnateur créé avec succès');
    }

    public function show(Ordonnateur $ordonnateur)
    {
        $ordonnateur->load('commune');
        return view('ordonnateurs.show', compact('ordonnateur'));
    }

    public function edit(Ordonnateur $ordonnateur)
    {
        $communes = Commune::all();
        return view('ordonnateurs.edit', compact('ordonnateur', 'communes'));
    }

    public function update(Request $request, Ordonnateur $ordonnateur)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'date_prise_fonction' => 'required|date',
            'telephone' => 'nullable|string|max:20',
            'commune_id' => 'required|exists:communes,id',
        ]);

        $ordonnateur->update([
            'nom' => $request->nom,
            'fonction' => $request->fonction,
            'date_prise_fonction' => $request->date_prise_fonction,
            'telephone' => $request->telephone,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('ordonnateurs.index')
            ->with('success', 'Ordonnateur mis à jour avec succès');
    }

    public function destroy(Ordonnateur $ordonnateur)
    {
        $ordonnateur->delete();
        return redirect()->route('ordonnateurs.index')
            ->with('success', 'Ordonnateur supprimé avec succès');
    }
}
