<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Linkage;

interface RelationReader
{
    public function getRelated(ILinkage $linkage): bool;
}
