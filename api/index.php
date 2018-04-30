<?php


use AllThings\Development\Page;
use Slim\Http\Request;
use Slim\Http\Response;

define('APPLICATION_ROOT', realpath(__DIR__) . DIRECTORY_SEPARATOR . '..');

require APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


// Create and configure Slim app
$configuration['displayErrorDetails'] = true;
$configuration['addContentLengthHeader'] = false;
$container = new \Slim\Container(['settings' => $configuration]);

const ROUTER_COMPONENT = 'router';
const VIEWER_COMPONENT = 'view';
$container[VIEWER_COMPONENT] = new \Slim\Views\PhpRenderer(APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'view');

$app = new \Slim\App($container);

// Define app routes

$app->get('/', function (Request $request, Response $response, array $arguments) {
    $router = $this->get(ROUTER_COMPONENT);
    $viewer = $this->get(VIEWER_COMPONENT);
    $page = new Page($viewer, $router);

    $response = $page->root($request, $response, $arguments);

    return $response;
})->setName(Page::DEFAULT);

$app->post('/essence/{code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['code']);
})->setName(Page::ADD_ESSENCE);

$app->get('/essence/{code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['code']);
})->setName(Page::VIEW_ESSENCE);

$app->put('/essence/{code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['code']);
})->setName(Page::STORE_ESSENCE);

$app->get('/essence-catalog', function (Request $request, Response $response, array $arguments) {
    return $response->write("pong");
})->setName(Page::VIEW_ESSENCE_CATALOG);

$app->get('/essence-catalog/filter/[{params:.*}]', function (Request $request, Response $response, array $arguments) {
    return $response->write("pong");
})->setName(Page::FILTER_ESSENCE_CATALOG);

$app->post('/kind/{code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['code']);
})->setName(Page::ADD_KIND);

$app->get('/kind/{code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['code']);
})->setName(Page::VIEW_KIND);

$app->put('/kind/{code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['code']);
})->setName(Page::STORE_KIND);

$app->get('/kind-catalog', function (Request $request, Response $response, array $arguments) {
    return $response->write("pong");
})->setName(Page::VIEW_KIND_CATALOG);

$app->get('/kind-catalog/filter/[{params:.*}]', function (Request $request, Response $response, array $arguments) {
    return $response->write("pong");
})->setName(Page::FILTER_KIND_CATALOG);

$app->post('/essence-kind/{essence-code}/{kind-code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['essence-code'].' + '.$arguments['kind-code']);
})->setName(Page::ADD_ESSENCE_KIND_LINK);

$app->delete('/essence-kind/{essence-code}/{kind-code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['essence-code'].' + '.$arguments['kind-code']);
})->setName(Page::REMOVE_ESSENCE_KIND_LINK);

$app->get('/essence-kind[/{essence-code}]', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['essence-code']);
})->setName(Page::VIEW_KIND_OF_ESSENCE);

$app->post('/thing/{essence-code}/{thing-code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['essence-code'].' + '.$arguments['thing-code']);
})->setName(Page::ADD_THING);

$app->get('/thing/{code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['code']);
})->setName(Page::VIEW_THING);

$app->put('/thing/{code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['code']);
})->setName(Page::STORE_THING);

$app->get('/essence-filer/{essence-code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['essence-code']);
})->setName(Page::FILTER_OF_ESSENCE);

$app->post('/thing-kind/{thing-code}/{kind-code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['thing-code'].' + '.$arguments['kind-code']);
})->setName(Page::ADD_KIND_TO_THING);

$app->put('/thing-kind/{thing-code}/{kind-code}', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['thing-code'].' + '.$arguments['kind-code']);
})->setName(Page::STORE_KIND_OF_THING);

$app->get('/thing-kind/filter/essence-code/{essence-code}[/{params:.*}]', function (Request $request, Response $response, array $arguments) {
    return $response->write($arguments['essence-code']);
})->setName(Page::FILTER_THING_BY_KIND);

// Run app
/** @noinspection PhpUnhandledExceptionInspection */
$app->run();
