<?php
use Controllers\RouteController;

$routes = new RouteController();
$routes->add('/', 'GET', 'Controllers\HomeController', 'home');
$routes->add('/quotes/{id}', 'GET', 'Controllers\QuotesController', 'index');
