<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\DonationsExterieures;
use Illuminate\Http\Request;

class DonationsExterieuresController extends Controller
{
public function index(Request $request)
{
    // Récupère uniquement les communes qui ont des dons associés
    $communes = Commune::whereHas('donsExterieurs')
        ->orderBy('nom')
        ->get(['id', 'nom', 'region']);
    
    // Construction de la requête pour les dons
    $query = DonationsExterieures::with(['commune' => function($query) {
        $query->select('id', 'nom', 'region');
    }])
    ->orderByDesc('date_reception');
    
    // Filtrage par commune si sélectionné
    if ($request->filled('commune_id')) {
        $query->where('commune_id', $request->commune_id);
    }
    
    return view('dons-exterieurs.index', [
        'dons' => $query->paginate(20),
        'communes' => $communes,
        'selectedCommune' => $request->commune_id
    ]);
}

    public function create()
    {
        $communes = Commune::orderBy('nom')->get();
        $typesAide = [
            'Financière', 'Matérielle', 'Technique',
            'Alimentaire', 'Médicale', 'Autre'
        ];
        
        return view('dons-exterieurs.create', compact('communes', 'typesAide'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'commune_id' => 'required|exists:communes,id',
            'donateur' => 'required|string|max:255',
            'type_aide' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'description' => 'required|string',
            'date_reception' => 'required|date',
            'conditions' => 'nullable|string',
            'projet_associe' => 'nullable|string|max:255',
        ]);

       DonationsExterieures::create($validated);

        return redirect()->route('dons-exterieurs.index')
                         ->with('success', 'Don extérieur enregistré avec succès.');
    }

    public function show(DonationsExterieures $donExterieure)
    {
        return view('dons-exterieurs.show', compact('donExterieure'));
    }

    public function edit(DonationsExterieures $donExterieure)
    {
        $communes = Commune::orderBy('nom')->get();
        $typesAide = [
            'Financière', 'Matérielle', 'Technique',
            'Alimentaire', 'Médicale', 'Autre'
        ];
        
        return view('dons-exterieurs.edit', compact('donExterieure', 'communes', 'typesAide'));
    }

    public function update(Request $request, DonationsExterieures $donExterieure)
    {
        $validated = $request->validate([
            'commune_id' => 'required|exists:communes,id',
            'donateur' => 'required|string|max:255',
            'type_aide' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'description' => 'required|string',
            'date_reception' => 'required|date',
            'conditions' => 'nullable|string',
            'projet_associe' => 'nullable|string|max:255',
        ]);

        $donExterieure->update($validated);

        return redirect()->route('dons-exterieurs.index')
                         ->with('success', 'Don extérieur mis à jour avec succès.');
    }

    public function destroy(DonationsExterieures $donExterieure)
    {
        $donExterieure->delete();

        return redirect()->route('dons-exterieurs.index')
                         ->with('success', 'Don extérieur supprimé avec succès.');
    }

    public function byCommune(Commune $commune)
    {
        $dons = $commune->donsExterieurs()
                ->orderBy('date_reception', 'desc')
                ->paginate(20);
        
        return view('dons-exterieurs.by-commune', compact('commune', 'dons'));
    }
}