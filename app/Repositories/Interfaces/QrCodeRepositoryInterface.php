<?php

namespace App\Repositories\Interfaces;

interface QrCodeRepositoryInterface
{
    public function generateQrCode(string $data): string;
}