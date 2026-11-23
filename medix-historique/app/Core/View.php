<?php
namespace App\Core;

final class View
{
    public static function render(string $view, array $data = [], int $code = 200): void
    {
        http_response_code($code);
        extract($data, EXTR_SKIP);
        ob_start();
        require APP_PATH . '/Views/' . $view . '.php';
        $content = ob_get_clean();
        require APP_PATH . '/Views/layouts/main.php';
    }
}
