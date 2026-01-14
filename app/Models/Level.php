<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model de nível de usuário
 * 
 * @package App\Models
 */
class Level extends Model
{
    protected string $table = 'levels';
    
    /**
     * Busca nível por nome
     * 
     * @param string $name
     * @return array|null
     */
    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->prefix}{$this->table} WHERE name = ? AND deleted_at IS NULL");
        $stmt->execute([$name]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
