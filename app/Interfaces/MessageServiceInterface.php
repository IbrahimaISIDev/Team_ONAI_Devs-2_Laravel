<?php

namespace App\Interfaces;

interface MessageServiceInterface
{
    public function sendMessage(int $clientId, string $message);
}