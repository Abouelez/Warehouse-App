<?php

namespace Core;

class Router
{
    private static $routes = [];

    public function routes(): array
    {
        return self::$routes;
    }

    public static function get($uri, $callback)
    {
        self::$routes['GET'][$uri] = $callback;
    }

    public static function post($uri, $callback)
    {
        self::$routes['POST'][$uri] = $callback;
    }

    public static function put($uri, $callback)
    {
        self::$routes['PUT'][$uri] = $callback;
    }

    public static function delete($uri, $callback)
    {
        self::$routes['DELETE'][$uri] = $callback;
    }


    public static function resolve()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (isset(self::$routes[$method][$uri])) {
            $callback = self::$routes[$method][$uri];

            if (is_array($callback)) {
                $controller = $callback[0];
                $method = $callback[1];

                $controller = new $controller();
                $controller->$method();
            } else {
                call_user_func($callback);
            }
        } else {
            http_response_code(404);
            echo '404 Not Found';
        }
    }
}
