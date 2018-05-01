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

/**
 * @SWG\Swagger(
 *   schemes={"http"},
 *   host="localhost",
 *   basePath="/",
 *   produces={"application/json"},
 *   consumes={"application/json"},
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Swagger Petstore",
 *         description="A sample API that uses a petstore as an example to demonstrate features in the swagger-2.0 specification",
 *         termsOfService="http://swagger.io/terms/",
 *         @SWG\Contact(name="Swagger API Team"),
 *         @SWG\License(name="MIT")
 *     ),
 * )
 */

/**
 * @SWG\Definition(
 *   definition="essence-code",
 *   type="string",
 *   description="unique name for type of thing"
 * )
 * @SWG\Definition(
 *   definition="essence-title",
 *   type="string",
 *   description="name for type of thing"
 * )
 * @SWG\Definition(
 *   definition="essence-remark",
 *   type="string",
 *   description="description for type of thing"
 * )
 * @SWG\Definition(
 *   definition="essence-storage",
 *   type="string",
 *   description="mode for storage data of thing, MUST be one of 'view' | 'materialized view' | 'table'"
 * )
 * @SWG\Definition(
 *   definition="kind-code",
 *   type="string",
 *   description="unique name for kind of thing"
 * )
 * @SWG\Definition(
 *   definition="kind-title",
 *   type="string",
 *   description="name for kind of thing"
 * )
 * @SWG\Definition(
 *   definition="kind-remark",
 *   type="string",
 *   description="description for kind of thing"
 * )
 * @SWG\Definition(
 *   definition="data-type",
 *   type="string",
 *   description="data type for value of kind, MUST be one of 'decimal' | 'datetime' | 'string'"
 * )
 * @SWG\Definition(
 *   definition="range-type",
 *   type="string",
 *   description="mode for define range of values, MUST be one of 'continuous' | 'discrete'"
 * )
 * @SWG\Definition(
 *   definition="thing-code",
 *   type="string",
 *   description="unique name for thing"
 * )
 * @SWG\Definition(
 *   definition="thing-title",
 *   type="string",
 *   description="name for thing"
 * )
 * @SWG\Definition(
 *   definition="thing-remark",
 *   type="string",
 *   description="description for thing"
 * )
 * @SWG\Definition(
 *   definition="filter-parameters",
 *   type="string",
 *   description="parameters for filtering items, MUST be 'key-value' pairs, for example: '/key1/value1/key1/value2'"
 * )
 */

$app->get('/', function (Request $request, Response $response, array $arguments) {
    $router = $this->get(ROUTER_COMPONENT);
    $viewer = $this->get(VIEWER_COMPONENT);
    $page = new Page($viewer, $router);

    $response = $page->root($request, $response, $arguments);

    return $response;
})->setName(Page::DEFAULT);

/**
 * @SWG\Post(
 *     path="/essence/{code}",
 *     description="ADD_ESSENCE",
 *     @SWG\Parameter(
 *         name="code",
 *         in="path",
 *         type="string",
 *         required=true,
 *     ),
 *     @SWG\Response(response=201, description="Null response"),
 * )
 */
$app->post('/essence/{code}', function (Request $request, Response $response, array $arguments) {
    return $response->withStatus(200);
})->setName(Page::ADD_ESSENCE);

/**
 * @SWG\Get(
 *     path="/essence/{code}",
 *     description="VIEW_ESSENCE",
 *     @SWG\Parameter(
 *         name="code",
 *         in="path",
 *         type="string",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/essence-code"),
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="VIEW_ESSENCE response",
 *         @SWG\Schema(
 *             type="string",
 *             @SWG\Items(ref="#/definitions/essence-code")
 *         ),
 *     ),
 * )
 */
$app->get('/essence/{code}', function (Request $request, Response $response, array $arguments) {

    $response = $response->withJson(array(
        "code" => $arguments['code'],
        "title" => "empty",
        "remark" => "empty",
    ));

    return $response;
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
