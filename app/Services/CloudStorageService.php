<?php

namespace App\Services;

use App\Interfaces\CloudStorageInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CloudStorageService implements CloudStorageInterface
{
    protected $diskName = 'cloud_storage';

    public function store(array $data)
    {
        if (!isset($data['id'])) {
            $data['id'] = (string) Str::uuid();
        }

        $fileName = $data['id'] . '.json';
        Storage::disk($this->diskName)->put($fileName, json_encode($data));

        return $data['id'];
    }

    public function retrieve(array $query = [], $page = 1, $perPage = 2)
    {
        $files = Storage::disk($this->diskName)->files();
        $results = [];

        foreach ($files as $file) {
            $content = json_decode(Storage::disk($this->diskName)->get($file), true);

            if ($this->matchesQuery($content, $query)) {
                $results[] = $content;
            }
        }

        return $results;
    }

    public function delete(array $query)
    {
        $files = Storage::disk($this->diskName)->files();
        $deletedCount = 0;

        foreach ($files as $file) {
            $content = json_decode(Storage::disk($this->diskName)->get($file), true);

            if ($this->matchesQuery($content, $query)) {
                Storage::disk($this->diskName)->delete($file);
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    public function update(array $query, array $data)
    {
        $files = Storage::disk($this->diskName)->files();
        $updatedCount = 0;

        foreach ($files as $file) {
            $content = json_decode(Storage::disk($this->diskName)->get($file), true);

            if ($this->matchesQuery($content, $query)) {
                $updatedContent = array_merge($content, $data);
                Storage::disk($this->diskName)->put($file, json_encode($updatedContent));
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    protected function matchesQuery(array $content, array $query)
    {
        foreach ($query as $key => $value) {
            if (!isset($content[$key]) || $content[$key] != $value) {
                return false;
            }
        }
        return true;
    }

    public function findById($id)
    {
        $fileName = $id . '.json';
        if (Storage::disk($this->diskName)->exists($fileName)) {
            $content = json_decode(Storage::disk($this->diskName)->get($fileName), true);
            return $content;
        }
        return null;
    }
}
