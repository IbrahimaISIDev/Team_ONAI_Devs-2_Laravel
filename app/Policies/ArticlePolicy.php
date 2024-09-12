<?php

// app/Policies/ArticlePolicy.php
namespace App\Policies;

use App\Models\User;
use App\Exceptions\ArticleException;

class ArticlePolicy
{   
    public function viewAny(User $user)
    {
        if (!$user->hasRole(['ADMIN', 'BOUTIQUIER, CLIENT'])) {
            throw new ArticleException('Ce profil n\'est pas autorisé à voir la liste des articles', 403);
        }
        return true;
    }

    public function view(User $user)
    {
        if (!$user->hasRole(['ADMIN', 'BOUTIQUIER, CLIENT'])) {
            throw new ArticleException('Ce profil ne peut pas voir cet article', 403);
        }
        return true;
    }

    public function create(User $user)
    {
        if (!$user->hasRole(['ADMIN', 'BOUTIQUIER'])) {
            throw new ArticleException('Ce profil n\'est pas autorisé à créer des articles', 403);
        }
        return true;
    }

    public function update(User $user)
    {
        if (!$user->hasRole(['ADMIN', 'BOUTIQUIER'])) {
            throw new ArticleException('Ce profil n\'est pas autorisé à mettre à jour cet article', 403);
        }
        return true;
    }

    public function delete(User $user)
    {
        if (!$user->hasRole(['ADMIN', 'BOUTIQUIER'])) {
            throw new ArticleException('Ce profil n\'est pas autorisé à supprimer cet article', 403);
        }
        return true;
    }

    public function restore(User $user)
    {
        if (!$user->hasRole(['ADMIN', 'BOUTIQUIER'])) {
            throw new ArticleException('Ce profil n\'est pas autorisé à restaurer cet article', 403);
        }
        return true;
    }

    public function forceDelete(User $user)
    {
        if (!$user->hasRole('ADMIN')) {
            throw new ArticleException('Ce profil n\'est pas autorisé à supprimer définitivement cet article', 403);
        }
        return true;
    }

    public function updateAny(User $user)
    {
        if (!$user->hasRole(['ADMIN', 'BOUTIQUIER'])) {
            throw new ArticleException('Ce profil n\'est pas autorisé à mettre à jour plusieurs articles', 403);
        }
        return true;
    }
}