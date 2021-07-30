<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\DataAccess\Nameable;


use AllThings\DataAccess\Retrievable;

interface INamedEntityManager extends DataManager, Retrievable
{

    public function retrieveData(): Nameable;

}
