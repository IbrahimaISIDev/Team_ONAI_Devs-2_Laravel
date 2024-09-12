<?php

// app/Models/Article.php
namespace App\Models;

use App\Models\Scopes\FilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['libelle', 'description', 'price', 'stock'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new FilterScope());
    }

    public static function filter(array $filters)
    {
        return static::withGlobalScope('filter', new FilterScope($filters));
    }

    public static function findByLibelle(string $libelle): ?self
    {
        return static::filter(['libelle' => $libelle])->first();
    }

    public static function findByEtat($etat)
    {
        return static::filter(['etat' => $etat])->get();
    }

    public function dette()
    {
        return $this->belongsTo(Dette::class);  // Relation inverse avec la dette
    }
}