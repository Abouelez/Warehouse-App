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
    private static $has_param = false;
    private static $auth_routes = [];
    // public function __call($name, $arguments)
    // {
    //     echo "Method $name doesn't exist";
    // }
    public function routes(): array
    {
        return self::$routes;
    }
    // static function middleware($middleware, $method, $uri)
    // {

    //     $s_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    //     $s_uri = self::check_uri($s_uri)['uri'];

    //     if (
    //         $_SERVER['REQUEST_METHOD'] == $method &&
    //         $s_uri == trim($uri, '/')
    //     )
    //         Middleware::apply_middleware($middleware);
    // }
    public static function get($uri, $callback, $middleware = null)
    {

        self::$routes['GET'][$uri] = $callback;
        if ($middleware) self::$auth_routes[] = $uri;
    }

    public static function post($uri, $callback, $middleware = null)
    {
        self::$routes['POST'][$uri] = $callback;

        if ($middleware) self::$auth_routes[] = $uri;
    }

    public static function put($uri, $callback, $middleware = null)
    {
        self::$routes['PUT'][$uri] = $callback;

        if ($middleware) self::$auth_routes[] = $uri;
    }

    public static function delete($uri, $callback, $middleware = null)
    {
        self::$routes['DELETE'][$uri] = $callback;

        if ($middleware) self::$auth_routes[] = $uri;
    }


    public static function resolve()
    {


        self::get_method();   //get request method

        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        foreach (self::$routes[self::$request_method] as $route => $callback) {
            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route); //Convert {id} to regex
            $pattern = "#^$pattern$#";

            if (preg_match($pattern, $uri, $matches)) {


                if (in_array($route, self::$auth_routes)) {
                    Middleware::apply_middleware('auth');
                }
                array_shift($matches);

                self::handle_callback_func($callback, $matches[0]);
            }
        }

        //if not match any defined routes 
        http_response_code(404);
        echo json_encode(['message' => '404 Not Found']);
        exit;

        // $uri_data = self::check_uri($uri); //get uri data 

        // if (!$uri_data || (!$uri_data['uri_param']) && self::$has_param) {
        //     http_response_code(404);
        //     echo json_encode(['message' => '404 Not Found']);
        //     exit;
        // }

        // if (in_array($uri, self::$auth_routes)) {
        //     Middleware::apply_middleware('auth');
        // }

        // $uri = $uri_data['uri'];
        // self::$uri_parameter = $uri_data['uri_param']; //if uri has a parameter => test/5
        // $callback = self::$routes[self::$request_method][$uri];
    }

    private static function handle_callback_func($callback, $param)
    {
        //get request data
        $data = self::get_requested_data(self::$request_method);

        if (is_array($callback)) {
            $controller = $callback[0];
            $method = $callback[1];

            $controller = new $controller();

            //check if method has parameters
            $reflection = new ReflectionMethod($controller, $method);

            if ($reflection->getNumberOfParameters() > 0) {
                if ($param) {
                    $controller->$method($param, $data);
                } else
                    $controller->$method($data);
            } else {
                if ($param)
                    $controller->$method($param);
                else
                    $controller->$method();
            }
        } else {
            call_user_func($callback, $param, $data);
        }
        exit;
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

        // if ($request_method == 'PUT') $request_method = 'POST';

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

    // private static function check_if_url_take_params($uri)
    // {
    //     if (substr($uri, -1) == '}') {
    //         $uri = substr($uri, 0, strpos($uri, '{'));
    //         self::$has_param = true;
    //     }
    //     return trim($uri, '/');
    // }
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
        self::$has_param = false;
    }
}
