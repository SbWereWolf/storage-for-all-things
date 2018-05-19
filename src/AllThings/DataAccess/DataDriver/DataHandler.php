<?php
/**
 * Created by PhpStorm.
 * User: СЕРГЕЙ
 * Date: 19.05.2018
 * Time: 16:21
 */

namespace AllThings\DataAccess\Implementation;


interface DataHandler
{

    function create(string $code): bool;

    function remove(string $code): bool;

    function correct(string $code): bool;

    function browse(string $code): bool;
}
