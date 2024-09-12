<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Events\ClientCreated;
use Illuminate\Http\UploadedFile;
use App\Exceptions\ClientException;
use App\Facades\UploadFacade as Upload;
use App\Services\Interfaces\ClientServiceInterface;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

class ClientService implements ClientServiceInterface
{
    protected $clientRepository;
    protected $userRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, UserRepositoryInterface $userRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
    }

    public function getAllClients(Request $request)
    {
        try {
            return $this->clientRepository->getAllClients($request);
        } catch (\Exception $e) {
            throw new ClientException("Erreur lors de la récupération des clients: " . $e->getMessage());
        }
    }

    public function createClient(array $data)
    {
        try {
            if (isset($data['user']['photo']) && $data['user']['photo'] instanceof UploadedFile) {
                $data['user']['photo'] = Upload::uploadPhoto($data['user']['photo'], 'clients');
            }

            $client = $this->clientRepository->createClient($data);

            event(new ClientCreated($client)); // Déclenchement de l'événement après la création

            return [
                'client' => $client,
                'message' => 'Client créé avec succès.'
            ];
        } catch (\Exception $e) {
            throw new ClientException("Erreur lors de la création du client: " . $e->getMessage());
        }
    }

    public function getClientById(string $id): ?Client
    {
        try {
            return $this->clientRepository->getClientById($id);
        } catch (\Exception $e) {
            throw new ClientException("Erreur lors de la récupération du client: " . $e->getMessage());
        }
    }

    public function updateClient(string $id, array $data)
    {
        try {
            $client = $this->clientRepository->getClientById($id);
            if (!$client) {
                throw new ClientException("Client non trouvé.");
            }

            return $this->clientRepository->updateClient($client, $data);
        } catch (\Exception $e) {
            throw new ClientException("Erreur lors de la mise à jour du client: " . $e->getMessage());
        }
    }

    public function deleteClient(string $id)
    {
        try {
            $client = $this->clientRepository->getClientById($id);
            if (!$client) {
                throw new ClientException("Client non trouvé.");
            }

            return $this->clientRepository->deleteClient($client);
        } catch (\Exception $e) {
            throw new ClientException("Erreur lors de la suppression du client: " . $e->getMessage());
        }
    }

    public function getClientByPhoneNumber(string $phoneNumber)
    {
        try {
            return $this->clientRepository->getClientByPhoneNumber($phoneNumber);
        } catch (\Exception $e) {
            throw new ClientException("Erreur lors de la récupération du client par numéro de téléphone: " . $e->getMessage());
        }
    }

    // public function addAccountToClient(array $data)
    // {
    //     try {
    //         $this->validateAccountData($data);
    //         return $this->clientRepository->addAccountToClient($data);
    //     } catch (\Exception $e) {
    //         throw new ClientException($e->getMessage());
    //     }
    // }

    public function addAccountToClient(array $data)
    {
        try {
            // Valider les données du compte
            $this->validateAccountData($data);

            // Récupérer le client
            $client = $this->clientRepository->getClientById($data['client_id']);
            if (!$client) {
                throw new ClientException("Client non trouvé.");
            }

            // Créer un nouvel utilisateur
            $user = $this->userRepository->createUser($data['user']);

            // Associer l'utilisateur au client
            $client->user()->associate($user);
            $client->save();

            return [
                'client' => $client,
                'message' => 'Compte utilisateur ajouté au client avec succès.'
            ];
        } catch (\Exception $e) {
            throw new ClientException("Erreur lors de l'ajout du compte utilisateur au client: " . $e->getMessage());
        }
    }

    private function validateAccountData(array $data)
    {
        if (!isset($data['client_id']) || !is_numeric($data['client_id'])) {
            throw new ClientException("L'ID du client est requis et doit être un nombre.");
        }

        if (!isset($data['user']) || !is_array($data['user'])) {
            throw new ClientException("Les données de l'utilisateur sont requises et doivent être un tableau.");
        }

        // Ajouter des validations pour les données de l'utilisateur si nécessaire
    }
}
