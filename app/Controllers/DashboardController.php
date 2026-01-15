<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Controller do Dashboard
 * 
 * Gerencia a página principal do sistema após o login.
 * Exibe informações do usuário logado e menu de navegação.
 * 
 * @package App\Controllers
 * @version 1.5.0
 */
class DashboardController extends Controller
{
    /**
     * Exibe a página do dashboard
     * 
     * Renderiza a view principal do sistema com informações
     * do usuário logado obtidas da sessão.
     * 
     * @return void
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->view('default/pages/dashboard.twig', [
            'user' => [
                'name' => $_SESSION['name'] ?? 'Usuário',
                'username' => $_SESSION['username'] ?? ''
            ]
        ]);
    }
}
