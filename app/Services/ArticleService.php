<?php

namespace App\Services;

use App\Services\Interfaces\ArticleServiceInterface;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Exceptions\ArticleException;
use Illuminate\Support\Collection;
use App\Models\Article;

class ArticleService implements ArticleServiceInterface
{
    protected $repository;

    public function __construct(ArticleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllArticles(?string $disponible): Collection
    {
        return $this->handleRepositoryOperation(function () use ($disponible) {
            return $this->repository->getByStock($disponible);
        });
    }

    public function createArticle(array $data): Article
    {
        return $this->handleRepositoryOperation(function () use ($data) {
            return $this->repository->create($data);
        });
    }

    public function getArticle(int $id): ?Article
    {
        return $this->handleRepositoryOperation(function () use ($id) {
            $article = $this->repository->find($id);
            if (!$article) {
                throw new ArticleException(ArticleException::NOT_FOUND);
            }
            return $article;
        });
    }

    public function updateArticle(int $id, array $data): ?Article
    {
        return $this->handleRepositoryOperation(function () use ($id, $data) {
            $article = $this->repository->update($id, $data);
            if (!$article) {
                throw new ArticleException(ArticleException::NOT_FOUND);
            }
            return $article;
        });
    }

    public function deleteArticle(int $id): bool
    {
        return $this->handleRepositoryOperation(function () use ($id) {
            $deleted = $this->repository->delete($id);
            if (!$deleted) {
                throw new ArticleException(ArticleException::NOT_FOUND);
            }
            return true;
        });
    }

    public function getArticleByLibelle(string $libelle): ?Article
    {
        if (empty($libelle)) {
            throw new ArticleException(ArticleException::INVALID_INPUT, "Le libellé est requis");
        }
        return $this->handleRepositoryOperation(function () use ($libelle) {
            $article = $this->repository->findByLibelle($libelle);
            if (!$article) {
                throw new ArticleException(ArticleException::NOT_FOUND);
            }
            return $article;
        });
    }

    public function updateMultipleArticles(array $articles): array
    {
        return $this->handleRepositoryOperation(function () use ($articles) {
            return $this->repository->updateMultiple($articles);
        });
    }

    public function getTrashedArticles(): Collection
    {
        return $this->handleRepositoryOperation(function () {
            return $this->repository->trashed();
        });
    }

    public function restoreArticle(int $id): ?Article
    {
        return $this->handleRepositoryOperation(function () use ($id) {
            $article = $this->repository->restore($id);
            if (!$article) {
                throw new ArticleException(ArticleException::NOT_FOUND);
            }
            return $article;
        });
    }

    public function forceDeleteArticle(int $id): bool
    {
        return $this->handleRepositoryOperation(function () use ($id) {
            $deleted = $this->repository->forceDelete($id);
            if (!$deleted) {
                throw new ArticleException(ArticleException::NOT_FOUND);
            }
            return true;
        });
    }

    private function handleRepositoryOperation(callable $operation)
    {
        try {
            return $operation();
        } catch (\Exception $e) {
            throw new ArticleException(ArticleException::RETRIEVE_ERROR, $e->getMessage());
        }
    }
}
