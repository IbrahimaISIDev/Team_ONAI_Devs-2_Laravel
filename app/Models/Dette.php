<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Dette extends Model
{
    use HasFactory;

    protected $fillable = ['montant', 'client_id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function getMontantRestantAttribute()
    {
        return $this->montant - $this->paiements->sum('montant');
    }

    public function getMontantDuAttribute()
    {
        return $this->montant;
    }

    public function articles()
    {
        return $this->hasMany(Article::class);  // Relation avec les articles liés à la dette
    }
}
