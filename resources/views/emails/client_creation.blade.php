<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue chez nous</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .cta-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Bienvenue chez nous, {{ $client->surname }} !</h1>
    
    <p>Nous sommes ravis de vous compter parmi nos clients. Votre compte a été créé avec succès.</p>

    <p>Vous trouverez ci-joint un document PDF contenant toutes les informations de votre compte. Veuillez le conserver précieusement.</p>

    @if($client->user)
    <p>Voici les détails de votre compte utilisateur :</p>
    <ul>
        <li>Nom : {{ $client->user->nom }}</li>
        <li>Prénom : {{ $client->user->prenom }}</li>
    </ul>
    @endif

    <p>Si vous avez des questions ou besoin d'assistance, n'hésitez pas à nous contacter.</p>

    <a href="{{ config('app.url') }}/login" class="cta-button">Accéder à votre espace client</a>

    <p>Merci de votre confiance et à bientôt !</p>

    <p>L'équipe {{ config('app.name') }}</p>
</body>
</html>
