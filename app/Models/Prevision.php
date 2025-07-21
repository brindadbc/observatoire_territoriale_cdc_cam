<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prevision extends Model
{
    use HasFactory;
    
    
    protected $fillable = [
        'annee_exercice',
        'montant',
        'commune_id'
    ];
    
    
    
     // Obtenir la commune associée à cette prévision
    
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
    
    
      //Obtenir les réalisations associées à cette prévision
     
    // public function realisations()
    // {
    //     return $this->hasMany(Realisation::class);
    // }
    
    
    public function realisations()
{
    return $this->hasMany(Realisation::class)->whereNotNull('date_realisation');
}

// Ou pour une relation plus sûre:
public function realisationsValides()
{
    return $this->hasMany(Realisation::class)
                ->whereNotNull('date_realisation')
                ->whereNotNull('montant');
}
      //Calculer le montant total réalisé pour cette prévision
    
    public function getMontantRealiseAttribute()
    {
        // return $this->realisations()->sum('montant');
        return $this->realisations()->sum('montant') ?? 0;
    }
    
    
     // Calculer le taux de réalisation
    
    // public function getTauxRealisationAttribute()
    // {
    //     if ($this->montant == 0) {
    //         return 0;
    //     }
        
    //     return ($this->montant_realise / $this->montant) * 100;
    // }

    // Dans le modèle Prevision
public function getTauxRealisationAttribute()
{
    // $montantRealise = $this->realisations->sum('montant');
    // return $this->montant > 0 ? ($montantRealise / $this->montant) * 100 : 0;
    $montantRealise = $this->montant_realise;
    return $this->montant > 0 ? round(($montantRealise / $this->montant) * 100, 2) : 0;

}

// public function getMontantRealiseAttribute()
// {
//     return $this->realisations->sum('montant');
// }

public function getEvaluationAttribute()
{
    $taux = $this->taux_realisation;
    if ($taux >= 90) return 'Excellent';
    if ($taux >= 75) return 'Bon';
    if ($taux >= 50) return 'Moyen';
    return 'Insuffisant';
}

}
