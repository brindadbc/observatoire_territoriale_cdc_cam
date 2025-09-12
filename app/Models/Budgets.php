<?php

namespace App\Models;

use App\Http\Controllers\RealisationController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budgets extends Model
{
    use HasFactory;

    protected $fillable = [
        'commune_id', 'annee', 'montant_total_ressources',
        'montant_total_emplois', 'taux_execution', 'statut', 'date_adoption'
    ];

    protected $casts = [
        'montant_total_ressources' => 'decimal:2',
        'montant_total_emplois' => 'decimal:2',
        'taux_execution' => 'decimal:2',
        'date_adoption' => 'date',
    ];

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function ressourcesTransferees()
    {
        return $this->hasMany(RessourceTransferee::class);
    }

    public function ressourcesPropres()
    {
        return $this->hasMany(RessourcePropre::class);
    }

    public function donationsExterieures()
    {
        return $this->hasMany(DonationExterieure::class);
    }

    public function emploisInfrastructures()
    {
        return $this->hasMany(RealisationInfrastructure::class);
    }

    public function emploisEquipements()
    {
        return $this->hasMany(Equipement::class);
    }

    public function emploisServicesSociaux()
    {
        return $this->hasMany(ServiceSocial::class);
    }

    public function emploisFonctionnement()
    {
        return $this->hasMany(AutresRessources::class);
    }

    public function calculerTauxExecution()
    {
        if ($this->montant_total_ressources > 0) {
            $this->taux_execution = ($this->montant_total_emplois / $this->montant_total_ressources) * 100;
            $this->save();
        }
        return $this->taux_execution;
    }
}
