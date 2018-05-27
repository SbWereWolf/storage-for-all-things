<?php
/**
 * src
 * © Volkhin Nikolay M., 2018
 * Date: 30.04.2018 Time: 1:27
 */

namespace AllThings\Environment\Development;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\PhpRenderer;

class Page
{
    const DEFAULT = 'default';
    /* Essence */
    const ADD_ESSENCE = 'post-essence';
    const VIEW_ESSENCE = 'get-essence';
    const STORE_ESSENCE = 'put-essence';
    /* Essence catalog */
    const VIEW_ESSENCE_CATALOG = 'get-whole-essences';
    const FILTER_ESSENCE_CATALOG = 'get-filtered-essences';
    /* attribute */
    const ADD_ATTRIBUTE = 'post-attribute';
    const VIEW_ATTRIBUTE = 'get-attribute';
    const STORE_ATTRIBUTE = 'put-attribute';
    /* attribute catalog */
    const VIEW_ATTRIBUTE_CATALOG = 'get-whole-attributes';
    const FILTER_ATTRIBUTE_CATALOG = 'get-filtered-attributes';
    /* attributes of Essence */
    const ADD_ESSENCE_ATTRIBUTE_LINK = 'post-essence-attribute';
    const REMOVE_ESSENCE_ATTRIBUTE_LINK = 'delete-essence-attribute';
    const VIEW_ATTRIBUTE_OF_ESSENCE = 'get-whole-essence-attributes';
    /* Thing */
    const ADD_THING = 'post-thing';
    const VIEW_THING = 'get-thing';
    const STORE_THING = 'put-thing';
    /* Essence filter */
    const FILTER_OF_ESSENCE = 'get-filter-for-essences';
    /* attributes of thing */
    const ADD_ATTRIBUTE_TO_THING = 'post-thing-attribute';
    const STORE_ATTRIBUTE_OF_THING = 'put-thing-attribute';
    const FILTER_THING_BY_ATTRIBUTE = 'get-filtered-things';
    /*thins of essence */
    const ADD_ESSENCE_THING_LINK = 'post-essence-thing';
    const REMOVE_ESSENCE_THING_LINK = 'delete-essence-thing';
    const VIEW_THINGS_OF_ESSENCE = 'get-essence-thing';

    private $viewer;
    private $router;

    public function __construct(PhpRenderer $viewer, Router $router)
    {
        $this->viewer = $viewer;
        $this->router = $router;
    }

    public function root(Request $request, Response $response, array $arguments)
    {
        $actionLinks = $this->setApiLinks();

        $response = $this->viewer->render($response,
            'api_menu.php',
            ['actionLinks' => $actionLinks]);

        return $response;
    }

    /**
     * @return array массив uri API вызовов
     */
    private function setApiLinks(): array
    {
        $actionLinks[self::DEFAULT] = $this->router->pathFor(self::DEFAULT);
        $actionLinks[self::ADD_ESSENCE] = $this->router->pathFor(self::ADD_ESSENCE, ['code' => 'code']);
        $actionLinks[self::VIEW_ESSENCE] = $this->router->pathFor(self::VIEW_ESSENCE, ['code' => 'code']);
        $actionLinks[self::STORE_ESSENCE] = $this->router->pathFor(self::STORE_ESSENCE, ['code' => 'code']);
        $actionLinks[self::VIEW_ESSENCE_CATALOG] = $this->router->pathFor(self::VIEW_ESSENCE_CATALOG);
        $actionLinks[self::FILTER_ESSENCE_CATALOG] = $this->router->pathFor(self::FILTER_ESSENCE_CATALOG, ['filter' => '']);
        $actionLinks[self::ADD_ATTRIBUTE] = $this->router->pathFor(self::ADD_ATTRIBUTE, ['code' => 'code']);
        $actionLinks[self::VIEW_ATTRIBUTE] = $this->router->pathFor(self::VIEW_ATTRIBUTE, ['code' => 'code']);
        $actionLinks[self::STORE_ATTRIBUTE] = $this->router->pathFor(self::STORE_ATTRIBUTE, ['code' => 'code']);
        $actionLinks[self::VIEW_ATTRIBUTE_CATALOG] = $this->router->pathFor(self::VIEW_ATTRIBUTE_CATALOG);
        $actionLinks[self::FILTER_ATTRIBUTE_CATALOG] = $this->router->pathFor(self::FILTER_ATTRIBUTE_CATALOG, ['filter' => '']);
        $actionLinks[self::ADD_ESSENCE_ATTRIBUTE_LINK] = $this->router->pathFor(self::ADD_ESSENCE_ATTRIBUTE_LINK, ['essence-code' => 'essence-code', 'attribute-code' => 'attribute-code']);
        $actionLinks[self::REMOVE_ESSENCE_ATTRIBUTE_LINK] = $this->router->pathFor(self::REMOVE_ESSENCE_ATTRIBUTE_LINK, ['essence-code' => 'essence-code', 'attribute-code' => 'attribute-code']);
        $actionLinks[self::VIEW_ATTRIBUTE_OF_ESSENCE] = $this->router->pathFor(self::VIEW_ATTRIBUTE_OF_ESSENCE, ['essence-code' => 'essence-code']);
        $actionLinks[self::ADD_THING] = $this->router->pathFor(self::ADD_THING, ['code' => 'code']);
        $actionLinks[self::VIEW_THING] = $this->router->pathFor(self::VIEW_THING, ['code' => 'code']);
        $actionLinks[self::STORE_THING] = $this->router->pathFor(self::STORE_THING, ['code' => 'code']);
        $actionLinks[self::FILTER_OF_ESSENCE] = $this->router->pathFor(self::FILTER_OF_ESSENCE, ['essence-code' => 'essence-code']);
        $actionLinks[self::ADD_ATTRIBUTE_TO_THING] = $this->router->pathFor(self::ADD_ATTRIBUTE_TO_THING, ['thing-code' => 'thing-code', 'attribute-code' => 'attribute-code']);
        $actionLinks[self::STORE_ATTRIBUTE_OF_THING] = $this->router->pathFor(self::STORE_ATTRIBUTE_OF_THING, ['thing-code' => 'thing-code', 'attribute-code' => 'attribute-code']);
        $actionLinks[self::FILTER_THING_BY_ATTRIBUTE] = $this->router->pathFor(self::FILTER_THING_BY_ATTRIBUTE, ['essence-code' => 'essence-code', 'filter' => '']);
        $actionLinks[self::ADD_ESSENCE_THING_LINK] = $this->router->pathFor(self::ADD_ESSENCE_THING_LINK, ['essence-code' => 'essence', 'thing-code' => 'thing']);
        $actionLinks[self::REMOVE_ESSENCE_THING_LINK] = $this->router->pathFor(self::REMOVE_ESSENCE_THING_LINK, ['essence-code' => 'essence', 'thing-code' => 'thing']);
        $actionLinks[self::VIEW_THINGS_OF_ESSENCE] = $this->router->pathFor(self::VIEW_THINGS_OF_ESSENCE, ['essence-code' => 'essence']);


        return $actionLinks;
    }

}
