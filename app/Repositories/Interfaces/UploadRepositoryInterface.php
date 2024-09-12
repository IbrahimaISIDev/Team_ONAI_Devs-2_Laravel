<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\UploadedFile;

interface UploadRepositoryInterface
{
    public function uploadPhoto(UploadedFile $file, string $folder = 'photos'): string;
    public function deletePhoto(string $path): bool;
    public function getBase64Photo(string $path): ?string;
}