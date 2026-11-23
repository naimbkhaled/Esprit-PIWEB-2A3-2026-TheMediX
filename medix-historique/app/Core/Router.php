<?php
namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $pattern, string $handler): void  { $this->add('GET', $pattern, $handler); }
    public function post(string $pattern, string $handler): void { $this->add('POST', $pattern, $handler); }

    private function add(string $method, string $pattern, string $handler): void
    {
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
        $this->routes[] = [$method, $regex, $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $base = base_path();
        if ($base !== '' && strpos($path, $base) === 0) $path = substr($path, strlen($base));
        $path = '/' . trim($path, '/');

        foreach ($this->routes as [$m, $regex, $handler]) {
            if ($m === $method && preg_match($regex, $path, $match)) {
                $params = array_values(array_filter($match, 'is_string', ARRAY_FILTER_USE_KEY));
                [$class, $action] = explode('@', $handler);
                $cls = 'App\\Controllers\\' . $class;
                (new $cls())->$action(...$params);
                return;
            }
        }
        View::render('erreur', ['titre' => 'Introuvable', 'message' => 'Page introuvable.'], 404);
    }
}
