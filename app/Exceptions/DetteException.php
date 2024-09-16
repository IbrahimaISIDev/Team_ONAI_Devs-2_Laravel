<?php

namespace App\Exceptions;

use Exception;

class DetteException extends Exception
{
    const NOT_FOUND = 'not_found';
    const CREATE_ERROR = 'create_error';
    const UPDATE_ERROR = 'update_error';
    const DELETE_ERROR = 'delete_error';
    const RETRIEVE_ERROR = 'retrieve_error';
    const INVALID_INPUT = 'invalid_input';

    protected static $messages = [
        self::NOT_FOUND => "Dette non trouvée",
        self::CREATE_ERROR => "Erreur lors de la création de la dette",
        self::UPDATE_ERROR => "Erreur lors de la mise à jour de la dette",
        self::DELETE_ERROR => "Erreur lors de la suppression de la dette",
        self::RETRIEVE_ERROR => "Erreur lors de la récupération de la dette",
        self::INVALID_INPUT => "Données d'entrée invalides pour la dette"
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
        $message = static::$messages[$errorType] ?? "Une erreur est survenue avec la dette";
        if ($additionalInfo) {
            $message .= ": $additionalInfo";
        }
        $code = static::$codes[$errorType] ?? 500;
        parent::__construct($message, $code);
    }
}
