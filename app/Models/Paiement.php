<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Paiement extends Model
{
    use HasFactory;

    protected $fillable = ['montant', 'dette_id'];

    public function dette()
    {
        return $this->belongsTo(Dette::class);  // Relation inverse avec la dette
    }
}
