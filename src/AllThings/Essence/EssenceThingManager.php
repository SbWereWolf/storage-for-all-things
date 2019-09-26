<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 21:43
 */


namespace AllThings\Essence;


use AllThings\DataAccess\Handler\EssenceThingHandler;
use AllThings\DataAccess\Handler\Retrievable;
use AllThings\DataAccess\Manager\LinkageManager;
use AllThings\DataObject\ForeignKey;
use PDO;

class EssenceThingManager implements LinkageManager, Retrievable
{
    const ESSENCE_IDENTIFIER = 'essence';
    const THING_IDENTIFIER = 'thing';

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

    function setUp(): bool
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

    function breakDown(): bool
    {
        $handler = $this->getHandler();
        $linkage = $this->linkage;

        $result = $handler->remove($linkage);

        return $result;
    }

    function getAssociated(): bool
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

    function retrieveData()
    {
        $result = $this->dataSet;

        return $result;
    }

    function has(): bool
    {
        return !is_null($this->dataSet);
    }
}
