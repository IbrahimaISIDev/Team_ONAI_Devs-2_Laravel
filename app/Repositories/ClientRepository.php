<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Client;
use App\Enums\EtatEnum;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ClientException;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;
use App\Facades\UploadFacade as Upload;
use App\Repositories\Interfaces\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    public function getAllClients(Request $request)
    {
        $comptes = $request->query('comptes');
        $active = $request->query('active');

        $query = QueryBuilder::for(Client::class)
            ->allowedFilters(['surname'])
            ->allowedIncludes(['user']);

        if ($comptes !== null) {
            $query = $comptes === 'oui' ? $query->whereHas('user') : $query->whereDoesntHave('user');
        }

        if ($active !== null) {
            $etat = $active === 'oui' ? EtatEnum::ACTIF->value : EtatEnum::INACTIF->value;
            $query->whereHas('user', function ($query) use ($etat) {
                $query->where('etat', $etat);
            });
        }

        $clients = $query->get();

        foreach ($clients as $client) {
            if ($client->user && $client->user->photo) {
                $client->user->photo_base64 = $this->getBase64Photo($client->user->photo);
            }
        }

        return $clients;
    }

    public function createClient(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = null;

            // Créer l'utilisateur d'abord s'il est fourni
            if (isset($data['user'])) {
                $userData = $data['user'];
                $userData['password'] = Hash::make($userData['password']);
                $userData['etat'] ??= EtatEnum::ACTIF->value;

                if (isset($userData['photo']) && $userData['photo'] instanceof UploadedFile) {
                    $userData['photo'] = Upload::uploadPhoto($userData['photo'], 'clients');
                }

                $user = User::create($userData);
            }

            // Créer le client
            $clientData = [
                'surname' => $data['surname'],
                'telephone' => $data['telephone'],
                'adresse' => $data['adresse'],
                'email' => $data['email']
            ];

            $client = Client::create($clientData);

            // Associer l'utilisateur au client si un utilisateur a été créé
            if ($user) {
                $client->user()->associate($user);
                $client->save();
            }

            return $client;
        });
    }

    public function getClientById(string $id)
    {
        $client = Client::with('user')->findOrFail($id);
        if ($client->user && $client->user->photo) {
            $client->user->photo_base64 = $this->getBase64Photo($client->user->photo);
        }
        return $client;
    }

    public function updateClient(Client $client, array $data)
    {
        return DB::transaction(function () use ($client, $data) {
            $client->update($data);

            if (isset($data['user'])) {
                $userData = $data['user'];
                if (isset($userData['password'])) {
                    $userData['password'] = Hash::make($userData['password']);
                }

                if ($client->user) {
                    $client->user->update($userData);
                } else {
                    $userData['etat'] ??= EtatEnum::ACTIF->value;
                    $user = User::create($userData);
                    $client->user()->associate($user);
                }
            }

            $client->save();
            return $client;
        });
    }

    public function deleteClient(Client $client)
    {
        return DB::transaction(function () use ($client) {
            if ($client->user) {
                $client->user->delete();
            }
            return $client->delete();
        });
    }

    public function getClientByPhoneNumber(string $phoneNumber)
    {
        return Client::where('telephone', $phoneNumber)->firstOrFail();
    }

    public function addAccountToClient(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Trouver le client par son ID
            $client = Client::findOrFail($data['client_id']);

            // Valider les données de l'utilisateur
            $userData = $data['user'];
            $userData['password'] = Hash::make($userData['password']);
            $userData['etat'] ??= EtatEnum::ACTIF->value;

            // Définir un rôle par défaut si non fourni
            if (!isset($userData['role_id'])) {
                $userData['role_id'] = 3;
            }

            if (isset($userData['photo']) && $userData['photo'] instanceof UploadedFile) {
                $userData['photo'] = Upload::uploadPhoto($userData['photo'], 'clients');
            }

            // Créer un nouvel utilisateur
            $user = User::create($userData);

            // Associer l'utilisateur au client
            $client->user()->associate($user);
            $client->save();

            return $client;
        });
    }




    private function storePhotoLocally(UploadedFile $photo)
    {
        return $photo->store('clients', 'public');
    }

    private function getBase64Photo($photoPath)
    {
        return Upload::getBase64Photo($photoPath);
    }
}
