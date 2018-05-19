<?php
/**
 * Created by PhpStorm.
 * User: СЕРГЕЙ
 * Date: 19.05.2018
 * Time: 13:24
 */

namespace AllThings\DataObject;


class Storage implements Storable
{

    private $storage = '';

    function setStorage(string $storage): Storable
    {
        $this->storage = $storage;

        return $this;
    }

    function getStorage(): string
    {
        $storage = $this->storage;

        return $storage;
    }

    function getStorableCopy(): Storable
    {
        $copy = new Storage();
        $copy->setStorage($this->storage);

        return $copy;
    }
}