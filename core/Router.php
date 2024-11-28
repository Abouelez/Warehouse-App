<?php

namespace Core;

use ReflectionMethod;

class Router
{
    private static $routes = [];
    private static $request_method;

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
        self::$request_method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (isset(self::$routes[self::$request_method][$uri])) {
            $callback = self::$routes[self::$request_method][$uri];

            if (is_array($callback)) {
                $controller = $callback[0];
                $method = $callback[1];

                $controller = new $controller();
                //get request data
                $data = self::get_requested_data(self::$request_method);

                //check if method has parameters
                $reflection = new ReflectionMethod($controller, $method);

                if ($reflection->getNumberOfParameters() > 0)
                    $controller->$method($data);
                else
                    $controller->$method();
            } else {
                call_user_func($callback);
            }
        } else {
            http_response_code(404);
            echo '404 Not Found';
        }
    }
    private static function get_requested_data($request_method)
    {

        $content_type = $_SERVER['CONTENT_TYPE'];

        if ($request_method == 'GET')
            return $_GET;

        //For Json data handling
        if (str_contains($content_type, 'application/json')) {
            return json_decode(file_get_contents('php://input'), true) ?? [];
        }

        // Form data (application/x-www-form-urlencoded or multipart/form-data) handling
        if (
            str_contains($content_type, 'application/x-www-form-urlencoded') ||
            str_contains($content_type, 'multipart/form-data')
        )
            return $_POST;

        return file_get_contents('php://input') ?: []; // For other raw inputs
    }
}
