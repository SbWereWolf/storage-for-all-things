<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 03.07.2021, 17:12
 */

namespace AllThings\StorageEngine;


use Exception;

class Storage implements Storable
{

    private $storeAt;

    public function __construct()
    {
        $this->storeAt = static::UNDEFINED;
    }

    public function getStorage(): string
    {
        $storeAt = $this->storeAt;

        return $storeAt;
    }

    public function setStorage(string $value): Storable
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
        if ($isAcceptable) {
            $this->storeAt = $value;
        }

        return $this;
    }

    public function getStorableCopy(): Storable
    {
        $copy = new Storage();
        $copy->setStorage($this->storeAt);

        return $copy;
    }
}
