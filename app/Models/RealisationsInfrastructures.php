<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealisationsInfrastructures extends Model
{
     use HasFactory;

    protected $table = 'emplois_infrastructures';

    protected $fillable = [
        'budget_id', 'type', 'designation', 'montant_prevu',
        'montant_engage', 'taux_execution', 'statut'
    ];

    protected $casts = [
        'montant_prevu' => 'decimal:2',
        'montant_engage' => 'decimal:2',
        'taux_execution' => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function calculerTauxExecution()
    {
        if ($this->montant_prevu > 0) {
            $this->taux_execution = ($this->montant_engage / $this->montant_prevu) * 100;
            $this->save();
        }
        return $this->taux_execution;
    }
}
