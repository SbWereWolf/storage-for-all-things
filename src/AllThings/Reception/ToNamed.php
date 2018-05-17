<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:06
 */

namespace AllThings\Reception;


class ToNamed implements ForNamed
{


    function fromCreate(string $code, ?array $body): \string
    {
        return $code;
    }
}
