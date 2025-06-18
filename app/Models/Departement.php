<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
     use HasFactory;

    protected $fillable = [
        'nom',
        'region_id'
    ];
    
    
     // Obtenir la région à laquelle appartient ce département
     
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    
    
      //Obtenir les communes de ce département
     
    public function communes()
    {
        return $this->hasMany(Commune::class);
    }
} 
