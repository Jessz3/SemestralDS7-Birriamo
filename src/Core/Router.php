<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router simple para el patron Front Controller.
 * Resuelve rutas de la forma ?controller=usuarios&action=index
 */
final class Router
{
    /** @var array<string, array<string, string>> */
    private array $routes = [];

    public function get(string $path, string $controller, string $action): void
    {
        $this->routes['GET'][$path] = "{$controller}@{$action}";
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->routes['POST'][$path] = "{$controller}@{$action}";
    }

    public function dispatch(string $method, string $path): void
    {
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            require __DIR__ . '/../../views/errors/404.php';
            return;
        }

        [$controllerName, $action] = explode('@', $handler);
        $controllerClass = "App\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo "Controlador {$controllerClass} no encontrado.";
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            http_response_code(500);
            echo "Accion {$action} no encontrada en {$controllerClass}.";
            return;
        }

        $controller->{$action}();
    }
}
