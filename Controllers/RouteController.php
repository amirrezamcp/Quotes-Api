<?php
namespace Controllers;
use Traits\SanitizerTrait;
class RouteController {
    use SanitizerTrait;

    /**
     * Properties $routes         It is an array to store route information.
     *
     * @var   array   $routes
     */
    private $routes = [];

    /**
     * function add                   Add route and store in $routes array
     *
     * @param   [type]   $url         This parameter indicates the URL address of the desired path
     *                                  that should be connected to the controller and the corresponding
     *                                  operation. In other words, $url specifies at what address a request
     *                                  should be sent to the appropriate controller.
     * 
     * @param   [type]   $method      This parameter indicates the type of HTTP request that is
     *                                  allowed for this path. For example, this could be GET, POST, PUT, or DELETE.
     *                                  By specifying $method , you specify which operations in
     *                                  the controller should be assigned to requests sent to this route.
     * 
     * @param   [type]   $controller  Specifies the name of the controller related to this route.
     * 
     * @param   [type]   $action      The name is the operation to be performed on it.ion]   
     */
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

    /**
     * function match                   Receives the request 
     *                                  Checks if $requestUrl and $requestMethod exist in $route 
     *                                  If present, it is passed to the callControllerAction method 
     *                                  If not, a 404 Not Found error.
     *
     * @param   [type]  $requestUrl     This variable in the PHP code contains the path (URL)
     *                                  of the current request, which is extracted from the
     *                                  $_SERVER['REQUEST_URI'] array using the parse_url and PHP_URL_PATH functions.
     * 
     * @param   [type]  $requestMethod  This variable contains the current HTTP request type (such as GET, POST, PUT, DELETE, etc.)
     *                                  obtained using the htmlspecialchars function and $_SERVER['REQUEST_METHOD']. 
     *
     * @return  string                  true : If the route is the same as the requested method and the address
     *                                          pattern of the route matches the requested address, the controller
     *                                          and operation for that route will be executed and the "callControllerAction"
     *                                          function will be called with this method and "true". is returned.
     *                                  false : If no matching path is found, the ``invalidRequest`` function is called and ``false`` is returned.
     */
    public function match($requestUrl, $requestMethod) {
        $requestUrl = $this->sanitizeInput($requestUrl);
        $requestMethod = $this->sanitizeInput($requestMethod);
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
        $this->invalidRequest($requestUrl);
        return false;
    }
    
    /**
     * function callControllerAction    If the desired method exists in the controller,
     *                                  it calls it using "call_user_func_array" with the required parameters. Otherwise,
     *                                  it will display "Internal Server Error" message.
     *
     * @param   [type]  $params         $params == $matches
     *                                  $params is the same as $matches
     */
    private function callControllerAction($controller, $action, $params) {
        $controller = $this->sanitizeInput($controller);
        $action = $this->sanitizeInput($action);
        $params = $this->sanitizeInput($params);

        $controllerInstance = new $controller();
        if(method_exists($controllerInstance, $action)) {
            call_user_func_array([$controllerInstance, $action], array($params));
        }else{
            echo "Internal server Error";
        }
    }

    /**
     * function invalidRequest          This function sends an error message for invalid requests.
     *                                  When a request is made with a path that does not exist, this function returns
     *                                  a response with a 404 "Not Found" status code.
     *                                  It then creates an array with error information including the error message, error code,and request path.
     *                                  This array is then converted to JSON and returned as the response.
     *
     * @param   [type]  $route          is the route that was sent for the invalid request. This variable holds
     *                                  the value of the requested path so that it can be used in an error
     *                                  message to inform the user that the requested path does not exist.
     */
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