<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'code',
        'telephone',
        'email',
        'departement_id',
        'population',
        'superficie',
        'adresse',
        'coordonnees_gps'
    ];

    protected $casts = [
        'population' => 'integer',
        'superficie' => 'decimal:2'
    ];

    /**
     * Obtenir le département auquel appartient cette commune
     */
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    /**
     * Obtenir la région via le département
     */
    public function region()
    {
        return $this->hasOneThrough(Region::class, Departement::class, 'id', 'id', 'departement_id', 'region_id');
    }

    /**
     * Obtenir les ordonnateurs de cette commune
     */
    public function ordonnateurs()
    {
        return $this->hasMany(Ordonnateur::class);
    }

    /**
     * Obtenir les receveurs de cette commune
     */
    public function receveurs()
    {
        return $this->hasMany(Receveur::class);
    }

    public function receveurActif()
    {
        return $this->hasOne(Receveur::class)->where('statut', 'Actif');
    }

    /**
     * Obtenir les dépôts de comptes de cette commune
     */
    public function depotsComptes()
    {
        return $this->hasMany(Depot_compte::class);
    }

    /**
     * Obtenir les différents types de dettes
     */
    public function dettesCnps()
    {
        return $this->hasMany(Dette_cnps::class);
    }

    public function dettesFeicom()
    {
        return $this->hasMany(Dette_feicom::class);
    }

    public function dettesSalariale()
    {
        return $this->hasMany(Dette_salariale::class);
    }

    public function dettesFiscale()
    {
        return $this->hasMany(Dette_fiscale::class);
    }

    /**
     * Obtenir les prévisions de cette commune
     */
    public function previsions()
    {
        return $this->hasMany(Prevision::class);
    }

    /**
     * Obtenir les réalisations de cette commune
     */
    public function realisations()
    {
        return $this->hasMany(Realisation::class);
    }

    /**
     * Obtenir les retards de cette commune
     */
    public function retards()
    {
        return $this->hasMany(Retard::class);
    }

    /**
     * Obtenir les taux de réalisation de cette commune
     */
    public function tauxRealisations()
    {
        return $this->hasMany(Taux_realisation::class);
    }

    /**
     * Obtenir les défaillances de cette commune
     */
    public function defaillances()
    {
        return $this->hasMany(Defaillance::class);
    }

    public function indicateursPerformance()
    {
        return $this->hasMany(IndicateurPerformance::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function budgetAnnuel($annee = null)
    {
        $annee = $annee ?? date('Y');
        return $this->hasOne(Budget::class)->where('annee', $annee);
    }

    /**
     * Relation projets - CORRECTION du problème principal
     */
    public function projets()
    {
        return $this->hasMany(Projet::class);
    }

    /**
     * Relation transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Accesseurs et mutateurs
     */
    public function getPopulationFormatteeAttribute()
    {
        return $this->population ? number_format($this->population, 0, ',', ' ') : 'Non renseignée';
    }

    public function getSuperficieFormatteeAttribute()
    {
        return $this->superficie ? number_format($this->superficie, 2, ',', ' ') . ' km²' : 'Non renseignée';
    }

    /**
     * Scopes
     */
    public function scopeWithPerformance($query, $annee = null)
    {
        $annee = $annee ?? date('Y');
        return $query->with(['tauxRealisations' => function($q) use ($annee) {
            $q->where('annee_exercice', $annee);
        }]);
    }

    public function scopeWithFinancialData($query, $annee = null)
    {
        $annee = $annee ?? date('Y');
        return $query->with([
            'previsions' => function($q) use ($annee) {
                $q->where('annee_exercice', $annee);
            },
            'realisations' => function($q) use ($annee) {
                $q->where('annee_exercice', $annee);
            }
        ]);
    }

    /**
     * Méthodes helper
     */
    public function getTauxRealisationAnnuel($annee = null)
    {
        $annee = $annee ?? date('Y');
        $taux = $this->tauxRealisations()->where('annee_exercice', $annee)->first();
        return $taux ? $taux->pourcentage : 0;
    }

    public function getTotalDettes($annee = null)
    {
        $annee = $annee ?? date('Y');
        
        return [
            'cnps' => $this->dettesCnps()->where('annee_exercice', $annee)->sum('montant'),
            'fiscale' => $this->dettesFiscale()->where('annee_exercice', $annee)->sum('montant'),
            'feicom' => $this->dettesFeicom()->where('annee_exercice', $annee)->sum('montant'),
            'salariale' => $this->dettesSalariale()->where('annee_exercice', $annee)->sum('montant')
        ];
    }

    public function hasActiveProjects()
    {
        return $this->projets()->where('statut', 'en_cours')->exists();
    }

    public function hasRecentTransactions($days = 30)
    {
        return $this->transactions()
            ->where('created_at', '>', now()->subDays($days))
            ->exists();
    }
}