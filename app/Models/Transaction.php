<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'commune_id',
        'type',
        'montant',
        'description',
        'date_transaction',
        'reference',
        'statut',
        'annee_exercice'
    ];

    protected $casts = [
        'date_transaction' => 'date',
        'montant' => 'decimal:2'
    ];

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
}
