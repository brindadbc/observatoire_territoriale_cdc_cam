<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Infrastructure extends Model
{
    protected $fillable = [
        'commune_id', 
        'type', 
        'nom', 
        'description', 
        'localisation', 
        'etat', 
        'date_construction', 
        'cout_construction'
    ];

    // Casting for proper data types
    protected $casts = [
        'date_construction' => 'date',
        'cout_construction' => 'decimal:2'
    ];

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function equipements(): HasMany
    {
        return $this->hasMany(Equipement::class);
    }

    public function fonctionnements(): MorphMany
    {
        return $this->morphMany(Fonctionnement::class, 'fonctionnable');
    }
}