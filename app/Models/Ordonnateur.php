<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordonnateur extends Model
{
     use HasFactory;
    
    
    protected $fillable = [
        'nom',
        'date_prise_fonction',
        'fonction',
        'telephone',
        'commune_id'
    ];
    
    protected $dates = [
        'date_prise_fonction'
    ];
    
    
     // Obtenir la commune à laquelle appartient cet ordonnateur
     
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
    
}
