<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 1:21
 */

/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:08
 */

namespace AllThings\Reception;


use AllThings\DataObject\IAttributeUpdateCommand;

interface ForAttribute
{
    function fromPost(): \string;

    function fromGet(): \string;

    function fromPut(): IAttributeUpdateCommand;

}
