<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\DataAccess\Linkage;

interface ILinkageManager
{
    public function attach(ILinkage $linkage): bool;

    public function detach(ILinkage $linkage): bool;

    /**
     * @param ILinkage $linkage
     *
     * @return array
     */
    public function getAssociated(ILinkage $linkage): array;
}
