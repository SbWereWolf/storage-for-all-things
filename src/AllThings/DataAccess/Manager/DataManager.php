<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 18.09.18 0:49
 */

/**
 * Created by PhpStorm.
 * User: СЕРГЕЙ
 * Date: 19.05.2018
 * Time: 16:21
 */

namespace AllThings\DataAccess\Manager;


interface DataManager
{

    public function create(): bool;

    public function remove(): bool;

    public function correct(string $targetIdentity): bool;

    public function browse(): bool;
}
