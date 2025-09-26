<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Dette_cnps extends Model
{
    use HasFactory;

    protected $table = 'dette_cnps'; 
    
    protected $fillable = [
        'montant',
        'date_evaluation',
        'annee_exercice',
        'commune_id',
        'description'
    ];

    protected $dates = [
        'date_evaluation'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_evaluation' => 'date'
    ];

    /**
     * Boot method pour auto-remplir l'année d'exercice
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dette) {
            // Auto-remplir l'année d'exercice si pas définie
            if (empty($dette->annee_exercice)) {
                $dette->annee_exercice = $dette->date_evaluation 
                    ? Carbon::parse($dette->date_evaluation)->year 
                    : date('Y');
            }
        });
    }

    // Obtenir la commune associée à cette dette
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    // Vérifier si la dette est en retard
    public function getEnRetardAttribute()
    {
        return $this->date_evaluation < now();
    }

    /**
     * Accesseur pour formater le montant
     */
    public function getFormattedMontantAttribute()
    {
        return number_format($this->montant, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Mutateur pour s'assurer que le montant est positif
     */
    public function setMontantAttribute($value)
    {
        // Nettoyer la valeur (supprimer espaces et caractères non numériques sauf point/virgule)
        $cleanValue = preg_replace('/[^\d.,]/', '', $value);
        $cleanValue = str_replace(',', '.', $cleanValue);
        
        $this->attributes['montant'] = max(0, (float) $cleanValue);
    }

    /**
     * Scope pour filtrer par année
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('annee_exercice', $year);
    }

    /**
     * Scope pour une commune donnée
     */
    public function scopeForCommune($query, $communeId)
    {
        return $query->where('commune_id', $communeId);
    }
}





// <?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class dette_cnps extends Model
// {
//     use HasFactory;
    
//     protected $table = 'dette_cnps';

//     protected $fillable = [
//         'montant',
//         'date_evaluation',
//         'annee_exercice', 
//         'commune_id'
//     ];
    
//     protected $dates = [
//         'date_evaluation'
//     ];
    
//     protected $casts = [
//         'montant' => 'decimal:2',
//     ];
    
    
//      // Obtenir la commune associée à cette dette
     
//     public function commune()
//     {
//         return $this->belongsTo(Commune::class);
//     }
    
    
//      // Vérifier si la dette est en retard
     
//     public function getEnRetardAttribute()
//     {
//         return $this->date_evaluation < now();
//     }

//      /**
//      * Accesseur pour formater le montant
//      */
//     public function getFormattedMontantAttribute()
//     {
//         return number_format($this->montant, 0, ',', ' ') . ' FCFA';
//     }

//     /**
//      * Mutateur pour s'assurer que le montant est positif
//      */
//     public function setMontantAttribute($value)
//     {
//         $this->attributes['montant'] = max(0, (float) $value);
//     }
// }
