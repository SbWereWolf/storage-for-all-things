<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 03.06.18 11:29
 */


namespace AllThings\Content;


use AllThings\DataAccess\Handler\CrossoverHandler;
use AllThings\DataAccess\Handler\Retrievable;
use AllThings\DataAccess\Manager\CrossoverManager;
use AllThings\DataObject\CrossoverTable;
use AllThings\DataObject\ForeignKey;
use AllThings\DataObject\ICrossover;
use PDO;

class ContentManager implements CrossoverManager, Retrievable
{
    private $container = null;
    private $dataPath = null;
    private $thingKey = null;
    private $attributeKey = null;
    private $contentTable = null;

    public function __construct(ICrossover $container, PDO $dataPath)
    {
        $this->container = $container->getCrossoverCopy();
        $this->dataPath = $dataPath;

        $this->thingKey = new ForeignKey('thing', 'id', 'code');
        $this->attributeKey = new ForeignKey('attribute', 'id', 'code');
        $this->contentTable = new CrossoverTable('content', 'thing_id', 'attribute_id');
    }


    function attach(): bool
    {
        $handler = $this->getHandler();

        $result = $handler->crossing();

        return $result;
    }

    private function getHandler(): CrossoverHandler
    {
        $handler = new CrossoverHandler($this->container, $this->thingKey, $this->attributeKey, $this->contentTable, $this->dataPath);

        return $handler;
    }

    function store(ICrossover $crossover): bool
    {
        $handler = $this->getHandler();

        $result = $handler->setCrossover($crossover);

        $this->loadContainer($result, $handler);

        return $result;
    }

    /**
     * @param bool             $result
     * @param CrossoverHandler $handler
     */
    private function loadContainer(bool $result, CrossoverHandler $handler): void
    {
        $isSuccess = $result === true;
        if ($isSuccess) {
            $this->container = $handler->retrieveData();
        }
    }

    function take(ICrossover $crossover): bool
    {
        $handler = $this->getHandler();

        $result = $handler->getCrossover($crossover);

        $this->loadContainer($result, $handler);

        return $result;
    }

    function retrieveData(): ICrossover
    {
        $data = $this->container->getCrossoverCopy();

        return $data;
    }

    function has(): bool
    {
        return !is_null($this->container);
    }
}
