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

    private $storeAt = '';

    function getStoreAt(): string
    {
        $storeAt = $this->storeAt;

        return $storeAt;
    }

    function setStoreAt(string $storeAt): Storable
    {
        $this->storeAt = $storeAt;

        return $this;
    }

    function getStorableCopy(): Storable
    {
        $copy = new Storage();
        $copy->setStoreAt($this->storeAt);

        return $copy;
    }
}
