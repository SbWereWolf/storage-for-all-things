<?php
/**
 * Created by PhpStorm.
 * User: СЕРГЕЙ
 * Date: 19.05.2018
 * Time: 13:09
 */

namespace AllThings\DataObject;


interface Storable
{

    function getStoreAt(): \string;

    function setStoreAt(\string $value): Storable;

    function getStorableCopy(): Storable;
}
