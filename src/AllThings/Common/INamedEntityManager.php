<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 26.05.18 12:26
 */

namespace AllThings\Common;


use AllThings\DataAccess\Handler\DataManager;
use AllThings\DataAccess\Handler\Retrievable;
use AllThings\DataObject\Nameable;

interface INamedEntityManager extends DataManager, Retrievable
{

    function retrieveData(): Nameable;

}
