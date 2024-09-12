<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClientService;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Services\Interfaces\ClientServiceInterface;

class ClientController extends Controller
{
    protected $clientService;

    public function __construct(ClientServiceInterface $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index(Request $request)
    {
        return $this->clientService->getAllClients($request);
    }

    public function store(StoreClientRequest $request)
    {
        return $this->clientService->createClient($request->validated());
    }

    public function show(string $id, Request $request)
    {
        return $this->clientService->getClientById($id, $request);
    }

    public function update(UpdateClientRequest $request, string $id)
    {
        return $this->clientService->updateClient($id, $request->validated());
    }

    public function destroy(string $id)
    {
        return $this->clientService->deleteClient($id);
    }

    public function getByPhoneNumber(Request $request)
    {
        return $this->clientService->getClientByPhoneNumber($request->input('telephone'));
    }

    public function addAccount(Request $request)
    {
        return $this->clientService->addAccountToClient($request->all());
    }
    public function getClientUser(string $id)
    {
        return $this->clientService->getClientById($id);
    }
    // public function addAccount(Request $request)
    // {
    //     return response()->json($this->clientService->addAccountToClient($request->all()));
    // }
}
