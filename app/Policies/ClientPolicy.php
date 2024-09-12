<?php

// app/Policies/ClientPolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Client;
use App\Exceptions\ClientException;

class ClientPolicy
{

    public function viewAny(User $user)
    {
        if (!$user->hasRole(['ADMIN', 'BOUTIQUIER'])) {
            throw new ClientException('Ce profil n\'est pas autorisé à voir la liste des clients', 403);
        }
        return true;
    }

    public function view(User $user)
    {
        if ($user->hasRole(['ADMIN', 'BOUTIQUIER, CLIENT'])) {
            throw new ClientException('Ce profil ne peut pas ce profil', 403);
        }
        return true;
    }

    public function create(User $user)
    {
        if (!$user->hasRole(['ADMIN', 'BOUTIQUIER'])) {
            throw new ClientException('Ce profil n\'est pas autorisé à créer des clients', 403);
        }
        return true;
    }

    public function update(User $user, Client $client)
    {
        if (!$user->hasRole(['ADMIN', 'BOUTIQUIER'])) {
            throw new ClientException('Ce profil n\'est pas autorisé à mettre à jour cet Client', 403);
        }
        return true;
    }

    public function addAccount(User $user, Client $client)
    {
        if (!$user->hasRole(['ADMIN', 'BOUTIQUIER'])) {
            throw new ClientException('Ce profil n\'est pas autorisé à ajouter un compte à cet client', 403);
        }
        return true;    
    }

    public function delete(User $user, Client $client)
    {
        if (!$user->hasRole(['ADMIN', 'BOUTIQUIER'])) {
            throw new ClientException('Ce profil n\'est pas autorisé à supprimer cet client', 403);
        }
        return true;
    }
}