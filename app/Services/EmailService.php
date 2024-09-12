<?php

namespace App\Services;

use App\Models\Client;
use App\Repositories\EmailRepository;

class EmailService
{
    protected $emailRepository;

    public function __construct(EmailRepository $emailRepository)
    {
        $this->emailRepository = $emailRepository;
    }

    public function sendClientCreationEmail(string $toEmail, Client $client, string $pdfPath)
    {
        $this->emailRepository->sendClientCreationEmail($toEmail, $client, $pdfPath);
    }
}