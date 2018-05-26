<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 26.05.18 12:26
 */

/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:33
 */

namespace AllThings\Common;


use AllThings\DataAccess\Handler\Retrievable;
use AllThings\DataAccess\Implementation\DataManager;
use AllThings\DataObject\Nameable;

interface INamedEntityManager extends DataManager, Retrievable
{

    function retrieveData(): Nameable;

}
