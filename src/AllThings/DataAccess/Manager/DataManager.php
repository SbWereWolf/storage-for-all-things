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

    function create(): bool;

    function remove(): bool;

    function correct(string $targetIdentity): bool;

    function browse(string $targetIdentity): bool;
}
