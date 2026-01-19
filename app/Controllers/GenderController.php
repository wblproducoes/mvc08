<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Gender;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\Pagination;

/**
 * Controller de Gêneros
 * 
 * Gerencia todas as operações CRUD relacionadas aos gêneros do sistema.
 * 
 * @package App\Controllers
 * @author Sistema MVC08
 * @version 1.0.0
 */
class GenderController extends Controller
{
    /**
     * @var Gender Model de Gênero
     */
    private Gender $genderModel;
    
    /**
     * Construtor do controller
     */
    public function __construct()
    {
        parent::__construct();
        $this->genderModel = new Gender();
    }
    
    /**
     * Lista todos os gêneros
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = 10;
        
        $total = $this->genderModel->count();
        $pagination = new Pagination($total, $perPage, $page);
        $genders = $this->genderModel->paginate($pagination->getLimit(), $pagination->getOffset());
        
        $this->view('default/pages/genders/index.twig', [
            'genders' => $genders,
            'pagination' => $pagination->toArray(),
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    /**
     * Exibe formulário de criação
     */
    public function create(): void
    {
        $this->view('default/pages/genders/create.twig', [
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    /**
     * Salva novo gênero
     */
    public function store(): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 403);
        }
        
        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'translate' => Security::sanitize($_POST['translate'] ?? ''),
            'description' => Security::sanitize($_POST['description'] ?? '')
        ];
        
        // Validação
        $validator = new Validator();
        $validator->required($data['name'], 'name')
                  ->required($data['translate'], 'translate');
        
        if ($validator->fails()) {
            $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Verifica se nome já existe
        if ($this->genderModel->findByName($data['name'])) {
            $this->json(['success' => false, 'message' => 'Este gênero já existe'], 422);
        }
        
        // Remove campos vazios opcionais
        if (empty($data['description'])) unset($data['description']);
        
        $data['dh'] = date('Y-m-d H:i:s');
        
        $genderId = $this->genderModel->create($data);
        
        if ($genderId) {
            $this->json([
                'success' => true,
                'message' => 'Gênero criado com sucesso',
                'redirect' => \App\Helpers\Url::to('genders')
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Erro ao criar gênero'], 500);
        }
    }
    
    /**
     * Exibe formulário de edição
     */
    public function edit(int $id): void
    {
        $gender = $this->genderModel->find($id);
        
        if (!$gender) {
            $this->redirect(\App\Helpers\Url::to('genders'));
            return;
        }
        
        $this->view('default/pages/genders/edit.twig', [
            'gender' => $gender,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    /**
     * Atualiza gênero
     */
    public function update(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 403);
        }
        
        $gender = $this->genderModel->find($id);
        if (!$gender) {
            $this->json(['success' => false, 'message' => 'Gênero não encontrado'], 404);
        }
        
        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'translate' => Security::sanitize($_POST['translate'] ?? ''),
            'description' => Security::sanitize($_POST['description'] ?? '')
        ];
        
        // Validação
        $validator = new Validator();
        $validator->required($data['name'], 'name')
                  ->required($data['translate'], 'translate');
        
        if ($validator->fails()) {
            $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Verifica se nome já existe (exceto o próprio gênero)
        $existingGender = $this->genderModel->findByName($data['name']);
        if ($existingGender && $existingGender['id'] != $id) {
            $this->json(['success' => false, 'message' => 'Este gênero já existe'], 422);
        }
        
        // Remove campos vazios opcionais
        if (empty($data['description'])) unset($data['description']);
        
        $data['dh_update'] = date('Y-m-d H:i:s');
        
        if ($this->genderModel->update($id, $data)) {
            $this->json([
                'success' => true,
                'message' => 'Gênero atualizado com sucesso',
                'redirect' => \App\Helpers\Url::to('genders')
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Erro ao atualizar gênero'], 500);
        }
    }
    
    /**
     * Deleta gênero (soft delete)
     */
    public function destroy(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 403);
        }
        
        $gender = $this->genderModel->find($id);
        if (!$gender) {
            $this->json(['success' => false, 'message' => 'Gênero não encontrado'], 404);
        }
        
        if ($this->genderModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'Gênero deletado com sucesso']);
        } else {
            $this->json(['success' => false, 'message' => 'Erro ao deletar gênero'], 500);
        }
    }
    
    /**
     * Exibe lixeira de gêneros
     */
    public function trash(): void
    {
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        
        $genders = $this->genderModel->trash();
        
        $this->view('default/pages/genders/trash.twig', [
            'genders' => $genders,
            'error' => $error,
            'success' => $success,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    /**
     * Restaura gênero deletado
     */
    public function restore(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('genders/trash'));
            return;
        }
        
        if ($this->genderModel->restore($id)) {
            $_SESSION['success'] = 'Gênero restaurado com sucesso';
        } else {
            $_SESSION['error'] = 'Erro ao restaurar gênero';
        }
        
        $this->redirect(\App\Helpers\Url::to('genders/trash'));
    }
    
    /**
     * Deleta gênero permanentemente
     */
    public function forceDelete(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('genders/trash'));
            return;
        }
        
        if ($this->genderModel->forceDelete($id)) {
            $_SESSION['success'] = 'Gênero deletado permanentemente';
        } else {
            $_SESSION['error'] = 'Erro ao deletar gênero permanentemente';
        }
        
        $this->redirect(\App\Helpers\Url::to('genders/trash'));
    }
}
