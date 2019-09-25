<?php


use AllThings\Content\ContentManager;
use AllThings\DataAccess\Manager\NamedEntityManager;
use AllThings\DataObject\EssenceAttributeCommand;
use AllThings\DataObject\EssenceThingCommand;
use AllThings\DataObject\NamedEntity;
use AllThings\Presentation\ForNameableEntity;
use AllThings\Presentation\FromAttributeEntity;
use AllThings\Presentation\FromCrossoverEntity;
use AllThings\Presentation\FromEssenceEntity;
use AllThings\Reception\ToAttributeEntity;
use AllThings\Reception\ToCrossoverEntity;
use AllThings\Reception\ToEssenceEntity;
use AllThings\Reception\ToNameableEntity;
use Environment\Development\Page;
use AllThings\Essence\Attribute;
use AllThings\Essence\Essence;
use AllThings\Essence\EssenceAttributeManager;
use AllThings\Essence\EssenceThingManager;
use Environment\DbConnection;
use Slim\App;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

define('APPLICATION_ROOT', realpath(__DIR__) . DIRECTORY_SEPARATOR . '..');

require APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

define('CONFIGURATION_ROOT', APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'configuration');
define('DB_READ_CONFIGURATION', CONFIGURATION_ROOT . DIRECTORY_SEPARATOR . 'db_read.php');
define('DB_WRITE_CONFIGURATION', CONFIGURATION_ROOT . DIRECTORY_SEPARATOR . 'db_write.php');
define('DB_DELETE_CONFIGURATION', CONFIGURATION_ROOT . DIRECTORY_SEPARATOR . 'db_delete.php');


// Create and configure Slim app
$configuration['displayErrorDetails'] = true;
$configuration['addContentLengthHeader'] = false;
$container = new Container(['settings' => $configuration]);

const ROUTER_COMPONENT = 'router';
const VIEWER_COMPONENT = 'view';
$container[VIEWER_COMPONENT] = new PhpRenderer(APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'view');

$app = new App($container);

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
 *   definition="content",
 *   type="object",
 *   description="some value of attribute of thing",
 *   @SWG\Property(
 *          property="thing",
 *          type="string"
 *   ),
 *   @SWG\Property(
 *          property="attribute",
 *          type="string"
 *   ),
 *   @SWG\Property(
 *          property="content",
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

    $essenceCode = (new ToEssenceEntity($request, $arguments))->fromPost();
    $essence = Essence::GetDefaultEssence();
    $essence->setCode($essenceCode);

    $handler = new AllThings\Essence\EssenceManager($essence, $dataPath);

    $isSuccess = $handler->create();
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

    $parameter = (new ToEssenceEntity($request, $arguments))->fromGet();

    $subject = Essence::GetDefaultEssence();
    $dataPath = (new DbConnection())->getForRead();
    $handler = new AllThings\Essence\EssenceManager($subject, $dataPath);

    $isSuccess = $handler->browse($parameter);

    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }
    if ($isSuccess) {
        $result = $handler->retrieveData();

        $presentation = new FromEssenceEntity($result);
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

    $command = (new ToEssenceEntity($request, $arguments))->fromPut();

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

    $attributeCode = (new ToAttributeEntity($request, $arguments))->fromPost();
    $attribute = (Attribute::GetDefaultAttribute());
    $attribute->setCode($attributeCode);

    $handler = new AllThings\Essence\AttributeManager($attribute, $dataPath);

    $isSuccess = $handler->create();
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

    $parameter = (new ToAttributeEntity($request, $arguments))->fromGet();

    $subject = Attribute::GetDefaultAttribute();
    $dataPath = (new DbConnection())->getForRead();
    $handler = new AllThings\Essence\AttributeManager($subject, $dataPath);

    $isSuccess = $handler->browse($parameter);

    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }
    if ($isSuccess) {
        $result = $handler->retrieveData();

        $presentation = new FromAttributeEntity($result);
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

    $command = (new ToAttributeEntity($request, $arguments))->fromPut();

    $subject = $command->getSubject();
    $dataPath = (new DbConnection())->getForWrite();
    $manager = new AllThings\Essence\AttributeManager($subject, $dataPath);

    $parameter = $command->getParameter();
    $isSuccess = $manager->correct($parameter);

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

    $command = new EssenceAttributeCommand($request, $arguments);

    $essence = $command->getEssenceIdentifier();
    $attribute = $command->getAttributeIdentifier();
    $dataPath = (new DbConnection())->getForWrite();

    $manager = new EssenceAttributeManager($essence,$attribute,$dataPath);

    $result = $manager->setUp();

    $isSuccess = $result === true;
    if($isSuccess){
        $response = $response->withStatus(201);
    }
    if(!$isSuccess){
        $response = $response->withStatus(500);
    }

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

    $command = new EssenceAttributeCommand($request, $arguments);

    $essence = $command->getEssenceIdentifier();
    $attribute = $command->getAttributeIdentifier();
    $dataPath = (new DbConnection())->getForDelete();

    $manager = new EssenceAttributeManager($essence,$attribute,$dataPath);

    $result = $manager->breakDown();

    $isSuccess = $result === true;
    if($isSuccess){
        $response = $response->withStatus(204);
    }
    if(!$isSuccess){
        $response = $response->withStatus(500);
    }

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

    $command = new EssenceAttributeCommand($request, $arguments);

    $essence = $command->getEssenceIdentifier();
    $attribute = $command->getAttributeIdentifier();
    $dataPath = (new DbConnection())->getForDelete();

    $manager = new EssenceAttributeManager($essence,$attribute,$dataPath);

    $result = $manager->getAssociated();

    $isSuccess = $result === true;

    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }
    if ($isSuccess) {
        $result = $manager->retrieveData();

        $json = (new AllThings\Presentation\FromEssenceAttribute($result))->toJson();

        $response->write($json);
        $response = $response->withStatus(200);
    }

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

    $command = new EssenceThingCommand($request, $arguments);

    $essenceIdentifier = $command->getEssenceIdentifier();
    $thingIdentifier = $command->getThingIdentifier();
    $dataPath = (new DbConnection())->getForWrite();

    $manager = new EssenceThingManager($essenceIdentifier,$thingIdentifier,$dataPath);

    $result = $manager->setUp();

    $isSuccess = $result === true;
    if($isSuccess){
        $response = $response->withStatus(201);
    }
    if(!$isSuccess){
        $response = $response->withStatus(500);
    }

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

    $command = new EssenceThingCommand($request, $arguments);

    $essenceIdentifier = $command->getEssenceIdentifier();
    $thingIdentifier = $command->getThingIdentifier();
    $dataPath = (new DbConnection())->getForDelete();

    $manager = new EssenceThingManager($essenceIdentifier,$thingIdentifier,$dataPath);

    $result = $manager->breakDown();

    $isSuccess = $result === true;
    if($isSuccess){
        $response = $response->withStatus(204);
    }
    if(!$isSuccess){
        $response = $response->withStatus(500);
    }

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

    $command = new EssenceThingCommand($request, $arguments);

    $essenceIdentifier = $command->getEssenceIdentifier();
    $thingIdentifier = $command->getThingIdentifier();
    $dataPath = (new DbConnection())->getForRead();

    $manager = new EssenceThingManager($essenceIdentifier,$thingIdentifier,$dataPath);

    $result = $manager->getAssociated();

    $isSuccess = $result === true;

    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }
    if ($isSuccess) {
        $result = $manager->retrieveData();

        $json = (new AllThings\Presentation\FromEssenceThing($result))->toJson();

        $response->write($json);
        $response = $response->withStatus(200);
    }

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

    $dataPath = (new DbConnection())->getForWrite();

    $thingCode = (new ToAttributeEntity($request, $arguments))->fromPost();
    $nameable = (new NamedEntity())->setCode($thingCode);
    $handler = new NamedEntityManager($nameable, 'thing' , $dataPath);

    $isSuccess = $handler->create();

    if ($isSuccess) {
        $response = $response->withStatus(201);
    }
    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }

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

    $parameter = (new ToNameableEntity($request, $arguments))->fromGet();

    $subject = new NamedEntity();
    $dataPath = (new DbConnection())->getForRead();
    $handler = new NamedEntityManager($subject, 'thing', $dataPath);

    $isSuccess = $handler->browse($parameter);

    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }
    if ($isSuccess) {
        $result = $handler->retrieveData();

        $presentation = new ForNameableEntity($result);
        $json = $presentation->toJson();

        $response->write($json);
        $response = $response->withStatus(200);
    }

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

    $command = (new ToAttributeEntity($request, $arguments))->fromPut();

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
 *    @SWG\Response(
 *        response=201,
 *        description="Null response"
 *    ),
 * )
 */
$app->post('/thing-attribute/{thing-code}/{attribute-code}', function (Request $request, Response $response, array $arguments) {

    $content = (new ToCrossoverEntity($request, $arguments))->fromPost();
    $dataPath = (new DbConnection())->getForWrite();
    $handler = new ContentManager($content, $dataPath);

    $isSuccess = $handler->attach();
    if ($isSuccess) {
        $response = $response->withStatus(201);
    }
    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }

    return $response;
})->setName(Page::ADD_ATTRIBUTE_TO_THING);

/**
 * @SWG\Get(
 *    path="/thing-attribute/{thing-code}/{attribute-code}",
 *     summary="Browse a finds parameters of specified an essence object",
 *    description="VIEW_CONTENT",
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
$app->get('/thing-attribute/{thing-code}/{attribute-code}', function (Request $request, Response $response, array $arguments) {

    $content = (new ToCrossoverEntity($request, $arguments))->fromGet();
    $dataPath = (new DbConnection())->getForRead();
    $handler = new ContentManager($content, $dataPath);

    $isSuccess = $handler->take($content);

    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }
    if ($isSuccess) {
        $result = $handler->retrieveData();

        $presentation = new FromCrossoverEntity($result);
        $json = $presentation->toJson();

        $response->write($json);
        $response = $response->withStatus(200);
    }

    return $response;
})->setName(Page::VIEW_CONTENT);

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
 *         name="content",
 *         in="body",
 *         description="properties of content for update",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/content"),
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Successful operation",
 *     ),
 * )
 */
$app->put('/thing-attribute/{thing-code}/{attribute-code}', function (Request $request, Response $response, array $arguments) {

    $command = (new ToCrossoverEntity($request, $arguments))->fromPut();

    $dataPath = (new DbConnection())->getForWrite();
    $subject = $command->getSubject();
    $handler = new ContentManager($subject, $dataPath);

    $parameter = $command->getParameter();
    $isSuccess = $handler->store($parameter);

    if ($isSuccess) {
        $response = $response->withStatus(200);
    }
    if (!$isSuccess) {
        $response = $response->withStatus(500);
    }

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
