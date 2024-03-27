<?php
use Controllers\RouteController;

$route = new RouteController();

// Home routes
$route->add('/', 'GET', 'Controllers\HomeController', 'home');

// GET Quotes routes
$route->auth()->add('/quotes', 'GET', 'Controllers\QuotesController', 'index');
$route->auth()->add('/quotes/{id}', 'GET', 'Controllers\QuotesController', 'getQuotes');

// POST Quotes routes
$route->auth()->add('/quotes', 'POST', 'Controllers\QuotesController', 'stores');

// PUT Quotes routes
$route->auth()->add('/quotes/{id}', 'PUT', 'Controllers\QuotesController', 'updateQuotes');

// DELETE Quotes routes
$route->auth()->add('/quotes/{id}', 'DELETE', 'Controllers\QuotesController', 'deleteQuotes');

// Filter by author
$route->auth()->add('/quotes/author/{author}', 'GET', 'Controllers\QuotesController', 'quotesByAuthor');

// Authentication using tokens
$route->auth()->add('/quotes/user/{id}', 'GET', 'Controllers\QuotesController', 'getQuoteByUserId');