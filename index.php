<?php
header('Content-Type: application/json');

use Controllers\route;
require_once "autoload.php";

// GET URL and REQUEST Typ
$requestUrl = parse_url(htmlspecialchars($_SERVER['REQUEST_URI']), PHP_URL_PATH);
$requestMethod = htmlspecialchars($_SERVER['REQUEST_METHOD']);

$routes = new route();
$routes->add('/', 'GET', 'Controllers\HomeController', 'home');
$routes->add('/quotes/{id}', 'GET', 'Controllers\quotesController', 'index');
$routes->match($requestUrl, $requestMethod);