<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\DBAL\Connection;

class SessionService
{
    private Connection $db;
    
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    /**
     * Criar sessão segura no banco após login
     */
    public function createSession(int $userId, string $ipAddress, string $userAgent): string
    {
        // Garantir que a sessão está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionId = session_id();
        
        // Se ainda estiver vazio, gerar um novo
        if (empty($sessionId)) {
            session_regenerate_id(true);
            $sessionId = session_id();
        }
        
        $expiresAt = date('Y-m-d H:i:s', time() + 86400); // 24 horas
        
        // Verificar se já existe uma sessão com esse ID
        $existing = $this->db->fetchOne(
            'SELECT session_id FROM sessions WHERE session_id = ?',
            [$sessionId]
        );
        
        if ($existing) {
            // Atualizar a existente
            $this->db->update('sessions', [
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'user_agent' => substr($userAgent, 0, 255),
                'expires_at' => $expiresAt,
                'created_at' => date('Y-m-d H:i:s')
            ], [
                'session_id' => $sessionId
            ]);
        } else {
            // Inserir nova
            $this->db->insert('sessions', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'user_agent' => substr($userAgent, 0, 255),
                'expires_at' => $expiresAt,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        return $sessionId;
    }
    
    /**
     * Validar sessão (verificar IP e User-Agent)
     */
    public function validateSession(string $sessionId, string $ipAddress, string $userAgent): bool
    {
        $session = $this->db->fetchAssociative(
            'SELECT * FROM sessions WHERE session_id = ? AND expires_at > NOW()',
            [$sessionId]
        );
        
        if (!$session) {
            return false;
        }
        
        // Verificar se IP e User-Agent são os mesmos (proteção contra session hijacking)
        if ($session['ip_address'] !== $ipAddress || $session['user_agent'] !== substr($userAgent, 0, 255)) {
            $this->destroySession($sessionId);
            return false;
        }
        
        // Atualizar último acesso
        $this->db->update('sessions', [
            'last_activity' => date('Y-m-d H:i:s')
        ], [
            'session_id' => $sessionId
        ]);
        
        return true;
    }
    
    /**
     * Destruir sessão do banco
     */
    public function destroySession(string $sessionId): void
    {
        $this->db->delete('sessions', ['session_id' => $sessionId]);
    }
    
    /**
     * Limpar sessões expiradas
     */
    public function cleanExpiredSessions(): void
    {
        $this->db->executeStatement('DELETE FROM sessions WHERE expires_at < NOW()');
    }
    
    /**
     * Verificar rate limiting (tentativas de login)
     */
    public function checkRateLimit(string $identifier, int $maxAttempts = 5, int $windowSeconds = 300): bool
    {
        $cacheKey = "login_attempts_{$identifier}";
        $cacheFile = __DIR__ . '/../../storage/cache/' . md5($cacheKey) . '.txt';
        
        // Criar diretório se não existir
        $dir = dirname($cacheFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $attempts = [];
        if (file_exists($cacheFile)) {
            $attempts = json_decode(file_get_contents($cacheFile), true) ?? [];
        }
        
        // Limpar tentativas antigas
        $cutoff = time() - $windowSeconds;
        $attempts = array_filter($attempts, fn($timestamp) => $timestamp > $cutoff);
        
        // Verificar limite
        if (count($attempts) >= $maxAttempts) {
            return false;
        }
        
        // Registrar nova tentativa
        $attempts[] = time();
        file_put_contents($cacheFile, json_encode($attempts));
        
        return true;
    }
    
    /**
     * Limpar rate limit após login bem-sucedido
     */
    public function clearRateLimit(string $identifier): void
    {
        $cacheKey = "login_attempts_{$identifier}";
        $cacheFile = __DIR__ . '/../../storage/cache/' . md5($cacheKey) . '.txt';
        
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }
    
    /**
     * Regenerar session ID (proteção contra session fixation)
     */
    public function regenerateSession(): void
    {
        session_regenerate_id(true);
    }
}
