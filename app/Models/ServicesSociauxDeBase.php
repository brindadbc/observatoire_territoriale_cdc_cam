<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicesSociauxDeBase extends Model
{
    use HasFactory;

    protected $table = 'emplois_services_sociaux';

    protected $fillable = [
        'budget_id', 'type', 'programme', 'montant_prevu',
        'montant_engage', 'beneficiaires_prevus', 'beneficiaires_reels'
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

    public function getTauxCouvertureBeneficiairesAttribute()
    {
        if ($this->beneficiaires_prevus > 0) {
            return ($this->beneficiaires_reels / $this->beneficiaires_prevus) * 100;
        }
        return 0;
    }
}
