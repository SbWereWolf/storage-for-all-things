<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */


namespace AllThings\Catalog;


use AllThings\DataAccess\Crossover\ForeignKey;
use AllThings\DataAccess\Crossover\LinkageManager;
use AllThings\DataAccess\Retrievable;
use PDO;

class CatalogManager implements LinkageManager, Retrievable
{
    public const ESSENCE_IDENTIFIER = 'essence';
    public const THING_IDENTIFIER = 'thing';

    private $linkage = [];
    private $essenceForeignKey = null;
    private $thingForeignKey = null;
    private $dataPath = null;
    private $dataSet = [];

    public function __construct(string $essence, string $thing, PDO $dataPath)
    {
        $linkage[self::ESSENCE_IDENTIFIER] = $essence;
        $linkage[self::THING_IDENTIFIER] = $thing;
        $this->linkage = $linkage;

        $this->essenceForeignKey = new ForeignKey('essence', 'id', 'code');
        $this->thingForeignKey = new ForeignKey('thing', 'id', 'code');

        $this->dataPath = $dataPath;
    }

    public function setUp(): bool
    {
        $handler = $this->getHandler();
        $linkage = $this->linkage;

        $result = $handler->add($linkage);

        return $result;
    }

    /**
     * @return CatalogHandler
     */
    private function getHandler(): CatalogHandler
    {
        $handler = new CatalogHandler($this->dataPath);

        return $handler;
    }

    public function breakDown(): bool
    {
        $handler = $this->getHandler();
        $linkage = $this->linkage;

        $result = $handler->remove($linkage);

        return $result;
    }

    public function getAssociated(): bool
    {
        $handler = $this->getHandler();
        $linkage = $this->linkage;

        $result = $handler->getRelated($linkage);

        $isSuccess = $result === true;
        if ($isSuccess) {
            $this->dataSet = $handler->retrieveData();
        }

        return $result;
    }

    public function retrieveData()
    {
        $result = $this->dataSet;

        return $result;
    }

    public function has(): bool
    {
        return !is_null($this->dataSet);
    }
}
