<?php

namespace App\Repositories;

use App\Repositories\Interfaces\QrCodeRepositoryInterface;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeRepository implements QrCodeRepositoryInterface
{
    public function generateQrCode(string $data): string
    {
        return QrCode::size(300)->generate($data);
    }
}