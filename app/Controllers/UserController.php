<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Status;
use App\Models\Level;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\Pagination;
use App\Helpers\Upload;

/**
 * Controller de Usuários
 * 
 * Gerencia todas as operações CRUD relacionadas aos usuários do sistema.
 * Inclui funcionalidades de:
 * - Listagem com paginação e filtros avançados (nome, email, status, nível)
 * - Criação de novos usuários
 * - Edição de usuários existentes
 * - Exclusão (soft delete)
 * - Lixeira (restauração e exclusão permanente)
 * - Proteção especial para usuário ID 1 (administrador master)
 * 
 * @package App\Controllers
 * @author Sistema MVC08
 * @version 1.5.0
 */
class UserController extends Controller
{
    /**
     * @var User Model de Usuário
     */
    private User $userModel;
    
    /**
     * @var Status Model de Status
     */
    private Status $statusModel;
    
    /**
     * @var Level Model de Nível de Acesso
     */
    private Level $levelModel;
    
    /**
     * Construtor do controller
     * 
     * Inicializa o controller pai e instancia os models necessários
     */
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->statusModel = new Status();
        $this->levelModel = new Level();
    }
    
    /**
     * Lista todos os usuários
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = 10;
        $search = $_GET['search'] ?? '';
        $statusId = $_GET['status_id'] ?? '';
        $levelId = $_GET['level_id'] ?? '';
        
        $filters = [
            'search' => $search,
            'status_id' => $statusId,
            'level_id' => $levelId
        ];
        
        if ($search || $statusId || $levelId) {
            $total = $this->userModel->countSearch($filters);
            $pagination = new Pagination($total, $perPage, $page);
            $users = $this->userModel->searchWithFilters($filters, $pagination->getLimit(), $pagination->getOffset());
        } else {
            $total = $this->userModel->count();
            $pagination = new Pagination($total, $perPage, $page);
            $users = $this->userModel->paginateWithRelations($pagination->getLimit(), $pagination->getOffset());
        }
        
        $allStatuses = $this->statusModel->allActive();
        $allLevels = $this->levelModel->allActive();
        
        $this->view('default/pages/users/index.twig', [
            'users' => $users,
            'pagination' => $pagination->toArray(),
            'search' => $search,
            'filters' => $filters,
            'all_statuses' => $allStatuses,
            'all_levels' => $allLevels,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    /**
     * Exibe formulário de criação
     */
    public function create(): void
    {
        $statuses = $this->statusModel->allActive();
        $levels = $this->levelModel->allActive();
        
        $this->view('default/pages/users/create.twig', [
            'csrf_token' => Security::generateCsrfToken(),
            'statuses' => $statuses,
            'levels' => $levels
        ]);
    }
    
    /**
     * Salva novo usuário
     */
    public function store(): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 403);
        }
        
        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'alias' => Security::sanitize($_POST['alias'] ?? ''),
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'cpf' => Security::sanitize($_POST['cpf'] ?? ''),
            'birth_date' => $_POST['birth_date'] ?? null,
            'gender_id' => !empty($_POST['gender_id']) ? (int) $_POST['gender_id'] : null,
            'phone_home' => Security::sanitize($_POST['phone_home'] ?? ''),
            'phone_mobile' => Security::sanitize($_POST['phone_mobile'] ?? ''),
            'phone_message' => Security::sanitize($_POST['phone_message'] ?? ''),
            'username' => Security::sanitize($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'status_id' => (int) ($_POST['status_id'] ?? 1),
            'level_id' => (int) ($_POST['level_id'] ?? 11),
            'unique_code' => uniqid('user_', true)
        ];
        
        // Upload de foto
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload = new Upload();
            $result = $upload->image($_FILES['photo'], 'users', 400, 400);
            
            if ($result['success']) {
                $data['photo'] = $result['filename'];
            } else {
                $this->json(['success' => false, 'message' => $result['message']], 422);
            }
        }
        
        // Validação
        $validator = new Validator();
        $validator->required($data['name'], 'name')
                  ->required($data['email'], 'email')
                  ->email($data['email'], 'email')
                  ->required($data['username'], 'username')
                  ->required($data['password'], 'password')
                  ->min($data['password'], 6, 'password');
        
        // Validação de confirmação de senha
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';
        if ($data['password'] !== $passwordConfirmation) {
            $this->json(['success' => false, 'message' => 'As senhas não coincidem'], 422);
        }
        
        // Validação de CPF
        if (!empty($data['cpf'])) {
            $cpf = preg_replace('/\D/', '', $data['cpf']);
            if (!$this->validateCPF($cpf)) {
                $this->json(['success' => false, 'message' => 'CPF inválido'], 422);
            }
            $data['cpf'] = $cpf; // Salva apenas números
        }
        
        if ($validator->fails()) {
            $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Verifica se email já existe
        if ($this->userModel->findByEmail($data['email'])) {
            $this->json(['success' => false, 'message' => 'Email já cadastrado'], 422);
        }
        
        // Verifica se username já existe
        if ($this->userModel->findByUsername($data['username'])) {
            $this->json(['success' => false, 'message' => 'Username já cadastrado'], 422);
        }
        
        // Hash da senha
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['dh'] = date('Y-m-d H:i:s');
        
        // Remove campos vazios opcionais
        if (empty($data['alias'])) unset($data['alias']);
        if (empty($data['cpf'])) unset($data['cpf']);
        if (empty($data['birth_date'])) unset($data['birth_date']);
        if (empty($data['gender_id'])) unset($data['gender_id']);
        if (empty($data['phone_home'])) unset($data['phone_home']);
        if (empty($data['phone_mobile'])) unset($data['phone_mobile']);
        if (empty($data['phone_message'])) unset($data['phone_message']);
        
        $userId = $this->userModel->create($data);
        
        if ($userId) {
            $this->json([
                'success' => true,
                'message' => 'Usuário criado com sucesso',
                'redirect' => \App\Helpers\Url::to('users')
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Erro ao criar usuário'], 500);
        }
    }
    
    /**
     * Valida CPF
     * 
     * @param string $cpf CPF apenas com números
     * @return bool
     */
    private function validateCPF(string $cpf): bool
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/\D/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        
        // Valida primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        if (intval($cpf[9]) != $digit1) {
            return false;
        }
        
        // Valida segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        if (intval($cpf[10]) != $digit2) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Exibe formulário de edição
     */
    public function edit(int $id): void
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->redirect(\App\Helpers\Url::to('users'));
            return;
        }
        
        $statuses = $this->statusModel->allActive();
        $levels = $this->levelModel->allActive();
        
        $this->view('default/pages/users/edit.twig', [
            'user' => $user,
            'csrf_token' => Security::generateCsrfToken(),
            'statuses' => $statuses,
            'levels' => $levels
        ]);
    }
    
    /**
     * Atualiza usuário
     */
    public function update(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 403);
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Usuário não encontrado'], 404);
        }
        
        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'alias' => Security::sanitize($_POST['alias'] ?? ''),
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'cpf' => Security::sanitize($_POST['cpf'] ?? ''),
            'birth_date' => $_POST['birth_date'] ?? null,
            'gender_id' => !empty($_POST['gender_id']) ? (int) $_POST['gender_id'] : null,
            'phone_home' => Security::sanitize($_POST['phone_home'] ?? ''),
            'phone_mobile' => Security::sanitize($_POST['phone_mobile'] ?? ''),
            'phone_message' => Security::sanitize($_POST['phone_message'] ?? ''),
            'username' => Security::sanitize($_POST['username'] ?? ''),
            'status_id' => (int) ($_POST['status_id'] ?? 1),
            'level_id' => (int) ($_POST['level_id'] ?? 11)
        ];
        
        // Upload de nova foto
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload = new Upload();
            $result = $upload->image($_FILES['photo'], 'users', 400, 400);
            
            if ($result['success']) {
                // Remove foto antiga se existir
                if (!empty($user['photo'])) {
                    $upload->delete($user['photo']);
                }
                $data['photo'] = $result['filename'];
            } else {
                $this->json(['success' => false, 'message' => $result['message']], 422);
            }
        }
        
        // Remove foto se solicitado
        if (isset($_POST['remove_photo']) && $_POST['remove_photo'] == '1') {
            if (!empty($user['photo'])) {
                $upload = new Upload();
                $upload->delete($user['photo']);
            }
            $data['photo'] = null;
        }
        
        // Validação
        $validator = new Validator();
        $validator->required($data['name'], 'name')
                  ->required($data['email'], 'email')
                  ->email($data['email'], 'email')
                  ->required($data['username'], 'username');
        
        // Validação de CPF
        if (!empty($data['cpf'])) {
            $cpf = preg_replace('/\D/', '', $data['cpf']);
            if (!$this->validateCPF($cpf)) {
                $this->json(['success' => false, 'message' => 'CPF inválido'], 422);
            }
            $data['cpf'] = $cpf; // Salva apenas números
        }
        
        if ($validator->fails()) {
            $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Verifica se email já existe (exceto o próprio usuário)
        $existingUser = $this->userModel->findByEmail($data['email']);
        if ($existingUser && $existingUser['id'] != $id) {
            $this->json(['success' => false, 'message' => 'Email já cadastrado'], 422);
        }
        
        // Verifica se username já existe (exceto o próprio usuário)
        $existingUser = $this->userModel->findByUsername($data['username']);
        if ($existingUser && $existingUser['id'] != $id) {
            $this->json(['success' => false, 'message' => 'Username já cadastrado'], 422);
        }
        
        // Se senha foi informada, atualiza
        if (!empty($_POST['password'])) {
            $validator->min($_POST['password'], 6, 'password');
            if ($validator->fails()) {
                $this->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            
            $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }
        
        $data['dh_update'] = date('Y-m-d H:i:s');
        
        // Remove campos vazios opcionais
        if (empty($data['alias'])) unset($data['alias']);
        if (empty($data['cpf'])) unset($data['cpf']);
        if (empty($data['birth_date'])) unset($data['birth_date']);
        if (empty($data['gender_id'])) unset($data['gender_id']);
        if (empty($data['phone_home'])) unset($data['phone_home']);
        if (empty($data['phone_mobile'])) unset($data['phone_mobile']);
        if (empty($data['phone_message'])) unset($data['phone_message']);
        
        if ($this->userModel->update($id, $data)) {
            $this->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso',
                'redirect' => \App\Helpers\Url::to('users')
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Erro ao atualizar usuário'], 500);
        }
    }
    
    /**
     * Deleta usuário (soft delete)
     */
    public function destroy(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 403);
        }
        
        // Não permite deletar o usuário master (ID 1)
        if ($id == 1) {
            $this->json(['success' => false, 'message' => 'O usuário administrador principal não pode ser deletado'], 403);
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Usuário não encontrado'], 404);
        }
        
        // Não permite deletar o próprio usuário
        if ($id == $_SESSION['user_id']) {
            $this->json(['success' => false, 'message' => 'Você não pode deletar seu próprio usuário'], 403);
        }
        
        if ($this->userModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'Usuário deletado com sucesso']);
        } else {
            $this->json(['success' => false, 'message' => 'Erro ao deletar usuário'], 500);
        }
    }
    
    /**
     * Exibe lixeira de usuários
     */
    public function trash(): void
    {
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        
        $users = $this->userModel->trash();
        
        $this->view('default/pages/users/trash.twig', [
            'users' => $users,
            'error' => $error,
            'success' => $success,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }
    
    /**
     * Restaura usuário deletado
     */
    public function restore(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('users/trash'));
            return;
        }
        
        if ($this->userModel->restore($id)) {
            $_SESSION['success'] = 'Usuário restaurado com sucesso';
        } else {
            $_SESSION['error'] = 'Erro ao restaurar usuário';
        }
        
        $this->redirect(\App\Helpers\Url::to('users/trash'));
    }
    
    /**
     * Deleta usuário permanentemente
     */
    public function forceDelete(int $id): void
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF inválido';
            $this->redirect(\App\Helpers\Url::to('users/trash'));
            return;
        }
        
        // Não permite deletar o usuário master (ID 1)
        if ($id == 1) {
            $_SESSION['error'] = 'O usuário administrador principal não pode ser deletado';
            $this->redirect(\App\Helpers\Url::to('users/trash'));
            return;
        }
        
        // Não permite deletar o próprio usuário
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Você não pode deletar seu próprio usuário';
            $this->redirect(\App\Helpers\Url::to('users/trash'));
            return;
        }
        
        if ($this->userModel->forceDelete($id)) {
            $_SESSION['success'] = 'Usuário deletado permanentemente';
        } else {
            $_SESSION['error'] = 'Erro ao deletar usuário permanentemente';
        }
        
        $this->redirect(\App\Helpers\Url::to('users/trash'));
    }
}
