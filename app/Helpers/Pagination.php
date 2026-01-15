<?php

namespace App\Helpers;

/**
 * Helper de Paginação
 * 
 * Classe responsável por gerenciar a paginação de listagens.
 * Calcula automaticamente:
 * - Total de páginas
 * - Página atual
 * - Offset para queries SQL
 * - Páginas a exibir na navegação
 * - Links de próxima/anterior
 * 
 * @package App\Helpers
 * 
 * @example
 * // Criar paginação para 100 itens, 10 por página, página 1
 * $pagination = new Pagination(100, 10, 1);
 * 
 * // Usar em query SQL
 * $users = $model->paginate($pagination->getLimit(), $pagination->getOffset());
 * 
 * // Passar para view
 * $view->render('users', ['pagination' => $pagination->toArray()]);
 */
class Pagination
{
    private int $totalItems;
    private int $itemsPerPage;
    private int $currentPage;
    private int $totalPages;
    
    /**
     * Construtor da classe Pagination
     * 
     * Inicializa a paginação calculando automaticamente o total de páginas
     * e ajustando a página atual se necessário.
     * 
     * @param int $totalItems Total de itens a paginar
     * @param int $itemsPerPage Número de itens por página (padrão: 10)
     * @param int $currentPage Página atual (padrão: 1)
     * 
     * @example
     * // Paginar 150 usuários, 20 por página, mostrando página 3
     * $pagination = new Pagination(150, 20, 3);
     */
    public function __construct(int $totalItems, int $itemsPerPage = 10, int $currentPage = 1)
    {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = max(1, $currentPage);
        $this->totalPages = max(1, (int) ceil($totalItems / $itemsPerPage));
        
        // Ajusta página atual se for maior que o total
        if ($this->currentPage > $this->totalPages) {
            $this->currentPage = $this->totalPages;
        }
    }
    
    public function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }
    
    public function getLimit(): int
    {
        return $this->itemsPerPage;
    }
    
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }
    
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }
    
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }
    
    public function hasPrevious(): bool
    {
        return $this->currentPage > 1;
    }
    
    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages;
    }
    
    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }
    
    public function getNextPage(): int
    {
        return min($this->totalPages, $this->currentPage + 1);
    }
    
    /**
     * Retorna array de páginas para exibir na navegação
     * 
     * Gera um array com 3 números de página centrados na página atual.
     * Ajusta automaticamente para não ultrapassar os limites.
     * 
     * @return array Array com números das páginas a exibir
     * 
     * @example
     * // Se página atual é 5 e total é 10, retorna [4, 5, 6]
     * // Se página atual é 1 e total é 10, retorna [1, 2, 3]
     * // Se página atual é 10 e total é 10, retorna [8, 9, 10]
     */
    public function getPages(): array
    {
        $pages = [];
        $start = max(1, $this->currentPage - 1);
        $end = min($this->totalPages, $start + 2);
        
        // Ajusta se estiver no final
        if ($end - $start < 2) {
            $start = max(1, $end - 2);
        }
        
        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }
        
        return $pages;
    }
    
    /**
     * Retorna todos os dados de paginação em formato de array
     * 
     * Útil para passar para views Twig ou retornar em APIs JSON.
     * Contém todas as informações necessárias para renderizar
     * a interface de paginação.
     * 
     * @return array Array associativo com:
     *               - current_page: Página atual
     *               - total_pages: Total de páginas
     *               - total_items: Total de itens
     *               - items_per_page: Itens por página
     *               - has_previous: Se tem página anterior
     *               - has_next: Se tem próxima página
     *               - previous_page: Número da página anterior
     *               - next_page: Número da próxima página
     *               - pages: Array com números das páginas a exibir
     * 
     * @example
     * $data = $pagination->toArray();
     * // ['current_page' => 2, 'total_pages' => 10, ...]
     */
    public function toArray(): array
    {
        return [
            'current_page' => $this->currentPage,
            'total_pages' => $this->totalPages,
            'total_items' => $this->totalItems,
            'items_per_page' => $this->itemsPerPage,
            'has_previous' => $this->hasPrevious(),
            'has_next' => $this->hasNext(),
            'previous_page' => $this->getPreviousPage(),
            'next_page' => $this->getNextPage(),
            'pages' => $this->getPages()
        ];
    }
}
