<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 18.09.18 0:51
 */

namespace AllThings\DataAccess\Manager;


use AllThings\DataAccess\Handler\Retrievable;
use AllThings\DataObject\Nameable;

interface INamedEntityManager extends DataManager, Retrievable
{

    function retrieveData(): Nameable;

}
