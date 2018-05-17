<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:08
 */

namespace AllThings\Reception;


use AllThings\DataObject\Named;

interface ForNamed
{
    function fromCreate(string $code, ?array $body):\string;

}
