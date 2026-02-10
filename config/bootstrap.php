<?php

declare(strict_types=1);

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Doctrine\DBAL\DriverManager;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/helpers.php';

// Configurar timezone para horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

// ===== HEADERS DE SEGURANÇA HTTP (TEMPORARIAMENTE DESABILITADOS) =====
// NOTA: Reabilitar quando resolver o problema de BOM/encoding do helpers.php
// Previne clickjacking - impede que o site seja carregado em iframe
//header('X-Frame-Options: DENY');

// Previne MIME type sniffing - força o navegador a respeitar o Content-Type declarado
//header('X-Content-Type-Options: nosniff');

// Ativa proteção XSS do navegador
//header('X-XSS-Protection: 1; mode=block');

// Controla informações de referrer enviadas em links externos
//header('Referrer-Policy: strict-origin-when-cross-origin');

// Content Security Policy (CSP) - define fontes confiáveis para recursos
/*header("Content-Security-Policy: " .
    "default-src 'self'; " .
    "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://unpkg.com; " .
    "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; " .
    "font-src 'self' https://fonts.gstatic.com; " .
    "img-src 'self' data: https:; " .
    "connect-src 'self'; " .
    "frame-ancestors 'none'; " .
    "base-uri 'self'; " .
    "form-action 'self'"
);*/

// Configurações de sessão seguras (ANTES de session_start)
if (session_status() === PHP_SESSION_NONE) {
    // Detectar se está em HTTPS
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', $isHttps ? '1' : '0'); // Só HTTPS se disponível
    ini_set('session.cookie_samesite', 'Lax'); // Mudado de None para Lax
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.gc_maxlifetime', '3600');
    
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax' // Mudado de None para Lax
    ]);
    
    session_start();
    
    // Regenerar ID de sessão periodicamente (a cada 30 minutos)
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// Carrega configurações
$settings = require __DIR__ . '/../config/settings.php';

// Cria container DI
$container = new Container();

// Configuração do banco de dados
$container->set('db', function () use ($settings) {
    $connectionParams = [
        'dbname' => $settings['database']['dbname'],
        'user' => $settings['database']['user'],
        'password' => $settings['database']['password'],
        'host' => $settings['database']['host'],
        'driver' => 'pdo_mysql',
        'charset' => $settings['database']['charset']
    ];
    return DriverManager::getConnection($connectionParams);
});

// Configuração da aplicação
$container->set('settings', $settings);

AppFactory::setContainer($container);
$app = AppFactory::create();

// Middleware para parse do corpo da requisição
$app->addBodyParsingMiddleware();

// Middleware de roteamento
$app->addRoutingMiddleware();

// Middleware de erro
$errorMiddleware = $app->addErrorMiddleware(
    $settings['app']['display_errors'], 
    true, 
    true
);

// Logger
//$container->set('logger', function() {
//    return (new \App\Services\LoggerService())->getLogger();
//});

return $app;
