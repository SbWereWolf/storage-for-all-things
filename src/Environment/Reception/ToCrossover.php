<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 03.06.18 15:04
 */

namespace Environment\Reception;


use AllThings\DataObject\IContentUpdateCommand;
use AllThings\DataObject\ICrossover;

interface ToCrossover
{
    function fromPost(): ICrossover;

    function fromGet(): ICrossover;

    function fromPut(): IContentUpdateCommand;

}
