<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 03.07.2021, 17:12
 */


namespace AllThings\Blueprint\Specification;


use AllThings\DataAccess\Crossover\ForeignKey;
use AllThings\DataAccess\Crossover\LinkageManager;
use AllThings\DataAccess\Retrievable;
use PDO;

class SpecificationManager implements LinkageManager, Retrievable
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
        $linkage[static::ESSENCE_IDENTIFIER] = $essence;
        $linkage[static::ATTRIBUTE_IDENTIFIER] = $attribute;
        $this->linkage = $linkage;

        $this->essenceForeignKey = new ForeignKey('essence', 'id', 'code');
        $this->attributeForeignKey = new ForeignKey('attribute', 'id', 'code');

        $this->dataPath = $dataPath;
    }

    public function linkUp(): bool
    {
        $handler = $this->getHandler();
        $linkage = $this->linkage;

        $result = $handler->add($linkage);

        return $result;
    }

    /**
     * @return SpecificationHandler
     */
    private function getHandler(): SpecificationHandler
    {
        $handler = new SpecificationHandler($this->dataPath);

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
