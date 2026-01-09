<?php

declare(strict_types=1);

namespace App\Services;

use Exception;

class EmailService
{
    private string $apiKey;
    private string $fromEmail;
    private string $fromName;
    private string $apiUrl;

    public function __construct()
    {
        $this->apiKey = $_ENV['TOKEN_MAILDIVER'] ?? '';
        $this->fromEmail = $_ENV['MAILDIVER'] ?? 'no-reply@linksbio.me';
        $this->fromName = 'Menu Digital - linksbio.me';
        $this->apiUrl = 'https://api.maildiver.com/v1/emails';
    }

    /**
     * Enviar e-mail via MailDiver API
     */
    public function send(string $to, string $subject, string $html): bool
    {
        $payload = [
            'to' => $to,
            'from' => $this->fromEmail,
            'from_name' => $this->fromName,
            'subject' => $subject,
            'html' => $html
        ];

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 400 || $curlError) {
            error_log('Erro ao enviar e-mail via MailDiver. Status: ' . $httpCode . '. Resposta: ' . $response);
            throw new Exception('Falha ao enviar e-mail. Código: ' . $httpCode);
        }

        return true;
    }

    /**
     * Enviar e-mail de verificação de conta
     */
    public function sendVerificationEmail(string $to, string $name, string $token): bool
    {
        $verificationUrl = ($_ENV['APP_URL'] ?? 'http://menu.linksbio.me') . '/verify-email?token=' . $token;
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4F46E5; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
                .button { display: inline-block; background: #4F46E5; color: white; padding: 15px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Bem-vindo ao menu.linksbio.me!</h1>
                </div>
                <div class="content">
                    <p>Olá, <strong>' . htmlspecialchars($name) . '</strong>!</p>
                    
                    <p>Obrigado por criar sua conta no menu.linksbio.me. Para começar a usar sua loja online, precisamos verificar seu e-mail.</p>
                    
                    <p style="text-align: center;">
                        <a href="' . $verificationUrl . '" class="button">Verificar E-mail</a>
                    </p>
                    
                    <p>Ou copie e cole este link no navegador:</p>
                    <p style="background: white; padding: 15px; border-radius: 4px; word-break: break-all;">
                        ' . $verificationUrl . '
                    </p>
                    
                    <p><strong>Este link expira em 24 horas.</strong></p>
                    
                    <p>Se você não criou esta conta, pode ignorar este e-mail.</p>
                </div>
                <div class="footer">
                    <p>menu.linksbio.me<br>
                    Este é um e-mail automático, por favor não responda.</p>
                </div>
            </div>
        </body>
        </html>
        ';

        return $this->send($to, 'Verifique seu e-mail - Menu Digital', $html);
    }
}
