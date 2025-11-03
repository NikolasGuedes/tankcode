<!-- filepath: /home/nikolas/tankcode/resources/views/emails/student-reset-password.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
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
            background-color: #fef3c7;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #f59e0b;
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
        <h1>🔒 Tank Code</h1>
    </div>
    
    <div class="content">
        <h2>Olá, {{ $student->name }}!</h2>
        
        <p>Recebemos uma solicitação para redefinir a senha da sua conta na plataforma Tank Code.</p>
        
        <p>Para criar uma nova senha, clique no botão abaixo:</p>
        
        <center>
            <a href="{{ $resetUrl }}" class="button">Redefinir Minha Senha</a>
        </center>
        
        <div class="info-box">
            <strong>⚠️ Importante:</strong><br>
            • Este link expira em 1 hora<br>
            • Se você não solicitou esta alteração, ignore este email<br>
            • Sua senha atual permanecerá inalterada até que você crie uma nova
        </div>
        
        <p>Se você não conseguir clicar no botão, copie e cole o seguinte link no seu navegador:</p>
        <p style="word-break: break-all; color: #4F46E5;">{{ $resetUrl }}</p>
        
        <p><strong>📧 Email da conta:</strong> {{ $student->email }}</p>
        
        <p>Se você não solicitou a redefinição de senha, recomendamos que entre em contato com seu professor imediatamente.</p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Tank Code. Todos os direitos reservados.</p>
        <p>Este é um email automático, por favor não responda.</p>
    </div>
</body>
</html>