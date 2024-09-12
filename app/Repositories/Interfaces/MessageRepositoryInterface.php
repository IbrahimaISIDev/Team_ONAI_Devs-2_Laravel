<?php

namespace App\Repositories\Interfaces;

interface MessageRepositoryInterface
{
    public function sendMessage($to, $message);
}
