<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Linkage;

use AllThings\DataAccess\Retrievable;

interface ILinkageHandler
    extends RelationReader,
            Retrievable
{
    public function combine(ILinkage $linkage): bool;

    public function split(ILinkage $linkage): bool;
}
