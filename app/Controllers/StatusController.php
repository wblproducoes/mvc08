<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Status;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\Pagination;

/**
 * Controller de Status
 * 
 * Gerencia todas as operações CRUD relacionadas aos status do sistema.
 * Inclui funcionalidades de:
 * - Listagem com paginação e pesquisa
 * - Criação de novos status
 * - Edição de status existentes
 * - Exclusão (soft delete)
 * - Lixeira (restauração e exclusão permanente)
 * 
 * @package App\Controllers
 * @author Sistema MVC08
 * @version 1.5.0
 */
class StatusController extends Controller
{
    /**
     * @var Status Model de Status
     */
    private Status $statusModel;
    
    /**
     * Construtor do controller
     * 
     * Inicializa o controller pai e instancia o model de Status
     */
    public function __construct()
    {
        parent::__construct();
        $this->statusModel = new Status();
    }
    
    /**
     * Lista todos os status com paginação e pesquisa
     * 
     * Exibe a página de listagem de status com suporte a:
     * - Paginação (10 itens por página)
     * - Pesquisa por nome, tradução ou descrição
     * - Mensagens de sucesso/erro via sessão
     * 
     * @return void
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = 10;
        $search = $_GET['search'] ?? '';
        
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        
        if ($search) {
            $total = $this->statusModel->countSearch($search);
            $pagination = new Pagination($total, $perPage, $page);
            $statuses = $this->statusModel->search($search, $pagination->getLimit(), $pagination->getOffset());
        } else {
            $total = $this->statusModel->count();
            $pagination = new Pagination($total, $perPage, $page);
            $statuses = $this->statusModel->paginate($pagination->getLimit(), $pagination->getOffset());
        }
        
        $this->view('default/pages/status/index.twig', [
            'statuses' => $statuses,
            'pagination' => $pagination->toArray(),
            'search' => $search,
            'error' => $error,
            'success' => $success,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    /**
     * Exibe o formulário de criação de status
     * 
     * Renderiza a view com o formulário para criar um novo status.
     * Inclui token CSRF e mensagens de erro/sucesso da sessão.
     * 
     * @return void
     */
    public function create(): void
    {
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        
        unset($_SESSION['error'], $_SESSION['success']);
        
        $this->view('default/pages/status/create.twig', [
            'csrf_token' => Security::generateCsrfToken(),
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Processa a criação de um novo status
     * 
     * Valida os dados do formulário e cria um novo status no banco.
     * Validações:
     * - Token CSRF
     * - Campos obrigatórios (name, translate)
     * - Nome único (não pode duplicar)
     * 
     * Em caso de sucesso, redireciona para a listagem.
     * Em caso de erro, redireciona de volta ao formulário com mensagem.
     * 
     * @return void
     */
    public function store(): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('status/create'));
            return;
        }
        
        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'translate' => Security::sanitize($_POST['translate'] ?? ''),
            'color' => Security::sanitize($_POST['color'] ?? 'secondary'),
            'description' => Security::sanitize($_POST['description'] ?? ''),
            'dh' => date('Y-m-d H:i:s')
        ];
        
        $validator = new Validator();
        $validator->required($data['name'], 'name')
                  ->required($data['translate'], 'translate');
        
        if ($validator->fails()) {
            $_SESSION['error'] = 'Preencha todos os campos obrigatórios';
            $this->redirect(\App\Helpers\Url::to('status/create'));
            return;
        }
        
        try {
            if ($this->statusModel->create($data)) {
                $_SESSION['success'] = 'Status criado com sucesso';
                $this->redirect(\App\Helpers\Url::to('status'));
            } else {
                $_SESSION['error'] = 'Erro ao criar status';
                $this->redirect(\App\Helpers\Url::to('status/create'));
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['error'] = 'Já existe um status com este nome';
            } else {
                $_SESSION['error'] = 'Erro ao criar status: ' . $e->getMessage();
            }
            $this->redirect(\App\Helpers\Url::to('status/create'));
        }
    }
    
    public function edit(int $id): void
    {
        $status = $this->statusModel->find($id);
        if (!$status) {
            $this->redirect(\App\Helpers\Url::to('status'));
            return;
        }
        
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        
        unset($_SESSION['error'], $_SESSION['success']);
        
        $this->view('default/pages/status/edit.twig', [
            'status' => $status,
            'csrf_token' => Security::generateCsrfToken(),
            'error' => $error,
            'success' => $success
        ]);
    }
    
    public function update(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('status/' . $id . '/edit'));
            return;
        }
        
        $status = $this->statusModel->find($id);
        if (!$status) {
            $_SESSION['error'] = 'Status não encontrado';
            $this->redirect(\App\Helpers\Url::to('status'));
            return;
        }
        
        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'translate' => Security::sanitize($_POST['translate'] ?? ''),
            'color' => Security::sanitize($_POST['color'] ?? 'secondary'),
            'description' => Security::sanitize($_POST['description'] ?? ''),
            'dh_update' => date('Y-m-d H:i:s')
        ];
        
        $validator = new Validator();
        $validator->required($data['name'], 'name')
                  ->required($data['translate'], 'translate');
        
        if ($validator->fails()) {
            $_SESSION['error'] = 'Preencha todos os campos obrigatórios';
            $this->redirect(\App\Helpers\Url::to('status/' . $id . '/edit'));
            return;
        }
        
        try {
            if ($this->statusModel->update($id, $data)) {
                $_SESSION['success'] = 'Status atualizado com sucesso';
                $this->redirect(\App\Helpers\Url::to('status'));
            } else {
                $_SESSION['error'] = 'Erro ao atualizar status';
                $this->redirect(\App\Helpers\Url::to('status/' . $id . '/edit'));
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['error'] = 'Já existe um status com este nome';
            } else {
                $_SESSION['error'] = 'Erro ao atualizar status: ' . $e->getMessage();
            }
            $this->redirect(\App\Helpers\Url::to('status/' . $id . '/edit'));
        }
    }
    
    public function destroy(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('status'));
            return;
        }
        
        if ($this->statusModel->delete($id)) {
            $_SESSION['success'] = 'Status deletado com sucesso';
        } else {
            $_SESSION['error'] = 'Erro ao deletar status';
        }
        
        $this->redirect(\App\Helpers\Url::to('status'));
    }
    
    /**
     * Exibe a lixeira de status
     * 
     * Lista todos os status que foram deletados (soft delete).
     * Permite restaurar ou deletar permanentemente os itens.
     * 
     * @return void
     */
    public function trash(): void
    {
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        
        $statuses = $this->statusModel->trash();
        
        $this->view('default/pages/status/trash.twig', [
            'statuses' => $statuses,
            'error' => $error,
            'success' => $success,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    /**
     * Restaura um status deletado
     * 
     * Remove a marcação de exclusão permitindo que o status
     * volte a aparecer nas listagens normais.
     * 
     * Validações:
     * - Token CSRF
     * 
     * Redireciona de volta para a lixeira com mensagem de sucesso/erro.
     * 
     * @param int $id ID do status a ser restaurado
     * @return void
     */
    public function restore(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('status/trash'));
            return;
        }
        
        if ($this->statusModel->restore($id)) {
            $_SESSION['success'] = 'Status restaurado com sucesso';
        } else {
            $_SESSION['error'] = 'Erro ao restaurar status';
        }
        
        $this->redirect(\App\Helpers\Url::to('status/trash'));
    }
    
    /**
     * Deleta um status permanentemente
     * 
     * ATENÇÃO: Remove completamente o registro do banco de dados.
     * Esta operação é IRREVERSÍVEL!
     * 
     * Validações:
     * - Token CSRF
     * 
     * Redireciona de volta para a lixeira com mensagem de sucesso/erro.
     * 
     * @param int $id ID do status a ser deletado permanentemente
     * @return void
     * 
     * @warning Esta operação não pode ser desfeita!
     */
    public function forceDelete(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('status/trash'));
            return;
        }
        
        if ($this->statusModel->forceDelete($id)) {
            $_SESSION['success'] = 'Status deletado permanentemente';
        } else {
            $_SESSION['error'] = 'Erro ao deletar status permanentemente';
        }
        
        $this->redirect(\App\Helpers\Url::to('status/trash'));
    }
}
