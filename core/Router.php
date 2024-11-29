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
        $uri = self::check_if_url_has_params($uri);
        self::$routes['GET'][$uri] = $callback;
    }

    public static function post($uri, $callback)
    {
        $uri = self::check_if_url_has_params($uri);
        self::$routes['POST'][$uri] = $callback;
    }

    public static function put($uri, $callback)
    {
        $uri = self::check_if_url_has_params($uri);
        self::$routes['PUT'][$uri] = $callback;
    }

    public static function delete($uri, $callback)
    {
        $uri = self::check_if_url_has_params($uri);
        self::$routes['DELETE'][$uri] = $callback;
    }


    public static function resolve()
    {

        self::$request_method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_parameter = null;

        $uri_data = self::check_uri($uri);
        if ($uri_data) {
            $uri = $uri_data['uri'];
            $uri_parameter = $uri_data['uri_param'];

            $callback = self::$routes[self::$request_method][$uri];
            if (is_array($callback)) {
                $controller = $callback[0];
                $method = $callback[1];

                $controller = new $controller();
                //get request data
                $data = self::get_requested_data(self::$request_method);

                //check if method has parameters
                $reflection = new ReflectionMethod($controller, $method);

                if ($reflection->getNumberOfParameters() > 0) {
                    if ($uri_parameter)
                        $controller->$method($uri_parameter, $data);
                    else
                        $controller->$method($data);
                } else {
                    if ($uri_parameter)
                        $controller->$method($uri_parameter);
                    else
                        $controller->$method();
                }
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

    private static function check_if_url_has_params($uri)
    {
        if (substr($uri, -1) == '}') {
            $uri = substr($uri, 0, strpos($uri, '{'));
        }
        return trim($uri, '/');
    }
    /**
     * check if uri is on routes
     * check if uri has parameter or not
     * return false if uri not exist in routes
     */
    private static function check_uri($uri)
    {
        $uri_segments = explode('/', trim($uri, '/'));
        $uri_parameter = array_pop($uri_segments);
        $new_uri = implode('/', $uri_segments);
        $uri = trim($uri, '/');
        if (isset(self::$routes[self::$request_method][$uri])) {
            return [
                'uri' => $uri,
                'uri_param' => null
            ];
        } elseif (isset(self::$routes[self::$request_method][$new_uri])) {
            return [
                'uri' => $new_uri,
                'uri_param' => $uri_parameter
            ];
        }
        return false;
    }
}
