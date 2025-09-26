<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonationsExterieures extends Model
{
   use HasFactory;

    protected $fillable = [
        'commune_id', 'donateur', 'type_aide', 'montant', 
        'description', 'date_reception', 'conditions', 'projet_associe'
    ];

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
}
