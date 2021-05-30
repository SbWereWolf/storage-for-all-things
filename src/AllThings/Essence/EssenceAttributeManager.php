<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 3:22
 */


namespace AllThings\Essence;


use AllThings\DataAccess\Handler\EssenceAttributeHandler;
use AllThings\DataAccess\Handler\Retrievable;
use AllThings\DataAccess\Manager\LinkageManager;
use AllThings\DataObject\ForeignKey;
use PDO;

class EssenceAttributeManager implements LinkageManager, Retrievable
{
    public const ESSENCE_IDENTIFIER = 'essence';
    public const ATTRIBUTE_IDENTIFIER = 'attribute';

    private $linkage = [];
    private $essenceForeignKey = null;
    private $attributeForeignKey = null;
    private $dataPath = null;
    private $dataSet = [];

    public function __construct(string $essence, string $attribute, PDO $dataPath)
    {
        $linkage[self::ESSENCE_IDENTIFIER] = $essence;
        $linkage[self::ATTRIBUTE_IDENTIFIER] = $attribute;
        $this->linkage = $linkage;

        $this->essenceForeignKey = new ForeignKey('essence', 'id', 'code');
        $this->attributeForeignKey = new ForeignKey('attribute', 'id', 'code');

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
     * @return EssenceAttributeHandler
     */
    private function getHandler(): EssenceAttributeHandler
    {
        $handler = new EssenceAttributeHandler($this->dataPath);

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
