<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\StateEnum;
use App\Exceptions\ClientException;

class RestResponseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);

            if (!$response instanceof Response) {
                $response = response()->json($response);
            }

            $statusCode = $response->getStatusCode();
            $originalContent = $response->getContent();

            $status = $statusCode < 400 ? StateEnum::SUCCESS : StateEnum::ECHEC;
            $data = json_decode($originalContent, true);

            $formattedResponse = [
                'data' => $data,
                'status' => $status->value,
                'message' => $this->getDefaultMessageForStatusCode($statusCode),
                'codeStatut' => $statusCode
            ];

            $response->setContent(json_encode($formattedResponse));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } catch (ClientException $e) {
            return response()->json([
                'data' => null,
                'status' => StateEnum::ECHEC->value,
                'message' => $e->getMessage(),
                'codeStatut' => $e->getCode()
            ], $e->getCode());
        }
    }

    private function getDefaultMessageForStatusCode(int $statusCode): string
    {
        return match ($statusCode) {
            200 => 'Opération réussie',
            201 => 'Ressource créée',
            204 => 'Pas de contenu',
            400 => 'Requête incorrecte',
            401 => 'Non autorisé',
            403 => 'Interdit',
            404 => 'Non trouvé',
            422 => 'Entité non traitable',
            500 => 'Erreur interne du serveur',
            default => 'Une erreur est survenue',
        };
    }
}