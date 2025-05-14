<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Defaillance extends Model
{
    use HasFactory;
    
    
    protected $fillable = [
        'type_defaillance',
        'description',
        'date_constat',
        'gravite',
        'est_resolue',
        'id_commune'
    ];
    
    protected $dates = [
        'date_constat'
    ];
    
    
      //Obtenir la commune associée à cette défaillance
     
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
    
    
     //Vérifier si la défaillance est grave
     
    public function getEstGraveAttribute()
    {
        return $this->gravite === 'élevée';
    }
}
