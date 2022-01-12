<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\DataAccess\Linkage;

interface ILinkageHandler
    extends RelationReader
{
    public function combine(ILinkage $linkage): bool;

    public function split(ILinkage $linkage): bool;
}
