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
