<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model de status
 * 
 * @package App\Models
 */
class Status extends Model
{
    protected string $table = 'status';
    
    /**
     * Busca status por nome
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
