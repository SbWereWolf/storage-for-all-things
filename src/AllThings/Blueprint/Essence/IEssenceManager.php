<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\Blueprint\Essence;


use AllThings\DataAccess\Nameable\DataManager;
use AllThings\DataAccess\Retrievable;

interface IEssenceManager extends DataManager, Retrievable
{

    public function retrieveData(): IEssence;

}
