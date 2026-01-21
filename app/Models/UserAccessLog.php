<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model de Logs de Acesso
 * 
 * Gerencia todos os logs de acesso e eventos do sistema.
 * 
 * @package App\Models
 * @author Sistema MVC08
 * @version 1.0.0
 */
class UserAccessLog extends Model
{
    /**
     * Nome da tabela
     * 
     * @var string
     */
    protected string $table = 'user_access_logs';
    
    /**
     * Busca todos os logs com informações do usuário
     * 
     * @return array
     */
    public function allWithUser(): array
    {
        $sql = "SELECT 
                    l.id,
                    l.dh_access,
                    u.name as user_name,
                    u.username,
                    l.ip_address,
                    l.event_type_id,
                    l.success,
                    l.details
                FROM {$this->prefix}{$this->table} l
                LEFT JOIN {$this->prefix}users u ON l.user_id = u.id
                ORDER BY l.dh_access DESC";
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Conta total de logs
     * 
     * @return int
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->prefix}{$this->table}";
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
        $sql = "SELECT 
                    l.id,
                    l.dh_access,
                    u.name as user_name,
                    u.username,
                    l.ip_address,
                    l.event_type_id,
                    l.success,
                    l.details
                FROM {$this->prefix}{$this->table} l
                LEFT JOIN {$this->prefix}users u ON l.user_id = u.id
                ORDER BY l.dh_access DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Busca logs com filtros
     * 
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchWithFilters(array $filters, int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT 
                    l.id,
                    l.dh_access,
                    u.name as user_name,
                    u.username,
                    l.ip_address,
                    l.event_type_id,
                    l.success,
                    l.details
                FROM {$this->prefix}{$this->table} l
                LEFT JOIN {$this->prefix}users u ON l.user_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (u.name LIKE ? OR u.username LIKE ? OR l.ip_address LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        
        if (!empty($filters['event_type_id'])) {
            $sql .= " AND l.event_type_id = ?";
            $params[] = $filters['event_type_id'];
        }
        
        if (!empty($filters['success'])) {
            $sql .= " AND l.success = ?";
            $params[] = $filters['success'];
        }
        
        $sql .= " ORDER BY l.dh_access DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Conta logs com filtros
     * 
     * @param array $filters
     * @return int
     */
    public function countSearch(array $filters): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->prefix}{$this->table} l
                LEFT JOIN {$this->prefix}users u ON l.user_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (u.name LIKE ? OR u.username LIKE ? OR l.ip_address LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        
        if (!empty($filters['event_type_id'])) {
            $sql .= " AND l.event_type_id = ?";
            $params[] = $filters['event_type_id'];
        }
        
        if (!empty($filters['success'])) {
            $sql .= " AND l.success = ?";
            $params[] = $filters['success'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    /**
     * Busca um log por ID
     * 
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT 
                    l.id,
                    l.user_id,
                    l.dh_access,
                    u.name as user_name,
                    u.username,
                    l.ip_address,
                    l.user_agent,
                    l.event_type_id,
                    l.success,
                    l.details
                FROM {$this->prefix}{$this->table} l
                LEFT JOIN {$this->prefix}users u ON l.user_id = u.id
                WHERE l.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Retorna o nome do tipo de evento
     * 
     * @param int $eventTypeId
     * @return string
     */
    public static function getEventTypeName(int $eventTypeId): string
    {
        $types = [
            1 => 'Login',
            2 => 'Logout',
            3 => 'Reset Password',
            4 => 'Failed Login'
        ];
        return $types[$eventTypeId] ?? 'Desconhecido';
    }
}
