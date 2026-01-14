<?php

namespace App\Middlewares;

/**
 * Middleware de autenticação
 * 
 * @package App\Middlewares
 */
class AuthMiddleware
{
    /**
     * Verifica se usuário está autenticado
     * 
     * @return bool
     */
    public function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        return true;
    }
}
