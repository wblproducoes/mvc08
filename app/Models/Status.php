<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model de Status
 * 
 * @package App\Models
 */
class Status extends Model
{
    protected string $table = 'status';
    
    /**
     * Busca todos os status ativos
     * 
     * @return array
     */
    public function allActive(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->prefix}{$this->table} WHERE deleted_at IS NULL ORDER BY name");
        return $stmt->fetchAll();
    }
    
    /**
     * Pesquisa status por nome, tradução ou descrição
     * 
     * Realiza busca com LIKE em múltiplos campos permitindo
     * encontrar status mesmo com busca parcial.
     * Retorna apenas status não deletados.
     * 
     * @param string $search Termo de busca (pode ser parcial)
     * @param int $limit Número máximo de registros a retornar (padrão: 10)
     * @param int $offset Número de registros a pular (para paginação)
     * @return array Array de status que correspondem à busca
     * 
     * @example
     * // Buscar status que contenham "ativo"
     * $statuses = $statusModel->search('ativo', 10, 0);
     */
    public function search(string $search, int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->prefix}{$this->table} 
            WHERE deleted_at IS NULL 
            AND (name LIKE ? OR translate LIKE ? OR description LIKE ?)
            ORDER BY id DESC 
            LIMIT ? OFFSET ?
        ");
        $searchTerm = "%{$search}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Conta registros da pesquisa
     * 
     * @param string $search
     * @return int
     */
    public function countSearch(string $search): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM {$this->prefix}{$this->table} 
            WHERE deleted_at IS NULL 
            AND (name LIKE ? OR translate LIKE ? OR description LIKE ?)
        ");
        $searchTerm = "%{$search}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $result = $stmt->fetch();
        return (int) $result['total'];
    }
    
    /**
     * Busca todos os status deletados (lixeira)
     * 
     * Retorna status que foram marcados como deletados (soft delete).
     * Útil para implementar funcionalidade de lixeira onde itens
     * podem ser restaurados ou deletados permanentemente.
     * Ordenado por data de exclusão (mais recentes primeiro).
     * 
     * @return array Array de status deletados
     * 
     * @example
     * $deletedStatuses = $statusModel->trash();
     */
    public function trash(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->prefix}{$this->table} WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Restaura um status deletado
     * 
     * Remove a marcação de exclusão (deleted_at = NULL) permitindo
     * que o status volte a aparecer nas listagens e possa ser
     * utilizado novamente no sistema.
     * 
     * @param int $id ID do status a ser restaurado
     * @return bool True se restaurado com sucesso, False caso contrário
     */
    public function restore(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->prefix}{$this->table} SET deleted_at = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Deleta um status permanentemente do banco de dados
     * 
     * ATENÇÃO: Esta operação é IRREVERSÍVEL!
     * Remove completamente o registro do banco de dados.
     * Use apenas para status que realmente não serão mais necessários.
     * 
     * @param int $id ID do status a ser deletado permanentemente
     * @return bool True se deletado com sucesso, False caso contrário
     * 
     * @warning Esta operação não pode ser desfeita!
     */
    public function forceDelete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->prefix}{$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
