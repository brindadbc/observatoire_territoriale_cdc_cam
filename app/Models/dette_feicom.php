<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dette_feicom extends Model
{
     use HasFactory;
    
    
    protected $fillable = [
        'montant',
        'date_evaluation',
        'annee_exercice', 
        'commune_id'
    ];
    
    protected $dates = [
        'date_evaluation'
    ];
    
    protected $casts = [
        'montant' => 'decimal:2',
    ];
    
    
     // Obtenir la commune associée à cette dette
     
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
    
    
     // Vérifier si la dette est en retard
     
    public function getEnRetardAttribute()
    {
        return $this->date_evaluation < now();
    }

    // Dans Dette_FeicomController.php
public function rapportParRegion()
{
    // Votre logique ici
    $regions = collect(); // Exemple
    $annee = request('annee', date('Y'));
    
    return view('dettes-feicom.rapport-regions', compact('regions', 'annee'));
}

public function export()
{
    $format = request('format');
    // Votre logique d'export ici
    
    if (!$format) {
        return view('dettes-feicom.export'); // Formulaire d'export
    }
    
    // Logique d'export selon le format
}
}
