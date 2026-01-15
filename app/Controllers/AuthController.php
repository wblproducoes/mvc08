<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\RateLimiter;

/**
 * Controller de autenticação
 * 
 * @package App\Controllers
 */
class AuthController extends Controller
{
    private AuthService $authService;
    
    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
    }
    
    /**
     * Exibe página de login
     */
    public function showLogin(): void
    {
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        
        unset($_SESSION['error'], $_SESSION['success']);
        
        $this->view('default/pages/login.twig', [
            'csrf_token' => Security::generateCsrfToken(),
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Processa login
     */
    public function login(): void
    {
        // Rate Limiting - Proteção contra força bruta
        $rateLimiter = new RateLimiter(5, 15); // 5 tentativas em 15 minutos
        
        if ($rateLimiter->isBlocked('login')) {
            $seconds = $rateLimiter->availableIn('login');
            $minutes = ceil($seconds / 60);
            $_SESSION['error'] = "Muitas tentativas de login. Tente novamente em {$minutes} minuto(s).";
            $this->redirect(\App\Helpers\Url::to('login'));
            return;
        }
        
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('login'));
            return;
        }
        
        $username = Security::sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $validator = new Validator();
        $validator->required($username, 'username')
                  ->required($password, 'password');
        
        if ($validator->fails()) {
            $_SESSION['error'] = 'Preencha todos os campos';
            $this->redirect(\App\Helpers\Url::to('login'));
            return;
        }
        
        $result = $this->authService->login($username, $password);
        
        if ($result['success']) {
            // Limpa tentativas após login bem-sucedido
            $rateLimiter->clear('login');
            $this->redirect(\App\Helpers\Url::to('dashboard'));
        } else {
            // Registra tentativa falha
            $rateLimiter->hit('login');
            $remaining = $rateLimiter->remaining('login');
            
            if ($remaining > 0) {
                $_SESSION['error'] = $result['message'] . " ({$remaining} tentativa(s) restante(s))";
            } else {
                $_SESSION['error'] = 'Muitas tentativas. Aguarde 15 minutos.';
            }
            
            $this->redirect(\App\Helpers\Url::to('login'));
        }
    }
    
    /**
     * Exibe página de esqueceu senha
     */
    public function showForgotPassword(): void
    {
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        
        unset($_SESSION['error'], $_SESSION['success']);
        
        $this->view('default/pages/forgot-password.twig', [
            'csrf_token' => Security::generateCsrfToken(),
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Processa esqueceu senha
     */
    public function forgotPassword(): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('forgot-password'));
            return;
        }
        
        $email = Security::sanitize($_POST['email'] ?? '');
        
        $validator = new Validator();
        $validator->required($email, 'email')->email($email, 'email');
        
        if ($validator->fails()) {
            $_SESSION['error'] = 'Por favor, informe um e-mail válido';
            $this->redirect(\App\Helpers\Url::to('forgot-password'));
            return;
        }
        
        $result = $this->authService->requestPasswordReset($email);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        $this->redirect(\App\Helpers\Url::to('forgot-password'));
    }
    
    /**
     * Logout
     */
    public function logout(): void
    {
        $this->authService->logout();
        $this->redirect(\App\Helpers\Url::to('login'));
    }
}
