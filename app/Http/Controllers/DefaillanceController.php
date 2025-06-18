<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commune;
use App\Models\Defaillance;

class DefaillanceController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {

//         // Retourner la vue avec les communes
//         return view('defaillance.index', compact('defaillances'));
//     }

//     /**
//      * Show the form for creating a new resource.
//      */
// public function create()
// {
//     // Afficher le formulaire de création avec la liste des communes
//     $communes = Commune::all();
//     return view('defaillance.create', compact('communes'));
// }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         // Validation des données
//         $request->validate([
//             'nom' => 'required|string|max:255',
//             'type_defaillance' => 'required|string|max:255',
//             'description' => 'required|string|max:255',
//             'date_constat' => 'required|date',
//             'gravite' => 'required|string|max:255',
//             'est_resolue' => 'required|boolean',
//             'id_commune' => 'required|exists:departements,id'

//         ]);

       
//         // Redirection vers la liste des communes avec un message de succès
//         return redirect()->route('defaillance.index')->with('success', 'Commune créée avec succès.');
//     }

//     /**
//      * Display the specified resource.
//      */
   
  
//     /**
//      * Show the form for editing the specified resource.
//      */
//     public function edit(string $id)
//     {
//         // Récupérer la commune par son ID
//         $commune = Commune::findOrFail($id);

//         // Retourner la vue avec la commune
//         return view('communes.edit', compact('commune'));
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, string $id)
//     {
//         // Validation des données
//         $request->validate([
//            'nom' => 'required|string|max:255',
//             'type_defaillance' => 'required|string|max:255',
//             'description' => 'required|string|max:255',
//             'date_constat' => 'required|date',
//             'gravite' => 'required|string|max:255',
//             'est_resolue' => 'required|boolean',
//             'id_commune' => 'required|exists:departements,id'
//         ]);

//         // Récupérer la commune par son ID
//         $commune = Commune::findOrFail($id);

//         // Mettre à jour la commune
//         $commune->update($request->all());
//      // Validation des données
//         $request->validate([
//            'nom' => 'required|string|max:255',
//             'type_defaillance' => 'required|string|max:255',
//             'description' => 'required|string|max:255',
//             'date_constat' => 'required|date',
//             'gravite' => 'required|string|max:255',
//             'est_resolue' => 'required|boolean',
//             'id_commune' => 'required|exists:departements,id'
//         ]);

//         // Mettre à jour la commune
//         $commune->update($request->all());
//         // Récupérer la commune par son ID
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
//         // Récupérer la commune par son ID
//         $commune = Commune::findOrFail($id);

//         // Supprimer la commune
//         $commune->delete();

//         // Redirection vers la liste des communes avec un message de succès
//         return redirect()->route('defaillance.index')->with('success', 'Commune supprimée avec succès.');

//     }
// }

{
    public function index()
    {
        $defaillances = Defaillance::with('commune')->get();
        return view('defaillances.index', compact('defaillances'));
    }

    public function create()
    {
        $communes = Commune::all();
        return view('defaillances.create', compact('communes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_defaillance' => 'required|string|max:255',
            'description' => 'required|string',
            'date_constat' => 'required|date',
            'gravite' => 'required|string|in:faible,moyenne,élevée',
            'est_resolue' => 'boolean',
            'commune_id' => 'required|exists:communes,id',
        ]);

        Defaillance::create([
            'type_defaillance' => $request->type_defaillance,
            'description' => $request->description,
            'date_constat' => $request->date_constat,
            'gravite' => $request->gravite,
            'est_resolue' => $request->est_resolue ?? false,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('defaillances.index')
            ->with('success', 'Défaillance créée avec succès');
    }

    public function show(Defaillance $defaillance)
    {
        $defaillance->load('commune');
        return view('defaillances.show', compact('defaillance'));
    }

    public function edit(Defaillance $defaillance)
    {
        $communes = Commune::all();
        return view('defaillances.edit', compact('defaillance', 'communes'));
    }

    public function update(Request $request, Defaillance $defaillance)
    {
        $request->validate([
            'type_defaillance' => 'required|string|max:255',
            'description' => 'required|string',
            'date_constat' => 'required|date',
            'gravite' => 'required|string|in:faible,moyenne,élevée',
            'est_resolue' => 'boolean',
            'commune_id' => 'required|exists:communes,id',
        ]);

        $defaillance->update([
            'type_defaillance' => $request->type_defaillance,
            'description' => $request->description,
            'date_constat' => $request->date_constat,
            'gravite' => $request->gravite,
            'est_resolue' => $request->est_resolue ?? false,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('defaillances.index')
            ->with('success', 'Défaillance mise à jour avec succès');
    }

    public function destroy(Defaillance $defaillance)
    {
        $defaillance->delete();
        return redirect()->route('defaillances.index')
            ->with('success', 'Défaillance supprimée avec succès');
    }
    
    public function markAsResolved(Defaillance $defaillance)
    {
        $defaillance->update(['est_resolue' => true]);
        return redirect()->route('defaillances.show', $defaillance)
            ->with('success', 'Défaillance marquée comme résolue');
    }
}
