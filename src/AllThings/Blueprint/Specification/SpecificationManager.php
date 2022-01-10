<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\Blueprint\Specification;

use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Attribute\IAttribute;
use Exception;
use PDO;

class SpecificationManager
{
    /**
     * @param string $attribute
     *
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

    /**
     * @param string $attribute
     * @param PDO    $dataPath
     *
     * @return string
     * @throws Exception
     */
    public static function getLocation(
        string $attribute,
        PDO $dataPath
    ): string {
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
