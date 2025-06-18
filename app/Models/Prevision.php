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
     
    public function realisations()
    {
        return $this->hasMany(Realisation::class);
    }
    
    
      //Calculer le montant total réalisé pour cette prévision
    
    public function getMontantRealiseAttribute()
    {
        return $this->realisations()->sum('montant');
    }
    
    
     // Calculer le taux de réalisation
    
    public function getTauxRealisationAttribute()
    {
        if ($this->montant == 0) {
            return 0;
        }
        
        return ($this->montant_realise / $this->montant) * 100;
    }
}
