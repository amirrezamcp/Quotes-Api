<?php
use Controllers\RouteController;

$route = new RouteController();

// Home routes
$route->add('/', 'GET', 'Controllers\HomeController', 'home');

// GET Quotes routes
$route->add('/quotes', 'GET', 'Controllers\QuotesController', 'index');
$route->add('/quotes/{id}', 'GET', 'Controllers\QuotesController', 'getQuotes');

// POST Quotes routes
$route->add('/quotes', 'POST', 'Controllers\QuotesController', 'stores');

// PUT Quotes routes
$route->add('/quotes/{id}', 'PUT', 'Controllers\QuotesController', 'updateQuotes');

// DELETE Quotes routes
$route->add('/quotes/{id}', 'DELETE', 'Controllers\QuotesController', 'deleteQuotes');

// Filter by author
$route->add('/quotes/author/{author}', 'GET', 'Controllers\QuotesController', 'quotesByAuthor');

// Authentication using tokens
$route->add('/quotes/user/{id}', 'GET', 'Controllers\QuotesController', 'getQuoteByUserId');