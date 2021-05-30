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

    public function getStoreAt(): string;

    public function setStoreAt(string $value): Storable;

    public function getStorableCopy(): Storable;
}
