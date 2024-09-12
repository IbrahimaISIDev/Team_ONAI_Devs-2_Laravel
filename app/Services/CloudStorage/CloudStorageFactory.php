<?php
namespace App\Services\CloudStorage;

use InvalidArgumentException;

class CloudStorageFactory
{
    public static function create(string $driver): CloudStorageInterface
    {
        switch ($driver) {
            case 'cloudinary':
                return new CloudinaryStorage();
            case 'local':
                return new LocalStorage();
            // Ajoutez ici d'autres cas pour de nouveaux services de stockage
            default:
                throw new InvalidArgumentException("Unsupported storage driver: {$driver}");
        }
    }
}

