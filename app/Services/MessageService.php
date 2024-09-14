<?php

namespace App\Services;

use App\Interfaces\MessageServiceInterface;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use App\Models\Client;
use Illuminate\Support\Facades\Log;

class MessageService implements MessageServiceInterface
{
    protected $messageRepository;
    protected $factureService;

    public function __construct(MessageRepositoryInterface $messageRepository, FactureService $factureService)
    {
        $this->messageRepository = $messageRepository;
        $this->factureService = $factureService;
    }

    public function sendMessage(string $to, string $message)
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
            //dd($client); // Vérifiez que c'est un objet Client et non une chaîne
            $totalDettes = $client->dettes->sum('montant');

            if ($totalDettes > 0) {
                // Assurez-vous que $client est bien un objet Client
                //$client = Client::find($clientId); // Assurez-vous que $clientId est valide
                //$pdfFile = $this->factureService->generateRecapitulatif($client);

                //$pdfFile = $this->factureService->generateRecapitulatif($client->surname);

                $message = "Objet : Récapitulatif de vos Dettes - Important\n\n"
                    . "Cher(e) {$client->surname},\n\n"
                    . "Nous vous informons que vous avez un total de **{$totalDettes} FCFA** en dettes. "
                    . "Veuillez consulter le récapitulatif détaillé de vos dettes dans le fichier PDF ci-joint.\n\n"
                    . "Voici un résumé des dettes en cours :\n";

                foreach ($client->dettes as $dette) {
                    $message .= "- **{$dette->montant} FCFA** due depuis le " . date('d/m/Y', strtotime($dette->created_at)) . "\n";
                }

                $message .= "\nLa facture détaillée a été jointe à ce message pour votre référence.\n\n"
                    . "Si vous avez des questions concernant ce récapitulatif ou si vous avez besoin d'assistance supplémentaire, "
                    . "n'hésitez pas à nous contacter à l'adresse suivante : <strong>support@gestion-shop.com</strong>.\n\n"
                    . "Merci pour votre collaboration et votre prompt règlement.\n\n"
                    . "Cordialement,\n"
                    . "L’équipe de Gestion-Shop";

                $formattedNumber = $this->formatPhoneNumber($client->telephone);

                Log::info("Sending message to {$formattedNumber}: {$message}");

                try {
                    $this->sendMessage($formattedNumber, $message);
                } catch (\Exception $e) {
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

    private function isValidPhoneNumber($number)
    {
        // Vérifier que le numéro commence par +221 et contient exactement 13 caractères
        return preg_match('/^\+221\d{9}$/', $number);
    }
}
