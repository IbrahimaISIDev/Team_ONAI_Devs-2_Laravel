<?php

namespace App\Services\CloudStorage;

use Illuminate\Http\UploadedFile;

interface CloudStorageInterface
{
    public function upload(UploadedFile $file, string $folder): string;
    public function delete(string $path): bool;
    public function getBase64(string $path): ?string;
}