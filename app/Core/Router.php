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
     * Adiciona rota PUT
     * 
     * @param string $path
     * @param string $controller
     * @param string $method
     * @return self
     */
    public function put(string $path, string $controller, string $method): self
    {
        $this->addRoute('PUT', $path, $controller, $method);
        return $this;
    }
    
    /**
     * Adiciona rota DELETE
     * 
     * @param string $path
     * @param string $controller
     * @param string $method
     * @return self
     */
    public function delete(string $path, string $controller, string $method): self
    {
        $this->addRoute('DELETE', $path, $controller, $method);
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
        
        // Suporte para method spoofing (PUT, DELETE via POST)
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }
        
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove o prefixo do diretório base se existir
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = str_replace('/public/index.php', '', $scriptName);
        
        if ($basePath !== '' && $basePath !== '/' && strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        
        // Garante que sempre começa com /
        if (empty($requestUri) || $requestUri[0] !== '/') {
            $requestUri = '/' . $requestUri;
        }
        
        foreach ($this->routes as $route) {
            $params = [];
            if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $requestUri, $params)) {
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
                    
                    // Passa parâmetros para o método
                    if (!empty($params)) {
                        $controller->$action(...array_values($params));
                    } else {
                        $controller->$action();
                    }
                    return;
                }
            }
        }
        
        http_response_code(404);
        echo "404 - Página não encontrada";
    }
    
    /**
     * Verifica se o caminho corresponde e extrai parâmetros
     * 
     * @param string $routePath
     * @param string $requestPath
     * @param array &$params
     * @return bool
     */
    private function matchPath(string $routePath, string $requestPath, array &$params = []): bool
    {
        // Converte {id} em regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $requestPath, $matches)) {
            // Remove o primeiro elemento (match completo)
            array_shift($matches);
            
            // Extrai nomes dos parâmetros
            preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $routePath, $paramNames);
            
            // Mapeia parâmetros
            foreach ($paramNames[1] as $index => $name) {
                $params[$name] = $matches[$index] ?? null;
            }
            
            return true;
        }
        
        return false;
    }
}
