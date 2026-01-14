<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model de usuário
 * 
 * @package App\Models
 */
class User extends Model
{
    protected string $table = 'users';
    
    /**
     * Busca usuário por username
     * 
     * @param string $username
     * @return array|null
     */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->prefix}{$this->table} WHERE username = ? AND deleted_at IS NULL");
        $stmt->execute([$username]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Busca usuário por email
     * 
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->prefix}{$this->table} WHERE email = ? AND deleted_at IS NULL");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Busca usuário por token de reset
     * 
     * @param string $token
     * @return array|null
     */
    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->prefix}{$this->table} 
            WHERE password_reset_token = ? 
            AND password_reset_expires > NOW() 
            AND deleted_at IS NULL
        ");
        $stmt->execute([$token]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Atualiza último acesso
     * 
     * @param int $userId
     * @return bool
     */
    public function updateLastAccess(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->prefix}{$this->table} SET last_access = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }
}
