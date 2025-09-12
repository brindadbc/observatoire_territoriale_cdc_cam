<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RessourcesTransféréesÉtat extends Model
{
    use HasFactory;

    protected $fillable = [
        'commune_id', 'type_ressource', 'description', 
        'montant', 'date_reception', 'projet_associe', 'reference'
    ];

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
}


//<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class RessourcesTransfereEtat extends Model
// {
//     use HasFactory;

//     protected $table = 'ressources_transferees';

//     protected $fillable = [
//         'budget_id', 'type', 'montant_prevu', 'montant_recu',
//         'taux_realisation', 'observations'
//     ];

//     protected $casts = [
//         'montant_prevu' => 'decimal:2',
//         'montant_recu' => 'decimal:2',
//         'taux_realisation' => 'decimal:2',
//     ];

//     public function budget()
//     {
//         return $this->belongsTo(Budget::class);
//     }

//     public function calculerTauxRealisation()
//     {
//         if ($this->montant_prevu > 0) {
//             $this->taux_realisation = ($this->montant_recu / $this->montant_prevu) * 100;
//             $this->save();
//         }
//         return $this->taux_realisation;
//     }

// }
