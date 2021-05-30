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
    public function fromPost(): string;

    public function fromGet(): string;

    public function fromPut(): NameableUpdateCommand;

}
