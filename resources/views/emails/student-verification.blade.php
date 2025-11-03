<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .info-box {
            background-color: #e0e7ff;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 Tank Code</h1>
    </div>
    
    <div class="content">
        <h2>Olá, {{ $student->name }}!</h2>
        
        <p>Bem-vindo à plataforma Tank Code! Sua conta foi criada com sucesso.</p>
        
        <p>Para ativar sua conta e criar sua senha de acesso, clique no botão abaixo:</p>
        
        <center>
            <a href="{{ $verificationUrl }}" class="button">Verificar Email e Criar Senha</a>
        </center>
        
        <div class="info-box">
            <strong>📋 Informações da sua conta:</strong><br>
            <strong>Código:</strong> {{ $student->cod }}<br>
            <strong>Email:</strong> {{ $student->email }}
        </div>
        
        <p><strong>⚠️ Importante:</strong> Este link de verificação expira em 24 horas. Após verificar seu email, você poderá criar sua senha personalizada para acessar a plataforma.</p>
        
        <p>Se você não conseguir clicar no botão, copie e cole o seguinte link no seu navegador:</p>
        <p style="word-break: break-all; color: #4F46E5;">{{ $verificationUrl }}</p>
        
        <p>Se você não criou esta conta, por favor ignore este email.</p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Tank Code. Todos os direitos reservados.</p>
    </div>
</body>
</html>
