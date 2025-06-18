<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taux_realisation extends Model
{
     use HasFactory;
    
    
    protected $fillable = [
        'pourcentage',
        'annee_exercice',
        'commune_id'
    ];
    
    protected $casts = [
        'pourcentage' => 'decimal:2'
    ];
    
    
      //Obtenir la commune associée à ce taux de réalisation
     
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
    
    
      //Obtenir l'évaluation du taux de réalisation
     
    public function getEvaluationAttribute()
    {
        if ($this->pourcentage < 50) {
            return 'Insuffisant';
        } elseif ($this->pourcentage < 75) {
            return 'Moyen';
        } elseif ($this->pourcentage < 90) {
            return 'Bon';
        } else {
            return 'Excellent';
        }
    }
    
    
     // Calculer l'écart par rapport à 100%
     
    public function getEcartAttribute()
    {
        return 100 - $this->pourcentage;
    }
}
