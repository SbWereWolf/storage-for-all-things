<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\StorageEngine;


class Storage implements Storable
{

    private $storeAt = '';

    public function getStoreAt(): string
    {
        $storeAt = $this->storeAt;

        return $storeAt;
    }

    public function setStoreAt(string $value): Storable
    {
        $this->storeAt = $value;

        return $this;
    }

    public function getStorableCopy(): Storable
    {
        $copy = new Storage();
        $copy->setStoreAt($this->storeAt);

        return $copy;
    }
}
