<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UploadRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadRepository implements UploadRepositoryInterface
{
    public function uploadPhoto(UploadedFile $file, string $folder = 'photos'): string
    {
        $filename = uniqid() . '_' . $file->getClientOriginalName();
        return $file->storeAs($folder, $filename, 'public');
    }

    public function deletePhoto(string $path): bool
    {
        return Storage::disk('public')->exists($path) && Storage::disk('public')->delete($path);
    }

    public function getBase64Photo(string $path): ?string
    {
        if (Storage::disk('public')->exists($path)) {
            $data = Storage::disk('public')->get($path);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }
}