<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\DBAL\Connection;
use RuntimeException;

/**
 * Serviço responsável pelo gerenciamento de sessões de usuário.
 * 
 * Responsabilidades:
 * - Persistência de sessões no banco de dados
 * - Validação de sessões (IP, User-Agent)
 * - Rate limiting para proteção contra brute force
 * 
 * IMPORTANTE: A inicialização da sessão PHP é feita no bootstrap.php.
 * Este serviço assume que a sessão já está ativa quando seus métodos são chamados.
 */
class SessionService
{
    private const SESSION_LIFETIME_SECONDS = 86400; // 24 horas
    private const USER_AGENT_MAX_LENGTH = 255;
    
    private Connection $db;
    
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    /**
     * Obtém o ID da sessão PHP atual.
     * 
     * @throws RuntimeException Se a sessão não estiver ativa ou ID estiver vazio
     */
    private function getSessionId(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException(
                'Sessão PHP não está ativa. Verifique se session_start() foi chamado no bootstrap.'
            );
        }
        
        $sessionId = session_id();
        
        if (empty($sessionId)) {
            throw new RuntimeException(
                'ID de sessão vazio. Isso indica um problema na configuração do PHP.'
            );
        }
        
        return $sessionId;
    }
    
    /**
     * Regenera o ID da sessão de forma segura.
     * Deve ser chamado após autenticação bem-sucedida para prevenir session fixation.
     */
    public function regenerateSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
    
    /**
     * Cria ou atualiza uma sessão no banco de dados após login bem-sucedido.
     * 
     * @param int $userId ID do usuário autenticado
     * @param string $ipAddress Endereço IP do cliente
     * @param string $userAgent User-Agent do navegador
     * @return string ID da sessão criada
     */
    public function createSession(int $userId, string $ipAddress, string $userAgent): string
    {
        // Regenerar ID para prevenir session fixation
        $this->regenerateSession();
        
        $sessionId = $this->getSessionId();
        $userAgent = $this->sanitizeUserAgent($userAgent);
        $expiresAt = $this->calculateExpirationTime();
        $now = $this->getCurrentTimestamp();
        
        // Persistir no banco usando upsert
        $this->upsertSession($sessionId, $userId, $ipAddress, $userAgent, $expiresAt, $now);
        
        return $sessionId;
    }
    
    /**
     * Valida se uma sessão é válida e pertence ao mesmo cliente.
     * Protege contra session hijacking verificando IP e User-Agent.
     */
    public function validateSession(string $sessionId, string $ipAddress, string $userAgent): bool
    {
        if (empty($sessionId)) {
            return false;
        }
        
        $session = $this->findActiveSession($sessionId);
        
        if ($session === null) {
            return false;
        }
        
        // Verificar fingerprint do cliente
        if (!$this->validateClientFingerprint($session, $ipAddress, $userAgent)) {
            $this->destroySession($sessionId);
            return false;
        }
        
        $this->updateLastActivity($sessionId);
        
        return true;
    }
    
    /**
     * Remove uma sessão do banco de dados.
     */
    public function destroySession(string $sessionId): void
    {
        if (empty($sessionId)) {
            return;
        }
        
        $this->db->delete('sessions', ['session_id' => $sessionId]);
    }
    
    /**
     * Remove todas as sessões expiradas do banco.
     */
    public function cleanExpiredSessions(): int
    {
        return (int) $this->db->executeStatement(
            'DELETE FROM sessions WHERE expires_at < NOW()'
        );
    }
    
    /**
     * Verifica rate limiting para tentativas de login.
     * 
     * @param string $identifier Identificador único (geralmente IP)
     * @param int $maxAttempts Máximo de tentativas permitidas
     * @param int $windowSeconds Janela de tempo em segundos
     * @return bool True se ainda pode tentar, false se bloqueado
     */
    public function checkRateLimit(string $identifier, int $maxAttempts = 5, int $windowSeconds = 300): bool
    {
        $cacheFile = $this->getRateLimitCacheFile($identifier);
        $attempts = $this->loadAttempts($cacheFile);
        
        // Remover tentativas fora da janela de tempo
        $cutoff = time() - $windowSeconds;
        $attempts = array_filter($attempts, fn(int $timestamp): bool => $timestamp > $cutoff);
        
        if (count($attempts) >= $maxAttempts) {
            return false;
        }
        
        // Registrar nova tentativa
        $attempts[] = time();
        $this->saveAttempts($cacheFile, $attempts);
        
        return true;
    }
    
    /**
     * Limpa o rate limit após login bem-sucedido.
     */
    public function clearRateLimit(string $identifier): void
    {
        $cacheFile = $this->getRateLimitCacheFile($identifier);
        
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }
    
    // ========== Métodos privados auxiliares ==========
    
    private function sanitizeUserAgent(string $userAgent): string
    {
        return substr($userAgent, 0, self::USER_AGENT_MAX_LENGTH);
    }
    
    private function calculateExpirationTime(): string
    {
        return date('Y-m-d H:i:s', time() + self::SESSION_LIFETIME_SECONDS);
    }
    
    private function getCurrentTimestamp(): string
    {
        return date('Y-m-d H:i:s');
    }
    
    private function findActiveSession(string $sessionId): ?array
    {
        $result = $this->db->fetchAssociative(
            'SELECT * FROM sessions WHERE session_id = ? AND expires_at > NOW()',
            [$sessionId]
        );
        
        return $result ?: null;
    }
    
    private function validateClientFingerprint(array $session, string $ipAddress, string $userAgent): bool
    {
        $storedUserAgent = $session['user_agent'] ?? '';
        $storedIpAddress = $session['ip_address'] ?? '';
        
        return $storedIpAddress === $ipAddress 
            && $storedUserAgent === $this->sanitizeUserAgent($userAgent);
    }
    
    private function updateLastActivity(string $sessionId): void
    {
        $this->db->update(
            'sessions',
            ['last_activity' => $this->getCurrentTimestamp()],
            ['session_id' => $sessionId]
        );
    }
    
    private function upsertSession(
        string $sessionId,
        int $userId,
        string $ipAddress,
        string $userAgent,
        string $expiresAt,
        string $now
    ): void {
        $data = [
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'expires_at' => $expiresAt,
            'created_at' => $now,
            'last_activity' => $now
        ];
        
        $existing = $this->db->fetchOne(
            'SELECT session_id FROM sessions WHERE session_id = ?',
            [$sessionId]
        );
        
        if ($existing) {
            $this->db->update('sessions', $data, ['session_id' => $sessionId]);
        } else {
            $data['session_id'] = $sessionId;
            $this->db->insert('sessions', $data);
        }
    }
    
    private function getRateLimitCacheFile(string $identifier): string
    {
        $cacheDir = dirname(__DIR__, 2) . '/storage/cache/rate_limit';
        
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        return $cacheDir . '/' . hash('sha256', $identifier) . '.json';
    }
    
    private function loadAttempts(string $cacheFile): array
    {
        if (!file_exists($cacheFile)) {
            return [];
        }
        
        $content = file_get_contents($cacheFile);
        return json_decode($content, true) ?? [];
    }
    
    private function saveAttempts(string $cacheFile, array $attempts): void
    {
        file_put_contents($cacheFile, json_encode(array_values($attempts)));
    }
}
