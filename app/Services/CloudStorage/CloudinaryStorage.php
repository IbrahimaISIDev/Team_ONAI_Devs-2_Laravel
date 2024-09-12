<?php
namespace App\Services\CloudStorage;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Exception;

class CloudinaryStorage implements CloudStorageInterface
{
    public function upload(UploadedFile $file, string $folder): string
    {
        try {
            $result = Cloudinary::upload($file->getRealPath(), ['folder' => $folder]);
            return $result->getSecurePath();
        } catch (Exception $e) {
            // En cas d'échec, on laisse l'exception se propager pour que le fallback puisse être géré
            throw new Exception("Cloudinary upload failed: " . $e->getMessage());
        }
    }

    public function delete(string $path): bool
    {
        $publicId = $this->getPublicIdFromUrl($path);
        try {
            Cloudinary::destroy($publicId);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getBase64(string $path): ?string
    {
        return $path; // Cloudinary URLs can be used directly
    }

    public function getPublicIdFromUrl(string $url): string
    {
        $parts = explode('/', parse_url($url, PHP_URL_PATH));
        return $parts[count($parts) - 2] . '/' . pathinfo(end($parts), PATHINFO_FILENAME);
    }
}