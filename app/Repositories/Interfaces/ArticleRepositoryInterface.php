<?php

// app/Repositories/Interfaces/ArticleRepositoryInterface.php
namespace App\Repositories\Interfaces;

use App\Models\Article;
use Illuminate\Support\Collection;

interface ArticleRepositoryInterface
{
    public function all(): Collection;
    public function create(array $data): Article;
    public function find(int $id): ?Article;
    public function update(int $id, array $data): ?Article;
    public function delete(int $id): bool;
    public function findByLibelle(string $libelle): ?Article;
    public function findByEtat(string $etat): Collection;
    public function trashed(): Collection;
    public function restore(int $id): ?Article;
    public function forceDelete(int $id): bool;
    public function updateMultiple(array $articles): array;
    public function getByStock(?string $disponible): Collection;

}
