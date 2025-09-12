<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RessourcesPropres extends Model
{
    use HasFactory;

    protected $fillable = [
        'commune_id', 'source', 'type_ressource', 
        'montant', 'date_generation', 'description'
    ];

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
}



//<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class RessourcesPropres extends Model
// {
//     use HasFactory;

//     protected $table = 'ressources_propres';

//     protected $fillable = [
//         'budget_id', 'type', 'montant_prevu', 'montant_realise',
//         'taux_realisation', 'observations'
//     ];

//     protected $casts = [
//         'montant_prevu' => 'decimal:2',
//         'montant_realise' => 'decimal:2',
//         'taux_realisation' => 'decimal:2',
//     ];

//     public function budget()
//     {
//         return $this->belongsTo(Budget::class);
//     }

//     public function calculerTauxRealisation()
//     {
//         if ($this->montant_prevu > 0) {
//             $this->taux_realisation = ($this->montant_realise / $this->montant_prevu) * 100;
//             $this->save();
//         }
//         return $this->taux_realisation;
//     }
// }
