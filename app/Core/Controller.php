<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Controller base
 * 
 * @package App\Core
 */
abstract class Controller
{
    protected Environment $twig;
    
    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../app/Views');
        $this->twig = new Environment($loader, [
            'cache' => __DIR__ . '/../../storage/cache',
            'auto_reload' => true,
            'debug' => $_ENV['APP_DEBUG'] ?? false
        ]);
        
        // Adiciona variáveis globais
        $this->twig->addGlobal('app_name', $_ENV['APP_NAME']);
        $this->twig->addGlobal('app_url', $_ENV['APP_URL']);
        
        // Adiciona função URL para gerar links corretos
        $this->twig->addFunction(new \Twig\TwigFunction('url', function($path = '') {
            return \App\Helpers\Url::to($path);
        }));
        
        $this->twig->addFunction(new \Twig\TwigFunction('asset', function($path) {
            return \App\Helpers\Url::asset($path);
        }));
    }
    
    /**
     * Renderiza view
     * 
     * @param string $view
     * @param array $data
     */
    protected function view(string $view, array $data = []): void
    {
        echo $this->twig->render($view, $data);
    }
    
    /**
     * Redireciona
     * 
     * @param string $url
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Retorna JSON
     * 
     * @param array $data
     * @param int $status
     */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
