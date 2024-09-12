<?php
// app/Http/Controllers/ArticleController.php
namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Services\Interfaces\ArticleServiceInterface;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(Request $request)
    {
        return $this->articleService->getAllArticles($request->query('disponible'));
    }

    public function store(StoreArticleRequest $request)
    {
        return $this->articleService->createArticle($request->validated());
    }

    public function show(int $id)
    {
        return $this->articleService->getArticle($id);
    }

    public function update(UpdateArticleRequest $request, int $id)
    {
        return $this->articleService->updateArticle($id, $request->validated());
    }

    public function destroy(int $id)
    {
        return $this->articleService->deleteArticle($id);
    }

    public function getByLibelle(Request $request)
    {
        return $this->articleService->getArticleByLibelle($request->input('libelle'));
    }

    public function updateMultiple(Request $request)
    {
        return $this->articleService->updateMultipleArticles($request->input('articles'));
    }

    public function trashed()
    {
        return $this->articleService->getTrashedArticles();
    }

    public function restore($id)
    {
        return $this->articleService->restoreArticle($id);
    }

    public function forceDelete($id)
    {
        return $this->articleService->forceDeleteArticle($id);
    }
}
