<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Linkage;

interface ILinkageHandler
    extends RelationReading
{
    public function combine(ILinkage $linkage): bool;

    public function split(ILinkage $linkage): bool;
}
