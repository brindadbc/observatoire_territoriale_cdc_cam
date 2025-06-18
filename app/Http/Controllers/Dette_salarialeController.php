<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\dette_salariale;
use Illuminate\Http\Request;

class Dette_salarialeController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
//         //
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
//         //
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
//         //
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, string $id)
//     {
//         //
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
        $dettesSalariales = dette_salariale::with('commune')->get();
        return view('dettes_salariales.index', compact('dettesSalariales'));
    }

    public function create()
    {
        $communes = Commune::all();
        return view('dettes_salariales.create', compact('communes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_evaluation' => 'required|date',
            'commune_id' => 'required|exists:communes,id',
        ]);

        Dette_salariale::create([
            'montant' => $request->montant,
            'date_evaluation' => $request->date_evaluation,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('dettes_salariales.index')
            ->with('success', 'Dette salariale créée avec succès');
    }

    public function show(Dette_salariale $detteSalariale)
    {
        $detteSalariale->load('commune');
        return view('dettes_salariales.show', compact('detteSalariale'));
    }

    public function edit(Dette_salariale $detteSalariale)
    {
        $communes = Commune::all();
        return view('dettes_salariales.edit', compact('detteSalariale', 'communes'));
    }

    public function update(Request $request, Dette_salariale $detteSalariale)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_evaluation' => 'required|date',
            'commune_id' => 'required|exists:communes,id',
        ]);

        $detteSalariale->update([
            'montant' => $request->montant,
            'date_evaluation' => $request->date_evaluation,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('dettes_salariales.index')
            ->with('success', 'Dette salariale mise à jour avec succès');
    }

    public function destroy(Dette_salariale $detteSalariale)
    {
        $detteSalariale->delete();
        return redirect()->route('dettes_salariales.index')
            ->with('success', 'Dette salariale supprimée avec succès');
    }
}
