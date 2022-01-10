<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Linkage;

use AllThings\DataAccess\Retrievable;

interface ILinkageManager extends Retrievable
{
    public function attach(ILinkage $linkage): bool;

    public function detach(ILinkage $linkage): bool;

    /**
     * @param ILinkage $linkage
     *
     * @return bool
     */
    public function getAssociated(ILinkage $linkage): bool;
}
