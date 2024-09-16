<?php

namespace App\Exceptions;

use Exception;

class PaiementException extends Exception
{
    public static function detteSoldee($detteId)
    {
        return new self("Impossible d'ajouter un paiement à la dette $detteId déjà soldée.");
    }

    public static function montantSuperieur($detteId, $montantDette, $montantPaiement)
    {
        return new self("Le montant du paiement ($montantPaiement) ne peut pas être supérieur au montant restant de la dette $detteId ($montantDette).");
    }
}