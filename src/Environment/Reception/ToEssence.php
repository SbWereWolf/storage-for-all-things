<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:08
 */

namespace AllThings\Reception;


use AllThings\DataObject\IEssenceUpdateCommand;

interface ToEssence
{
    function fromPost(): string;

    function fromGet(): string;

    function fromPut(): IEssenceUpdateCommand;

}
