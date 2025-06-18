<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
     use HasFactory;

     protected $fillable = [  'nom' ];

     // Obtenir les départements de cette région
    
     public function departements()
    {
        return $this->hasMany(Departement::class);
    }
    
      //Obtenir toutes les communes de cette région via les départements
     
    public function communes()
    {
        return $this->hasManyThrough(Commune::class, Departement::class, 'region_id', 'departement_id');
    }
}

