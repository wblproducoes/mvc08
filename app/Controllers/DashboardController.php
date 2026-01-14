<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Controller do dashboard
 * 
 * @package App\Controllers
 */
class DashboardController extends Controller
{
    /**
     * Exibe dashboard
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
