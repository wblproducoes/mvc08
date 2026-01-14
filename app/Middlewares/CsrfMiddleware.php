<?php

namespace App\Middlewares;

use App\Helpers\Security;

/**
 * Middleware de proteção CSRF
 * 
 * @package App\Middlewares
 */
class CsrfMiddleware
{
    /**
     * Valida token CSRF
     * 
     * @return bool
     */
    public function handle(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            
            if (!Security::validateCsrfToken($token)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
                exit;
            }
        }
        
        return true;
    }
}
