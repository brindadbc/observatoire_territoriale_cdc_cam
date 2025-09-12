<?php
// Modèle Projet.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'commune_id',
        'responsable_id',
        'date_debut',
        'date_fin_prevue',
        'date_fin_reelle',
        'budget',
        'cout_reel',
        'statut',
        'priorite',
        'pourcentage_completion'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
        'date_fin_reelle' => 'date',
        'budget' => 'decimal:2',
        'cout_reel' => 'decimal:2',
        'pourcentage_completion' => 'integer'
    ];

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function financements()
    {
        return $this->hasMany(ProjetFinancement::class);
    }
}