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

    /**
     * Accesseur pour obtenir le nombre de départements
     */
    public function getNombreDepartementsAttribute()
    {
        return $this->departements()->count();
    }

    /**
     * Accesseur pour obtenir le nombre de communes
     */
    public function getNombreCommunesAttribute()
    {
        return $this->communes()->count();
    }

    /**
     * Accesseur pour obtenir la densité de population
     */
    public function getDensiteAttribute()
    {
        if ($this->superficie && $this->population) {
            return round($this->population / $this->superficie, 1);
        }
        return null;
    }

    /**
     * Scope pour rechercher par nom
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('chef_lieu', 'like', "%{$search}%");
        });
    }

    /**
     * Scope pour ordonner par population
     */
    public function scopeOrderByPopulation($query, $direction = 'desc')
    {
        return $query->orderBy('population', $direction);
    }

    /**
     * Scope pour ordonner par superficie
     */
    public function scopeOrderBySuperficie($query, $direction = 'desc')
    {
        return $query->orderBy('superficie', $direction);
    }
}