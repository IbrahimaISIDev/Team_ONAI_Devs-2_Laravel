<?php

namespace App\Interfaces;

interface MessageServiceInterface
{
    public function sendMessage(string $to, string $message);
}
