<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Fonctionnement extends Model
{
    protected $fillable = ['date', 'statut', 'notes', 'cout_maintenance'];

    public function fonctionnable(): MorphTo
    {
        return $this->morphTo();
    }
}