<?php

namespace App\Services;

use App\Models\Client;
use Barryvdh\DomPDF\PDF;

class FactureService
{
    protected $pdf;

    public function __construct(PDF $pdf)
    {
        $this->pdf = $pdf;
    }

    public function generateRecapitulatif(Client $client)
    {
        // Vérifiez le type de $client
        if (!$client instanceof Client) {
            throw new \InvalidArgumentException('Expected instance of App\Models\Client');
        }

        // Récupérer les dettes du client
        $dettes = $client->dettes;
        $totalDettes = $dettes->sum('montant');

        // Création du contenu pour la facture
        $htmlContent = "
        <h1>Récapitulatif de vos Dettes</h1>
        <p>Client : <strong>{$client->nom}</strong></p>
        <p>Total des dettes : <strong>{$totalDettes} FCFA</strong></p>
        <table border='1' style='width: 100%; border-collapse: collapse;'>
            <thead>
                <tr>
                    <th style='border: 1px solid black; padding: 8px;'>Date</th>
                    <th style='border: 1px solid black; padding: 8px;'>Description</th>
                    <th style='border: 1px solid black; padding: 8px;'>Montant</th>
                </tr>
            </thead>
            <tbody>";

        foreach ($dettes as $dette) {
            $htmlContent .= "
                <tr>
                    <td style='border: 1px solid black; padding: 8px;'>" . date('d/m/Y', strtotime($dette->created_at)) . "</td>
                    <td style='border: 1px solid black; padding: 8px;'>Détail de la dette</td>
                    <td style='border: 1px solid black; padding: 8px;'>{$dette->montant} FCFA</td>
                </tr>";
        }

        $htmlContent .= "
            </tbody>
        </table>
        <p>Date de génération : " . date('d/m/Y') . "</p>
        <p>Pour toute question concernant ce récapitulatif ou pour obtenir des informations supplémentaires, veuillez nous contacter à l'adresse <strong>support@gestion-shop.com</strong>.</p>
        <p>Nous vous remercions pour votre attention.</p>";

        // Génération du PDF
        $pdf = $this->pdf->loadHTML($htmlContent);

        return $pdf->download('recapitulatif_dettes_'.$client->id.'.pdf');
    }
}
