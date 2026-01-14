<?php

namespace App\Helpers;

/**
 * Helper de segurança
 * 
 * @package App\Helpers
 */
class Security
{
    /**
     * Gera token CSRF
     * 
     * @return string
     */
    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        
        return $token;
    }
    
    /**
     * Valida token CSRF
     * 
     * @param string $token
     * @return bool
     */
    public static function validateCsrfToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Sanitiza string
     * 
     * @param string $data
     * @return string
     */
    public static function sanitize(string $data): string
    {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Hash de senha
     * 
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    /**
     * Verifica senha
     * 
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Gera token aleatório
     * 
     * @param int $length
     * @return string
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
