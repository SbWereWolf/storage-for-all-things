<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:08
 */

namespace Environment\Reception;


use AllThings\DataObject\NameableUpdateCommand;

interface ToNamed
{
    function fromPost(): string;

    function fromGet(): string;

    function fromPut(): NameableUpdateCommand;

}