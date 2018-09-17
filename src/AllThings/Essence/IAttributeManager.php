<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 0:58
 */

namespace AllThings\Essence;


use AllThings\DataAccess\Handler\Retrievable;
use AllThings\DataAccess\Manager\DataManager;

interface IAttributeManager extends DataManager, Retrievable
{

    function retrieveData(): IAttribute;

}