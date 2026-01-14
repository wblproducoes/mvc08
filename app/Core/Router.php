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
