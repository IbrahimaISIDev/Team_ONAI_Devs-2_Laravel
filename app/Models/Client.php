<?php

namespace App\Models;

use App\Models\Scopes\FilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'surname',
        'adresse',
        'telephone',
        'user_id',
        'email'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $with = ['user'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    protected static function booted()
    {
        static::addGlobalScope(new FilterScope());
    }

    public static function filter(array $filters)
    {
        return static::withGlobalScope('filter', new FilterScope($filters));
    }

    public static function findByTelephone(string $telephone): ?self
    {
        return static::filter(['telephone' => $telephone])->first();
    }

    public function scopeSurname($query, $surname)
    {
        return $query->where('surname', $surname);
    }

    public static function findByEtat($etat)
    {
        return static::filter(['etat' => $etat])->get();
    }

    public function getFirstName(): ?string
    {
        return $this->user->prenom ?? null;
    }

    public function getLastName(): ?string
    {
        return $this->user->nom ?? null;
    }

    public function getFullName(): string
    {
        $name = trim(($this->getFirstName() ?? '') . ' ' . ($this->getLastName() ?? ''));
        return $name ?: 'Client';
    }

    public function getEmail(): ?string
    {
        return $this->user->email ?? $this->email ?? null;
    }

    public function hasAccount(): bool
    {
        return $this->user !== null;
    }

    public function dettes()
    {
        return $this->hasMany(Dette::class);  // Relation avec les dettes
    }
}
