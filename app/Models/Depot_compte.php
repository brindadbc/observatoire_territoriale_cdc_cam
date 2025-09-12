<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depot_compte extends Model
{
    //  use HasFactory;
    
    // protected $fillable = [
    //     'date_depot',
    //     'annee_exercice',
    //     'validation',
    //     'commune_id',
    //     'receveur_id'
    // ];
    
    // protected $dates = [
    //     'date_depot'
    // ];
    
    // protected $casts = [
    //     'validation' => 'boolean'
    // ];
    
    
    //  // Obtenir la commune associée à ce dépôt
     
    // public function commune()
    // {
    //     return $this->belongsTo(Commune::class, 'commune_id');
    // }
    
    
    //  // Obtenir le receveur qui a effectué ce dépôt
     
    // public function receveur()
    // {
    //     return $this->belongsTo(Receveur::class);
    // }


    use HasFactory;

    protected $table = 'depot_comptes';

    protected $fillable = [
        'commune_id', 'exercice', 'type', 'date_limite_depot',
        'date_depot_effectif', 'jours_retard', 'statut', 'observations'
    ];

    protected $casts = [
        'date_limite_depot' => 'date',
        'date_depot_effectif' => 'date',
    ];

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function calculerRetard()
    {
        if ($this->date_depot_effectif && $this->date_limite_depot) {
            $this->jours_retard = max(0, $this->date_depot_effectif->diffInDays($this->date_limite_depot, false));
            $this->save();
        }
        return $this->jours_retard;
    }

    public function isEnRetard()
    {
        return $this->jours_retard > 0 || 
               ($this->statut === 'non_depose' && now()->greaterThan($this->date_limite_depot));
    }
}
