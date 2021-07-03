<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 04.07.2021, 2:22
 */

namespace AllThings\StorageEngine;


use Exception;

class Storage implements Storable
{
    private string $storeAt;

    public function __construct()
    {
        $this->storeAt = static::UNDEFINED;
    }

    public function getStorageKind(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $storeAt = $this->storeAt;

        return $storeAt;
    }

    /**
     * @throws Exception
     */
    public function setStorageKind(string $value): Storable
    {
        $isAcceptable = in_array(
            $value,
            static::AVAILABLE,
            true
        );
        if (!$isAcceptable) {
            throw new Exception('Storage kind'
                . ' MUST be one of :'
                . ' view | materialized view | table'
                . ", `$value` given");
        }
        $this->storeAt = $value;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function getStorableCopy(): Storable
    {
        $copy = new Storage();
        $copy->setStorageKind($this->storeAt);

        return $copy;
    }
}
