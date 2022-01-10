<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Linkage;

interface LinkageWriter
{
    public function insert(ILinkage $entity): bool;

    public function delete(ILinkage $entity): bool;
}
