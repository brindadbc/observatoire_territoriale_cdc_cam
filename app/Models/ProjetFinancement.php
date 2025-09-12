<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetFinancement extends Model
{
    use HasFactory;

    protected $table = 'projet_financements';

    protected $fillable = [
        'projet_id',
        'source',
        'montant',
        'type',
        'date_obtention',
        'conditions'
    ];

    protected $casts = [
        'date_obtention' => 'date',
        'montant' => 'decimal:2'
    ];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }
}
