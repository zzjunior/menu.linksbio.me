<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use App\Models\Store;
use App\Services\TemplateService;
use App\Services\SessionService;
use App\Services\EmailService;

class AuthController
{
    private User $userModel;
    private Store $storeModel;
    private TemplateService $templateService;
    private SessionService $sessionService;
    private EmailService $emailService;

    public function __construct(
        User $userModel, 
        Store $storeModel, 
        TemplateService $templateService,
        SessionService $sessionService,
        EmailService $emailService
    ) {
        $this->userModel = $userModel;
        $this->storeModel = $storeModel;
        $this->templateService = $templateService;
        $this->sessionService = $sessionService;
        $this->emailService = $emailService;
    }

    /**
     * Exibe o formulário de login
     */
    public function loginForm(Request $request, Response $response): Response
    {
        // Se já estiver logado, redireciona para o dashboard
        if (isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/admin')->withStatus(302);
        }
        
        $data = [
            'title' => 'Login - Admin',
            'error' => $_SESSION['error'] ?? null
        ];
        
        unset($_SESSION['error']);
        
        return $this->templateService->renderResponse($response, 'auth.login', $data);
    }

    /**
     * Processa o login
     */
    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $csrfToken = $data['csrf_token'] ?? '';
        
        // Obter IP e User-Agent
        $serverParams = $request->getServerParams();
        $ipAddress = $serverParams['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $serverParams['HTTP_USER_AGENT'] ?? 'Unknown';

        // Validar CSRF
        if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Requisição inválida. Tente novamente.';
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        // Validar campos obrigatórios
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email e senha são obrigatórios';
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        // Rate limiting - 5 tentativas a cada 5 minutos
        if (!$this->sessionService->checkRateLimit($ipAddress, 5, 300)) {
            $_SESSION['error'] = 'Muitas tentativas de login. Aguarde 5 minutos e tente novamente.';
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        // Buscar usuário
        $user = $this->userModel->findByEmail($email);
        
        // Mensagem genérica para evitar timing attack
        if (!$user || !$this->userModel->verifyPassword($password, $user['password']) || !$this->userModel->isActive($user['id'])) {
            $_SESSION['error'] = 'Credenciais inválidas ou conta inativa';
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        // Login bem-sucedido - limpar rate limit
        $this->sessionService->clearRateLimit($ipAddress);
        
        // Criar sessão no banco (já regenera o ID internamente)
        $this->sessionService->createSession($user['id'], $ipAddress, $userAgent);
        
        // Armazenar dados do usuário na sessão PHP
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['store_id'] = $user['store_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['store_name'] = $user['store_name'];
        $_SESSION['store_slug'] = $user['store_slug'];
        $_SESSION['ip_address'] = $ipAddress;
        $_SESSION['user_agent'] = $userAgent;

        return $response->withHeader('Location', '/admin')->withStatus(302);
    }

    /**
     * Processa o logout
     */
    public function logout(Request $request, Response $response): Response
    {
        // Destruir sessão do banco
        if (isset($_SESSION['user_id'])) {
            $sessionId = session_id();
            $this->sessionService->destroySession($sessionId);
        }
        
        // Limpar cookies
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
        
        // Destruir sessão PHP
        session_destroy();
        
        return $response->withHeader('Location', '/admin/login')->withStatus(302);
    }

    /**
     * Exibe o formulário de registro
     */
    public function registerForm(Request $request, Response $response): Response
    {
        // Se já estiver logado, redireciona para o dashboard
        if (isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/admin')->withStatus(302);
        }
        
        $data = [
            'title' => 'Cadastro - Nova Loja',
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ];
        
        unset($_SESSION['error'], $_SESSION['success']);
        
        return $this->templateService->renderResponse($response, 'auth/register', $data);
    }

    /**
     * Processa o registro
     */
    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        $required = ['name', 'email', 'password', 'confirm_password', 'store_name', 'whatsapp'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $_SESSION['error'] = 'Todos os campos são obrigatórios';
                return $response->withHeader('Location', '/admin/register')->withStatus(302);
            }
        }

        if ($data['password'] !== $data['confirm_password']) {
            $_SESSION['error'] = 'As senhas não coincidem';
            return $response->withHeader('Location', '/admin/register')->withStatus(302);
        }

        if (strlen($data['password']) < 6) {
            $_SESSION['error'] = 'A senha deve ter pelo menos 6 caracteres';
            return $response->withHeader('Location', '/admin/register')->withStatus(302);
        }

        // Verificar se email já existe
        if ($this->userModel->findByEmail($data['email'])) {
            $_SESSION['error'] = 'Este email já está cadastrado';
            return $response->withHeader('Location', '/admin/register')->withStatus(302);
        }

        // Gerar token de verificação de email
        $verificationToken = bin2hex(random_bytes(32));
        
        // Criar usuário
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $this->userModel->hashPassword($data['password']),
            'store_name' => $data['store_name'],
            'store_slug' => $this->userModel->generateSlug($data['store_name']),
            'whatsapp' => preg_replace('/[^0-9]/', '', $data['whatsapp']),
            'address' => $data['address'] ?? '',
            'email_verification_token' => $verificationToken,
            'is_active' => 1
        ];

        try {
            // Criar usuário
            $userId = $this->userModel->create($userData);
            
            // Criar store para o usuário com os mesmos dados
            $storeId = $this->storeModel->createFromUser($userData);
            
            // Associar usuário à store criada
            $this->userModel->updateById($userId, ['store_id' => $storeId]);
            
            // Enviar email de verificação
            try {
                $this->emailService->sendVerificationEmail(
                    $data['email'],
                    $data['name'],
                    $verificationToken
                );
                $_SESSION['success'] = 'Conta criada! Verifique seu e-mail para ativar a conta.';
            } catch (\Exception $e) {
                error_log('Erro ao enviar email de verificação: ' . $e->getMessage());
                $_SESSION['success'] = 'Conta criada! Você já pode fazer login.';
            }
            
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        } catch (\Exception $e) {
            error_log('Erro ao criar usuário/loja: ' . $e->getMessage());
            $_SESSION['error'] = 'Erro ao criar conta. Tente novamente.';
            return $response->withHeader('Location', '/admin/register')->withStatus(302);
        }
    }
    
    /**
     * Verificar e-mail com token
     */
    public function verifyEmail(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $token = $queryParams['token'] ?? '';
        
        if (empty($token)) {
            $_SESSION['error'] = 'Token de verificação inválido';
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }
        
        // Buscar usuário pelo token
        $sql = "SELECT * FROM users WHERE email_verification_token = ? LIMIT 1";
        $stmt = $this->userModel->getConnection()->prepare($sql);
        $result = $stmt->executeQuery([$token]);
        $user = $result->fetchAssociative();
        
        if (!$user) {
            $_SESSION['error'] = 'Token de verificação inválido ou expirado';
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }
        
        // Verificar e-mail
        $this->userModel->updateById($user['id'], [
            'email_verified_at' => date('Y-m-d H:i:s'),
            'email_verification_token' => null
        ]);
        
        $_SESSION['success'] = 'E-mail verificado com sucesso! Você já pode fazer login.';
        return $response->withHeader('Location', '/admin/login')->withStatus(302);
    }
}
