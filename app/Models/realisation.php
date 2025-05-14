<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class realisation extends Model
{
    use HasFactory;
    
   
    
    protected $fillable = [
        'annee_exercice',
        'montant',
        'date_realisation',
        'id_prevision',
        'id_commune'
    ];
    
    protected $dates = [
        'date_realisation'
    ];
    
    protected $casts = [
        'montant' => 'decimal:2'
    ];
    
    
     // Obtenir la commune associée à cette réalisation
     
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
    
    
      //Obtenir la prévision associée à cette réalisation
     
    public function prevision()
    {
        return $this->belongsTo(Prevision::class);
    }
    
    
    //   Calculer l'écart par rapport à la prévision
     
    public function getEcartPrevisionAttribute()
    {
        if (!$this->prevision) {
            return null;
        }
        
        return $this->montant - $this->prevision->montant;
    }
}
