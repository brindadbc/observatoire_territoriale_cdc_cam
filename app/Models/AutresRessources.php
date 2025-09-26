<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AutresRessources extends Model
{
    use HasFactory;

    protected $fillable = [
        'commune_id', 'source', 'type_ressource', 
        'montant', 'date_reception', 'description'
    ];

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
}
<<<<<<< HEAD






//<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class AutresRessources extends Model
// {
//      use HasFactory;

//     protected $table = 'autre_ressources';

//     protected $fillable = [
//         'budget_id', 'type', 'montant_prevu', 'montant_engage', 'observations'
//     ];

//     protected $casts = [
//         'montant_prevu' => 'decimal:2',
//         'montant_engage' => 'decimal:2',
//     ];

//     public function budget()
//     {
//         return $this->belongsTo(Budget::class);
//     }

//     public function getTauxExecutionAttribute()
//     {
//         if ($this->montant_prevu > 0) {
//             return ($this->montant_engage / $this->montant_prevu) * 100;
//         }
//         return 0;
//     }
// }
=======
>>>>>>> 26296e8 (manas)
