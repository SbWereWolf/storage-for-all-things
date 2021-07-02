<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:08
 */

namespace Environment\Reception;


use Environment\Command\NameableUpdateCommand;

interface ToNamed
{
    public function fromPost(): string;

    public function fromGet(): string;

    public function fromPut(): NameableUpdateCommand;

}
