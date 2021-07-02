<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

namespace Environment\Reception;


use AllThings\DataObject\ICrossover;
use Environment\Command\IContentUpdateCommand;

interface ToCrossover
{
    public function fromPost(): ICrossover;

    public function fromGet(): ICrossover;

    public function fromPut(): IContentUpdateCommand;

}
