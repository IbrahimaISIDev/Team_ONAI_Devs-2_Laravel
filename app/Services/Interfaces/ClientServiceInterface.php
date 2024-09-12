<?php

// app/Services/Interfaces/ClientServiceInterface.php
namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface ClientServiceInterface
{
    public function getAllClients(Request $request);
    public function createClient(array $data);
    public function getClientById(string $id);
    public function updateClient(string $id, array $data);
    public function deleteClient(string $id);
    public function getClientByPhoneNumber(string $phoneNumber);
    public function addAccountToClient(array $data);
}