<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

/**
 * Middleware de autenticação para rotas protegidas
 */
class AuthMiddleware
{
    private $sessionService;

    public function __construct($sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * Verifica se usuário está autenticado e valida sessão
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Verificar se usuário está logado
        if (!isset($_SESSION['user_id'])) {
            $response = new SlimResponse();
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }
        
        // Validar sessão no banco (verificar IP e User-Agent para detectar hijacking)
        $serverParams = $request->getServerParams();
        $sessionId = session_id();
        $ipAddress = $serverParams['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $serverParams['HTTP_USER_AGENT'] ?? 'Unknown';
        
        if (!$this->sessionService->validateSession($sessionId, $ipAddress, $userAgent)) {
            // Sessão inválida ou hijacking detectado
            session_destroy();
            $_SESSION = [];
            $response = new SlimResponse();
            return $response->withHeader('Location', '/admin/login?error=session_invalid')->withStatus(302);
        }
        
        return $handler->handle($request);
    }
}
