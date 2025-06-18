<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dette_salariale extends Model
{
     use HasFactory;
    
    
    protected $fillable = [
        'montant',
        'date_evaluation',
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
        return !$this->date_evaluation < now();
    }
}
