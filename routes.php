<?php
use Controllers\RouteController;

$routes = new RouteController();

// Home routes
$routes->add('/', 'GET', 'Controllers\HomeController', 'home');

// Quotes routes
$routes->add('/quotes', 'GET', 'Controllers\QuotesController', 'index');
$routes->add('/quotes/{id}', 'GET', 'Controllers\QuotesController', 'getQuotes');
