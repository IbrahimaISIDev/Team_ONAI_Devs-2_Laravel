<?php
// app/Services/Interfaces/ArticleServiceInterface.php
namespace App\Services\Interfaces;

use Illuminate\Support\Collection;
use App\Models\Article;

interface ArticleServiceInterface
{
    public function getAllArticles(?string $disponible): Collection;
    public function createArticle(array $data): Article;
    public function getArticle(int $id): ?Article;
    public function updateArticle(int $id, array $data): ?Article;
    public function deleteArticle(int $id): bool;
    public function getArticleByLibelle(string $libelle): ?Article;
    public function updateMultipleArticles(array $articles): array;
    public function getTrashedArticles(): Collection;
    public function restoreArticle(int $id): ?Article;
    public function forceDeleteArticle(int $id): bool;
}