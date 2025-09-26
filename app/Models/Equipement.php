<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipement extends Model
{
    protected $fillable = ['infrastructure_id', 'type', 'nom', 'quantite', 'etat', 'date_acquisition', 'cout'];

    public function infrastructure(): BelongsTo
    {
        return $this->belongsTo(Infrastructure::class);
    }
}