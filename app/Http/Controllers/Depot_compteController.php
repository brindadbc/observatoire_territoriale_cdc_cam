<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use Illuminate\Http\Request;
use App\Models\Depot_Compte;
use App\Models\Receveur;

class Depot_compteController extends Controller
{
    /**
     * Affichage de la liste des dépôts de comptes
     */
    public function index()
    {
        $annee = request('annee', date('Y'));
        
        $depots = Depot_compte::with(['commune.departement.region'])
            ->whereYear('date_depot', $annee)
            ->orderBy('date_depot', 'desc')
            ->paginate(20);
        
        return view('depots-comptes.index', compact('depots', 'annee'));
    }
    
    /**
     * Affichage du formulaire de création
     */
    public function create()
    {
        $communes = Commune::with('departement')->orderBy('nom')->get();
        
        return view('depots-comptes.create', compact('communes'));
    }
    
    /**
     * Enregistrement d'un nouveau dépôt
     */
    public function store(Request $request)
    {
        $request->validate([
            'commune_id' => 'required|exists:communes,id',
            'date_depot' => 'required|date',
            'annee_exercice' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'validation' => 'boolean'
        ]);
        
        Depot_compte::create($request->all());
        
        return redirect()->route('depots-comptes.index')
            ->with('success', 'Dépôt de compte enregistré avec succès.');
    }
    
    /**
     * Affichage des détails d'un dépôt
     */
    public function show(Depot_compte $depotCompte)
    {
        $depotCompte->load('commune.departement.region');
        
        return view('depots-comptes.show', compact('depotCompte'));
    }
    
    /**
     * Affichage du formulaire d'édition
     */
    public function edit(Depot_compte $depotCompte)
    {
        $communes = Commune::with('departement')->orderBy('nom')->get();
        
        return view('depots-comptes.edit', compact('depotCompte', 'communes'));
    }
    
    /**
     * Mise à jour d'un dépôt
     */
    public function update(Request $request, Depot_compte $depotCompte)
    {
        $request->validate([
            'commune_id' => 'required|exists:communes,id',
            'date_depot' => 'required|date',
            'annee_exercice' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'validation' => 'boolean'
        ]);
        
        $depotCompte->update($request->all());
        
        return redirect()->route('depots-comptes.index')
            ->with('success', 'Dépôt de compte mis à jour avec succès.');
    }
    
    /**
     * Suppression d'un dépôt
     */
    public function destroy(Depot_compte $depotCompte)
    {
        $depotCompte->delete();
        
        return redirect()->route('depots-comptes.index')
            ->with('success', 'Dépôt de compte supprimé avec succès.');
    }
}