<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Receveur;
use Illuminate\Http\Request;

class ReceveurController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
//         return view('receveurs.index');
//     }

//     /**
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         // Afficher le formulaire de création
//         return view('receveurs.create');
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         // Validation des données
//         $request->validate([
//             'nom' => 'required|string|max:255',
//             'staut' => 'required|string|max:255',
//             'matricule' => 'required|email|max:255',
//             'date_prise_fonction' => 'required|date',
//             'telephone' => 'required|string|max:15',
//             'id_commune' => 'required|exists:communes,id',
//         ]);

//         // Création du receveur
//         // $receveur = new Receveur($request->all());
//         // $receveur->save();

//         // Redirection vers la liste des receveurs avec un message de succès
//         return redirect()->route('receveurs.index')->with('success', 'Receveur créé avec succès.');
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
//         // Récupérer le receveur par son ID
//         // $receveur = Receveur::findOrFail($id);

//         // Afficher le formulaire d'édition avec les données du receveur
//         return view('receveurs.edit', compact('receveur'));
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, string $id)
//     {
        
//         $request->validate([
//             'nom' => 'required|string|max:255',
//             'staut' => 'required|string|max:255',
//             'matricule' => 'required|email|max:255',
//             'date_prise_fonction' => 'required|date',
//             'telephone' => 'required|string|max:15',
//             'id_commune' => 'required|exists:communes,id',  
//         ]);
//     }
//    /**/
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
        $receveurs = Receveur::with('commune')->get();
        return view('receveurs.index', compact('receveurs'));
    }

    public function create()
    {
        $communes = Commune::all();
        return view('receveurs.create', compact('communes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'statut' => 'required|string|max:255',
            'matricule' => 'required|string|max:255',
            'date_prise_fonction' => 'required|date',
            'telephone' => 'nullable|string|max:20',
            'commune_id' => 'required|exists:communes,id',
        ]);

        Receveur::create([
            'nom' => $request->nom,
            'statut' => $request->statut,
            'matricule' => $request->matricule,
            'date_prise_fonction' => $request->date_prise_fonction,
            'telephone' => $request->telephone,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('receveurs.index')
            ->with('success', 'Receveur créé avec succès');
    }

    public function show(Receveur $receveur)
    {
        $receveur->load('commune', 'depotsComptes');
        return view('receveurs.show', compact('receveur'));
    }

    public function edit(Receveur $receveur)
    {
        $communes = Commune::all();
        return view('receveurs.edit', compact('receveur', 'communes'));
    }

    public function update(Request $request, Receveur $receveur)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'statut' => 'required|string|max:255',
            'matricule' => 'required|string|max:255',
            'date_prise_fonction' => 'required|date',
            'telephone' => 'nullable|string|max:20',
            'commune_id' => 'required|exists:communes,id',
        ]);

        $receveur->update([
            'nom' => $request->nom,
            'statut' => $request->statut,
            'matricule' => $request->matricule,
            'date_prise_fonction' => $request->date_prise_fonction,
            'telephone' => $request->telephone,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('receveurs.index')
            ->with('success', 'Receveur mis à jour avec succès');
    }

    public function destroy(Receveur $receveur)
    {
        $receveur->delete();
        return redirect()->route('receveurs.index')
            ->with('success', 'Receveur supprimé avec succès');
    }
}
