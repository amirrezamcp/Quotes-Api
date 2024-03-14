<?php
use Controllers\RouteController;

$routes = new RouteController();

// Home routes
$routes->add('/', 'GET', 'Controllers\HomeController', 'home');

// GET Quotes routes
$routes->add('/quotes', 'GET', 'Controllers\QuotesController', 'index');
$routes->add('/quotes/{id}', 'GET', 'Controllers\QuotesController', 'getQuotes');

// POST Quotes routes
$routes->add('/quotes', 'POST', 'Controllers\QuotesController', 'stores');

// PUT Quotes routes
$routes->add('/quotes/{id}', 'PUT', 'Controllers\QuotesController', 'updateQuotes');

// DELETE Quotes routes
$routes->add('/quotes/{id}', 'DELETE', 'Controllers\QuotesController', 'deleteQuotes');