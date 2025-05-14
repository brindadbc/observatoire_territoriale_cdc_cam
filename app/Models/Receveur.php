<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receveur extends Model
{
    use HasFactory;
    
    
    protected $fillable = [
        'nom',
        'statut',
        'matricule',
        'date_prise_fonction',
        'telephone',
        'id_commune'
    ];
    
    protected $dates = [
        'date_prise_fonction'
    ];
    
    
     // Obtenir la commune à laquelle appartient ce receveur
     
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
    
    
      //Obtenir les dépôts de comptes effectués par ce receveur
     
    public function depotsComptes()
    {
        return $this->hasMany(Depot_compte::class);
    }
    
}
