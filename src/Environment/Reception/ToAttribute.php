<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

namespace Environment\Reception;


use Environment\Command\IAttributeUpdateCommand;

interface ToAttribute
{
    public function fromPost(): string;

    public function fromGet(): string;

    public function fromPut(): IAttributeUpdateCommand;

}
