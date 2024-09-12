<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interfaces\MessageRepositoryInterface;

class MessageService
{
    protected $messageRepository;

    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function sendMessage($to, $message)
    {
        // Validate phone number format
        if (!$this->isValidPhoneNumber($to)) {
            throw new \InvalidArgumentException("Invalid phone number: {$to}");
        }

        return $this->messageRepository->sendMessage($to, $message);
    }

    public function envoyerRecapitulatifHebdomadaire()
    {
        $clients = Client::with('dettes')->get();

        foreach ($clients as $client) {
            $totalDettes = $client->dettes->sum('montant');

            if ($totalDettes > 0) {
                $message = "Récapitulatif hebdomadaire : Votre total de dettes est de {$totalDettes} FCFA.";
                $formattedNumber = $this->formatPhoneNumber($client->telephone);

                Log::info("Sending message to {$formattedNumber}: {$message}");

                try {
                    $this->sendMessage($formattedNumber, $message);
                } catch (\Exception $e) {
                    // Vérifiez si l'erreur concerne un numéro non vérifié
                    if (strpos($e->getMessage(), 'unverified') !== false) {
                        Log::error("Failed to send message to {$formattedNumber}: Number not verified or invalid. Please check the number verification status.");
                    } else {
                        Log::error("Failed to send message to {$formattedNumber}: " . $e->getMessage());
                    }
                }
            }
        }
    }


    private function formatPhoneNumber($number)
    {
        // Supprimer tous les caractères non numériques sauf le signe plus
        $cleanNumber = preg_replace('/[^0-9]/', '', $number);

        // Ajouter le code pays +221 si nécessaire
        if (strlen($cleanNumber) === 9 && !strpos($cleanNumber, '+')) {
            $formatted = '+221' . $cleanNumber;
        } elseif (strpos($cleanNumber, '+') === 0 && strlen($cleanNumber) === 13) {
            $formatted = $cleanNumber;
        } else {
            // Format incorrect
            Log::error("Invalid phone number format: {$number}");
            return '';
        }

        Log::info("Formatted phone number: {$formatted}");

        // Vérifiez si le numéro formaté est valide
        if ($this->isValidPhoneNumber($formatted)) {
            return $formatted;
        } else {
            Log::error("Invalid phone number after formatting: {$formatted}");
            return ''; // Retourner une chaîne vide si le numéro est invalide
        }
    }



    // private function isValidPhoneNumber($number)
    // {
    //     // Strip non-numeric characters except the plus sign
    //     $cleanNumber = preg_replace('/[^0-9+]/', '', $number);

    //     // Check if the number starts with a plus sign and has the correct length
    //     return preg_match('/^\+\d{10,15}$/', $cleanNumber);
    // }

    private function isValidPhoneNumber($number)
    {
        // Vérifier que le numéro commence par +221 et contient exactement 13 caractères
        return preg_match('/^\+221\d{9}$/', $number);
    }
}
