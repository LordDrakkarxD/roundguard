<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Code - {{ $checkpoint->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: white;
            padding: 40px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 8px;
            color: #111;
        }
        p {
            color: #555;
            margin-bottom: 32px;
            font-size: 14px;
        }
        .qr {
            border: 1px solid #e5e7eb;
            padding: 16px;
            border-radius: 8px;
        }
        .code {
            margin-top: 20px;
            font-family: monospace;
            font-size: 14px;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <h1>{{ $checkpoint->name }}</h1>
    <p>Ponto de Ronda - RoundGuard</p>

    <div class="qr">
        {!! $qr !!}
    </div>

    <div class="code">{{ $checkpoint->code }}</div>

    <button class="no-print" onclick="window.print()" style="margin-top: 40px; padding: 10px 24px; background: #0284c7; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px;">
        Imprimir
    </button>

    <script>
        // Opcional: abrir a caixa de impressão automaticamente
        // window.onload = () => window.print();
    </script>
</body>
</html>