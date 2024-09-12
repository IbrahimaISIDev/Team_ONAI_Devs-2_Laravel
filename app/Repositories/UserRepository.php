<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use App\Enums\EtatEnum;
use Illuminate\Http\Request;
use App\Exceptions\UserException;
use Illuminate\Http\UploadedFile;
use App\Http\Resources\UserResource;
use App\Facades\UploadFacade as Upload;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserCollection;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers(Request $request)
    {
        // Récupération des paramètres de filtrage depuis la requête
        $active = $request->query('active');
        $role = $request->query('role');

        // Création d'une requête de base
        $query = User::query();

        // Filtrer par état (actif/inactif)
        if ($active !== null) {
            $etat = $active === 'oui' ? EtatEnum::ACTIF->value : EtatEnum::INACTIF->value;
            $query->where('etat', $etat);
        }

        // Filtrer par rôle
        if ($role !== null) {
            // Vérification si le rôle existe
            $roleExists = Role::where('id', $role)->exists();

            if (!$roleExists) {
                throw new UserException('Le rôle spécifié n\'existe pas', 404);
            }

            // Filtrer par rôle via la relation
            $query->whereHas('role', function ($query) use ($role) {
                $query->where('id', $role);
            });
        }

        // Retourner la collection des utilisateurs filtrés
        return $query->get();
    }

    public function createUser(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = Upload::uploadPhoto($data['photo'], 'users');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return new UserResource(User::create($data));
    }


    public function getUserById(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            throw new UserException('Utilisateur non trouvé', 404);
        }
        return new UserResource($user);
    }

    public function updateUser(User $user, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return new UserResource($user);
    }

    public function deleteUser(User $user)
    {
        return $user->delete();
    }
}
