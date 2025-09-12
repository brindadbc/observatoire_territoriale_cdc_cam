<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicateursPerformance extends Model
{
    use HasFactory;

    protected $table = 'indicateurs_performance';

    protected $fillable = [
        'commune_id', 'annee', 'taux_execution_budget', 'taux_recouvrement_recettes',
        'autonomie_financiere', 'endettement_par_habitant', 'investissement_par_habitant',
        'score_gouvernance', 'niveau_performance'
    ];

    protected $casts = [
        'taux_execution_budget' => 'decimal:2',
        'taux_recouvrement_recettes' => 'decimal:2',
        'autonomie_financiere' => 'decimal:2',
        'endettement_par_habitant' => 'decimal:2',
        'investissement_par_habitant' => 'decimal:2',
    ];

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function calculerNiveauPerformance()
    {
        $score = 0;
        
        // Critères de performance (sur 100 points)
        if ($this->taux_execution_budget >= 80) $score += 25;
        elseif ($this->taux_execution_budget >= 60) $score += 15;
        elseif ($this->taux_execution_budget >= 40) $score += 10;
        
        if ($this->taux_recouvrement_recettes >= 80) $score += 25;
        elseif ($this->taux_recouvrement_recettes >= 60) $score += 15;
        elseif ($this->taux_recouvrement_recettes >= 40) $score += 10;
        
        if ($this->autonomie_financiere >= 50) $score += 20;
        elseif ($this->autonomie_financiere >= 30) $score += 15;
        elseif ($this->autonomie_financiere >= 20) $score += 10;
        
        $score += min(30, $this->score_gouvernance * 0.3);
        
        $this->score_gouvernance = $score;
        
        if ($score >= 80) $this->niveau_performance = 'excellent';
        elseif ($score >= 65) $this->niveau_performance = 'bon';
        elseif ($score >= 50) $this->niveau_performance = 'moyen';
        elseif ($score >= 35) $this->niveau_performance = 'faible';
        else $this->niveau_performance = 'critique';
        
        $this->save();
        return $this->niveau_performance;
    }
}
