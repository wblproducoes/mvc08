<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Helpers\Security;
use App\Helpers\Validator;

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
        $this->view('default/pages/login.twig', [
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    /**
     * Processa login
     */
    public function login(): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 403);
        }
        
        $username = Security::sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $validator = new Validator();
        $validator->required($username, 'username')
                  ->required($password, 'password');
        
        if ($validator->fails()) {
            $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $result = $this->authService->login($username, $password);
        
        if ($result['success']) {
            $this->json(['success' => true, 'redirect' => \App\Helpers\Url::to('dashboard')]);
        } else {
            $this->json(['success' => false, 'message' => $result['message']], 401);
        }
    }
    
    /**
     * Exibe página de esqueceu senha
     */
    public function showForgotPassword(): void
    {
        $this->view('default/pages/forgot-password.twig', [
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    /**
     * Processa esqueceu senha
     */
    public function forgotPassword(): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 403);
        }
        
        $email = Security::sanitize($_POST['email'] ?? '');
        
        $validator = new Validator();
        $validator->required($email, 'email')->email($email, 'email');
        
        if ($validator->fails()) {
            $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $result = $this->authService->requestPasswordReset($email);
        $this->json($result);
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
