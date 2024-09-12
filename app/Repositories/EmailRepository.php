<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Mail\Mailer;
use App\Mail\ClientCreationMail;

class EmailRepository
{
    protected $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendClientCreationEmail(string $toEmail, Client $client, string $pdfPath)
    {
        $this->mailer->to($toEmail)->send(new ClientCreationMail($client, $pdfPath));
    }
}