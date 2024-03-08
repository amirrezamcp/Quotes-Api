<?php
namespace Controllers;
class route {
    private $routes = [];
    public function add($url, $method, $controller, $action) {
        $urlPattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_-]*)\}/', function ($matches) {
            return "(?<$matches[1]>[^/]+)";
        }, $url);
        $this->routes[] = [
            'urlPattern' => "#^$urlPattern$#",
            'method' => $method,
            'controller' => $controller,
            'action' => $action
        ];
    }
    public function match($requestUrl, $requestMethod) {
        foreach($this->routes as $route) {
            if($route['method'] === $requestMethod) {
                if(preg_match($route['urlPattern'], $requestUrl, $matches)) {
                    $controller = $route['controller'];
                    $action = $route['action'];
                    unset($matches[0]);
                    $this->callControllerAction($controller, $action, $matches);
                    return true;
                }
            }
        }
        return false;
    }
    private function callControllerAction($controller, $action, $params) {

        $controllerInstance = new $controller();
        if(method_exists($controllerInstance, $action)) {
            call_user_func_array([$controllerInstance, $action], array($params));
        }else{
            echo "Internal server Error";
        }
    }
     private function invalidRequest($route) {
        $response = [
            'error' => 'Invalid Route, 404 not found.',
            'error_code' => '404',
            'requested_route' => $route
        ];
        http_response_code(404);
        $response = json_encode($response);
        echo $response;
    }
}