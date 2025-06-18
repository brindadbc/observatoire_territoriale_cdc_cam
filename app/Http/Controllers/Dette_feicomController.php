<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\dette_feicom;
use Illuminate\Http\Request;

class Dette_feicomController extends Controller
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
        $dettesFeicom = Dette_feicom::with('commune')->get();
        return view('dettes_feicom.index', compact('dettesFeicom'));
    }

    public function create()
    {
        $communes = Commune::all();
        return view('dettes_feicom.create', compact('communes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_evaluation' => 'required|date',
            'commune_id' => 'required|exists:communes,id',
        ]);

        dette_feicom::create([
            'montant' => $request->montant,
            'date_evaluation' => $request->date_evaluation,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('dettes_feicom.index')
            ->with('success', 'Dette FEICOM créée avec succès');
    }

    public function show(dette_feicom $detteFeicom)
    {
        $detteFeicom->load('commune');
        return view('dettes_feicom.show', compact('detteFeicom'));
    }

    public function edit(Dette_feicom $detteFeicom)
    {
        $communes = Commune::all();
        return view('dettes_feicom.edit', compact('detteFeicom', 'communes'));
    }

    public function update(Request $request, Dette_feicom $detteFeicom)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_evaluation' => 'required|date',
            'commune_id' => 'required|exists:communes,id',
        ]);

        $detteFeicom->update([
            'montant' => $request->montant,
            'date_evaluation' => $request->date_evaluation,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('dettes_feicom.index')
            ->with('success', 'Dette FEICOM mise à jour avec succès');
    }

    public function destroy(Dette_feicom $detteFeicom)
    {
        $detteFeicom->delete();
        return redirect()->route('dettes_feicom.index')
            ->with('success', 'Dette FEICOM supprimée avec succès');
    }
}