<?php
// use Controllers\AuthController;

header('Content-Type: application/json');
require_once "autoload.php";
require_once "routes.php";

// GET URL and REQUEST Typ
$requestUrl = parse_url(htmlspecialchars($_SERVER['REQUEST_URI']), PHP_URL_PATH);
$requestMethod = htmlspecialchars($_SERVER['REQUEST_METHOD']);

// load routes here
$route->match($requestUrl, $requestMethod);