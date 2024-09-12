<?php

namespace App\Exceptions;

use Exception;

class ArticleException extends Exception
{
    const NOT_FOUND = 'not_found';
    const CREATE_ERROR = 'create_error';
    const UPDATE_ERROR = 'update_error';
    const DELETE_ERROR = 'delete_error';
    const RETRIEVE_ERROR = 'retrieve_error';
    const INVALID_INPUT = 'invalid_input';

    protected static $messages = [
        self::NOT_FOUND => "Article non trouvé",
        self::CREATE_ERROR => "Erreur lors de la création de l'article",
        self::UPDATE_ERROR => "Erreur lors de la mise à jour de l'article",
        self::DELETE_ERROR => "Erreur lors de la suppression de l'article",
        self::RETRIEVE_ERROR => "Erreur lors de la récupération de l'article",
        self::INVALID_INPUT => "Données d'entrée invalides pour l'article"
    ];

    protected static $codes = [
        self::NOT_FOUND => 404,
        self::CREATE_ERROR => 500,
        self::UPDATE_ERROR => 500,
        self::DELETE_ERROR => 500,
        self::RETRIEVE_ERROR => 500,
        self::INVALID_INPUT => 400
    ];

    public function __construct($errorType, $additionalInfo = '')
    {
        $message = static::$messages[$errorType] ?? "Une erreur est survenue avec l'article";
        if ($additionalInfo) {
            $message .= ": $additionalInfo";
        }
        $code = static::$codes[$errorType] ?? 500;
        parent::__construct($message, $code);
    }
}