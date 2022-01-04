<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\Blueprint\Specification;

use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\DataAccess\Crossover\ForeignKey;
use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\DataAccess\Crossover\LinkageManager;
use AllThings\DataAccess\Retrievable;
use PDO;

class SpecificationManager implements LinkageManager, Retrievable
{
    private $dataSet = [];
    private SpecificationHandler $handler;

    public function __construct(PDO $dataPath)
    {
        $essenceForeignKey = new ForeignKey('essence', 'id', 'code');
        $attributeForeignKey = new ForeignKey('attribute', 'id', 'code');
        $this->handler = new SpecificationHandler(
            $dataPath,
            $essenceForeignKey,
            $attributeForeignKey
        );
    }

    /**
     * @param string $attribute
     * @return IAttribute
     */
    private static function getDefault(string $attribute): IAttribute
    {
        $default = Attribute::GetDefaultAttribute();
        $default->setCode($attribute);
        return $default;
    }

    /**
     * @param string $attribute
     * @param PDO $dataPath
     * @return AttributeManager
     */
    private static function setupAttributeManager(string $attribute, PDO $dataPath): AttributeManager
    {
        $default = static::getDefault($attribute);
        $manager = new AttributeManager(
            $attribute,
            'attribute',
            $dataPath,
        );
        $manager->setSubject($default);
        return $manager;
    }

    public function linkUp(ICrossover $linkage): bool
    {
        $handler = $this->getHandler();

        $result = $handler->add($linkage);

        return $result;
    }

    /**
     * @return SpecificationHandler
     */
    private function getHandler(): SpecificationHandler
    {
        return $this->handler;
    }

    public function breakDown(ICrossover $linkage): bool
    {
        $handler = $this->getHandler();
        $result = $handler->remove($linkage);

        return $result;
    }

    public function getAssociated(ICrossover $linkage): bool
    {
        $handler = $this->getHandler();
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

    public static function getLocation(
        string $attribute,
        PDO $dataPath
    ) {
        $manager = static::setupAttributeManager(
            $attribute,
            $dataPath,
        );
        $table = $manager->getLocation();

        return $table;
    }

    public static function getFormat(
        string $attribute,
        PDO $dataPath
    ) {
        $manager = static::setupAttributeManager(
            $attribute,
            $dataPath,
        );
        $format = $manager->getFormat();

        return $format;
    }
}
