<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Controlador base. Provee renderizado de vistas con layout
 * y utilidades comunes de autenticacion/autorizacion.
 */
abstract class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'layout/main'): void
    {
        $csrf = $this->csrfToken();
        extract($data, EXTR_SKIP);
        $viewPath = __DIR__ . "/../../views/{$view}.php";

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Vista no encontrada: {$view}");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        $layoutPath = __DIR__ . "/../../views/{$layout}.php";
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    protected function renderPartial(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = __DIR__ . "/../../views/{$view}.php";
        require $viewPath;
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    protected function jsonResponse(array $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    protected function requireRole(string ...$roles): void
    {
        $this->requireAuth();
        if (!in_array($_SESSION['usuario_rol'] ?? '', $roles, true)) {
            http_response_code(403);
            $this->render('errors/403', [], 'layout/main');
            exit;
        }
    }

    protected function verifyCsrf(?string $token = null): void
    {
        $token ??= $_POST['csrf_token'] ?? '';
        $expected = $this->csrfToken();
        if (!hash_equals($expected, $token)) {
            http_response_code(419);
            echo 'Token CSRF invalido. Recargue el formulario e intente nuevamente.';
            exit;
        }
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    protected function oldInput(): array
    {
        $old = $_SESSION['_old_input'] ?? [];
        unset($_SESSION['_old_input']);
        return $old;
    }

    protected function flashErrors(array $errors, array $input = []): void
    {
        $_SESSION['_errors'] = $errors;
        $_SESSION['_old_input'] = $input;
    }

    protected function getErrors(): array
    {
        $errors = $_SESSION['_errors'] ?? [];
        unset($_SESSION['_errors']);
        return $errors;
    }

    protected function flashSuccess(string $message): void
    {
        $_SESSION['_success'] = $message;
    }

    protected function getSuccess(): ?string
    {
        $message = $_SESSION['_success'] ?? null;
        unset($_SESSION['_success']);
        return $message;
    }
}
