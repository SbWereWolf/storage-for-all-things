<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 0:58
 */

namespace AllThings\Essence;


use AllThings\DataAccess\Handler\DataManager;
use AllThings\DataAccess\Handler\Retrievable;

interface IAttributeManager extends DataManager, Retrievable
{

    function retrieveData(): IAttribute;

}
