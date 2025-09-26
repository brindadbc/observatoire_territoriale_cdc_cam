<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RessourcesTransfereesEtat extends Model
{
    use HasFactory;

    protected $fillable = [
        'commune_id', 'type_ressource', 'description', 
        'montant', 'date_reception', 'projet_associe', 'reference'
    ];
protected $casts = [
    'date_reception' => 'date',
    // other casts if needed
];
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
}
