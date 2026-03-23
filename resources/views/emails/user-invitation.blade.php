<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ative sua conta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #111827;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5ff;
        }
        .header {
            background-color: #110D2E;
            color: #ffffff;
            padding: 24px;
            text-align: center;
            border-radius: 12px 12px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 32px;
            border-radius: 0 0 12px 12px;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            background-color: #1D15F0;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            margin: 24px 0;
            font-weight: 600;
        }
        .info-box {
            background-color: #f3f1ff;
            padding: 16px;
            border-radius: 10px;
            margin: 24px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>TankCode</h1>
    </div>

    <div class="content">
        <h2>Ola, {{ $user->name }}!</h2>

        <p>Sua conta foi criada na plataforma TankCode.</p>
        <p>Para ativar o acesso e definir sua senha de primeiro login, clique no botao abaixo:</p>

        <p style="text-align: center;">
            <a href="{{ $activationUrl }}" class="button">Ativar conta e criar senha</a>
        </p>

        <div class="info-box">
            <strong>Perfil:</strong> {{ $user->role?->label ?? 'Usuario' }}<br>
            <strong>Email:</strong> {{ $user->email }}<br>
            <strong>Escola:</strong> {{ $user->school?->name ?? 'Sistema' }}
        </div>

        <p>Este link expira em 24 horas.</p>
        <p>Se o botao nao abrir, copie e cole o link abaixo no navegador:</p>
        <p style="word-break: break-all; color: #1D15F0;">{{ $activationUrl }}</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} TankCode. Todos os direitos reservados.</p>
    </div>
</body>
</html>
