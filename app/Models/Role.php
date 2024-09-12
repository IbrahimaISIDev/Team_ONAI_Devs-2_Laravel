<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function hasRole($roles)
    {
        if (is_array($roles)) {
            return in_array($this->name, $roles);
        }
        return $this->name === $roles;
    }
}