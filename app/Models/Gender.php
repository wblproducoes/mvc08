<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model de Gêneros
 * 
 * Gerencia todas as operações relacionadas aos gêneros do sistema.
 * 
 * @package App\Models
 * @author Sistema MVC08
 * @version 1.0.0
 */
class Gender extends Model
{
    /**
     * Nome da tabela
     * 
     * @var string
     */
    protected string $table = 'genders';
    
    /**
     * Chave primária
     * 
     * @var string
     */
    protected string $primaryKey = 'id';
    
    /**
     * Busca todos os gêneros ativos
     * 
     * @return array
     */
    public function allActive(): array
    {
        $sql = "SELECT * FROM {$this->prefix}{$this->table} WHERE deleted_at IS NULL ORDER BY translate ASC";
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Busca todos os gêneros (incluindo deletados)
     * 
     * @return array
     */
    public function all(): array
    {
        $sql = "SELECT * FROM {$this->prefix}{$this->table} ORDER BY translate ASC";
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Busca um gênero por ID
     * 
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->prefix}{$this->table} WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Busca um gênero por nome
     * 
     * @param string $name
     * @return array|null
     */
    public function findByName(string $name): ?array
    {
        $sql = "SELECT * FROM {$this->prefix}{$this->table} WHERE name = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Conta total de gêneros ativos
     * 
     * @return int
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->prefix}{$this->table} WHERE deleted_at IS NULL";
        $result = $this->db->query($sql)->fetch();
        return $result['total'] ?? 0;
    }
    
    /**
     * Busca com paginação
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function paginate(int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->prefix}{$this->table} WHERE deleted_at IS NULL ORDER BY translate ASC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Cria um novo gênero
     * 
     * @param array $data
     * @return int|false
     */
    public function create(array $data): int|false
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->prefix}{$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute(array_values($data))) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Atualiza um gênero
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $set = implode(', ', array_map(fn($key) => "{$key} = ?", array_keys($data)));
        $sql = "UPDATE {$this->prefix}{$this->table} SET {$set} WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([...array_values($data), $id]);
    }
    
    /**
     * Deleta um gênero (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "UPDATE {$this->prefix}{$this->table} SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Restaura um gênero deletado
     * 
     * @param int $id
     * @return bool
     */
    public function restore(int $id): bool
    {
        $sql = "UPDATE {$this->prefix}{$this->table} SET deleted_at = NULL WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Busca gêneros deletados
     * 
     * @return array
     */
    public function trash(): array
    {
        $sql = "SELECT * FROM {$this->prefix}{$this->table} WHERE deleted_at IS NOT NULL ORDER BY translate ASC";
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Deleta permanentemente um gênero
     * 
     * @param int $id
     * @return bool
     */
    public function forceDelete(int $id): bool
    {
        $sql = "DELETE FROM {$this->prefix}{$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
