<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// class Region extends Model
// {
//      use HasFactory;

//      protected $fillable = [  'nom' ];

//      // Obtenir les départements de cette région
    
//      public function departements()
//     {
//         return $this->hasMany(Departement::class);
//     }
    
//       //Obtenir toutes les communes de cette région via les départements
     
//     public function communes()
//     {
//         return $this->hasManyThrough(Commune::class, Departement::class, 'region_id', 'departement_id');
//     }
// }

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'code', 'chef_lieu', 'nombre_departements', 
        'nombre_communes', 'superficie', 'population'
    ];

    protected $casts = [
        'superficie' => 'decimal:2',
        'population' => 'integer',
    ];

    public function departements()
    {
        return $this->hasMany(Departement::class);
    }

    public function communes()
    {
        return $this->hasManyThrough(Commune::class, Departement::class);
    }

    public function updateCounters()
    {
        $this->nombre_departements = $this->departements()->count();
        $this->nombre_communes = $this->communes()->count();
        $this->save();
    }
}