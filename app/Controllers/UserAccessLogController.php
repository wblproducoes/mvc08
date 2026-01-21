<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserAccessLog;
use App\Helpers\Security;
use App\Helpers\Pagination;

/**
 * Controller de Logs de Acesso
 * 
 * Gerencia visualização e filtros dos logs de acesso do sistema.
 * 
 * @package App\Controllers
 * @author Sistema MVC08
 * @version 1.0.0
 */
class UserAccessLogController extends Controller
{
    /**
     * @var UserAccessLog Model de Logs de Acesso
     */
    private UserAccessLog $logModel;
    
    /**
     * Construtor do controller
     */
    public function __construct()
    {
        parent::__construct();
        $this->logModel = new UserAccessLog();
    }
    
    /**
     * Lista todos os logs de acesso com filtros
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = 10;
        
        // Coleta filtros
        $filters = [
            'search' => Security::sanitize($_GET['search'] ?? ''),
            'event_type_id' => !empty($_GET['event_type_id']) ? (int) $_GET['event_type_id'] : null,
            'success' => !empty($_GET['success']) ? (int) $_GET['success'] : null
        ];
        
        // Conta total com filtros
        $total = $this->logModel->countSearch($filters);
        $pagination = new Pagination($total, $perPage, $page);
        
        // Busca logs com filtros e paginação
        $logs = $this->logModel->searchWithFilters($filters, $pagination->getLimit(), $pagination->getOffset());
        
        // Formata dados para exibição
        foreach ($logs as &$log) {
            $log['event_type_name'] = UserAccessLog::getEventTypeName($log['event_type_id']);
            $log['success_text'] = $log['success'] ? 'Sucesso' : 'Falha';
            $log['success_badge'] = $log['success'] ? 'success' : 'danger';
        }
        
        $this->view('default/pages/user_access_logs/index.twig', [
            'logs' => $logs,
            'pagination' => $pagination->toArray(),
            'filters' => $filters,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    /**
     * Exibe detalhes de um log específico
     */
    public function show(int $id): void
    {
        $log = $this->logModel->find($id);
        
        if (!$log) {
            $this->redirect(\App\Helpers\Url::to('user-access-logs'));
            return;
        }
        
        // Formata dados
        $log['event_type_name'] = UserAccessLog::getEventTypeName($log['event_type_id']);
        $log['success_text'] = $log['success'] ? 'Sucesso' : 'Falha';
        $log['success_badge'] = $log['success'] ? 'success' : 'danger';
        
        $this->view('default/pages/user_access_logs/show.twig', [
            'log' => $log,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
}
