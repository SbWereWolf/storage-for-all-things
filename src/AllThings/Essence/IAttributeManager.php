<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 0:58
 */

/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:33
 */

namespace AllThings\Essence;


use AllThings\DataAccess\Handler\Retrievable;
use AllThings\DataAccess\Implementation\DataManager;

interface IAttributeManager extends DataManager, Retrievable
{

    function retrieveData(): IAttribute;

}
