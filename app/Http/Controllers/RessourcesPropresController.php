<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\RessourceCommune;
use App\Models\RessourcesPropres;
use Illuminate\Http\Request;

class RessourcesPropresController extends Controller
{
   // app/Http/Controllers/RessourcesPropresController.php

// app/Http/Controllers/RessourcesPropresController.php

public function index()
{
    // Use paginate() instead of get()
    $ressources = RessourcesPropres::with('commune')->paginate(15); // 15 items per page
    
    // Calculate totals from ALL records (not just current page)
    $totalRessources = RessourcesPropres::sum('montant');
    $averagePerCommune = RessourcesPropres::avg('montant');
    
    return view('ressources-commune.index', [
        'ressources' => $ressources,
        'totalRessources' => $totalRessources,
        'averagePerCommune' => $averagePerCommune,
    ]);

}
    public function create()
    {
        $communes = Commune::orderBy('nom')->get();
        $sources = [
            'Impôts', 'Taxes', 'Revenus fonciers',
            'Services municipaux', 'Activités économiques', 'Autres'
        ];
        
        return view('ressources-commune.create', compact('communes', 'sources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'commune_id' => 'required|exists:communes,id',
            'source' => 'required|string|max:255',
            'type_ressource' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date_generation' => 'required|date',
            'description' => 'required|string',
        ]);

        RessourcesPropres::create($validated);

        return redirect()->route('ressources-commune.index')
                         ->with('success', 'Ressource générée par la commune enregistrée avec succès.');
    }

    public function show($id)
{
    $ressource = RessourcesPropres::with('commune')->findOrFail($id);
    
    return view('ressources-commune.show', [
        'ressource' => $ressource
    ]);
}

   public function edit($id)
{
    $ressource = RessourcesPropres::findOrFail($id);
    $communes = Commune::orderBy('nom')->get();

    return view('ressources-commune.edit', [
        'ressource' => $ressource,
        'communes' => $communes
    ]);
    
    return view('ressources-commune.edit', [
        'ressource' => $ressources_commune,
        'communes' => $communes
    ]);
}

  public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'source' => 'required|string|max:255',
        'type_ressource' => 'required|string|max:255',
        'montant' => 'required|numeric',
        'date_generation' => 'required|date',
        'description' => 'required|string'
    ]);

    $ressource = RessourcesPropres::findOrFail($id);
    
    if ($ressource->update($validatedData)) {
        return redirect()
            ->route('ressources-commune.show', $ressource->id)
            ->with('success', 'Ressource modifiée avec succès!');
    }

    return back()
        ->withInput()
        ->with('error', 'Échec de la modification');
}
public function destroy($id)
{
    $ressource = RessourcesPropres::findOrFail($id);
    
    if ($ressource->delete()) {
        return redirect()->route('ressources-commune.index')
            ->with('success', 'Ressource supprimée avec succès');
    } else {
        return redirect()->back()
            ->with('error', 'La suppression a échoué');
    }
}

public function byCommune(Commune $commune)
{
    $ressources = $commune->ressourcesCommune()
                    ->orderBy('date_generation', 'desc')
                    ->paginate(20);
        
        return view('ressources-commune.by-commune', compact('commune', 'ressources'));
    }
}