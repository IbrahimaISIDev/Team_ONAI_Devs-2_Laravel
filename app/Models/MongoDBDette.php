<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class MongoDBDette extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'dettes';

    protected $fillable = [
        'client_id',
        'montant',
        'description',
        'date_echeance',
        'solde',
        'articles',
        'paiements'
    ];

    protected $casts = [
        'solde' => 'boolean',
        'date_echeance' => 'datetime',
        'articles' => 'array',
        'paiements' => 'array'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}