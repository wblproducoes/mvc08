<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Level;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\Pagination;

/**
 * Controller de Níveis de Acesso
 * 
 * Gerencia operações CRUD de níveis de acesso do sistema.
 * Funcionalidades: listagem, criação, edição, exclusão e lixeira.
 * 
 * @package App\Controllers
 * @version 1.5.0
 */
class LevelController extends Controller
{
    private Level $levelModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->levelModel = new Level();
    }
    
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = 10;
        $search = $_GET['search'] ?? '';
        
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        
        if ($search) {
            $total = $this->levelModel->countSearch($search);
            $pagination = new Pagination($total, $perPage, $page);
            $levels = $this->levelModel->search($search, $pagination->getLimit(), $pagination->getOffset());
        } else {
            $total = $this->levelModel->count();
            $pagination = new Pagination($total, $perPage, $page);
            $levels = $this->levelModel->paginate($pagination->getLimit(), $pagination->getOffset());
        }
        
        $this->view('default/pages/levels/index.twig', [
            'levels' => $levels,
            'pagination' => $pagination->toArray(),
            'search' => $search,
            'error' => $error,
            'success' => $success,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    public function create(): void
    {
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        $this->view('default/pages/levels/create.twig', ['csrf_token' => Security::generateCsrfToken(), 'error' => $error, 'success' => $success]);
    }
    
    public function store(): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('levels/create'));
            return;
        }
        $data = ['name' => Security::sanitize($_POST['name'] ?? ''), 'translate' => Security::sanitize($_POST['translate'] ?? ''), 'description' => Security::sanitize($_POST['description'] ?? ''), 'dh' => date('Y-m-d H:i:s')];
        $validator = new Validator();
        $validator->required($data['name'], 'name')->required($data['translate'], 'translate');
        if ($validator->fails()) {
            $_SESSION['error'] = 'Preencha todos os campos obrigatórios';
            $this->redirect(\App\Helpers\Url::to('levels/create'));
            return;
        }
        try {
            if ($this->levelModel->create($data)) {
                $_SESSION['success'] = 'Nível criado com sucesso';
                $this->redirect(\App\Helpers\Url::to('levels'));
            } else {
                $_SESSION['error'] = 'Erro ao criar nível';
                $this->redirect(\App\Helpers\Url::to('levels/create'));
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['error'] = 'Já existe um nível com este nome';
            } else {
                $_SESSION['error'] = 'Erro ao criar nível: ' . $e->getMessage();
            }
            $this->redirect(\App\Helpers\Url::to('levels/create'));
        }
    }
    
    public function edit(int $id): void
    {
        $level = $this->levelModel->find($id);
        if (!$level) { $this->redirect(\App\Helpers\Url::to('levels')); return; }
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        $this->view('default/pages/levels/edit.twig', ['level' => $level, 'csrf_token' => Security::generateCsrfToken(), 'error' => $error, 'success' => $success]);
    }
    
    public function update(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('levels/' . $id . '/edit'));
            return;
        }
        $level = $this->levelModel->find($id);
        if (!$level) { $_SESSION['error'] = 'Nível não encontrado'; $this->redirect(\App\Helpers\Url::to('levels')); return; }
        $data = ['name' => Security::sanitize($_POST['name'] ?? ''), 'translate' => Security::sanitize($_POST['translate'] ?? ''), 'description' => Security::sanitize($_POST['description'] ?? ''), 'dh_update' => date('Y-m-d H:i:s')];
        $validator = new Validator();
        $validator->required($data['name'], 'name')->required($data['translate'], 'translate');
        if ($validator->fails()) {
            $_SESSION['error'] = 'Preencha todos os campos obrigatórios';
            $this->redirect(\App\Helpers\Url::to('levels/' . $id . '/edit'));
            return;
        }
        try {
            if ($this->levelModel->update($id, $data)) {
                $_SESSION['success'] = 'Nível atualizado com sucesso';
                $this->redirect(\App\Helpers\Url::to('levels'));
            } else {
                $_SESSION['error'] = 'Erro ao atualizar nível';
                $this->redirect(\App\Helpers\Url::to('levels/' . $id . '/edit'));
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['error'] = 'Já existe um nível com este nome';
            } else {
                $_SESSION['error'] = 'Erro ao atualizar nível: ' . $e->getMessage();
            }
            $this->redirect(\App\Helpers\Url::to('levels/' . $id . '/edit'));
        }
    }
    
    public function destroy(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('levels'));
            return;
        }
        
        if ($this->levelModel->delete($id)) {
            $_SESSION['success'] = 'Nível deletado com sucesso';
        } else {
            $_SESSION['error'] = 'Erro ao deletar nível';
        }
        
        $this->redirect(\App\Helpers\Url::to('levels'));
    }
    
    public function trash(): void
    {
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        
        $levels = $this->levelModel->trash();
        
        $this->view('default/pages/levels/trash.twig', [
            'levels' => $levels,
            'error' => $error,
            'success' => $success,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    public function restore(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('levels/trash'));
            return;
        }
        
        if ($this->levelModel->restore($id)) {
            $_SESSION['success'] = 'Nível restaurado com sucesso';
        } else {
            $_SESSION['error'] = 'Erro ao restaurar nível';
        }
        
        $this->redirect(\App\Helpers\Url::to('levels/trash'));
    }
    
    public function forceDelete(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('levels/trash'));
            return;
        }
        
        if ($this->levelModel->forceDelete($id)) {
            $_SESSION['success'] = 'Nível deletado permanentemente';
        } else {
            $_SESSION['error'] = 'Erro ao deletar nível permanentemente';
        }
        
        $this->redirect(\App\Helpers\Url::to('levels/trash'));
    }
}
