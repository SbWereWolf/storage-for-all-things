<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:40
 */


namespace AllThings\Content;


use AllThings\DataAccess\Crossover\CrossoverHandler;
use AllThings\DataAccess\Crossover\CrossoverManager;
use AllThings\DataAccess\Crossover\CrossoverTable;
use AllThings\DataAccess\Crossover\ForeignKey;
use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\DataAccess\Retrievable;
use PDO;

class ContentManager implements CrossoverManager, Retrievable
{
    private ICrossover $container;
    private PDO $dataPath;
    private CrossoverHandler $handler;

    public function __construct(ICrossover $container, PDO $dataPath)
    {
        $this->container = $container->getCrossoverCopy();
        $this->dataPath = $dataPath;

        $thingKey = new ForeignKey(
            'thing',
            'id',
            'code'
        );
        $attributeKey = new ForeignKey(
            'attribute',
            'id',
            'code'
        );
        $contentTable = new CrossoverTable(
            'content',
            'thing_id',
            'attribute_id'
        );

        $this->handler = new CrossoverHandler(
            $this->container,
            $thingKey,
            $attributeKey,
            $contentTable,
            $this->dataPath
        );
    }


    public function attach(): bool
    {
        $result = $this->getHandler()->combine();

        return $result;
    }

    public function store(ICrossover $crossover): bool
    {
        $handler = $this->getHandler();
        $result = $handler->push($crossover);

        $this->unloadContainer($result, $handler);

        return $result;
    }

    public function take(ICrossover $crossover): bool
    {
        $handler = $this->getHandler();
        $result = $handler->pull($crossover);

        $this->unloadContainer($result, $handler);

        return $result;
    }

    public function has(): bool
    {
        return !is_null($this->container);
    }

    public function retrieveData(): ICrossover
    {
        $data = $this->container->getCrossoverCopy();

        return $data;
    }

    private function getHandler(): CrossoverHandler
    {
        return $this->handler;
    }

    /**
     * @param bool $result
     * @param CrossoverHandler $handler
     */
    private function unloadContainer(
        bool $result,
        CrossoverHandler $handler
    ): void {
        $isSuccess = $result === true;
        if ($isSuccess) {
            $this->container = $handler->retrieveData();
        }
    }
}
