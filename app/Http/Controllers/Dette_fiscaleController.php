<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\dette_fiscale;
use Illuminate\Http\Request;

class Dette_fiscaleController extends Controller
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
        $dettesFiscales = dette_fiscale::with('commune')->get();
        return view('dettes_fiscales.index', compact('dettesFiscales'));
    }

    public function create()
    {
        $communes = Commune::all();
        return view('dettes_fiscales.create', compact('communes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_evaluation' => 'required|date',
            'commune_id' => 'required|exists:communes,id',
        ]);

        dette_fiscale::create([
            'montant' => $request->montant,
            'date_evaluation' => $request->date_evaluation,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('dettes_fiscales.index')
            ->with('success', 'Dette fiscale créée avec succès');
    }

    public function show(Dette_fiscale $detteFiscale)
    {
        $detteFiscale->load('commune');
        return view('dettes_fiscales.show', compact('detteFiscale'));
    }

    public function edit(Dette_fiscale $detteFiscale)
    {
        $communes = Commune::all();
        return view('dettes_fiscales.edit', compact('detteFiscale', 'communes'));
    }

    public function update(Request $request, Dette_fiscale $detteFiscale)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_evaluation' => 'required|date',
            'commune_id' => 'required|exists:communes,id',
        ]);

        $detteFiscale->update([
            'montant' => $request->montant,
            'date_evaluation' => $request->date_evaluation,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('dettes_fiscales.index')
            ->with('success', 'Dette fiscale mise à jour avec succès');
    }

    public function destroy(Dette_fiscale $detteFiscale)
    {
        $detteFiscale->delete();
        return redirect()->route('dettes_fiscales.index')
            ->with('success', 'Dette fiscale supprimée avec succès');
    }
}