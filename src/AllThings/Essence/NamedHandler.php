<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:33
 */

namespace AllThings\Essence;


use AllThings\DataObject\Named;
interface NamedHandler
{
    function create(string $code): bool;
    function remove(string $code): bool;
    function correct(string $code): bool;
    function browse(string $code): bool;
    function getData(): Named;

}
