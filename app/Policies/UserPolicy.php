<?php

namespace App\Policies;

use App\Models\User;
use App\Exceptions\ArticleException;

class UserPolicy
{
    public function viewAny(User $user)
    {
        if (!$user->hasRole('ADMIN')) {
            throw new ArticleException('Ce profil n\'est pas autorisé à voir la liste des Utilisateurs', 403);
        }
        return true;    
    }

    public function view(User $user, User $model)
    {
        if (!$user->hasRole('ADMIN')) {
            throw new ArticleException('Ce profil ne peut pas voir cet Utilisateur', 403);
        }
        return true;  
    }

    public function create(User $user)
    {
        if (!$user->hasRole('ADMIN')) {
            throw new ArticleException('Ce profil n\'est pas autorisé à créer des Utilisateurs', 403);
        }
        return true;     
    }

    public function update(User $user, User $model)
    {
        if (!$user->hasRole('ADMIN')) {
            throw new ArticleException('Ce profil n\'est pas autorisé à mettre à jour cet Utilisateur', 403);
        }
        return true;
    }

    public function delete(User $user, User $model)
    {
        if (!$user->hasRole('ADMIN')) {
            throw new ArticleException('Ce profil n\'est pas autorisé à supprimer cet Utilisateur', 403);
        }
        return true;
    }
}