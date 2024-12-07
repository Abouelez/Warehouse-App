<?php

namespace Core;

use App\Helpers\RequestHelper;
use App\Middlewares\Middleware;
use ReflectionMethod;

class Router
{
    use RequestHelper;
    private static $routes = [];
    private static $request_method;
    private static $uri_parameter = null;
    // public function __call($name, $arguments)
    // {
    //     echo "Method $name doesn't exist";
    // }
    public function routes(): array
    {
        return self::$routes;
    }
    static function middleware($middleware, $method, $uri)
    {
        if (
            $_SERVER['REQUEST_METHOD'] == $method &&
            trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') == trim($uri, '/')
        )
            Middleware::apply_middleware($middleware);
    }
    public static function get($uri, $callback, $middleware = null)
    {
        if ($middleware) self::middleware($middleware, 'GET', $uri);

        $uri = self::check_if_url_has_params($uri);
        self::$routes['GET'][$uri] = $callback;
    }

    public static function post($uri, $callback, $middleware = null)
    {
        if ($middleware) self::middleware($middleware, 'POST', $uri);

        $uri = self::check_if_url_has_params($uri);
        self::$routes['POST'][$uri] = $callback;
    }

    public static function put($uri, $callback, $middleware = null)
    {
        if ($middleware) self::middleware($middleware, 'POST', $uri);

        $uri = self::check_if_url_has_params($uri);
        self::$routes['PUT'][$uri] = $callback;
    }

    public static function delete($uri, $callback, $middleware = null)
    {
        if ($middleware) self::middleware($middleware, 'POST', $uri);

        $uri = self::check_if_url_has_params($uri);
        self::$routes['DELETE'][$uri] = $callback;
    }


    public static function resolve()
    {
        self::get_method();   //get request method

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_data = self::check_uri($uri); //get uri data 
        if ($uri_data) {

            $uri = $uri_data['uri'];
            self::$uri_parameter = $uri_data['uri_param']; //if uri has a parameter => test/{id}

            $callback = self::$routes[self::$request_method][$uri];
            self::handle_callback_func($callback);
        } else {
            http_response_code(404);
            echo '404 Not Found';
        }
    }

    private static function handle_callback_func($callback)
    {
        if (is_array($callback)) {
            $controller = $callback[0];
            $method = $callback[1];

            $controller = new $controller();
            //get request data
            $data = self::get_requested_data(self::$request_method);

            //check if method has parameters
            $reflection = new ReflectionMethod($controller, $method);

            if ($reflection->getNumberOfParameters() > 0) {
                if (self::$uri_parameter)
                    $controller->$method(self::$uri_parameter, $data);
                else
                    $controller->$method($data);
            } else {
                if (self::$uri_parameter)
                    $controller->$method(self::$uri_parameter);
                else
                    $controller->$method();
            }
        } else {
            call_user_func($callback);
        }
    }

    private static function get_method()
    {
        self::$request_method = $_SERVER['REQUEST_METHOD'];
        if (isset($_POST['__method'])) {
            // var_dump($_POST['__method']);
            self::$request_method = strtoupper($_POST['__method']);
            unset($_POST['__method']);
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

    function __destruct()
    {
        self::$uri_parameter = null;
    }
}
