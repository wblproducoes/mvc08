<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAccessLog;
use App\Helpers\Security;

/**
 * Serviço de autenticação
 * 
 * @package App\Services
 */
class AuthService
{
    private User $userModel;
    private UserAccessLog $logModel;
    
    public function __construct()
    {
        $this->userModel = new User();
        $this->logModel = new UserAccessLog();
    }
    
    /**
     * Realiza login
     * 
     * @param string $username
     * @param string $password
     * @return array
     */
    public function login(string $username, string $password): array
    {
        $user = $this->userModel->findByUsername($username);
        
        if (!$user) {
            $this->logModel->log(null, 1, false, "Username não encontrado: {$username}");
            return ['success' => false, 'message' => 'Credenciais inválidas'];
        }
        
        if (!Security::verifyPassword($password, $user['password'])) {
            $this->logModel->log($user['id'], 1, false, "Senha incorreta");
            return ['success' => false, 'message' => 'Credenciais inválidas'];
        }
        
        if ($user['status_id'] != 1) {
            $this->logModel->log($user['id'], 1, false, "Usuário inativo/bloqueado");
            return ['success' => false, 'message' => 'Usuário inativo'];
        }
        
        // Inicia sessão
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['level_id'] = $user['level_id'];
        
        // Atualiza último acesso
        $this->userModel->updateLastAccess($user['id']);
        
        // Registra log
        $this->logModel->log($user['id'], 1, true, "Login realizado com sucesso");
        
        return ['success' => true, 'message' => 'Login realizado com sucesso'];
    }
    
    /**
     * Realiza logout
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            $this->logModel->log($_SESSION['user_id'], 2, true, "Logout realizado");
        }
        
        session_destroy();
    }
    
    /**
     * Solicita reset de senha
     * 
     * @param string $email
     * @return array
     */
    public function requestPasswordReset(string $email): array
    {
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Email não encontrado'];
        }
        
        $token = Security::generateToken(32);
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->userModel->update($user['id'], [
            'password_reset_token' => $token,
            'password_reset_expires' => $expires
        ]);
        
        // Aqui você enviaria o email com o token
        // EmailService::sendPasswordReset($email, $token);
        
        $this->logModel->log($user['id'], 3, true, "Solicitação de reset de senha");
        
        return [
            'success' => true, 
            'message' => 'Email de recuperação enviado',
            'token' => $token // Remover em produção
        ];
    }
    
    /**
     * Reseta senha
     * 
     * @param string $token
     * @param string $newPassword
     * @return array
     */
    public function resetPassword(string $token, string $newPassword): array
    {
        $user = $this->userModel->findByResetToken($token);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Token inválido ou expirado'];
        }
        
        $hashedPassword = Security::hashPassword($newPassword);
        
        $this->userModel->update($user['id'], [
            'password' => $hashedPassword,
            'password_reset_token' => null,
            'password_reset_expires' => null
        ]);
        
        $this->logModel->log($user['id'], 4, true, "Senha resetada com sucesso");
        
        return ['success' => true, 'message' => 'Senha alterada com sucesso'];
    }
}
