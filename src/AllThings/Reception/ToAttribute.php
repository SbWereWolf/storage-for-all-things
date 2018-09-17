<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 1:21
 */

namespace AllThings\Reception;


use AllThings\DataObject\IAttributeUpdateCommand;

interface ToAttribute
{
    function fromPost(): \string;

    function fromGet(): \string;

    function fromPut(): IAttributeUpdateCommand;

}
