<?php
/**
 * src
 * © Volkhin Nikolay M., 2018
 * Date: 30.04.2018 Time: 1:27
 */

namespace AllThings\Development;

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
    /* Kind */
    const ADD_KIND = 'post-kind';
    const VIEW_KIND = 'get-kind';
    const STORE_KIND = 'put-kind';
    /* Kind catalog */
    const VIEW_KIND_CATALOG = 'get-whole-kinds';
    const FILTER_KIND_CATALOG = 'get-filtered-kinds';
    /* Kinds of Essence */
    const ADD_ESSENCE_KIND_LINK = 'post-essence-kind';
    const REMOVE_ESSENCE_KIND_LINK = 'delete-essence-kind';
    const VIEW_KIND_OF_ESSENCE = 'get-whole-essence-kinds';
    /* Thing */
    const ADD_THING = 'post-thing';
    const VIEW_THING = 'get-thing';
    const STORE_THING = 'put-thing';
    /* Essence filter */
    const FILTER_OF_ESSENCE = 'get-filter-for-essences';
    /* Kinds of thing */
    const ADD_KIND_TO_THING = 'post-thing-kind';
    const STORE_KIND_OF_THING = 'put-thing-kind';
    const FILTER_THING_BY_KIND = 'get-filtered-things';

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
        $actionLinks[self::ADD_ESSENCE] = $this->router->pathFor(self::ADD_ESSENCE,['code'=>'code']);
        $actionLinks[self::VIEW_ESSENCE] = $this->router->pathFor(self::VIEW_ESSENCE,['code'=>'code']);
        $actionLinks[self::STORE_ESSENCE] = $this->router->pathFor(self::STORE_ESSENCE,['code'=>'code']);
        $actionLinks[self::VIEW_ESSENCE_CATALOG] = $this->router->pathFor(self::VIEW_ESSENCE_CATALOG);
        $actionLinks[self::FILTER_ESSENCE_CATALOG] = $this->router->pathFor(self::FILTER_ESSENCE_CATALOG);
        $actionLinks[self::ADD_KIND] = $this->router->pathFor(self::ADD_KIND,['code'=>'code']);
        $actionLinks[self::VIEW_KIND] = $this->router->pathFor(self::VIEW_KIND,['code'=>'code']);
        $actionLinks[self::STORE_KIND] = $this->router->pathFor(self::STORE_KIND,['code'=>'code']);
        $actionLinks[self::VIEW_KIND_CATALOG] = $this->router->pathFor(self::VIEW_KIND_CATALOG);
        $actionLinks[self::FILTER_KIND_CATALOG] = $this->router->pathFor(self::FILTER_KIND_CATALOG);
        $actionLinks[self::ADD_ESSENCE_KIND_LINK] = $this->router->pathFor(self::ADD_ESSENCE_KIND_LINK,['essence-code'=>'essence-code','kind-code'=>'kind-code']);
        $actionLinks[self::REMOVE_ESSENCE_KIND_LINK] = $this->router->pathFor(self::REMOVE_ESSENCE_KIND_LINK,['essence-code'=>'essence-code','kind-code'=>'kind-code']);
        $actionLinks[self::VIEW_KIND_OF_ESSENCE] = $this->router->pathFor(self::VIEW_KIND_OF_ESSENCE,['essence-code'=>'essence-code']);
        $actionLinks[self::ADD_THING] = $this->router->pathFor(self::ADD_THING,['essence-code'=>'essence-code','thing-code'=>'thing-code']);
        $actionLinks[self::VIEW_THING] = $this->router->pathFor(self::VIEW_THING,['code'=>'code']);
        $actionLinks[self::STORE_THING] = $this->router->pathFor(self::STORE_THING,['code'=>'code']);
        $actionLinks[self::FILTER_OF_ESSENCE] = $this->router->pathFor(self::FILTER_OF_ESSENCE,['essence-code'=>'essence-code']);
        $actionLinks[self::ADD_KIND_TO_THING] = $this->router->pathFor(self::ADD_KIND_TO_THING,['thing-code'=>'thing-code','kind-code'=>'kind-code']);
        $actionLinks[self::STORE_KIND_OF_THING] = $this->router->pathFor(self::STORE_KIND_OF_THING,['thing-code'=>'thing-code','kind-code'=>'kind-code']);
        $actionLinks[self::FILTER_THING_BY_KIND] = $this->router->pathFor(self::FILTER_THING_BY_KIND,['essence-code'=>'essence-code']);


        return $actionLinks;
    }

}
