<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:44
 */


namespace AllThings\Catalog;


use AllThings\DataAccess\Handler\EssenceThingHandler;
use AllThings\DataAccess\Handler\Retrievable;
use AllThings\DataAccess\Manager\LinkageManager;
use AllThings\DataObject\ForeignKey;
use PDO;

class EssenceThingManager implements LinkageManager, Retrievable
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
     * @return EssenceThingHandler
     */
    private function getHandler(): EssenceThingHandler
    {
        $handler = new EssenceThingHandler($this->dataPath);

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
