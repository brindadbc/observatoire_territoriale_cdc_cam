<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retard extends Model
{
     use HasFactory;
    
    
    protected $fillable = [
        'type_retard',
        'duree_jours',
        'date_constat',
        'annee_exercice', 
        'date_retard',
        'commune_id'
    ];
    
    protected $dates = [
        'date_constat'
    ];
    
    
     // Obtenir la commune associée à ce retard
     
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
    
    
    
     // Obtenir le niveau de gravité en fonction de la durée
     
    public function getGraviteAttribute()
    {
        if ($this->duree_jours <= 15) {
            return 'Faible';
        } elseif ($this->duree_jours <= 30) {
            return 'Moyenne';
        } else {
            return 'Élevée';
        }
    }
}
