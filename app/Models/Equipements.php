<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipements extends Model
{
     use HasFactory;

    protected $table = 'equipements';

    protected $fillable = [
        'budget_id', 'type', 'designation', 'montant_prevu',
        'montant_engage', 'quantite', 'statut'
    ];

    protected $casts = [
        'montant_prevu' => 'decimal:2',
        'montant_engage' => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function getTauxExecutionAttribute()
    {
        if ($this->montant_prevu > 0) {
            return ($this->montant_engage / $this->montant_prevu) * 100;
        }
        return 0;
    }
}
