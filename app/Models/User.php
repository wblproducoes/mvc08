<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model de Usuário
 * 
 * @package App\Models
 */
class User extends Model
{
    protected string $table = 'users';
    
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
     * Busca todos os usuários com informações de status e nível de acesso
     * 
     * @return array
     */
    public function all(): array
    {
        $sql = "SELECT u.*, s.translate as status_name, l.translate as level_name 
                FROM {$this->prefix}{$this->table} u
                LEFT JOIN {$this->prefix}status s ON u.status_id = s.id
                LEFT JOIN {$this->prefix}levels l ON u.level_id = l.id
                WHERE u.deleted_at IS NULL
                ORDER BY u.dh DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Busca usuários com paginação e relações
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function paginateWithRelations(int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT u.*, s.translate as status_name, l.translate as level_name 
                FROM {$this->prefix}{$this->table} u
                LEFT JOIN {$this->prefix}status s ON u.status_id = s.id
                LEFT JOIN {$this->prefix}levels l ON u.level_id = l.id
                WHERE u.deleted_at IS NULL
                ORDER BY u.dh DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Busca usuário por ID com informações de status e nível de acesso
     * 
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT u.*, s.translate as status_name, l.translate as level_name 
                FROM {$this->prefix}{$this->table} u
                LEFT JOIN {$this->prefix}status s ON u.status_id = s.id
                LEFT JOIN {$this->prefix}levels l ON u.level_id = l.id
                WHERE u.id = ? AND u.deleted_at IS NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Atualiza último acesso do usuário
     * 
     * @param int $id
     * @return bool
     */
    public function updateLastAccess(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->prefix}{$this->table} SET last_access = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Pesquisa usuários com filtros avançados
     * 
     * Permite filtrar usuários por:
     * - Texto de busca (nome, email ou username)
     * - Status específico
     * - Nível de acesso específico
     * 
     * Os filtros podem ser combinados para busca mais precisa.
     * Retorna apenas usuários não deletados (deleted_at IS NULL).
     * 
     * @param array $filters Array associativo com os filtros:
     *                       - 'search' (string): Termo de busca para nome, email ou username
     *                       - 'status_id' (int): ID do status para filtrar
     *                       - 'level_id' (int): ID do nível de acesso para filtrar
     * @param int $limit Número máximo de registros a retornar (padrão: 10)
     * @param int $offset Número de registros a pular (para paginação)
     * @return array Array de usuários com informações de status e nível
     * 
     * @example
     * // Buscar usuários ativos com nível administrador
     * $filters = ['status_id' => 1, 'level_id' => 10];
     * $users = $userModel->searchWithFilters($filters, 10, 0);
     */
    public function searchWithFilters(array $filters, int $limit = 10, int $offset = 0): array
    {
        $conditions = ["u.deleted_at IS NULL"];
        $params = [];
        
        if (!empty($filters['search'])) {
            $conditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.username LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['status_id'])) {
            $conditions[] = "u.status_id = ?";
            $params[] = $filters['status_id'];
        }
        
        if (!empty($filters['level_id'])) {
            $conditions[] = "u.level_id = ?";
            $params[] = $filters['level_id'];
        }
        
        $where = implode(' AND ', $conditions);
        
        $sql = "SELECT u.*, s.translate as status_name, l.translate as level_name 
                FROM {$this->prefix}{$this->table} u
                LEFT JOIN {$this->prefix}status s ON u.status_id = s.id
                LEFT JOIN {$this->prefix}levels l ON u.level_id = l.id
                WHERE {$where}
                ORDER BY u.dh DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Conta o total de usuários que correspondem aos filtros
     * 
     * Utilizado para calcular a paginação ao usar filtros de busca.
     * Conta apenas usuários não deletados (deleted_at IS NULL).
     * 
     * @param array $filters Array associativo com os filtros:
     *                       - 'search' (string): Termo de busca para nome, email ou username
     *                       - 'status_id' (int): ID do status para filtrar
     *                       - 'level_id' (int): ID do nível de acesso para filtrar
     * @return int Número total de usuários que correspondem aos filtros
     * 
     * @example
     * // Contar usuários bloqueados
     * $filters = ['status_id' => 2];
     * $total = $userModel->countSearch($filters);
     */
    public function countSearch(array $filters): int
    {
        $conditions = ["deleted_at IS NULL"];
        $params = [];
        
        if (!empty($filters['search'])) {
            $conditions[] = "(name LIKE ? OR email LIKE ? OR username LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['status_id'])) {
            $conditions[] = "status_id = ?";
            $params[] = $filters['status_id'];
        }
        
        if (!empty($filters['level_id'])) {
            $conditions[] = "level_id = ?";
            $params[] = $filters['level_id'];
        }
        
        $where = implode(' AND ', $conditions);
        
        $sql = "SELECT COUNT(*) as total FROM {$this->prefix}{$this->table} WHERE {$where}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int) $result['total'];
    }
    
    /**
     * Busca todos os usuários deletados (lixeira)
     * 
     * Retorna usuários que foram marcados como deletados (soft delete).
     * Inclui informações de status e nível de acesso através de JOIN.
     * Ordenado por data de exclusão (mais recentes primeiro).
     * 
     * @return array Array de usuários deletados com suas informações completas
     * 
     * @example
     * $deletedUsers = $userModel->trash();
     * foreach ($deletedUsers as $user) {
     *     echo $user['name'] . ' - Deletado em: ' . $user['deleted_at'];
     * }
     */
    public function trash(): array
    {
        $sql = "SELECT u.*, s.translate as status_name, l.translate as level_name 
                FROM {$this->prefix}{$this->table} u
                LEFT JOIN {$this->prefix}status s ON u.status_id = s.id
                LEFT JOIN {$this->prefix}levels l ON u.level_id = l.id
                WHERE u.deleted_at IS NOT NULL
                ORDER BY u.deleted_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Restaura um usuário deletado
     * 
     * Remove a marcação de exclusão (deleted_at = NULL) permitindo
     * que o usuário volte a aparecer nas listagens normais e possa
     * fazer login novamente no sistema.
     * 
     * @param int $id ID do usuário a ser restaurado
     * @return bool True se restaurado com sucesso, False caso contrário
     * 
     * @example
     * if ($userModel->restore(5)) {
     *     echo "Usuário restaurado com sucesso!";
     * }
     */
    public function restore(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->prefix}{$this->table} SET deleted_at = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Deleta um usuário permanentemente do banco de dados
     * 
     * ATENÇÃO: Esta operação é IRREVERSÍVEL!
     * Remove completamente o registro do banco de dados.
     * Diferente do soft delete, não há como recuperar após esta operação.
     * 
     * Recomenda-se usar apenas para usuários que já estão na lixeira
     * e que realmente precisam ser removidos permanentemente.
     * 
     * @param int $id ID do usuário a ser deletado permanentemente
     * @return bool True se deletado com sucesso, False caso contrário
     * 
     * @warning Esta operação não pode ser desfeita!
     * 
     * @example
     * // Deletar permanentemente apenas se necessário
     * if (confirm("Tem certeza?")) {
     *     $userModel->forceDelete(5);
     * }
     */
    public function forceDelete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->prefix}{$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
