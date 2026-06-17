<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Routes;

class Router
{
    private array $routes = [];

    public function __construct()
    {
        $router = $this;
        require_once __DIR__ . '/api.php';
    }

    public function get(string $path, array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, array $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, array $handler): void
    {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, array $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, array $handler): void
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>\d+)', $path);
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if (empty($uri)) $uri = '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        echo json_encode([
            'error' => true,
            'message' => 'Rota não encontrada',
        ]);
    }

    private function callHandler(array $handler, array $params): void
    {
        [$controllerClass, $method] = $handler;

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Controller {$controllerClass} não encontrado");
        }

        $controller = new $controllerClass();
        $controller->{$method}(...array_values($params));
    }
}
