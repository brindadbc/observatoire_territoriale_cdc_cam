<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonationsExtérieures extends Model
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


//<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class DonationExterieurs extends Model
// {
//     use HasFactory;

//     protected $table = 'donations_exterieures';

//     protected $fillable = [
//         'budget_id', 'donateur', 'type', 'projet_finance', 'montant',
//         'date_signature', 'date_decaissement', 'statut', 'conditions'
//     ];

//     protected $casts = [
//         'montant' => 'decimal:2',
//         'date_signature' => 'date',
//         'date_decaissement' => 'date',
//     ];

//     public function budget()
//     {
//         return $this->belongsTo(Budget::class);
//     }
// }
