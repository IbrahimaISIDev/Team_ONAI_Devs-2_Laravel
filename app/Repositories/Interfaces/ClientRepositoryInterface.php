<?php

// app/Repositories/Interfaces/ClientRepositoryInterface.php
namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;
use App\Models\Client;

interface ClientRepositoryInterface
{
    public function getAllClients(Request $request);
    public function createClient(array $data);
    public function getClientById(string $id);
    public function updateClient(Client $client, array $data);
    public function deleteClient(Client $client);
    public function getClientByPhoneNumber(string $phoneNumber);
    public function addAccountToClient(array $data);
}