<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2;

class Router
{
    private array $routes = [];

    public function get(string $pattern, callable $handler)
    {
        $this->addRoute('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler)
    {
        $this->addRoute('POST', $pattern, $handler);
    }

    public function delete(string $pattern, callable $handler)
    {
        $this->addRoute('DELETE', $pattern, $handler);
    }

    private function addRoute(string $method, string $pattern, callable $handler)
    {
        $this->routes[] = [
            'method'  => $method,
            'pattern' => $this->convertPattern($pattern),
            'handler' => $handler,
            'raw'     => $pattern
        ];
    }

    private function convertPattern(string $pattern): string
    {
        // Convert "/links/{id}" → "/links/([^/]+)"
        return preg_replace('#\{([^}]+)\}#', '([^/]+)', $pattern);
    }

    public function dispatch(string $method, string $uri)
    {
        $path = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match('#^' . $route['pattern'] . '$#', $path, $matches)) {
                array_shift($matches); // remove full match
                return call_user_func_array($route['handler'], $matches);
            }
        }

        http_response_code(404);
        echo "404 Not Found";
        return null;
    }
}
