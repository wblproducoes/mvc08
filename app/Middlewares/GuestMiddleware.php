<?php

namespace App\Middlewares;

/**
 * Middleware para visitantes (não autenticados)
 * 
 * @package App\Middlewares
 */
class GuestMiddleware
{
    /**
     * Verifica se usuário não está autenticado
     * 
     * @return bool
     */
    public function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . \App\Helpers\Url::to('dashboard'));
            exit;
        }
        
        return true;
    }
}
