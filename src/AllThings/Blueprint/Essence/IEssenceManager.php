<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
 */

namespace AllThings\Blueprint\Essence;


use AllThings\DataAccess\DataTransfer\Haves;
use AllThings\DataAccess\DataTransfer\Retrievable;
use AllThings\DataAccess\Nameable\DataManager;

interface IEssenceManager
    extends DataManager,
            Retrievable,
            Haves
{

    public function retrieve(): IEssence;

}
