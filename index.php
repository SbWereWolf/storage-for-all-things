<?php

use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/vendor/autoload.php';

// Create and configure Slim app
$configuration['displayErrorDetails'] = true;
$configuration['addContentLengthHeader'] = false;

$container = new \Slim\Container(['settings' => $configuration]);
$app = new \Slim\App($container);

// Define app routes
$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    return $response->write("Hello " . $args['name']);
});

// Run app
/** @noinspection PhpUnhandledExceptionInspection */
$app->run();
