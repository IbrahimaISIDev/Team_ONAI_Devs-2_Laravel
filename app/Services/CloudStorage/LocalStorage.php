<?php
namespace App\Services\CloudStorage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalStorage implements CloudStorageInterface
{
    public function upload(UploadedFile $file, string $folder): string
    {
        $filename = uniqid() . '_' . $file->getClientOriginalName();
        return $file->storeAs($folder, $filename, 'public');
    }

    public function delete(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    public function getBase64(string $path): ?string
    {
        if (Storage::disk('public')->exists($path)) {
            $data = Storage::disk('public')->get($path);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }
}