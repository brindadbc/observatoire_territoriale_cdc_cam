<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
     use HasFactory;

    protected $fillable = [
        'nom',
        'type',
        'population',
        'id_departement'
    ];
    
    /**
     * Obtenir le département auquel appartient cette commune
     */
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }
    
    
     // Obtenir la région via le département
     
    public function region()
    {
        return $this->belongsTo(Region::class, 'id_region')
            ->withDefault(['nom' => 'Région non définie']);
    }
    
    
     // Obtenir les ordonnateurs de cette commune
     
    public function ordonnateurs()
    {
        return $this->hasMany(Ordonnateur::class);
    }
    
    
      //Obtenir les receveurs de cette commune
     
    public function receveurs()
    {
        return $this->hasMany(Receveur::class);
    }
    
    
      //Obtenir les dépôts de comptes de cette commune
     
    public function depotsComptes()
    {
        return $this->hasMany(Depot_compte::class);
    }
    
    
     // Obtenir les dettes CNPS de cette commune
     
    public function dettesCnps()
    {
        return $this->hasMany(Dette_cnps::class);
    }
    
    
    
     //Obtenir les prévisions de cette commune
     
    public function previsions()
    {
        return $this->hasMany(Prevision::class);
    }
    
    
     // Obtenir les réalisations de cette commune
    
    public function realisations()
    {
        return $this->hasMany(Realisation::class);
    }
    
    
     // Obtenir les retards de cette commune
     
    public function retards()
    {
        return $this->hasMany(Retard::class);
    }
    
    
     // Obtenir les taux de réalisation de cette commune
     
    public function tauxRealisations()
    {
        return $this->hasMany(Taux_realisation::class);
    }
    
    
      //Obtenir les défaillances de cette commune
     
    public function defaillances()
    {
        return $this->hasMany(Defaillance::class);
    }
}
