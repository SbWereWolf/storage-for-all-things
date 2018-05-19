<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 19.05.18 23:59
 */

namespace AllThings\DataObject;


use AllThings\Essence\IEssence;

interface IEssenceUpdateCommand
{
    function getParameter(): \string;

    function getSubject(): IEssence;

}
