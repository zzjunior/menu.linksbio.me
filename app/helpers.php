<?php
if (!function_exists('csrf_field')) {
    function csrf_field() {
        $token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('validate_csrf')) {
    function validate_csrf(array $data): bool {
        $token = $data['csrf_token'] ?? '';
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('sanitize_input')) {
    function sanitize_input(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('validate_phone')) {
    function validate_phone(string $phone): bool {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return strlen($phone) >= 10 && strlen($phone) <= 11;
    }
}

if (!function_exists('validate_email')) {
    function validate_email(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('log_security_event')) {
    function log_security_event(string $eventType, string $message, array $context = []): void {
        $logDir = __DIR__ . '/../storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/security-' . date('Y-m-d') . '.log';
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event_type' => $eventType,
            'message' => $message,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'user_id' => $_SESSION['user_id'] ?? null,
            'context' => $context
        ];
        
        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
}