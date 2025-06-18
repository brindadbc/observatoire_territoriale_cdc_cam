<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Prevision;
use Illuminate\Http\Request;

class PrevisionController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
//         return view('previsions.index');
//     }

//     /**
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         // Afficher le formulaire de création
//         return view('previsions.create');
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         // Validation des données
//         $request->validate([
//             'montant_exercise' => 'required|numeric',
//             'date_evaluation' => 'required|date',
//             'id_commune' => 'required|exists:communes,id'
//         ]);

//         // Création de la prévision
//         // $prevision = new Prevision($request->all());
//         // $prevision->save();

//         // Redirection vers la liste des prévisions avec un message de succès
//         return redirect()->route('previsions.index')->with('success', 'Prévision créée avec succès.');
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
//         // Récupérer la prévision par son ID
//         // $prevision = Prevision::findOrFail($id);

//         // Retourner la vue avec la prévision
//         return view('previsions.edit', compact('prevision'));
//     // Afficher le formulaire d'édition avec la prévision

//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, string $id)
//     {
//         // Validation des données
//         $request->validate([
//             'montant_exercise' => 'required|numeric',
//             'date_evaluation' => 'required|date',
//             'id_commune' => 'required|exists:communes,id'
//         ]);


//         // Mettre à jour la prévision
//         // $prevision = Prevision::findOrFail($id);
//         $prevision->update($request->all());

//         // Récupérer la prévision par son ID
//         // $prevision = Prevision::findOrFail($id);
//         // $prevision->update($request->all());
           
//         // Redirection vers la liste des prévisions avec un message de succès
//         return redirect()->route('previsions.index')->with('success', 'Prévision mise à jour avec succès.');
//     // Mettre à jour la prévision
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy(string $id)
//     {
//         // Récupérer la prévision par son ID
//         // $prevision = Prevision::findOrFail($id);
//         // $prevision->delete();

//         // Redirection vers la liste des prévisions avec un message de succès
//         return redirect()->route('previsions.index')->with('success', 'Prévision supprimée avec succès.');
//         // Supprimer la prévision

//     }
// }

{
    public function index()
    {
        $previsions = Prevision::with('commune', 'realisations')->get();
        return view('previsions.index', compact('previsions'));
    }

    public function create()
    {
        $communes = Commune::all();
        return view('previsions.create', compact('communes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'annee_exercice' => 'required|string|max:4',
            'montant' => 'required|numeric|min:0',
            'commune_id' => 'required|exists:communes,id',
        ]);

        Prevision::create([
            'annee_exercice' => $request->annee_exercice,
            'montant' => $request->montant,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('previsions.index')
            ->with('success', 'Prévision créée avec succès');
    }

    public function show(Prevision $prevision)
    {
        $prevision->load('commune', 'realisations');
        return view('previsions.show', compact('prevision'));
    }

    public function edit(Prevision $prevision)
    {
        $communes = Commune::all();
        return view('previsions.edit', compact('prevision', 'communes'));
    }

    public function update(Request $request, Prevision $prevision)
    {
        $request->validate([
            'annee_exercice' => 'required|string|max:4',
            'montant' => 'required|numeric|min:0',
            'commune_id' => 'required|exists:communes,id',
        ]);

        $prevision->update([
            'annee_exercice' => $request->annee_exercice,
            'montant' => $request->montant,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('previsions.index')
            ->with('success', 'Prévision mise à jour avec succès');
    }

    public function destroy(Prevision $prevision)
    {
        $prevision->delete();
        return redirect()->route('previsions.index')
            ->with('success', 'Prévision supprimée avec succès');
    }
}
