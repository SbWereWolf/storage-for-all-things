<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

/**
 * Created by PhpStorm.
 * User: СЕРГЕЙ
 * Date: 19.05.2018
 * Time: 13:09
 */

namespace AllThings\StorageEngine;


interface Storable
{

    public function getStoreAt(): string;

    public function setStoreAt(string $value): Storable;

    public function getStorableCopy(): Storable;
}
