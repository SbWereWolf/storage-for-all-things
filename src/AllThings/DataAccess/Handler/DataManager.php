<?php
/**
 * Created by PhpStorm.
 * User: СЕРГЕЙ
 * Date: 19.05.2018
 * Time: 16:21
 */

namespace AllThings\DataAccess\Implementation;


interface DataManager
{

    function create(string $targetIdentity): bool;

    function remove(string $targetIdentity): bool;

    function correct(string $targetIdentity): bool;

    function browse(string $targetIdentity): bool;
}
