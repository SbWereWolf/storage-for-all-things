<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\SearchEngine;

use Exception;

class  Converter implements Findable
{
    /**
     * @throws Exception
     */
    public static function getFieldType(string $dataType): string
    {
        $sqlType = static::FIELD_TYPES[$dataType] ?? '';
        if (!$sqlType) {
            throw new Exception(
                "SQL data type for `$dataType` is not defined"
            );
        }

        return $sqlType;
    }

    /**
     * @throws Exception
     */
    public static function getDataLocation(string $dataType): string
    {
        $table = static::DATA_LOCATIONS[$dataType] ?? '';
        if (!$table) {
            throw new Exception(
                "Content storage for `$dataType` is not defined"
            );
        }

        return $table;
    }

    /**
     * @throws Exception
     */
    public static function getFieldFormat(string $dataType): string
    {
        $table = static::DATA_FORMATS[$dataType] ?? '';
        if (!$table) {
            throw new Exception(
                "Type conversion for `$dataType` is not defined"
            );
        }

        return $table;
    }
}