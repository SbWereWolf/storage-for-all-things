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
 *   definition="essence",
 *   type="object",
 *   description="type of thing",
 *   @SWG\Property(
 *          property="code",
 *          type="string"
 *   ),
 *   @SWG\Property(
 *          property="title",
 *          type="string"
 *   ),
 *   @SWG\Property(
 *          property="remark",
 *          type="string"
 *   ),
 *   @SWG\Property(
 *          property="storage",
 *          type="string",
 *          enum={"view", "materialized view", "table"}
 *          )
 * )
 * @SWG\Definition(
 *   definition="attribute",
 *   type="object",
 *   description="some property for any thing",
 *   @SWG\Property(
 *          property="code",
 *          type="string"
 *   ),
 *   @SWG\Property(
 *          property="title",
 *          type="string"
 *   ),
 *   @SWG\Property(
 *          property="remark",
 *          type="string"
 *   ),
 *   @SWG\Property(
 *          property="data-type",
 *          type="string",
 *          enum={"decimal", "datetime", "string"}
 *          ),
 *   @SWG\Property(
 *          property="range-type",
 *          type="string",
 *          enum={"continuous", "discrete"}
 *          )
 * )
 * @SWG\Definition(
 *   definition="thing",
 *   type="object",
 *   description="some thing",
 *   @SWG\Property(
 *          property="code",
 *          type="string"
 *   ),
 *   @SWG\Property(
 *          property="title",
 *          type="string"
 *   ),
 *   @SWG\Property(
 *          property="remark",
 *          type="string"
 *   )
 * )
 * @SWG\Definition(
 *   definition="attribute-filer",
 *   type="object",
 *   description="filtering option for search things of same type",
 *   @SWG\Property(
 *          property="value",
 *          type="array",
 *          @SWG\Items(type="string")
 *   ),
 *   @SWG\Property(
 *          property="data-type",
 *          type="string",
 *          enum={"decimal", "datetime", "string"}
 *          ),
 *   @SWG\Property(
 *          property="range-type",
 *          type="string",
 *          enum={"continuous", "discrete"}
 *          )
 * )
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
 *   description="mode for storage data of thing, MUST be one of 'view' | 'materialized view' | 'table'",
 *   enum={"view", "materialized view", "table"}
 * )
 * @SWG\Definition(
 *   definition="attribute-code",
 *   type="string",
 *   description="unique name for attribute of thing"
 * )
 * @SWG\Definition(
 *   definition="attribute-title",
 *   type="string",
 *   description="name for attribute of thing"
 * )
 * @SWG\Definition(
 *   definition="attribute-remark",
 *   type="string",
 *   description="description for attribute of thing"
 * )
 * @SWG\Definition(
 *   definition="data-type",
 *   type="string",
 *   description="data type for value of attribute, MUST be one of 'decimal' | 'datetime' | 'string'",
 *   enum={"decimal", "datetime", "string"}
 * )
 * @SWG\Definition(
 *   definition="range-type",
 *   type="string",
 *   description="mode for define range of values, MUST be one of 'continuous' | 'discrete'",
 *   enum={"continuous", "discrete"}
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
 *   description="parameters for filtering items, SHOULD has format like this: 'key:value1;value2; .. valueN;key2:minimal-value;maximal-value'"
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
 *    path="/essence/{code}",
 *    description="ADD_ESSENCE",
 *    @SWG\Parameter(
 *        name="code",
 *        in="path",
 *        type="string",
 *        required=true,
 *    ),
 *    @SWG\Response(
 *        response=201,
 *        description="Null response"
 *    ),
 * )
 */
$app->post('/essence/{code}', function (Request $request, Response $response, array $arguments) {
    return $response->withStatus(201);
})->setName(Page::ADD_ESSENCE);

/**
 * @SWG\Get(
 *    path="/essence/{code}",
 *    description="VIEW_ESSENCE",
 *    @SWG\Parameter(
 *        name="code",
 *        in="path",
 *        type="string",
 *        required=true
 *    ),
 *    @SWG\Response(
 *       response=200,
 *       description="VIEW_ESSENCE response",
 *        @SWG\Schema(
 *            ref="$/definitions/essence"
 *        )
 *    ),
 * )
 */
$app->get('/essence/{code}', function (Request $request, Response $response, array $arguments) {

    $response = $response->withJson(array(
        'code' => $arguments['code'],
        'title' => 'empty',
        'remark' => 'empty',
        'storage' => 'empty'
    ));

    return $response;
})->setName(Page::VIEW_ESSENCE);

/**
 * @SWG\Put(
 *     path="/essence/{code}",
 *     summary="Update an existing essence",
 *     description="STORE_ESSENCE_PROPERTY",
 *     @SWG\Parameter(
 *         name="code",
 *         in="path",
 *         type="string",
 *         description="code of essence (type of thing) object",
 *         required=true,
 *     ),
 *     @SWG\Parameter(
 *         name="essence",
 *         in="body",
 *         description="property of essence (type of thing) for update",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/essence"),
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *     ),
 * )
 */
$app->put('/essence/{code}', function (Request $request, Response $response, array $arguments) {
    return $response->withStatus(200);
})->setName(Page::STORE_ESSENCE);

/**
 * @SWG\Get(
 *    path="/essence-catalog",
 *    description="VIEW_ESSENCE_CATALOG",
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *         @SWG\Schema(
 *             type="array",
 *             @SWG\Items(ref="#/definitions/essence-code")
 *         ),
 *     ),
 * )
 */
$app->get('/essence-catalog', function (Request $request, Response $response, array $arguments) {
    $response = $response->withJson(array
    (
        'some' => array('code' => 'one'),
        'other' => array('code' => 'two')
    ));

    return $response;
})->setName(Page::VIEW_ESSENCE_CATALOG);

/**
 * @SWG\Get(
 *    path="/essence-catalog/filter/{params}",
 *    description="FILTER_ESSENCE_CATALOG",
 *     @SWG\Parameter(
 *         name="params",
 *         in="path",
 *         type="string",
 *         description="parameters of filtering, SHOULD has format like this: 'key:value1;value2; .. valueN;key2:minimal-value;maximal-value'",
 *         required=true,
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *         @SWG\Schema(
 *             type="array",
 *             @SWG\Items(ref="#/definitions/essence-code")
 *         ),
 *     ),
 * )
 */
$app->get('/essence-catalog/filter/{params}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withJson(array
    (
        'some' => array('code' => 'one')
    ));

    return $response;
})->setName(Page::FILTER_ESSENCE_CATALOG);

/**
 * @SWG\Post(
 *    path="/attribute/{code}",
 *    description="ADD_ATTRIBUTE",
 *    @SWG\Parameter(
 *        name="code",
 *        in="path",
 *        type="string",
 *        required=true,
 *    ),
 *    @SWG\Response(
 *        response=201,
 *        description="Null response"
 *    ),
 * )
 */
$app->post('/attribute/{code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(201);

    return $response;
})->setName(Page::ADD_ATTRIBUTE);

/**
 * @SWG\Get(
 *    path="/attribute/{code}",
 *    description="VIEW_ATTRIBUTE",
 *    @SWG\Parameter(
 *        name="code",
 *        in="path",
 *        type="string",
 *        required=true
 *    ),
 *    @SWG\Response(
 *       response=200,
 *       description="VIEW_ATTRIBUTE response",
 *        @SWG\Schema(
 *            ref="$/definitions/attribute"
 *        )
 *    ),
 * )
 */
$app->get('/attribute/{code}', function (Request $request, Response $response, array $arguments) {

    $response = $response->withJson(array(
        'code' => $arguments['code'],
        'title' => 'empty',
        'remark' => 'empty',
        'data-type' => 'decimal',
        'range-type' => 'discrete',
    ));

    return $response;
})->setName(Page::VIEW_ATTRIBUTE);

/**
 * @SWG\Put(
 *     path="/attribute/{code}",
 *     summary="Update an existing attribute",
 *     description="STORE_ATTRIBUTE",
 *     @SWG\Parameter(
 *         name="code",
 *         in="path",
 *         type="string",
 *         description="code of attribute object",
 *         required=true,
 *     ),
 *     @SWG\Parameter(
 *         name="attribute",
 *         in="body",
 *         description="properties of attribute for update",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/attribute"),
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *     ),
 * )
 */
$app->put('/attribute/{code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(200);

    return $response;
})->setName(Page::VIEW_ATTRIBUTE_CATALOG);

/**
 * @SWG\Get(
 *    path="/attribute-catalog",
 *    description="VIEW_ATTRIBUTE_CATALOG",
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *         @SWG\Schema(
 *             type="array",
 *             @SWG\Items(ref="#/definitions/attribute-code")
 *         ),
 *     ),
 * )
 */
$app->get('/attribute-catalog', function (Request $request, Response $response, array $arguments) {
    $response = $response->withJson(array
    (
        'color' => array('code' => '8-bit-color'),
        'size' => array('code' => 'size-in-centimeters')
    ));

    return $response;
})->setName(Page::VIEW_ATTRIBUTE_CATALOG);

/**
 * @SWG\Get(
 *    path="/attribute-catalog/filter/{params}",
 *    description="FILTER_ATTRIBUTE_CATALOG",
 *     @SWG\Parameter(
 *         name="params",
 *         in="path",
 *         type="string",
 *         description="parameters of filtering, SHOULD has format like this: 'key:value1;value2; .. valueN;key2:minimal-value;maximal-value'",
 *         required=true,
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *         @SWG\Schema(
 *             type="array",
 *             @SWG\Items(ref="#/definitions/attribute-code")
 *         ),
 *     ),
 * )
 */
$app->get('/attribute-catalog/filter/{params}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(200);

    return $response;
})->setName(Page::FILTER_ATTRIBUTE_CATALOG);

/**
 * @SWG\Post(
 *    path="/essence-attribute/{essence-code}/{attribute-code}",
 *    description="ADD_ESSENCE_ATTRIBUTE_LINK",
 *    @SWG\Parameter(
 *        name="essence-code",
 *        in="path",
 *        type="string",
 *        required=true,
 *    ),
 *    @SWG\Parameter(
 *        name="attribute-code",
 *        in="path",
 *        type="string",
 *        required=true,
 *    ),
 *    @SWG\Response(
 *        response=201,
 *        description="Null response"
 *    ),
 * )
 */
$app->post('/essence-attribute/{essence-code}/{attribute-code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(201);

    return $response;
})->setName(Page::ADD_ESSENCE_ATTRIBUTE_LINK);

/**
 * @SWG\Delete(
 *    path="/essence-attribute/{essence-code}/{attribute-code}",
 *    description="REMOVE_ESSENCE_ATTRIBUTE_LINK",
 *    @SWG\Parameter(
 *        name="essence-code",
 *        in="path",
 *        type="string",
 *        required=true,
 *    ),
 *    @SWG\Parameter(
 *        name="attribute-code",
 *        in="path",
 *        type="string",
 *        required=true,
 *    ),
 *    @SWG\Response(
 *        response=200,
 *        description="Null response"
 *    ),
 * )
 */
$app->delete('/essence-attribute/{essence-code}/{attribute-code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(200);

    return $response;
})->setName(Page::REMOVE_ESSENCE_ATTRIBUTE_LINK);

/**
 * @SWG\Get(
 *    path="/essence-attribute/{essence-code}",
 *    description="VIEW_ATTRIBUTE_OF_ESSENCE",
 *     @SWG\Parameter(
 *         name="essence-code",
 *         in="path",
 *         type="string",
 *         description="code of essence for browse attributes",
 *         required=true,
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *         @SWG\Schema(
 *             type="array",
 *             @SWG\Items(ref="#/definitions/attribute-code")
 *         ),
 *     ),
 * )
 */
$app->get('/essence-attribute/{essence-code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withJson(array
    (
        'some' => array('color', 'size', 'weight', 'price')
    ));

    return $response;
})->setName(Page::VIEW_ATTRIBUTE_OF_ESSENCE);

/**
 * @SWG\Post(
 *    path="/thing/{essence-code}/{thing-code}",
 *    description="ADD_ESSENCE_ATTRIBUTE_LINK",
 *    @SWG\Parameter(
 *        name="essence-code",
 *        in="path",
 *        type="string",
 *        required=true,
 *    ),
 *    @SWG\Parameter(
 *        name="thing-code",
 *        in="path",
 *        type="string",
 *        required=true,
 *    ),
 *    @SWG\Response(
 *        response=201,
 *        description="Null response"
 *    ),
 * )
 */
$app->post('/thing/{essence-code}/{thing-code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(201);

    return $response;
})->setName(Page::ADD_THING);

/**
 * @SWG\Get(
 *    path="/thing/{code}",
 *    description="VIEW_THING",
 *     @SWG\Parameter(
 *         name="code",
 *         in="path",
 *         type="string",
 *         description="code of the thing",
 *         required=true,
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *         @SWG\Schema(
 *             type="array",
 *             @SWG\Items(ref="#/definitions/thing")
 *         ),
 *     ),
 * )
 */
$app->get('/thing/{code}', function (Request $request, Response $response, array $arguments) {

    $response = $response->withJson(array(
        'code' => $arguments['code'],
        'title' => 'empty',
        'remark' => 'empty'
    ));

    return $response;
})->setName(Page::VIEW_THING);

/**
 * @SWG\Put(
 *     path="/thing/{code}",
 *     summary="Update an existing thing",
 *     description="STORE_THING",
 *     @SWG\Parameter(
 *         name="code",
 *         in="path",
 *         type="string",
 *         description="code of thing object",
 *         required=true,
 *     ),
 *     @SWG\Parameter(
 *         name="thing",
 *         in="body",
 *         description="properties of attribute for update",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/thing"),
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *     ),
 * )
 */
$app->put('/thing/{code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(200);

    return $response;
})->setName(Page::STORE_THING);

/**
 * @SWG\Get(
 *    path="/essence-filer/{essence-code}",
 *    description="FILTER_OF_ESSENCE",
 *     @SWG\Parameter(
 *         name="essence-code",
 *         in="path",
 *         type="string",
 *         required=true,
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *         @SWG\Schema(
 *             type="array",
 *             @SWG\Items(ref="#/definitions/attribute-filer")
 *         ),
 *     ),
 * )
 */
$app->get('/essence-filer/{essence-code}', function (Request $request, Response $response, array $arguments) {

    $response = $response->withJson(array(
        'size'=> array(
            'range-type' => 'continuous',
            'data-type' => 'decimal',
            'value' => array(5.55,7.8),
        ),
        'color'=> array(
            'range-type' => 'discrete',
            'data-type' => 'string',
            'value' => array('red','orange','yellow','green','blue','purple'),
        ),
    ));

    return $response;
})->setName(Page::FILTER_OF_ESSENCE);

/**
 * @SWG\Post(
 *    path="/thing/{thing-code}/{attribute-code}",
 *    description="ADD_ATTRIBUTE_TO_THING",
 *    @SWG\Parameter(
 *        name="thing-code",
 *        in="path",
 *        type="string",
 *        required=true,
 *    ),
 *    @SWG\Parameter(
 *        name="attribute-code",
 *        in="path",
 *        type="string",
 *        required=true,
 *    ),
 *    @SWG\Parameter(
 *        name="value",
 *        in="body",
 *        type="string",
 *        required=true,
 *        @SWG\Schema(
 *            type="string",
 *        ),
 *    ),
 *    @SWG\Response(
 *        response=201,
 *        description="Null response"
 *    ),
 * )
 */
$app->post('/thing-attribute/{thing-code}/{attribute-code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(201);

    return $response;
})->setName(Page::ADD_ATTRIBUTE_TO_THING);

/**
 * @SWG\Put(
 *     path="/thing-attribute/{thing-code}/{attribute-code}",
 *     summary="Update value of the attribute of the thing",
 *     description="STORE_THING",
 *     @SWG\Parameter(
 *         name="thing-code",
 *         in="path",
 *         type="string",
 *         description="code of thing object",
 *         required=true,
 *     ),
 *     @SWG\Parameter(
 *         name="attribute-code",
 *         in="path",
 *         type="string",
 *         description="code of attribute object",
 *         required=true,
 *     ),
 *     @SWG\Parameter(
 *         name="value",
 *         in="body",
 *         type="string",
 *         required=true,
 *         @SWG\Schema(
 *             type="string",
 *         ),
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *     ),
 * )
 */
$app->put('/thing-attribute/{thing-code}/{attribute-code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(200);

    return $response;
})->setName(Page::STORE_ATTRIBUTE_OF_THING);

/**
 * @SWG\Get(
 *    path="/thing-attribute/filter/essence-code/{essence-code}/{params}",
 *    description="FILTER_THING_BY_ATTRIBUTE",
 *     @SWG\Parameter(
 *         name="essence-code",
 *         in="path",
 *         type="string",
 *         description="code of essence object",
 *         required=true,
 *     ),
 *     @SWG\Parameter(
 *         name="params",
 *         in="path",
 *         type="string",
 *         description="parameters of filtering, SHOULD has format like this: 'key:value1;value2; .. valueN;key2:minimal-value;maximal-value'",
 *         required=true,
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *         @SWG\Schema(
 *             type="array",
 *             @SWG\Items(ref="#/definitions/thing-code")
 *         ),
 *     ),
 * )
 */
$app->get('/thing-attribute/filter/essence-code/{essence-code}/{params}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withJson(array
    (
        'some' => array('red big thing', 'green medium model', 'light and bright glass')
    ));

    return $response;
})->setName(Page::FILTER_THING_BY_ATTRIBUTE);

// Run app
/** @noinspection PhpUnhandledExceptionInspection */
$app->run();
