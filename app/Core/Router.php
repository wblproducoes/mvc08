<?php

namespace App\Core;

/**
 * Sistema de roteamento
 * 
 * @package App\Core
 */
class Router
{
    private array $routes = [];
    private array $middlewares = [];
    
    /**
     * Adiciona rota GET
     * 
     * @param string $path
     * @param string $controller
     * @param string $method
     * @return self
     */
    public function get(string $path, string $controller, string $method): self
    {
        $this->addRoute('GET', $path, $controller, $method);
        return $this;
    }
    
    /**
     * Adiciona rota POST
     * 
     * @param string $path
     * @param string $controller
     * @param string $method
     * @return self
     */
    public function post(string $path, string $controller, string $method): self
    {
        $this->addRoute('POST', $path, $controller, $method);
        return $this;
    }
    
    /**
     * Adiciona middleware à última rota
     * 
     * @param string $middleware
     * @return self
     */
    public function middleware(string $middleware): self
    {
        $lastRoute = array_key_last($this->routes);
        if ($lastRoute !== null) {
            $this->routes[$lastRoute]['middleware'][] = $middleware;
        }
        return $this;
    }
    
    /**
     * Adiciona rota
     * 
     * @param string $method
     * @param string $path
     * @param string $controller
     * @param string $action
     */
    private function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
            'middleware' => []
        ];
    }
    
    /**
     * Executa roteamento
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove o prefixo do diretório base se existir
        // Pega o diretório base da aplicação (ex: /mvc08)
        $scriptName = $_SERVER['SCRIPT_NAME']; // ex: /mvc08/public/index.php
        $basePath = str_replace('/public/index.php', '', $scriptName); // ex: /mvc08
        
        if ($basePath !== '' && $basePath !== '/' && strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        
        // Garante que sempre começa com /
        if (empty($requestUri) || $requestUri[0] !== '/') {
            $requestUri = '/' . $requestUri;
        }
        
        // DEBUG TEMPORÁRIO - REMOVER DEPOIS
        if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
            echo "<!-- DEBUG: Request URI: {$requestUri} | Method: {$requestMethod} -->\n";
            echo "<!-- DEBUG: Script Name: {$_SERVER['SCRIPT_NAME']} -->\n";
            echo "<!-- DEBUG: Base Path: {$basePath} -->\n";
            echo "<!-- DEBUG: Rotas registradas: " . count($this->routes) . " -->\n";
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $requestUri)) {
                // Executa middlewares
                foreach ($route['middleware'] as $middleware) {
                    $middlewareClass = "App\\Middlewares\\{$middleware}";
                    if (class_exists($middlewareClass)) {
                        $middlewareInstance = new $middlewareClass();
                        if (!$middlewareInstance->handle()) {
                            return;
                        }
                    }
                }
                
                // Executa controller
                $controllerClass = "App\\Controllers\\{$route['controller']}";
                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    $action = $route['action'];
                    $controller->$action();
                    return;
                }
            }
        }
        
        http_response_code(404);
        echo "404 - Página não encontrada";
        
        // DEBUG TEMPORÁRIO - REMOVER DEPOIS
        if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
            echo "<br><br>DEBUG INFO:<br>";
            echo "Request URI processado: {$requestUri}<br>";
            echo "Request Method: {$requestMethod}<br>";
            echo "Base Path: {$basePath}<br>";
            echo "Rotas disponíveis:<br>";
            foreach ($this->routes as $route) {
                echo "- {$route['method']} {$route['path']}<br>";
            }
        }
    }
    
    /**
     * Verifica se o caminho corresponde
     * 
     * @param string $routePath
     * @param string $requestPath
     * @return bool
     */
    private function matchPath(string $routePath, string $requestPath): bool
    {
        return $routePath === $requestPath;
    }
}
