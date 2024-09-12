<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badge de fidélité</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        body {
            background-color: #f5f5f5;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .badge-card {
            width: 340px;
            height: auto;
            min-height: 520px;
            background: linear-gradient(145deg, #ffffff, #e6f0fa, #b3d4fc, #80b3ff);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 25px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 2px solid #0069d9;
            overflow: visible;
            position: relative;
        }

        .title-bar {
            background-color: #007BFF;
            color: #fff;
            padding: 15px 0;
            font-size: 15px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1.2px;
            margin: -27px -27px 25px -27px;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #333;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            font-weight: 500;
        }

        .client-photo img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #0069d9;
            box-shadow: 0 4px 12px rgba(0, 105, 217, 0.3);
        }

        .client-info {
            font-size: 15px;
            text-align: left;
            background-color: rgba(255, 255, 255, 0.85);
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }

        .client-info p {
            margin: 10px 0;
            color: #4f4f4f;
        }

        .client-info strong {
            font-weight: 600;
            color: #202020;
        }

        .qr-code {
            width: 120px;
            height: 120px;
            background-color: #fff;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            margin: 0 auto;
        }

        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .footer {
            font-size: 13px;
            color: #2c3e50;
            margin-top: 10px;
            font-weight: 400;
            text-align: center;
            padding-top: 10px;
            border-top: 1px solid #e6e6e6;
        }

        .badge-card:before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 160px;
            height: 160px;
            background: radial-gradient(circle, rgba(0, 123, 255, 0.2), rgba(0, 123, 255, 0));
            border-radius: 50%;
        }

        .badge-card:after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: -40px;
            width: 160px;
            height: 160px;
            background: radial-gradient(circle, rgba(0, 123, 255, 0.2), rgba(0, 123, 255, 0));
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <div class="badge-card">
        <div class="title-bar">Carte De Fidélité</div>
        <h1>Team ONAI Devs</h1>

        <div class="client-photo">
            @if ($photoData)
                <img src="{{ $photoData }}" alt="Photo du client">
            @else
                <img src="{{ asset('path/to/default/image.png') }}" alt="Photo par défaut">
            @endif
        </div>

        <div class="client-info">
            <p><strong>Nom :</strong> {{ $client->surname }}</p>
            <p><strong>Prénom :</strong> {{ $client->surname }}</p>
            <p><strong>Adresse :</strong> {{ $client->adresse }}</p>
            <p><strong>Email :</strong> {{ $client->email }}</p>
        </div>

        <div class="qr-code">
            <img src="{{ $qrCodeSvg }}" alt="QR Code">
        </div>

        <div class="footer">Merci de votre fidélité !</div>
    </div>
</body>

</html>