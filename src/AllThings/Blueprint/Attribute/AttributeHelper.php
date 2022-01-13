<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 13:52
 */

namespace AllThings\Blueprint\Attribute;

use Exception;
use PDO;

class AttributeHelper
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
        $manager->setAttribute($default);
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
