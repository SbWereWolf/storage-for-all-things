<?php


use AllThings\Essence\Attribute;
use AllThings\Essence\Essence;
use Environment\DbConnection;
use AllThings\Development\Page;
use Slim\Http\Request;
use Slim\Http\Response;

define('APPLICATION_ROOT', realpath(__DIR__) . DIRECTORY_SEPARATOR . '..');

require APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

define('CONFIGURATION_ROOT', APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'configuration');
define('DB_READ_CONFIGURATION', CONFIGURATION_ROOT . DIRECTORY_SEPARATOR . 'db_read.php');
define('DB_WRITE_CONFIGURATION', CONFIGURATION_ROOT . DIRECTORY_SEPARATOR . 'db_write.php');


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
 *         version="0.1.0",
 *         title="storage-for-all-things",
 *         description="API that uses a storage-for-all-things",
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
 *   definition="attribute-code",
 *   type="string",
 *   description="unique name for attribute of thing"
 * )
 * @SWG\Definition(
 *   definition="thing-code",
 *   type="string",
 *   description="unique name for thing"
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
 *     path="/essence/{code}",
 *     summary="Create an essence object",
 *     description="ADD_ESSENCE",
 *     @SWG\Parameter(
 *         name="code",
 *         in="path",
 *         type="string",
 *         required=true,
 *     ),
 *     @SWG\Response(
 *         response=201,
 *         description="Null response"
 *     ),
 * )
 */
$app->post('/essence/{code}', function (Request $request, Response $response, array $arguments) {

    $dataPath = (new DbConnection())->getForWrite();

    $essence = Essence::GetDefaultEssence();
    $handler = new AllThings\Essence\EssenceManager($essence, $dataPath);

    $essenceCode = (new \AllThings\Reception\ForEssenceEntity($request, $arguments))->fromPost();

    $isSuccess = $handler->create($essenceCode);

    if ($isSuccess) {
        $response = $response->withStatus(201);
    }
    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }

    return $response;
})->setName(Page::ADD_ESSENCE);

/**
 * @SWG\Get(
 *    path="/essence/{code}",
 *     summary="Browse an essence object",
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

    $parameter = (new \AllThings\Reception\ForEssenceEntity($request, $arguments))->fromGet();

    $subject = Essence::GetDefaultEssence();
    $dataPath = (new DbConnection())->getForRead();
    $handler = new AllThings\Essence\EssenceManager($subject, $dataPath);

    $isSuccess = $handler->browse($parameter);

    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }
    if ($isSuccess) {
        $result = $handler->retrieveData();

        $presentation = new \AllThings\Presentation\ForEssenceEntity($result);
        $json = $presentation->toJson();

        $response->write($json);
        $response = $response->withStatus(200);
    }

    return $response;
})->setName(Page::VIEW_ESSENCE);

/**
 * @SWG\Put(
 *     path="/essence/{code}",
 *     summary="Update an existing essence object",
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

    $command = (new \AllThings\Reception\ForEssenceEntity($request, $arguments))->fromPut();

    $subject = $command->getSubject();
    $dataPath = (new DbConnection())->getForWrite();
    $handler = new AllThings\Essence\EssenceManager($subject, $dataPath);

    $parameter = $command->getParameter();
    $isSuccess = $handler->correct($parameter);

    if ($isSuccess) {
        $response = $response->withStatus(200);
    }
    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }

    return $response;
})->setName(Page::STORE_ESSENCE);

/**
 * @SWG\Get(
 *    path="/essence-catalog",
 *     summary="Browse whole collection of essence objects",
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
 *    path="/essence-catalog/filter/{filter}",
 *     summary="Find an essence object with certain characteristics",
 *    description="FILTER_ESSENCE_CATALOG",
 *     @SWG\Parameter(
 *         name="filter",
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
$app->get('/essence-catalog/filter/{filter}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withJson(array
    (
        'some' => array('code' => 'one')
    ));

    return $response;
})->setName(Page::FILTER_ESSENCE_CATALOG);

/**
 * @SWG\Post(
 *    path="/attribute/{code}",
 *     summary="Create an attribute object",
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

    $dataPath = (new DbConnection())->getForWrite();

    $attribute = Attribute::GetDefaultAttribute();
    $handler = new AllThings\Essence\AttributeManager($attribute, $dataPath);

    $parameter = (new \AllThings\Reception\ForAttributeEntity($request, $arguments))->fromPost();

    $isSuccess = $handler->create($parameter);

    if ($isSuccess) {
        $response = $response->withStatus(201);
    }
    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }

    return $response;
})->setName(Page::ADD_ATTRIBUTE);

/**
 * @SWG\Get(
 *    path="/attribute/{code}",
 *     summary="Browse an attribute object",
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

    $parameter = (new \AllThings\Reception\ForAttributeEntity($request, $arguments))->fromGet();

    $subject = Attribute::GetDefaultAttribute();
    $dataPath = (new DbConnection())->getForRead();
    $handler = new AllThings\Essence\AttributeManager($subject, $dataPath);

    $isSuccess = $handler->browse($parameter);

    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }
    if ($isSuccess) {
        $result = $handler->retrieveData();

        $presentation = new \AllThings\Presentation\ForAttributeEntity($result);
        $json = $presentation->toJson();

        $response->write($json);
        $response = $response->withStatus(200);
    }

    return $response;
})->setName(Page::VIEW_ATTRIBUTE);

/**
 * @SWG\Put(
 *     path="/attribute/{code}",
 *     summary="Update an existing attribute object",
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

    $command = (new \AllThings\Reception\ForAttributeEntity($request, $arguments))->fromPut();

    $subject = $command->getSubject();
    $dataPath = (new DbConnection())->getForWrite();
    $handler = new AllThings\Essence\AttributeManager($subject, $dataPath);

    $parameter = $command->getParameter();
    $isSuccess = $handler->correct($parameter);

    if ($isSuccess) {
        $response = $response->withStatus(200);
    }
    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }

    return $response;
})->setName(Page::STORE_ATTRIBUTE);

/**
 * @SWG\Get(
 *    path="/attribute-catalog",
 *     summary="Browse whole collection of attribute objects",
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
 *    path="/attribute-catalog/filter/{filter}",
 *     summary="Find an essence objects with certain characteristics",
 *    description="FILTER_ATTRIBUTE_CATALOG",
 *     @SWG\Parameter(
 *         name="filter",
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
$app->get('/attribute-catalog/filter/{filter}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(200);

    return $response;
})->setName(Page::FILTER_ATTRIBUTE_CATALOG);

/**
 * @SWG\Post(
 *    path="/essence-attribute/{essence-code}/{attribute-code}",
 *     summary="Create linkage of an essence object and an attribute object",
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
 *     summary="Remove linkage of an essence object and an attribute object",
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
 *     summary="Browse collection of attribute objects linked with an essence object",
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
 *    path="/essence-thing/{essence-code}/{thing-code}",
 *     summary="Create linkage of an essence object and an attribute object",
 *    description="ADD_ESSENCE_THING_LINK",
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
$app->post('/essence-thing/{essence-code}/{thing-code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(201);

    return $response;
})->setName(Page::ADD_ESSENCE_THING_LINK);

/**
 * @SWG\Delete(
 *    path="/essence-thing/{essence-code}/{thing-code}",
 *     summary="Remove linkage of an essence object and an attribute object",
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
$app->delete('/essence-thing/{essence-code}/{thing-code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(200);

    return $response;
})->setName(Page::REMOVE_ESSENCE_THING_LINK);

/**
 * @SWG\Get(
 *    path="/essence-thing/{essence-code}",
 *     summary="Browse collection of attribute objects linked with an essence object",
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
$app->get('/essence-thing/{essence-code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withJson(array
    (
        'some' => array('color', 'size', 'weight', 'price')
    ));

    return $response;
})->setName(Page::VIEW_THINGS_OF_ESSENCE);


/**
 * @SWG\Post(
 *    path="/thing/{code}",
 *     summary="Create a thing object of specified an essence object",
 *    description="ADD_THING",
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
$app->post('/thing/{code}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withStatus(201);

    return $response;
})->setName(Page::ADD_THING);

/**
 * @SWG\Get(
 *    path="/thing/{code}",
 *     summary="Browse a thing object",
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
 *     summary="Update an existing thing object",
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
 *     summary="Browse a finds parameters of specified an essence object",
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
        'size' => array(
            'range-type' => 'continuous',
            'data-type' => 'decimal',
            'value' => array(5.55, 7.8),
        ),
        'color' => array(
            'range-type' => 'discrete',
            'data-type' => 'string',
            'value' => array('red', 'orange', 'yellow', 'green', 'blue', 'purple'),
        ),
    ));

    return $response;
})->setName(Page::FILTER_OF_ESSENCE);

/**
 * @SWG\Post(
 *    path="/thing-attribute/{thing-code}/{attribute-code}",
 *     summary="Create a content object (value of the attribute) of the thing object with specified value",
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
 *    path="/thing-attribute/essence-code/{essence-code}/filter/{filter}",
 *     summary="Find an thing object with certain characteristics",
 *    description="FILTER_THING_BY_ATTRIBUTE",
 *     @SWG\Parameter(
 *         name="essence-code",
 *         in="path",
 *         type="string",
 *         description="code of essence object",
 *         required=true,
 *     ),
 *     @SWG\Parameter(
 *         name="filter",
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
$app->get('/thing-attribute/essence-code/{essence-code}/filter/{filter}', function (Request $request, Response $response, array $arguments) {
    $response = $response->withJson(array
    (
        'some' => array('red big thing', 'green medium model', 'light and bright glass')
    ));

    return $response;
})->setName(Page::FILTER_THING_BY_ATTRIBUTE);

// Run app

try {
    $app->run();
} catch (Exception $e) {
    echo $e->getMessage();
}


