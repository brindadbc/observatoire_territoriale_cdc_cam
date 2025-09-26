<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Fonctionnement;

class ServiceSocial extends Model
{
    protected $table = 'service_socials'; // Explicit table name
    
    protected $fillable = ['commune_id', 'type', 'nom', 'description', 'capacite', 'personnel', 'budget_annuel'];
    
    // ... rest of your model code

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function fonctionnements(): MorphMany
    {
        return $this->morphMany(Fonctionnement::class, 'fonctionnable');
    }
}