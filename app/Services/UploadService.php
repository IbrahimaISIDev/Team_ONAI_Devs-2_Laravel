<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use App\Services\CloudStorage\CloudinaryStorage;
use App\Services\CloudStorage\CloudStorageFactory;
use App\Services\CloudStorage\CloudStorageInterface;

class UploadService
{
    private CloudStorageInterface $primaryStorage;
    private CloudStorageInterface $fallbackStorage;

    public function __construct(CloudStorageInterface $primaryStorage, CloudStorageInterface $fallbackStorage)
    {
        $this->primaryStorage = $primaryStorage;
        $this->fallbackStorage = $fallbackStorage;
    }


    public function uploadPhoto(UploadedFile $file, string $folder = 'photos'): string
    {
        try {
            return $this->primaryStorage->upload($file, $folder);
        } catch (Exception $e) {
            // Si l'upload sur le stockage primaire échoue, on utilise le stockage local
            return $this->fallbackStorage->upload($file, $folder);
        }
    }

    public function deletePhoto(string $path): bool
    {
        // Tenter de supprimer d'abord avec le stockage primaire
        if ($this->primaryStorage->delete($path)) {
            return true;
        }
        // Si ça échoue ou si le fichier n'existe pas dans le stockage primaire, essayer avec le fallback
        return $this->fallbackStorage->delete($path);
    }

    public function getBase64Photo(string $path): ?string
    {
        // Essayer d'abord avec le stockage primaire
        $base64 = $this->primaryStorage->getBase64($path);
        if ($base64 !== null) {
            return $base64;
        }
        // Si ça échoue, essayer avec le fallback
        return $this->fallbackStorage->getBase64($path);
    }

    public function getPublicIdFromUrl(string $url): string
    {
        if ($this->primaryStorage instanceof CloudinaryStorage) {
            return $this->primaryStorage->getPublicIdFromUrl($url);
        }
        // Fallback si ce n'est pas Cloudinary
        $parts = explode('/', parse_url($url, PHP_URL_PATH));
        return $parts[count($parts) - 2] . '/' . pathinfo(end($parts), PATHINFO_FILENAME);
    }
}