<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 1:21
 */

namespace Environment\Reception;


use AllThings\DataObject\IAttributeUpdateCommand;

interface ToAttribute
{
    public function fromPost(): string;

    public function fromGet(): string;

    public function fromPut(): IAttributeUpdateCommand;

}
