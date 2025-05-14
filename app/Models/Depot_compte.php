<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depot_compte extends Model
{
     use HasFactory;
    
    protected $fillable = [
        'date_depot',
        'annee_exercice',
        'validation',
        'id_commune',
        'id_receveur'
    ];
    
    protected $dates = [
        'date_depot'
    ];
    
    protected $casts = [
        'validation' => 'boolean'
    ];
    
    
     // Obtenir la commune associée à ce dépôt
     
    public function commune()
    {
        return $this->belongsTo(Commune::class, 'id_commune');
    }
    
    
     // Obtenir le receveur qui a effectué ce dépôt
     
    public function receveur()
    {
        return $this->belongsTo(Receveur::class);
    }
}
