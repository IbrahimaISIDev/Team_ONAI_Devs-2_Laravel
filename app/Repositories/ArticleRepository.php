<?php

// app/Repositories/ArticleRepository.php
namespace App\Repositories;

use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Models\Article;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateArticleRequest;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function all(): Collection
    {
        return Article::all();
    }

    public function create(array $data): Article
    {
        return Article::create($data);
    }

    public function find(int $id): ?Article
    {
        return Article::find($id);
    }

    public function update(int $id, array $data): ?Article
    {
        $article = $this->find($id);
        if (!$article) {
            return null;
        }
        $article->update($data);
        return $article;
    }

    public function delete(int $id): bool
    {
        $article = $this->find($id);
        if (!$article) {
            return false;
        }
        return $article->delete();
    }

    public function findByLibelle(string $libelle): ?Article
    {
        return Article::findByLibelle($libelle);
    }

    public function findByEtat(string $etat): Collection
    {
        return Article::findByEtat($etat);
    }

    public function trashed(): Collection
    {
        return Article::onlyTrashed()->get();
    }

    public function restore(int $id): ?Article
    {
        $article = Article::withTrashed()->find($id);
        if (!$article) {
            return null;
        }
        $article->restore();
        return $article;
    }

    public function forceDelete(int $id): bool
    {
        $article = Article::withTrashed()->find($id);
        if (!$article) {
            return false;
        }
        return $article->forceDelete();
    }

    public function updateMultiple(array $articles): array
    {
        $updatedArticles = [];
        $failedUpdates = [];

        DB::beginTransaction();

        try {
            foreach ($articles as $articleData) {
                $article = $this->find($articleData['id']);
                if (!$article) {
                    $failedUpdates[] = [
                        'article_data' => $articleData,
                        'error_message' => "Article avec l'ID {$articleData['id']} introuvable"
                    ];
                    continue;
                }

                // Utilisez UpdateArticleRequest pour valider les données
                $updateRequest = new UpdateArticleRequest();
                $updateRequest->replace($articleData);

                try {
                    $validatedData = $updateRequest->validate($updateRequest->rules());

                    if (isset($validatedData['stock'])) {
                        $newStock = $article->stock + $validatedData['stock'];
                        if ($newStock < 0) {
                            throw new \Exception("Le stock ne peut pas être négatif");
                        }
                        $validatedData['stock'] = $newStock;
                    }

                    // Mise à jour de l'article valide
                    $article->update($validatedData);
                    $updatedArticles[] = $article;
                } catch (ValidationException $e) {
                    $failedUpdates[] = [
                        'article_data' => $articleData,
                        'error_message' => 'Validation échouée: ' . $e->getMessage()
                    ];
                } catch (\Exception $e) {
                    $failedUpdates[] = [
                        'article_data' => $articleData,
                        'error_message' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return [
                'updated_articles' => $updatedArticles,
                'failed_updates' => $failedUpdates,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'updated_articles' => $updatedArticles,
                'failed_updates' => array_merge($failedUpdates, [
                    [
                        'article_data' => null,
                        'error_message' => 'Erreur lors de la mise à jour multiple : ' . $e->getMessage()
                    ]
                ]),
            ];
        }
    }

    public function getByStock(?string $disponible): Collection
    {
        $query = Article::query();

        if ($disponible !== null) {
            $query->where('stock', $disponible === 'oui' ? '>' : '=', 0);
        }

        return $query->get();
    }
}
