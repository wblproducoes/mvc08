<?php

namespace App\Core;

use App\Config\Database;
use PDO;

/**
 * Model Base
 * 
 * Classe abstrata que fornece métodos CRUD básicos para todos os models.
 * Implementa soft delete (deleted_at) por padrão em todas as operações.
 * 
 * Métodos disponíveis:
 * - all(): Busca todos os registros não deletados
 * - count(): Conta registros não deletados
 * - paginate(): Busca com paginação
 * - find(): Busca por ID
 * - create(): Cria novo registro
 * - update(): Atualiza registro existente
 * - delete(): Soft delete (marca como deletado)
 * 
 * @package App\Core
 * @abstract
 * @version 1.5.0
 */
abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $prefix;
    
    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->prefix = Database::getPrefix();
    }
    
    /**
     * Busca todos os registros
     * 
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->prefix}{$this->table} WHERE deleted_at IS NULL");
        return $stmt->fetchAll();
    }
    
    /**
     * Conta total de registros
     * 
     * @return int
     */
    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->prefix}{$this->table} WHERE deleted_at IS NULL");
        $result = $stmt->fetch();
        return (int) $result['total'];
    }
    
    /**
     * Busca registros com paginação
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function paginate(int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->prefix}{$this->table} WHERE deleted_at IS NULL ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Busca por ID
     * 
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->prefix}{$this->table} WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Cria novo registro
     * 
     * @param array $data
     * @return int|false
     */
    public function create(array $data): int|false
    {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $sql = "INSERT INTO {$this->prefix}{$this->table} (" . implode(',', $fields) . ") VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($values)) {
            return (int) $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Atualiza registro
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "{$field} = ?";
        }
        
        $sql = "UPDATE {$this->prefix}{$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        $values = array_values($data);
        $values[] = $id;
        
        return $stmt->execute($values);
    }
    
    /**
     * Deleta registro (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->prefix}{$this->table} SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
