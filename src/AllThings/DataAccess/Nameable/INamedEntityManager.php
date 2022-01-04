<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\DataAccess\Retrievable;

interface INamedEntityManager extends DataManager, Retrievable
{
    public function retrieveData(): Nameable;
}
