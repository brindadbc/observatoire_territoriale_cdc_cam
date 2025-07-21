<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taux_realisation extends Model
// {
//      use HasFactory;
    
    
//     protected $fillable = [
//         'pourcentage',
//         'annee_exercice',
//         'commune_id'
//     ];
    
//     protected $casts = [
//         'pourcentage' => 'decimal:2'
//     ];
    
    
//       //Obtenir la commune associée à ce taux de réalisation
     
//     public function commune()
//     {
//         return $this->belongsTo(Commune::class);
//     }
    
    
//       //Obtenir l'évaluation du taux de réalisation
     
//     public function getEvaluationAttribute()
//     {
//         if ($this->pourcentage < 50) {
//             return 'Insuffisant';
//         } elseif ($this->pourcentage < 75) {
//             return 'Moyen';
//         } elseif ($this->pourcentage < 90) {
//             return 'Bon';
//         } else {
//             return 'Excellent';
//         }
//     }
    
    
//      // Calculer l'écart par rapport à 100%
     
//     public function getEcartAttribute()
//     {
//         return 100 - $this->pourcentage;
//     }
// }



{
    use HasFactory;

    protected $table = 'taux_realisations';

    protected $fillable = [
        'commune_id',
        'annee_exercice',
        'pourcentage',
        'evaluation',
        'ecart',
        'date_calcul'
    ];

    protected $casts = [
        'pourcentage' => 'decimal:2',
        'ecart' => 'decimal:2',
        'date_calcul' => 'datetime',
        'annee_exercice' => 'integer'
    ];

    /**
     * Obtenir la commune associée à ce taux
     */
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    /**
     * Obtenir la prévision associée
     */
    public function prevision()
    {
        return $this->hasOne(Prevision::class, 'commune_id', 'commune_id')
                    ->where('annee_exercice', $this->annee_exercice);
    }

    /**
     * Obtenir les réalisations associées
     */
    public function realisations()
    {
        return $this->hasMany(Realisation::class, 'commune_id', 'commune_id')
                    ->where('annee_exercice', $this->annee_exercice);
    }

    /**
     * Scope pour filtrer par année
     */
    public function scopeAnnee($query, $annee)
    {
        return $query->where('annee_exercice', $annee);
    }

    /**
     * Scope pour filtrer par évaluation
     */
    public function scopeEvaluation($query, $evaluation)
    {
        return $query->where('evaluation', $evaluation);
    }

    /**
     * Accesseur pour obtenir la couleur selon l'évaluation
     */
    public function getCouleurEvaluationAttribute()
    {
        return match($this->evaluation) {
            'Excellent' => 'success',
            'Bon' => 'primary',
            'Moyen' => 'warning',
            'Insuffisant' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Accesseur pour obtenir l'icône selon l'évaluation
     */
    public function getIconeEvaluationAttribute()
    {
        return match($this->evaluation) {
            'Excellent' => 'fas fa-star',
            'Bon' => 'fas fa-thumbs-up',
            'Moyen' => 'fas fa-minus-circle',
            'Insuffisant' => 'fas fa-times-circle',
            default => 'fas fa-question-circle'
        };
    }

}