<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use App\Services\TemplateService;

class AuthController
{
    private User $userModel;
    private TemplateService $templateService;

    public function __construct(User $userModel, TemplateService $templateService)
    {
        $this->userModel = $userModel;
        $this->templateService = $templateService;
    }

    /**
     * Exibe o formulário de login
     */
    public function loginForm(Request $request, Response $response): Response
    {
        $data = [
            'title' => 'Login - Admin',
            'error' => $_SESSION['error'] ?? null
        ];
        
        unset($_SESSION['error']);
        
        return $this->templateService->renderResponse($response, 'auth/login', $data);
    }

    /**
     * Processa o login
     */
    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email e senha são obrigatórios';
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $_SESSION['error'] = 'Email ou senha inválidos';
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        if (!$this->userModel->isActive($user['id'])) {
            $_SESSION['error'] = 'Conta inativa. Entre em contato com o suporte.';
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        // Criar sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['store_name'] = $user['store_name'];
        $_SESSION['store_slug'] = $user['store_slug'];

        return $response->withHeader('Location', '/admin')->withStatus(302);
    }

    /**
     * Processa o logout
     */
    public function logout(Request $request, Response $response): Response
    {
        session_destroy();
        return $response->withHeader('Location', '/admin/login')->withStatus(302);
    }

    /**
     * Exibe o formulário de registro
     */
    public function registerForm(Request $request, Response $response): Response
    {
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

        // Criar usuário
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $this->userModel->hashPassword($data['password']),
            'store_name' => $data['store_name'],
            'store_slug' => $this->userModel->generateSlug($data['store_name']),
            'whatsapp' => preg_replace('/[^0-9]/', '', $data['whatsapp']),
            'address' => $data['address'] ?? '',
            'is_active' => 1
        ];

        try {
            $userId = $this->userModel->create($userData);
            $_SESSION['success'] = 'Conta criada com sucesso! Faça login para continuar.';
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao criar conta. Tente novamente.';
            return $response->withHeader('Location', '/admin/register')->withStatus(302);
        }
    }

    /**
     * Middleware para verificar se usuário está logado
     */
    public function checkAuth(Request $request, Response $response, $next)
    {
        if (!isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        return $next($request, $response);
    }
}
